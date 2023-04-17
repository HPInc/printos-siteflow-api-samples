# Python SDK for OneFlow #

## Requirements ##

python 3
 
## Basic usage

```python

import os
from OneflowSDK import OneflowSDK

#OneflowSDK instance
client = OneflowSDK(os.environ['OFS_URL'],
                    os.environ['OFS_TOKEN'],
                    os.environ['OFS_SECRET'])

# ... Fill in requet path and data

#make the POST request to the endpoint
result = client.request('POST', api_path, data)

```
### Git ###
    
    git clone https://github.com/Oneflow/oneflow-sdk-python <your-target-directory>
    
## Samples ##

You can find order validation and submission examples in root folder