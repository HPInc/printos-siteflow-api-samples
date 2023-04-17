<?php
date_default_timezone_set('Europe/London');
error_reporting(E_ALL);
require_once '../src/OneflowSDK.php';

//SETUP THE SDK
$client = new OneflowSDK(
	 $_ENV['ONEFLOW_ENDPOINT'],
	 $_ENV['ONEFLOW_TOKEN'],
	 $_ENV['ONEFLOW_SECRET']
);

//order details

$destination = "oneflow";						//this is the destination account code
$orderId = "order-".mt_rand(100000,1000000);	//creating a random order number (normally use your own)
$item1Id = "item-1";
$item2Id = "item-2";
$skuCode = "TEST_PRODUCT";
$componentCode = "text";
$quantity = 1;
$fetchUrl = "http://www.mashreghnews.ir/files/fa/news/1392/9/6/448036_657.pdf";
$shipToName = "Professor Xavier";
$shipToCompanyName = "X-Men Inc";
$shipToAddress1 = "Xavier Institute for Gifted Youngsters";
$shipToAddress2 = "1407 Graymalkin Lane";
$shipToAddress3 = "Salem Center";
$shipToTown = "Westchester";
$shipToState = "New York";
$shipToPostcode = "12345";
$shipToIsoCountry = "US";
$shipToCountry = "United States";
$shipToPhone = "xxx-xxx-xxxx";
$shipToEmail = "xavier@x-men.com";
$carrierCode = "royalmail";
$carrierService = "firstclass";

//CREATE THE ORDER

$order = new OneFlowOrder();
$order->setDestination($destination);
$orderData = new OneFlowOrderData();
$orderData->setSourceOrderId($orderId);

//SET THE SHIPMENT
$shipment = $orderData->newShipment();
$shipment->setShipTo($shipToName, $shipToCompanyName, $shipToAddress1, $shipToAddress2, $shipToAddress3, $shipToTown, $shipToState, $shipToPostcode, $shipToIsoCountry, $shipToCountry, $shipToPhone, $shipToEmail);
$shipment->setCarrier($carrierCode, $carrierService);

//CREATE THE ITEMS
$item1 = $orderData->newSKUItem($skuCode, $item1Id, $quantity);
$component1 = $item1->newComponent($componentCode);
$component1->setFetchUrl($fetchUrl);

$item2 = $orderData->newSKUItem($skuCode, $item2Id, $quantity);
$component2 = $item2->newComponent($componentCode);
$component2->setFetchUrl($fetchUrl);

//SUBMIT THE ORDER
$order->setOrderData($orderData);
$orderResults = $client->ordersCreate($order);
//If validation fails then print out this
// print_r($orderResults);
$orderPost = json_decode($orderResults);

if (isset($orderPost->error))	{
	echo "Error Code     : ".$orderPost->error->code."\n";
	echo "Error Message  : ".$orderPost->error->message."\n";
}	else	{
	echo "OneFlow ID     : ".$orderPost->order->_id."\n";
}
