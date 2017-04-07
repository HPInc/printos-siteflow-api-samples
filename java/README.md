# Java

## General Information

Code was written in Eclipse Neon

External libraries used:
 * Apache HttpComponents
 * org.json

The JAR files to the libraries are not included. You will need to download and configure the jars to your build path to compile and run the project. You may also use different libraries to create the needed JSON strings and Http requests.

## How To Run / Program Information

The SiteFlow_Example.java class is the one that you will run.

Before you can run the code, you need to provide the Key/Secret. The SiteFlow.java class has two baseUrls. Uncomment the one that your Key/Secret was created/provided in.

The initial methods will validate the premade order. The premade order structure follows the structure documented [here](https://developers.hp.com/printos/doc/order-json-structure) 

Submitting an order will return information relating to it. See [../sample_output/submit_order_output.txt](https://github.com/HPInc/printos-siteflow-api-samples/blob/master/sample_output/submit_order_output.txt) to see the return information for a successful submission. Line 7 of the file is the id you pass into GetOrder(). To cancel one of the orders, you need the source account name (Line 241) and source order id (Line 234). The source order id is user generated. Having an order with an existing source order id will cause the validate and submit order to fail.

Note: The sample output is in python so the initial print statements will be different, but the structure of the JSON should be the same.