# PrintOS Site Flow

The PrintOS Site Flow API allows 3rd party applications to validate and submit orders to the Site Flow production application.

The PrintOS Site Flow API is a RESTful HTTP-based API that allows you to validate and submit production orders, query and cancel existing orders and receive postback messages for order status updates.

##General Information
The use of the Site Flow API requires the generation of HMAC authentication headers. These are generated with a Key/Secret from PrintOS.
If you're a third party without a Key/Secret, visit [Site Flow API Access] (https://developers.hp.com/printos/doc/site-flow-api) to request access.

For existing PrintOS users, you can generate your own Key/Secret following the instructions [Here] (https://www.dropbox.com/s/qbwmnniehw2jn0s/Requesting%20an%20API.pdf?dl=0)

* Creating HMAC: https://developers.hp.com/printos/doc/site-flow-authentication-0
* API Documentation: https://developers.hp.com/printos/api/site-flow-api-documentation
* Structure of Order JSON: https://developers.hp.com/printos/doc/order-json-structure
* Support Forums: https://developers.hp.com/forums/printos

##Building and Running
Information for running code in each language can be found within each language's folder.
