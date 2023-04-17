<?php
require_once "base.php";
require_once __DIR__.'/order/order.php';



class OneFlowSDKLoader {

	public $mappings;
	public $orderModel;

	public function __construct() {

		$this->mappings = Array(
			'source'=>'OneFlowPoint',
			'destination'=>'OneFlowPoint',
			'orderData'=>'OneFlowOrderData',
			'error'=>'OneFlowError',
			'item'=>'OneFlowItem',
			'shipment'=>'OneFlowShipment',
			'component'=>'OneFlowComponent',
			'colour'=>'OneFlowColour',
			'finish'=>'OneFlowFinish',
			'shipTo'=>'OneFlowAddress',
			'returnAddress'=>'OneFlowReturnAddress',
			'carrier'=>'OneFlowCarrier'
		);

	}

}

$loader = new OneFlowSDKLoader();

/**
 * OneflowSDK class.
 */
class OneflowSDK {

	protected $url;
	protected $file_url;
	protected $key;
	protected $secret;
	protected $client;
	protected $retries;
	protected $retryDelay;
	protected $retryCondition;

	public function __construct($url, $key, $secret, $options = null){
		if(!$url || !$key || !$secret){
			throw new Exception("Error creating sdk instance. Url, key and secret are required", 1);
		}
		$this->url = $url;
		$this->key = $key;
		$this->secret = $secret;
		$this->apiName = "connect";
		$this->version = "0.1";
		$this->authHeader = "x-oneflow-authorization";
		$this->retries = isset($options->retries) ? $options->retries : 3;
		$this->retryCondition = isset($options->retryCondition) ? $options->retryCondition : "OneflowSDK::isRetryableError";
		$this->retryDelay = isset($options->retryDelay) ? $options->retryDelay : "OneflowSDK::exponentialDelay";
	}

	//ACCOUNTS

	public function setAuthHeader($header){
		$this->authHeader = $header;
	}

	/**
	 * accountsGetMy function.
	 *
	 * @access public
	 * @return mixed
	 */
	public function accountsGetMy(){
		return json_decode($this->get('/account'));
	}

	/**
	 * accountsGetAll function.
	 *
	 * @access public
	 * @return mixed
	 */
	public function accountsGetAll(){
		return json_decode($this->get('/account/all'));
	}

	/**
	 * accountsGetById function.
	 *
	 * @access public
	 * @param mixed $id
	 * @return mixed
	 */
	public function accountsGetById($id){
		return json_decode($this->get('/account/' . $id));
	}

	/**
	 * accountsCreate function.
	 *
	 * @access public
	 * @return mixed
	 */
	public function accountsCreate($data){
		return json_decode($this->post('/account', $data));
	}

	//ORDERS

	/**
	 * processOrderArray function.
	 *
	 * @access private
	 * @param mixed $orderResponse
	 * @return mixed
	 */
	private function processOrderArray($orderResponse){
		$orders = Array();

		if ($orderResponse)	{
			foreach ($orderResponse as $k=>$order)	{
				$orders[] = new OneFlowOrder($order);
			}
			return $orders;
		}	else	{
			echo "Order Fetch Error\n";
			return false;
		}

	}

	/**
	 * Get all orders
	 *
	 * @access public
	 *
	 * @param int $page
	 * @param int|null $pagesize
	 *
	 * @return \OneFlowOrder
	 */
	public function ordersList($page = 1, $pagesize = 10){
		$path = '/order?page=' . $page . ($pagesize ? "&pagesize=" .  $pagesize : "");
		$list = json_decode($this->get($path));

		return $list;
	}

	/**
	 * ordersGetById function.
	 *
	 * @access public
	 * @param mixed $id
	 * @return OneFlowOrder
	 */
	public function ordersGetById($id){
		$order = json_decode($this->get('/order/' . $id));
		return new OneFlowOrder($order);
	}

	/**
	 * ordersCreate function.
	 *
	 * @access public
	 * @param mixed $order
	 * @return mixed
	 */
	public function orderValidate($order)	{
		return $this->post('/order/validate', $order->toJSON());
	}

	/**
	 * ordersCreate function.
	 *
	 * @access public
	 * @param mixed $order
	 * @return mixed
	 */
	public function ordersCreate($order)	{
		//check that order is valid before submission
		if (count($order->isValid())>0)	{
			return $order->validateOrder();
		}	else	{
			return $this->post('/order', $order->toJSON());
		}
	}

