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
# "741852963" will need to be a unique user generated id or validation/submission will fail. This is also the id used to cancel an order
def create_order()
	order_data = "{\"orderData\": {\"shipments\": [{\"shipTo\": {\"town\": \"New York\", \"isoCountry\": \"US\", \"state\": \"New York\", \"name\": \"John Doe\", \"phone\": \"01234567890\", \"address1\": \"5th Avenue\", \"email\": \"johnd@acme.com\", \"postcode\": \"12345\"}, \"carrier\": {\"code\": \"customer\", \"service\": \"pickup\"}}], \"items\": [{\"sku\": \"Business Cards\", \"sourceItemId\": \"1\", \"components\": [{\"path\": \"https://Server/Path/business_cards.pdf\", \"code\": \"Content\", \"fetch\": \"true\"}], \"quantity\": 1}], \"postbackAddress\": \"http://postback.genesis.com\", \"sourceOrderId\": \"741858555552963\"}, \"destination\": {\"name\": \"hp.jpeng\"}}"
	return order_data
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

validate_order()
#submit_order()
#get_all_orders()
#get_order("OrderId")
#cancel_order("sourceAccount", "sourceOrderId")