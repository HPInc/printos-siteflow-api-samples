<?php
date_default_timezone_set('Europe/London');
error_reporting(E_ALL);
require_once '../src/OneflowSDK.php';

$retryDelay = function($attempt, $status) {
	echo "Delaying. Attempt: " .$attempt." Status: " .$status."\n";
	sleep(1 * $attempt);
};

$retryCondition = function ($status, $method, $path) {
	if ($status === '200') return false;
	echo "Retrying. Status: " .$status." Method: " .$method." Path: " .$path."\n";
	return true;
};

//SETUP THE SDK
$client = new OneflowSDK(
	'https://localhost:3000/api',
	'API_TOKEN_HERE',
	'API_SECRET_HERE',
	(object)['retries' => 2, 'retryDelay' => $retryDelay, 'retryCondition' => $retryCondition]
);

$response = $client->request('GET', '/wrongPath');
print_r($response);