	/**
	 * postFile function.
	 *
	 * @access public
	 * @param mixed $uploadUrl
	 * @param mixed $localPath
	 * @return mixed
	 */
	public function postFile($uploadUrl, $localPath){
		if (file_exists($localPath))	{
			return json_decode($this->post_file_s3($uploadUrl, $localPath));
		}	else	{
			return json_decode('{"success":false, "message":"File: '.$localPath.' does not exist"}');
		}
	}

	/**
	 * ordersCancel function.
	 *
	 * @access public
	 * @param mixed $id
	 * @return mixed
	 */
	public function orderCancel($id){
		if (strlen($id)>0)		return $this->put("/order/$id/cancel");
		else					return false;
	}

	/**
	 * ordersUpdateById function.
	 *
	 * @access public
	 * @param mixed $id
	 * @param mixed $orderData
	 * @return mixed
	 */
	public function ordersUpdateById($id, $orderData){
		if (strlen($id)>0)	return $this->put("/order/$id", json_encode($orderData));
		else				return false;
	}

	/**
	 * Performs an HTTP request to the OneFlow API
	 *
	 * @param mixed $method
	 * @param mixed $path
	 * @param mixed $jsonData (default: null)
	 * @param mixed $optional_headers (default: null)
     * @throws Exception
	 * @return mixed
	 */
	public function request($method, $path, $jsonData=null, $optional_headers = null) {
		ini_set("track_errors","on");

		$timestamp = time();
		$url = $this->url.$path;
		$urlParts = parse_url($url);
		$fullPath = $urlParts['path'];

		if (filter_var($url, FILTER_VALIDATE_URL)===FALSE)	return false;

		$params = array(
			'http' => array(
				'ignore_errors' => '1',
				'method' => $method
			),
			'ssl' => array(
				'ciphers' => 'ECDHE-RSA-AES256-GCM-SHA384:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-SHA384:ECDHE-ECDSA-AES256-SHA384:DH-DSS-AES256-GCM-SHA384:DHE-DSS-AES256-GCM-SHA384:DH-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-SHA256:DHE-DSS-AES256-SHA256:DH-RSA-AES256-SHA256:DH-DSS-AES256-SHA256:ECDH-RSA-AES256-GCM-SHA384:ECDH-ECDSA-AES256-GCM-SHA384:ECDH-RSA-AES256-SHA384:ECDH-ECDSA-AES256-SHA384:AES256-GCM-SHA384:AES256-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-SHA256:ECDHE-ECDSA-AES128-SHA256:DH-DSS-AES128-GCM-SHA256:DHE-DSS-AES128-GCM-SHA256:DH-RSA-AES128-GCM-SHA256:DHE-RSA-AES128-GCM-SHA256:DHE-RSA-AES128-SHA256:DHE-DSS-AES128-SHA256:DH-RSA-AES128-SHA256:DH-DSS-AES128-SHA256:ECDH-RSA-AES128-GCM-SHA256:ECDH-ECDSA-AES128-GCM-SHA256:ECDH-RSA-AES128-SHA256:ECDH-ECDSA-AES128-SHA256:AES128-GCM-SHA256:AES128-SHA256'
			)
		);

		if ($method=="POST" || $method=="PUT")	{
			$params['http']['content'] = $jsonData;
		}

		$params['http']['header'][] = "x-oneflow-date: $timestamp";
		$params['http']['header'][] = "x-oneflow-algorithm: SHA256";
		$params['http']['header'][] = $this->authHeader.": ".$this->token($method, $fullPath, $timestamp);

		foreach ($optional_headers as $name => $value)	{
			$params['http']['header'][] = "$name: $value";
		}

		$attempt = 0;
		while ($attempt < $this->retries) {
			$context = stream_context_create($params);
			$fp = fopen($url, 'rb', false, $context);
			if (!$fp)	{
				throw new Exception("Problem creating stream from $url, \n\t".implode("\n\t", error_get_last()));
			}
			
			$response = stream_get_contents($fp);
			if ($response === false)	throw new Exception("Problem reading data from $url, $php_errormsg");

			preg_match('{HTTP\/\S*\s(\d{3})}', $http_response_header[0], $match);
			$status = $match[1];

			if (!call_user_func($this->retryCondition, $status, $method, $path)) break;
			$attempt++;
			call_user_func($this->retryDelay, $attempt, $status);
		}

		return $response;
	}

