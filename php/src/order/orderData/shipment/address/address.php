<?php

/**
 * OneFlowAddress class.
 *
 * @extends OneFlowBase
 */
class OneFlowAddress extends OneFlowBase {

	/**
	 * init function.
	 *
	 * @access public
	 * @return void
	 */
	public function init()      {

		$this->__addProperty("name", "", true);
		$this->__addProperty("companyName");
		$this->__addProperty("address1", "", true);
		$this->__addProperty("address2", "");
		$this->__addProperty("address3", "");
		$this->__addProperty("town", "", true);
		$this->__addProperty("state", "");
		$this->__addProperty("postcode", "");
		$this->__addProperty("isoCountry", "", true);
		$this->__addProperty("country", "");
		$this->__addProperty("phone");
		$this->__addProperty("email", "");
	}
}

?>
