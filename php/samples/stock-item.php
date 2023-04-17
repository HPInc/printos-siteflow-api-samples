<?php
date_default_timezone_set('Europe/London');
error_reporting(E_ALL);
require_once '../src/OneflowSDK.php';

//SETUP THE SDK
$client = new OneflowSDK(
	'http://ONEFLOW-URL-HERE/api',
	'ONEFLOW-ACCOUNT-KEY-HERE',
	'ONEFLOW-ACCOUNT-SECRET-HERE'
);

//CREATE THE ORDER

$orderId = "YOUR-ORDER-ID-HERE";
$destination = "DESTINATION-ACCOUNT-NAME";
$localPath = "PATH-LOCAL-FILE";

$order = new OneFlowOrder();
$order->setDestination($destination);
$orderData = new OneFlowOrderData();
$orderData->setSourceOrderId($orderId);
$orderData->setCustomerName("CUSTOMER-NAME-GOES-HERE");
$orderData->setEmail("CUTSOMER-EMAIL-GOES-HERE");

//SET THE SHIPMENT
$shipment = $orderData->newShipment();
$shipment->setShipTo("NAME","COMPANY-NAME", "ADDRESS1","ADDRESS2","ADDRESS3","TOWN/CITY","STATE","POSTCODE/ZIP","ISO-COUNTRY-CODE","COUNTRY-NAME", "PHONE", "EMAIL");
$shipment->setCarrier("CARRIER-CODE","CARRIER-SERVICE");

//CREATE AN ITEM
$item = $orderData->newSKUItem("YOUR-SKU-CODE", "YOUR-ITEM-ID", QUANTITY);
$item->setShipment($shipment);

//ADD THE COMPONENT  
$component = $item->newComponent("COMPONENT-CODE");

// SET THE COMPONENT FILE SOURCE (fetch, upload or local)
$component->setFetchUrl("http://site.com/file.pdf"); // to tell OneFlow to fetch a file -OR- 
$component->setUploadFile("upload-file.pdf");        // to upload a file straight to OneFlow -OR- 
$component->setLocalPath("local-file.pdf");          // to use local files (not uploaded)

//CREATE A STOCK ITEM
$stockItem = $orderData->newStockItem("YOUR-STOCK-CODE", QUANTITY);
$item->setShipment($shipment);


//SUBMIT THE ORDER
$order->setOrderData($orderData);
$orderResults = $client->ordersCreate($order);
$orderPost = json_decode($orderResults);

if (isset($orderPost->error))	{
	echo "Error Code     : ".$orderPost->error->code."\n";
	echo "Error Message  : ".$orderPost->error->message."\n";
}	else	{

	echo "OneFlow ID     : ".$orderPost->order->_id."\n";

	//CYCLE THROUGH ORDER FILES AND UPLOAD
	$files = $orderPost->order->files;

	foreach ($files as $file) {
		//ONLY UPLOAD FOR FILES THAT ARE NOT LOCAL OR FETCH
		if (!$file->fetch && !$file->localFile)	{
			$fileUpload = $client->postFile($file->url, $localPath);
		}
	}

}
