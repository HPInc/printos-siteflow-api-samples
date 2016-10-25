<?php 
# © Copyright 2016 HP Development Company, L.P.
# SPDX-License-Identifier: MIT

#Access credentials
#$baseUrl = 'https://printos.api.hp.com/siteflow'; #use for a production account
#$baseUrl = 'https://stage.printos.api.hp.com/siteflow'; #use for a staging account
$key = '';
$secret = '';

#Function Calls 
#--------------------------------------------------------------#

validateOrder();
#submitOrder();
#getAllOrders();
#getOrder('OrderId');
#cancelOrder('sourceAccount', 'sourceOrderId');


#SiteFlow APIs
#--------------------------------------------------------------#

/**
 * Cancels an order in Site Flow.
 * 
 * @param $sourceAccount - name of the source account
 * @param $orderId - source order id of the order (user generated)
 */
function cancelOrder($sourceAccount, $orderId) {
	echo "Canceling Order: " . $orderId . " from: " . $sourceAccount . "</br>";
	$response = putRequest('/api/order/' . $sourceAccount . '/' . $orderId . '/cancel');
	printInfo($response);
}

/**
 * Gets a list of all orders in Site Flow.
 */
function getAllOrders() {
	echo "Getting All Order </br>";
	$response = getRequest('/api/order');
	printInfo($response);
}

/**
 * Gets an order with the specified order id in Site Flow.
 *
 * @param $orderId - id of the order (SiteFlow generated)
 */
function getOrder($orderId) {
	echo "Getting Order: " . $orderId . "</br>";
	$response = getRequest('/api/order/' . $orderId);
	printInfo($response);
}

/**
 * Submits an order into Site Flow
 */
function submitOrder() {
	echo "Submitting Order </br>";
	$data = createOrder();
	$response = postRequest('/api/order', $data);
	printInfo($response);
}

/**
 * Validates an order to see if its able to be submitted successfully
 */
function validateOrder() {
	echo "Validating Order </br>";
	$data = createOrder();
	$response = postRequest('/api/order/validate', $data);
	printInfo($response);
}


#Helper functions
#--------------------------------------------------------------#

/**
 * Creates the HMAC header to authenticate the API calls.
 *
 * @param $method - type of http method (GET, POST, PUT)
 * @param $path - api path
 * @param $timestamp - time in utc format 
 */
function createHmacAuth($method, $path, $timestamp) {
	global $key, $secret;
	$str = $method . ' ' . $path . $timestamp;
	$hash = hash_hmac('sha1', $str, $secret);
	return $key . ':' . $hash;
}

/**
 * Creates a mock order to test validate and submission of an order.
 *
 * Note: "hp.jpeng" will need to be changed to your own printos account username.
 * "1238576" will need to be a unique user generated id or validation/submission will fail. This is also the id used to cancel an order.
 */
function createOrder() {
	$postData = "{\"orderData\": {\"shipments\": [{\"shipTo\": {\"town\": \"New York\", \"isoCountry\": \"US\", \"state\": \"New York\", \"name\": \"John Doe\", \"phone\": \"01234567890\", \"address1\": \"5th Avenue\", \"email\": \"johnd@acme.com\", \"postcode\": \"12345\"}, \"carrier\": {\"code\": \"customer\", \"service\": \"pickup\"}}], \"items\": [{\"sku\": \"Business Cards\", \"sourceItemId\": \"1\", \"components\": [{\"path\": \"https://Server/Path/business_cards.pdf\", \"code\": \"Content\", \"fetch\": \"true\"}], \"quantity\": 1}], \"postbackAddress\": \"http://postback.genesis.com\", \"sourceOrderId\": \"124568746\"}, \"destination\": {\"name\": \"hp.jpeng\"}}";

	return $postData;
}

/**
 * Prints the responses in a "pretty" format, majority of the responses are in JSON format.
 *
 * @param $response - http response of the requests
 */
function printInfo($response) {
	// Check for errors
	if($response === FALSE){
		echo $response . "</br>";
		die($response);
	}

	$responseData = json_decode($response, TRUE);
	echo "<pre>"; print_r($responseData); echo "</pre>";
}


#GET, POST, and PUT
#--------------------------------------------------------------#

/**
 * HTTP GET request 
 *
 * @global $baseUrl - base url/path for the apis
 * @param $path - api path
 *
 * Note: $baseUrl . $path will be the full url to call a certain api.
 */
function getRequest($path) {
	global $baseUrl;
	
	$t = microtime(true);
	$micro = sprintf("%03d",($t - floor($t)) * 1000);
	$time = gmdate('Y-m-d\TH:i:s.', $t).$micro.'Z';
	$auth = createHmacAuth('GET', $path, $time);

	$options = array(
		'http' => array(
			'header'=>  "Content-Type: application/json\r\n" .
						"x-hp-hmac-date: " . $time . "\r\n" .
						"x-hp-hmac-authentication: " . $auth . "\r\n",
			'method'  => 'GET',
		),
	); 

	$context = stream_context_create($options);
	return file_get_contents($baseUrl . $path, false, $context);
}

/**
 * HTTP POST request 
 *
 * @global $baseUrl - base url/path for the apis
 * @param $path - api path
 * @param $data - json data to post
 *
 * Note: $baseUrl . $path will be the full url to call a certain api.
 */
function postRequest($path, $data) {
	global $baseUrl;
	
	$t = microtime(true);
	$micro = sprintf("%03d",($t - floor($t)) * 1000);
	$time = gmdate('Y-m-d\TH:i:s.', $t).$micro.'Z';
	$auth = createHmacAuth('POST', $path, $time);

	$options = array(
		'http' => array(
			'header'=>  "Content-Type: application/json\r\n" .
						"x-hp-hmac-date: " . $time . "\r\n" .
						"x-hp-hmac-authentication: " . $auth . "\r\n",
			'method'  => 'POST',
			'content' => $data
		),
	); 

	$context = stream_context_create($options);
	return file_get_contents($baseUrl . $path, false, $context);
}

/**
 * HTTP PUT request 
 *
 * @global $baseUrl - base url/path for the apis
 * @param $path - api path
 *
 * Note: $baseUrl . $path will be the full url to call a certain api.
 */
function putRequest($path) {
	global $baseUrl;
	
	$t = microtime(true);
	$micro = sprintf("%03d",($t - floor($t)) * 1000);
	$time = gmdate('Y-m-d\TH:i:s.', $t).$micro.'Z';
	$auth = createHmacAuth('PUT', $path, $time);

	$options = array(
		'http' => array(
			'header'=>  "x-hp-hmac-date: " . $time . "\r\n" .
						"x-hp-hmac-authentication: " . $auth . "\r\n",
			'method'  => 'PUT'
		),
	); 

	$context = stream_context_create($options);
	return file_get_contents($baseUrl . $path, false, $context);
}

?>