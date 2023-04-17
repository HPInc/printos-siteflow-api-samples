require './oneflowclient'
require './models/order'
require './models/item'
require './models/component'
require './models/shipment'
require './models/carrier'
require './models/address'
require 'json'

#initialise the sdk
endpoint = "ONEFLOW-ENDPOINT" # Don't forget the /api at the end of the endpoint
token = "ONEFLOW-TOKEN"
secret = "ONEFLOW-SECRET"

#order settings
destination = "DESTINATION-ACCOUNT"
orderId = "order-"+rand(1000000).to_s
itemId = orderId + "-1"
fetchUrl = "FETCHURL"
quantity = 1
skuCode = "SKU-CODE"
componentCode = "COMPONENT-CODE"
carrierCode = "CARRIER-CODE"
carrierService = "CARRIER-SERVICE"
name = "Nigel Watson"
address1 = "999 Letsbe Avenue"
town = "London"
postcode = "EC2N 0BJ"
isoCountry = "GB"

# Retry options
class Options
    attr_accessor :retries

    def initialize()
        @retries = 3
    end

    def retryCondition(response)
        return response.code != 200 # retry everything but OK
    end

    def retryDelay(response, retryCount)
        sleep 1 # just delay 1 sec
    end
end

#create the onelflow client. Pass Options.new as 4th argument and customize logic or skip for defaults (exponential retry in case of 429)
client = OneflowClient.new(endpoint, token, secret)

# No need to edit below here

order = client.create_order(destination)
order.orderData.sourceOrderId = orderId
item = Item.new
item.sku = skuCode
item.quantity = quantity
item.sourceItemId = itemId

component = Component.new
component.code = componentCode
component.fetch = false
component.path = fetchUrl
item.components.push(component)
order.orderData.items.push(item)

shipment = Shipment.new
shipment.carrier = Carrier.new(carrierCode, carrierService)
address = Address.new
address.name = name
address.address1 = address1
address.town = town
address.isoCountry = isoCountry
address.postcode = postcode
shipment.shipTo = address
order.orderData.shipments.push(shipment)

response = client.submit_order
response_json = JSON.parse(response.body)

if (response.code != 200)
    puts "Error"
    puts "====="
    puts response_json["error"] ? response_json["error"]["message"] : response_json["message"]
    if (response_json["error"] && response_json["error"]["validations"])
        response_json["error"]["validations"].each {|val| puts val["path"] + " -> " + val["message"]}
    end
else
    saved_order = response_json["order"]

    puts "Success"
    puts "======="
    puts "Order ID        : " + saved_order["_id"]

#    for file in saved_order["files"]
#        puts "Uploading File  : " + file["_id"]
#        # res = client.upload_file(file, "files/" + file["path"])
#    end
end
