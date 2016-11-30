# PrintOS Site Flow

The PrintOS SiteFlow API allows 3rd party applications to validate and submit orders to the SiteFlow production application.

The PrintOS SiteFlow API is a RESTful HTTP-based API that allows you to validate and submit production orders, query and cancel existing orders and receive postback messages for order status updates.

##General Information
The use of the SiteFlow API requires the generation of HMAC authentication headers. These are generated with a Key/Secret that can be generated from within a PrintOS Print Service Provider (PSP) account.
If you are a third party company without a PrintOS account, visit [SiteFlow API Access] (https://developers.hp.com/printos/doc/api-authentication) to request access and an HP representative will review your request for generation of a development account.

For existing PrintOS users, you can generate your own Key/Secret following the instructions [Here] (https://www.dropbox.com/s/qbwmnniehw2jn0s/Requesting%20an%20API.pdf?dl=0)

##Additional Detail
* Generating HMACs for API Authentication: https://developers.hp.com/printos/doc/api-authentication
* API Documentation: https://developers.hp.com/printos/doc/site-flow-documentation
* Structure of Order JSON: https://developers.hp.com/printos/doc/order-json-structure
* Support Forums: https://developers.hp.com/forums/printos (Post SiteFlow specific enquiries under the "PrintOS SiteFlow API" forum) 

##Building and Running
Information for running code in each language can be found within each language's folder.
