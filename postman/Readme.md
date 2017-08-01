# Postman Collection

## General Information

This is an exported collection for the Postman application (exported as a v2 Collection).  It allows you to import a working set of API calls to test the functionality of the current APIs.

The Postman application can be downloaded here:  https://www.getpostman.com/

## How To Run / Program Information

1. Import the "Site Flow API.postman_collection.json" file in the main Import dialog of Postman.
2. Import the "globals.postman_globals.json" file in the main import dialog of Postman to add the necessary global variables (Note: globals can be manually added if necessary).
3. Import either the "Production Create+Query.postman_environment.json" or "Staging Create+Query.postman_environment.json" file through the Manage Environments dialog in the upper right settings menu
	- "Production Environment..." is used for standard PrintOS accounts in the main production environment (most common)
	- "Staging Environment..." is used if you were provided a development account for API development (less common)
3. In the Manage Environments dialog edit the environment you just imported and replace the "key" and "secret" values with the key and secret from the PrintOS account you are using.  Click "Update" to save changes.
4. Select the Environment you just imported in the environment drop-down menu.
5. In the Site Flow API collection in the left pane select the API call you wish to make then click the "Send" button to send the API call.  

## About the Postman collection

- A Pre-request Script inside the collection uses the CryptoJS library to dynamically generate the authentication HMAC.  
- This Pre-request Script also sets the necessary environment variables which are used in the HTTP Headers section for each call.
- The response field can be used to capture response JSON messages from the Box API calls.

NOTE: Some of the API calls such as Site Flow Query One Order are dependant on earlier API calls.  You should submit at least one order through Postman before querying an order.