<?php

require_once "carrier/carrier.php";
require_once "address/address.php";
require_once "attachments/attachments.php";

/**
 * OneFlowShipment class.
 *
 * @extends OneFlowBase
 */
class OneFlowShipment extends OneFlowBase {

	/**
	 * init function.
	 *
	 * @access public
	 * @return void
	 */
	public function init()      {

		$this->__addObject("shipTo", "Address");
		$this->__addObject("carrier","Carrier");

		$this->__addArray("attachments","Attachments");

		$this->__addProperty("shipmentIndex", 0, true);
		$this->__addProperty("pspBranding", true);
		$this->__addProperty("labelName");
		$this->__addProperty("cost");
		$this->__addProperty("slaDays");
		$this->__addProperty("dispatchAlert");
		$this->__addProperty("canShipEarly");
		$this->__addProperty("shipByDate");
		$this->__addArray("carrierFields", null);
	}

	/**
	 * newAttachment function.
	 *
	 * @access public
	 *
	 * @param string        $path
	 * @param null|string   $type
	 * @param null|string   $contentType
	 * @param null|boolean  $fetch
	 *
	 * @return \OneFlowAttachment
	 */
	public function newAttachment($path, $type = null, $contentType = null, $fetch = null)	{
		$attachment = new OneFlowAttachment();
		$attachment->setPath($path);
		if($type) $attachment->setType($type);
		if($contentType) $attachment->setContentType($contentType);
		if($fetch !== null) $attachment->setFetch($fetch);
		$this->attachments[] = $attachment;
		return end($this->attachments);
	}

    /**
     * setDispatchAlert function.
     *
     * @access public
     * @param mixed $dispatchAlert
     * @return void
     */
    public function setDispatchAlert($dispatchAlert)	{
	    $this->dispatchAlert = $dispatchAlert;
    }

	/**
	 * setShipmentIndex function.
	 *
	 * @access public
	 * @param mixed $index
	 * @return void
	 */
	public function setShipmentIndex($index)      {
		$this->shipmentIndex = $index;
	}

	/**
	 * setShipTo function.
	 *
	 * @access public
	 * @param mixed $name
	 * @param mixed $companyName
	 * @param mixed $address1
	 * @param string $address2
	 * @param string $address3
	 * @param mixed $town
	 * @param string $state
	 * @param mixed $postcode
	 * @param mixed $isoCountry
	 * @param string $country (default: "")
	 * @param string $phone (default: "")
	 * @param string $email (default: "")
	 * @return void
	 */
	public function setShipTo($name, $companyName, $address1, $address2, $address3, $town, $state, $postcode, $isoCountry, $country="", $phone="", $email="")      {
		$this->shipTo->name = $name;
		$this->shipTo->companyName = $companyName;
		$this->shipTo->address1 = $address1;
		$this->shipTo->address2 = $address2;
		$this->shipTo->address3 = $address3;
		$this->shipTo->town = $town;
		$this->shipTo->state = $state;
		$this->shipTo->isoCountry = $isoCountry;
		$this->shipTo->country = $country;
		$this->shipTo->postcode = $postcode;
		$this->shipTo->phone = $phone;
		$this->shipTo->email = $email;
	}

	/**
	 * setReturnAddress function.
	 *
	 * @access public
	 * @param mixed $name
	 * @param mixed $companyName
	 * @param mixed $address1
	 * @param string $address2
	 * @param string $address3
	 * @param mixed $town
	 * @param string $state
	 * @param mixed $postcode
	 * @param mixed $isoCountry
	 * @param mixed $country
	 * @param mixed $phone
	 * @return void
	 */
	public function setReturnAddress($name, $companyName, $address1, $address2, $address3, $town, $state, $postcode, $isoCountry, $country, $phone)      {

		$this->__addObject("returnAddress","Address");

		$this->returnAddress->name = $name;
		$this->returnAddress->companyName = $companyName;
		$this->returnAddress->address1 = $address1;
		$this->returnAddress->address2 = $address2;
		$this->returnAddress->address3 = $address3;
		$this->returnAddress->town = $town;
		$this->returnAddress->state = $state;
		$this->returnAddress->isoCountry = $isoCountry;
		$this->returnAddress->postcode = $postcode;
		$this->returnAddress->country = $country;
		$this->returnAddress->phone = $phone;

	}

	/**
	 * setCarrier function.
	 *
	 * @access public
	 * @param mixed $code
	 * @param mixed $service
	 * @return void
	 */
	public function setCarrier($code, $service)      {
		$this->carrier->code = $code;
		$this->carrier->service = $service;
	}

	/**
	 * setCarrierByAlias function.
	 *
	 * @access public
	 * @param mixed $alias
	 * @return void
	 */
	public function setCarrierByAlias($alias)      {
		$this->carrier->alias = $alias;
	}

	/**
	 * setLabelName function.
	 *
	 * @access public
	 * @param mixed $labelName
	 * @return void
	 */
	public function setLabelName($labelName)      {
		$this->labelName = $labelName;
	}

	/**
	 * setSlaDays function.
	 *
	 * @access public
	 * @param integer $slaDays
	 * @return void
	 */
	public function setSlaDays($slaDays)      {
		$this->slaDays = $slaDays;
	}

	/**
	 * setCanShipEarly function.
	 *
	 * @access public
	 * @param boolean $canShipEarly
	 * @return void
	 */
	public function setCanShipEarly($canShipEarly)
	{
		$this->canShipEarly = $canShipEarly;
	}

	/**
	 * setShipByDate function.
	 *
	 * @access public
	 * @param string $shipByDate Using the format YYYY-MM-DD
	 * @return void
	 */
	public function setShipByDate($shipByDate)
	{
		$this->shipByDate = $shipByDate;
	}

	/**
	 * setCarrierFields function.
	 *
	 * @access public
	 * @param mixed $carrierFields
	 * @return void
	 */
	public function setCarrierFields($carrierFields)	{
		$this->carrierFields = $carrierFields;
	}
}

?>
