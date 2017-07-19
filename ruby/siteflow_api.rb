# © Copyright 2016 HP Development Company, L.P.
# SPDX-License-Identifier: MIT

require 'json'
require 'net/http'
require 'openssl'
require 'time'

#Access credentials
#$baseUrl = "https://printos.api.hp.com/siteflow";	 		#use for a production account
#$baseUrl = "https://stage.printos.api.hp.com/siteflow";	#use for a staging account
$key = "";
$secret = "";

#SiteFlow APIs
#--------------------------------------------------------------#

# Cancels an order in Site Flow.
#
# Param: 
#   source_account - name of the source account
#   order_id - source order id of the order (user generated)
def cancel_order(source_account, order_id)
	puts "Canceling order: " + order_id + " from: " + source_account
	response = request_put("/api/order/" + source_account + "/" + order_id + "/cancel")
	puts response.body
end

# Gets a list of all orders in Site Flow.
def get_all_orders() 
	puts "Getting all orders"
	response = request_get("/api/order")
	puts response.body
end

# Gets an order with the specified order id in Site Flow
#
# Param: 
#   order_id - id of the order (SiteFlow generated)
def get_order(order_id)
	puts "Getting order: " + order_id
	response = request_get("/api/order/" + order_id)
	puts response.body
end

# Gets a list of products in Site Flow.
def get_products()
	puts "Getting products"
	response = request_get("/api/product")
	puts response.body
end

# Gets a list of skus in Site Flow.
def get_skus()
	puts "Getting skus"
	response = request_get("/api/sku")
	puts response.body
end

# Gets the amazon aws upload urls for a file.
#
# Param: 
#   mime_type - MIME type of the file to upload
def get_upload_urls(mime_type)
	puts "Getting upload urls"
	response = request_get_with_param('/api/file/getpreupload', mime_type)
	puts response.body
end

# Submits an order into SiteFlow
def submit_order()
	puts "Submitting order"
	data = create_order()
	response = request_post("/api/order", data)
	puts response.body
end

# Validates an order to see if its able to be submitted successfully
def validate_order() 
	puts "Validating order" 
	data = create_order()
	response = request_post("/api/order/validate", data)
	puts response.body
end


#Helper functions
#--------------------------------------------------------------#

# Creates the HMAC header to authenticate the API calls.
#
# Param: 
#   method - type of http method (GET, POST, PUT)
#   path - api path
#   timestamp - time in utc format
def create_hmac_auth(method, path, timestamp)
	str = method + ' ' + path + timestamp;
	sha1 = OpenSSL::Digest.new('sha1')
	hash = OpenSSL::HMAC.hexdigest(sha1, $secret, str)
	return $key + ":" + hash
end

# Creates a mock order to test validate and submission of an order
#
# Note: "hp.jpeng" will need to be changed to your own printos account username
def create_order()
	{
		"destination" => {
			"name" => "hp.jpeng"
		},
		"orderData" => {
			"sourceOrderId" => [*('A'..'Z')].sample(8).join,
			"items" => [{
				"sourceItemId" => [*('A'..'Z')].sample(8).join,
				"sku" => "Flat",
				"quantity" => 1,
				"components" => [{
					"code" => "Content",
					"path" => "https://Server/Path/business_cards.pdf",
					"fetch" => "true",
					# "route" => [{
					# 		"name" => "Print",
					# 		"eventTypeId" => ""		#eventTypeId found within Site Flow -> Events
					# 	}, {
					# 		"name" => "Cut",
					# 		"eventTypeId" => ""
					# 	}, {
					# 		"name" => "Laminate",
					# 		"eventTypeId" => ""
					# 	}, {
					# 		"name" => "Finish",
					# 		"eventTypeId" => ""
					# }]
				}],
			}],
			"shipments" => [{
				"shipTo" => {
					"name" => "John Doe",
					"address1" => "5th Avenue",
					"town" => "New York",
					"postcode" => "12345",
					"state" => "New York",
					"isoCountry" => "US",
					"email" => "johnd@acme.com",
					"phone" => "01234567890"
				},
				"carrier" => {
					"code" => "customer",
					"service" => "shipping"
				}
			}]
		}
	}.to_json()
end


#GET, POST, and PUT
#--------------------------------------------------------------#

# HTTP GET request
#
# Param: 
#   path - api path
#
# Note: baseUrl + path will be the full url to call a certain api
def request_get(path)
	timestamp = Time.now.utc.iso8601
	auth = create_hmac_auth("GET", path, timestamp)
	
	uri = URI($baseUrl + path)

	request = Net::HTTP::Get.new(uri)
	request.add_field("x-hp-hmac-authentication", auth)
	request.add_field("x-hp-hmac-date", timestamp)

	response = Net::HTTP.start(uri.host, uri.port,
		:use_ssl => uri.scheme == 'https',
		:verify_mode => OpenSSL::SSL::VERIFY_NONE) do |http|
		http.request(request)
	end

	return response
end

# HTTP GET request with a mime_type parameter
#
# Param: 
#   path - api path
#   param - MIME type to be added to the end of path
#
# Note: +baseUrl + path + param will be the full url to get the upload urls specific to the MIME type.
def request_get_with_param(path, param)
	timestamp = Time.now.utc.iso8601
	auth = create_hmac_auth("GET", path, timestamp)
	
	uri = URI($baseUrl + path + "?mimeType=" + param)

	request = Net::HTTP::Get.new(uri)
	request.add_field("x-hp-hmac-authentication", auth)
	request.add_field("x-hp-hmac-date", timestamp)

	response = Net::HTTP.start(uri.host, uri.port,
		:use_ssl => uri.scheme == 'https',
		:verify_mode => OpenSSL::SSL::VERIFY_NONE) do |http|
		http.request(request)
	end
end

# HTTP POST request
#
# Param: 
#   path - api path
#   data - json data to post
#
# Note: baseUrl + path will be the full url to call a certain api
def request_post(path, data)
	timestamp = Time.now.utc.iso8601
	auth = create_hmac_auth("POST", path, timestamp)
	
	uri = URI($baseUrl + path)

	request = Net::HTTP::Post.new(uri)
	request.add_field("Content-Type", "application/json")
	request.add_field("x-hp-hmac-authentication", auth)
	request.add_field("x-hp-hmac-date", timestamp)
	request.body = data

	response = Net::HTTP.start(uri.host, uri.port,
		:use_ssl => uri.scheme == 'https',
		:verify_mode => OpenSSL::SSL::VERIFY_NONE) do |http|
		http.request(request)
	end

	return response
end

# HTTP PUT request
#
# Param: 
#   path - api path
#
# Note: baseUrl + path will be the full url to call a certain api
def request_put(path)
	timestamp = Time.now.utc.iso8601
	auth = create_hmac_auth("PUT", path, timestamp)
	
	uri = URI($baseUrl + path)

	request = Net::HTTP::Put.new(uri)
	request.add_field("x-hp-hmac-authentication", auth)
	request.add_field("x-hp-hmac-date", timestamp)

	response = Net::HTTP.start(uri.host, uri.port,
		:use_ssl => uri.scheme == 'https',
		:verify_mode => OpenSSL::SSL::VERIFY_NONE) do |http|
		http.request(request)
	end

	return response
end


#Function Calls 
#--------------------------------------------------------------#

#validate_order()
#submit_order()
#get_products()
#get_skus()
#get_upload_urls("application/pdf")
#get_all_orders()
#get_order("OrderId")
#cancel_order("sourceAccount", "sourceOrderId")
