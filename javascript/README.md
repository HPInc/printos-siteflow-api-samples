# JavaScript

## General Information

The modules that the code requires can be installed using npm. You can download Nodejs which comes with npm together with it.

Modules required:
* superagent
* superagent-proxy

Install modules using:
> npm install

## How To Run / Program Information

Run on the command line using ```node app.js```

Before you can run the code, you need to provide the Key/Secret in app.js. There are two baseUrls provided. Uncomment the one that your Key/Secret was created/provided in.

The initial functions will validate the premade order. The premade order structure follows the structure documented [here](https://developers.hp.com/printos/doc/order-json-structure) 

Submitting an order will return information relating to it. See [../sample_output/submit_order_output.txt](https://github.com/HPInc/printos-siteflow-api-samples/blob/master/sample_output/submit_order_output.txt) to see the return information for a successful submission. Line 7 of the file is the id you pass into get_order(). To cancel one of the orders, you need the source account name (Line 241) and source order id (Line 234). The source order id is user generated.

Note: The sample output is in python so the initial print statements will be different, but the structure of the JSON should be the same.
