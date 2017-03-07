#JavaScript

##General Information

Code was written in PHP 5.6.24

##How To Run / Program Information

Installing XAMPP and adding the box_api.php file to the htdocs folder is a quick way to run the code.

Before you can run the code, you need to provide the Key/Secret. There are two baseUrls provided. Uncomment the one that your Key/Secret was created/provided in.

The initial functions will validate the premade order. The premade order structure follows the structure documented [here](https://developers.hp.com/printos/doc/order-json-structure) 

Submitting an order will return information relating to it. See [../sample_output/submit_order_output.txt](https://github.com/HPInc/printos-siteflow-api-samples/blob/master/sample_output/submit_order_output.txt) to see the return information for a successful submission. Line 7 of the file is the id you pass into getOrder(). To cancel one of the orders, you need the source account name (Line 241) and source order id (Line 234). The source order id is user generated.

Note: The sample output is in python so the initial print statements will be different, but the structure of the JSON should be the same.
