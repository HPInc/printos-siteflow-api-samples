#!/usr/bin/python

__author__ = 'printos'

import requests, json, hmac, hashlib, datetime, base64, string, random

#access credentials
#baseUrl = "https://printos.api.hp.com/siteflow"		#use for production server account
baseUrl = "https://stage.printos.api.hp.com/siteflow"	#use for staging server account
key = ''
secret = ''
destination = 'hp.jpeng'


#--------------------------------------------------------------#


'''
Cancels an order

Params:
  sourceAccount - Account the order was sent to
  order_id - ID of the order when it was created
'''
def cancel_order(sourceAccount, order_id):
	print("Canceling Order")
	path = '/api/order/' + sourceAccount + '/' + order_id + "/cancel"
	timestamp = datetime.datetime.utcnow().strftime('%Y-%m-%dT%H:%M:%SZ')
	headers = create_headers("PUT", path, timestamp)
	url = baseUrl + path
	print("URL: " + url)
	return requests.put(url, headers=headers)


'''
Creates the header using the key/secret which
allows you to make the API calls

Params:
  method - type of method (POST, GET, PUT, etc)
  path - api path (excluding the base url)
  timestamp - current time in specified format
'''
def create_headers(method, path, timestamp):
	string_to_sign = method + ' ' + path + timestamp
	local_secret = secret.encode('utf-8')
	string_to_sign = string_to_sign.encode('utf-8')
	signature = hmac.new(local_secret, string_to_sign, hashlib.sha1).hexdigest()
	genesis_auth = key + ':' + signature
	return {'content-type': 'application/json',
		'x-hp-hmac-authentication': genesis_auth,
		'x-hp-hmac-date': timestamp,
		'x-hp-hmac-algorithm' : 'SHA1'
	}


'''
Retrieves information on all orders
'''
def get_all_orders():
	print("Getting all orders")
	return request_get('/api/order/')


'''
Retrieves information about an order

Params:
  order_id - ID of the order (when it was created) 
'''
def get_order(order_id):
	print("Retrieving Order")
	return request_get('/api/order/' + order_id)


'''
Generates an ID for the order
'''
def id_generator(size=6, chars=string.ascii_uppercase + string.digits):
	return ''.join(random.choice(chars) for _ in range(size))


'''
Prints the data into a cleaner JSON format

Params:
  data - data that needs to be printed into JSON format
'''
def print_json(data):
	print(json.dumps(data.json(), indent=4, sort_keys=True))


'''
GET call

Params:
  path - api path (excluding the base url)
'''
def request_get(path):
	print("In request_get() function")
	timestamp = datetime.datetime.utcnow().strftime('%Y-%m-%dT%H:%M:%SZ')
	print(" Timestamp: ", timestamp)
	url = baseUrl + path
	headers = create_headers("GET", path, timestamp)
	result = requests.get(url, headers=headers)
	return result


'''
POST call

Params:
  path - api path (excluding the base url)
  data - data to be posted
'''
def request_post(path, data):
	print("In request_post() function")
	timestamp = datetime.datetime.utcnow().strftime('%Y-%m-%dT%H:%M:%SZ')
	print(" Timestamp: ", timestamp)
	url = baseUrl + path
	headers = create_headers("POST", path, timestamp)
	result = requests.post(url, data, headers=headers)
	return result


''''
Submit an order into SiteFlow

Params:
  order - Order to be submitted (validate order first to make sure order is in correct format)
'''
def submit_order(order):
	print ("Submitting Order")
	return request_post('/api/order', order)


'''
Checks if an order has all the required information and is in the correct format

Params:
  order - Order to be validated
'''
def validate_order(order):
	print ("Validating Order")
	return request_post('/api/order/validate', order)


#--------------------------------------------------------------#

'''
Variables required for a single item order
Not the full set of fields available
'''
componentCode = "Content"
fetchPath = "https://Server/Path/business_cards.pdf"
itemId = id_generator()
orderId = id_generator()
postbackAddress = "http://postback.genesis.com"
quantity = 1
sku = "Business Cards"


'''
For more information about the JSON structure of the Order
See the documentation found at: https://developers.hp.com/printos/doc/order-json-structure
'''

#Shipping Information
shipTo = {
	"name":           "John Doe",
	"address1":       "5th Avenue",
	"town":           "New York",
	"postcode":       "12345",
	"state":          "New York",
	"isoCountry":     "US",
	"email":          "johnd@acme.com",
	"phone":          "01234567890"
}

#Courier Services
carrier = {
	"code":       "customer",
	"service":    "shipping"
}

#Create an Item
item = {
	"sourceItemId":         itemId,
	"sku":                  sku,
	"quantity":             quantity,
	"components": [{
		"code":             componentCode,
		"path":             fetchPath,
		"fetch":			"true"
	}]
}

#Create a shipment
shipment = {
	"shipTo":  shipTo,
	"carrier": carrier
}

#Put the complete order together
order = {
	"destination": {
		"name": destination												
	},
	"orderData": {
		"sourceOrderId": orderId,										
		"postbackAddress": postbackAddress,
		"items": [ item ],
		"shipments": [ shipment ]
	}
}

#--------------------------------------------------------------#


print_json(validate_order(json.dumps(order)))							
#print_json(submit_order(json.dumps(order)))							
#print_json(get_all_orders())											
#print_json(get_order("OrderId"))						
#print_json(cancel_order("hp.jpeng", "sourceOrderId"))							