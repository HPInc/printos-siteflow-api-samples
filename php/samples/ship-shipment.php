<?php
date_default_timezone_set('Europe/London');
error_reporting(E_ALL);
require_once '../src/ProductionSDK.php';

$productionClient = new ProductionSDK(
	'https://ONEFLOW-URL-HERE/api',
	'ONEFLOW-ACCOUNT-TOKEN-HERE',
	'ONEFLOW-ACCOUNT-SECRET-HERE'
);

$shipmentId = "SHIPMENT_ID_HERE";

$response = $productionClient->setShipmentAsShipped($shipmentId);

if (!empty($response)) {
	echo $response;
}
