<?php
date_default_timezone_set('Europe/London');
error_reporting(E_ALL ^ E_NOTICE);
require_once '../src/OneflowSDK.php';

//SETUP THE SDK
$client = new OneflowSDK(
	'http://ONEFLOW-URL-HERE/api',
	'ONEFLOW-ACCOUNT-KEY-HERE',
	'ONEFLOW-ACCOUNT-SECRET-HERE'
);

//ORDER DATA
$orderId        = date("Y-m-dHis-").rand(1, 10000);
$itemId         = $orderId."-1";
$quantity       = 1;
$destination    = "order-test";
$localPath      = "files/sample1.pdf";
$customerName   = "Postcard Printer App";
$customerEmail  = "alert@email.com";
$componentCode  = "text";
$skuCode        = "test-sku";
$carrierCode    = "royalmail";
$carrierService = "firstclass";
$shipTo         = array(
	"name"         => "Han Solo",
	"companyName"  => "The Rebel Alliance",
	"address1"     => "The Cantina",
	"address2"     => "Space Port",
	"address3"     => "",
	"town"         => "Mos Eilsley",
	"state"        => "Djerba",
	"postcode"     => "EC12 0HQ",
	"isoCountry"   => "GB",
	"country"      => "Tatooine",
	"email"        => "han@starwars.com",
	"phone"        => "0123412341234"
);

/////////////////////////////////////////////////////////////////
//NO CHANGES REQUIRED BELOW HERE

//CREATE THE ORDER
$order = new OneFlowOrder();
$order->setDestination($destination);
$orderData = new OneFlowOrderData();
$orderData->setSourceOrderId($orderId);
$orderData->setCustomerName($customerName);
$orderData->setEmail($customerEmail);

//SET THE SHIPMENT
$shipment = $orderData->newShipment();
$shipment->setShipTo(
	$shipTo['name'],
	$shipTo['companyName'],
	$shipTo['address1'],
	$shipTo['address2'],
	$shipTo['address3'],
	$shipTo['town'],
	$shipTo['state'],
	$shipTo['postcode'],
	$shipTo['isoCountry'],
	$shipTo['country'],
	$shipTo['phone'],
	$shipTo['email']);
$shipment->setCarrier($carrierCode, $carrierService);

//CREATE AN ITEM
$item = $orderData->newSKUItem($skuCode, $itemId, $quantity);

//ADD THE COMPONENT AND FILENAME
$component = $item->newComponent($componentCode);
$component->setUploadFile($localPath);

//SUBMIT THE ORDER
$order->setOrderData($orderData);
$orderResults = $client->ordersCreate($order);
//If validation fails then print out this
//print_r($orderResults);
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
		echo "Status         : Uploads Complete\n";
	}
}
