<?php

if (!class_exists('OneflowSDK')) {
	require_once __DIR__ . '/OneflowSDK.php';
}

class ProductionSDK extends OneflowSDK	{

	/**
	 * Returns a list of stations
	 * @return mixed
	 */
	public function getStations(){
		return $this->get("/station");
	}

	/**
	 * Creates a station
	 * @param $jsonData
	 * @return mixed
	 */
	public function addStation($jsonData){
		return $this->post("/station", $jsonData);
	}

	/**
	 * Returns a list of SKUs
	 * @return mixed
	 */
	public function getSKUs(){
		return $this->get("/sku");
	}

	/**
	 * Creates a SKU
	 * @param $jsonData
	 * @return mixed
	 */
	public function addSKU($jsonData){
		return $this->post("/sku", $jsonData);
	}

	/**
	 * Returns a list of clients
	 * @return mixed
	 */
	public function getClients(){
		return $this->get("/client");
	}

	/**
	 * Creates a client
	 * @param $jsonData
	 * @return mixed
	 */
	public function addClient($jsonData){
		return $this->post("/client", $jsonData);
	}

	/**
	 * Returns a list of batches
	 * @return mixed
	 */
	public function getBatches(){
		return $this->get("/batch");
	}

	/**
	 * Returns a list of walls
	 * @return mixed
	 */
	public function getWalls(){
		return $this->get("/wall");
	}

	/**
	 * Creates a wall
	 * @param $jsonData
	 * @return mixed
	 */
	public function addWall($jsonData){
		return $this->post("/wall", $jsonData);
	}

	/**
	 * Returns a list of stocks
	 * @return mixed
	 */
	public function getStock(){
		return $this->get("/stock");
	}

	/**
	 * Creates a stock
	 * @param $jsonData
	 * @return mixed
	 */
	public function addStock($jsonData){
		return $this->post("/stock", $jsonData);
	}

	/**
	 * Returns a list of couriers
	 * @return mixed
	 */
	public function getCouriers(){
		return $this->get("/courier");
	}

	/**
	 * Creates a courier
	 * @param $jsonData
	 * @return mixed
	 */
	public function addCourier($jsonData){
		return $this->post("/courier", $jsonData);
	}

	/**
	 * Returns a list of accounts
	 * @return mixed
	 */
	public function getAccounts(){
		return $this->get("/account");
	}

	/**
	 * Return a list of accounts
	 * @return mixed
	 */
	public function getAccount(){
		return $this->get("/account");
	}

	/**
	 * Returns the current account settings
	 * @return mixed
	 */
	public function getAccountSettings(){
		return $this->get("/accountSettings");
	}

	/**
	 * Returns the sub-batches for a given station
	 * @param $id
	 * @return mixed
	 */
	public function getStationWorkList($id){
		return $this->get("/worklist/id/$id");
	}

	/**
	 * Returns a list of products
	 * @return mixed
	 */
	public function getProducts(){
		return $this->get("/product");
	}

	/**
	 * Creates a product
	 * @param $jsonData
	 * @return mixed
	 */
	public function addProduct($jsonData){
		return $this->post("/product", $jsonData);
	}

	/**
	 * Deletes a product
	 * @param $id
	 * @return mixed
	 */
	public function removeProduct($id){
		return $this->del("/product/$id");
	}

	/**
	 * Creates a event type
	 * @param $jsonData
	 * @return mixed
	 */
	public function addEventType($jsonData){
		return $this->post("/event-type", $jsonData);
	}

	/**
	 * Returns a list of event types
	 * @return mixed
	 */
	public function getEventTypes(){
		return $this->get("/event-type");
	}

	/**
	 * Returns a list of papers
	 * @return mixed
	 */
	public function getPaper(){
		return $this->get("/paper");
	}

	/**
	 * Creates a paper
	 * @param $jsonData
	 * @return mixed
	 */
	public function addPaper($jsonData){
		return $this->post("/paper", $jsonData);
	}

	/**
	 * Returns a list of printers
	 * @return mixed
	 */
	public function getPrinters(){
		return $this->get("/printer");
	}

	/**
	 * Creates a printer
	 * @param $jsonData
	 * @return mixed
	 */
	public function addPrinter($jsonData){
		return $this->post("/printer", $jsonData);
	}

	/**
	 * Returns a list of users
	 * @return mixed
	 */
	public function getUsers(){
		return $this->get("/user");
	}

	/**
	 * Creates a user
	 * @param $jsonData
	 * @return mixed
	 */
	public function addUser($jsonData){
		return $this->post("/register", $jsonData);
	}

	/**
	 * Returns a list of files for a given order
	 * @param $orderId
	 * @return mixed
	 */
	public function getOrderFiles($orderId){
		return $this->get("/files/order/$orderId");
	}

	/**
	 * Sets a shipment as shipped
	 * @param $orderId
	 * @param $jsonData
	 * @return mixed
	 */
	public function setShipmentAsShipped($shipmentId, $jsonData = null){
		return $this->put("/shipment/shipped/$shipmentId", $jsonData);
	}

}
