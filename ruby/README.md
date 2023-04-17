# Ruby SDK for OneFlow #
 
## Basic usage

```ruby

require './oneflowclient'

#OneflowClient instance
client = OneflowClient.new(endpoint, token, secret)

# ... Fill in request path and data

#make the POST request to the endpoint
response = client.request("POST", api_path, data)

```
### Git ###
    
    git clone https://github.com/Oneflow/oneflow-sdk-ruby <your-target-directory>
    
## Samples ##

You can find order validation and submission examples in root folder