	/**
	 * Performs a GET HTTP request
	 *
	 * @param mixed $path
	 * @param string $format (default: 'application/json')
	 * @return mixed
	 */
	public function get($path, $format = 'application/json'){
		try {
			$response = $this->request("GET", $path, "", array(
	    		'Accept' => $format,
			));
		} catch (Exception $e) {
		    $response = null;
			echo "get exception\n";
			echo $e->getMessage()."\n";
		}

		return $response;
	}

	/**
	 * Performs a POST HTTP request
	 *
	 * @param mixed $path
	 * @param mixed $jsonData
	 * @param string $format (default: 'application/json')
	 * @return mixed
	 */
	public function post($path, $jsonData, $format = 'application/json')	{
		try {
			$response = $this->request("POST", $path, $jsonData, array(
	    		'Content-Type' => $format,
	    		'Accept' => $format,
			));
		} catch (Exception $e) {
            $response = null;
            echo $e->getMessage()."\n";
		}

		return $response;
	}

	/**
	 * Performs a PUT HTTP request
	 *
	 * @param mixed $path
	 * @param mixed $jsonData
	 * @param string $format (default: 'application/json')
	 * @return mixed
	 */
	public function put($path, $jsonData = null, $format = 'application/json'){
		try {
			$response = $this->request("PUT", $path, $jsonData, array(
	    		'Content-Type' => $format,
	    		'Accept' => $format,
			));
		} catch (Exception $e) {
            $response = null;
            echo $e->getMessage()."\n";
		}

		return $response;
	}

	/**
	 * Performs a DELETE HTTP request
	 *
	 * @param mixed $path
	 * @param string $format (default: 'application/json')
	 * @return mixed
	 */
	public function del($path, $format = 'application/json'){
		try {
			$response = $this->request("DELETE", $path, "", array(
	    		'Accept' => $format,
			));
		} catch (Exception $e) {
            $response = null;
            echo $e->getMessage()."\n";
		}

		return $response;
	}

	/**
	 * post_file function.
	 *
	 * @access public
	 * @param mixed $uploadUrl
	 * @param mixed $localPath
     * @throws Exception when a problem occurs
	 * @return mixed
	 */
	protected function post_file_s3($uploadUrl, $localPath)	{

		echo "Uploading      : $localPath\n";
		echo "To             : $uploadUrl\n";

		//get the file
		$fileHandle = fopen($localPath, "rb");
		$fileContents = stream_get_contents($fileHandle);
		fclose($fileHandle);

		echo "File Size      : ".strlen($fileContents)."\n";

		//set the ctx params
		$params = array(
			'http' => array(
				'ignore_errors' => '1',
				'method' => 'PUT',
				'header' => Array(
					"Content-Type: application/pdf"
				),
				'content' => $fileContents
		    ));
		$ctx = stream_context_create($params);

		//upload the file
		$fp = fopen($uploadUrl, 'rb', false, $ctx);
		if (!$fp)					throw new Exception("PROBLEM:\n".implode("\n\t", error_get_last())."\n\n\n\n");

		$response = stream_get_contents($fp);
		if ($response === false) 	throw new Exception("Problem reading data from $uploadUrl, $php_errormsg");

		return $response;
	}

	/**
	 * token function.
	 *
	 * @access private
	 * @param mixed $method
	 * @param mixed $path
	 * @param mixed $timestamp
	 * @return string
	 */
	private function token($method, $path, $timestamp){
		$stringToSign = strtoupper($method) . ' ' . $path . ' ' . $timestamp;
		return $this->key . ':' . hash_hmac('sha256', $stringToSign, $this->secret);
	}

	/**
	 * Delay function.
	 *
	 * @access private
	 * @param mixed $attempt
	 * @param mixed $status
	 * @return void
	 */
	private static function exponentialDelay($attempt, $status) {
		$coefficient = ($status === '429') ? (300 * 60) : 100; // assume 429 limit by minute
		$delay = pow(2, $attempt) * $coefficient;
		$randomSum = $delay * 0.04 * random_int(0, 10); // 0-40% of the delay
		usleep(($delay + $randomSum) * 1000);
	}

	/**
	 * Retry function.
	 *
	 * @access private
	 * @param mixed $status
	 * @param mixed $method
	 * @param mixed $path
	 * @return boolean Indicating whether to retry
	 */
	private static function isRetryableError($status, $method, $path) {
		return $status === '429';
	}
}
