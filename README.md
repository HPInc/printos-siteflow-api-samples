# HP Site Flow

The HP SiteFlow API allows 3rd party applications to validate and submit orders and perform a wide range of production-based actions to the Site Flow application.

The HP SiteFlow API is a RESTful HTTP-based API that allows you to validate and submit production orders, query and cancel existing orders and receive postback messages for order status updates.

## General Information
The use of the SiteFlow API requires the generation of HMAC authentication headers. These are generated with a Key/Secret that can be obtained from your HP Site Flow support team.
If you are a third party company without a PrintOS account, visit [SiteFlow API Access](https://developers.hp.com/printos/doc/api-authentication) to request access and an HP representative will review your request for generation of a development account.

For existing Site Flow users, you can open a support ticket to request a token/secret for your account.

## Additional Detail
* Generating HMACs for API Authentication: https://hpsiteflow.com/docs/siteflow/api-authentication.html
* API Documentation: https://hpsiteflow.com/docs/api-reference/siteflow-pro.html
* Structure of Order JSON: https://hpsiteflow.com/docs/siteflow/order-structure.html

## Building and Running
Information for running code in each language can be found within each language's folder.
