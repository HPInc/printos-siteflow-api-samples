#Ruby

##General Information

Code was written in Ruby 2.3.1

The code uses Net::HTTP and sets the verify_mode to VERIFY_NONE. This is a easy and quick way to bypass verifying TLS certificates. You may want to change this if security is a priority.

##How To Run / Program Information

Run on the command line using ```ruby box_api.rb```

Before you can run the code, you need to provide the Key/Secret (Line 10/11). There are two baseUrls provided. Uncomment the one that your Key/Secret was created/provided in.

The initial functions will validate the premade order. The premade order structure follows the structure documented [here] (https://developers.hp.com/printos/doc/order-json-structure) 

Submitting an order will return information relating to it. See [../sample_output/submit_order_output.txt] (https://github.com/HPInc/printos-siteflow-api-samples/blob/master/sample_output/submit_order_output.txt) to see the return information for a successful submission. Line 7 of the file is the id you pass into get_order(). To cancel one of the orders, you need the source account name (Line 241) and source order id (Line 234). The source order id is user generated.

Note: The sample output is in python so the initial print statements will be different, but the structure of the JSON should be the same.
