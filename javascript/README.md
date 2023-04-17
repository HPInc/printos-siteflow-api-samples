# oneflow-sdk-js

## Installation 

```bash
    npm install oneflow-sdk-js
```

## Basic usage

```javascript
    const OneflowClient = require('oneflow-sdk-js');

    const client = new OneflowClient(
    	process.env.OFS_URL || 'https://pro-api.oneflowcloud.com/api',
    	process.env.OFS_TOKEN,
    	process.env.OFS_SECRET
    );

    // ...

    const destinationName  = 'oneflow';
    const orderData  = { sourceOrderId: 'aUniqueId' };
    const order = client.createOrder(destinationName, orderData);

    // ... instructions to complete the order data ...

    const result = await client.submitOrder();
```

If a unique routing rule path needs to be specified, it can be done using:

```javascript
const result = await client.submitOrder({ routingRule: 'your routing rule key here' });
```
The routing rule key is a unique string of numbers and letters

For more detailed examples checkout the [examples](examples) folder.

You can find more information about the required fields and the order structure in [https://hpsiteflow.com](http://hpsiteflow.com)
