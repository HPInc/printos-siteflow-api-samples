#Python

##General Information

Code was written in Python 3.5.2
Uses the "requests" module so that will need to be installed in order to run the code

Windows: Go to location of easy_install.exe

```easy_install.exe requests```

Linux/Mac:

```sudo easy_install requests```

It also uses json, hmac, hashlib, datetime, base64, string, random modules as well. If they aren't found, you may need to install these as well.

##How To Run / Program Information

Run on the command line using ```python siteflow_api.py```

Before you can run the code, you need to provide the Key/Secret (Line 10/11). There are two baseUrls provided. Uncomment the one that your Key/Secret was created/provided in.

The initial functions will validate the premade order. The premade order structure follows the structure documented [here] (https://developers.hp.com/printos/doc/order-json-structure) 

Submitting an order will return information relating to it. See [../sample_output/submit_order_output.txt] (https://github.com/HPInc/printos-siteflow-api-samples/blob/master/sample_output/submit_order_output.txt) to see the return information for a successful submission. Line 7 of the file is the id you pass into get_order(). To cancel one of the orders, you need the source account name (Line 241) and source order id (Line 234). The source order id is user generated (in this case, we generated a 6 character string).
