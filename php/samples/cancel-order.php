<?php
date_default_timezone_set('Europe/London');
error_reporting(E_ALL);
require_once '../src/OneflowSDK.php';

//SETUP THE SDK
$client = new OneflowSDK(
	'http://localhost:3000/api',
	'API_TOKEN_HERE',
	'API_SECRET_HERE'
);

//CANCEL THE ORDER

$orderId = "##ORDER_ID##";

$response = $client->orderCancel($orderId);


if (isset($response->error))	{
	echo "Error Code     : ".$response->error->code."\n";
	echo "Error Message  : ".$response->error->message."\n";
}	else	{

	echo "OneFlow ID     : ".$response->order->_id."\n";

	print_r($response);
}
