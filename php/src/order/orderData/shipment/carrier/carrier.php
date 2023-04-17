<?php

/**
 * OneFlowCarrier class.
 * 
 * @extends OneFlowBase
 */
class OneFlowCarrier extends OneFlowBase {

	/**
	 * init function.
	 * 
	 * @access public
	 * @return void
	 */
	public function init()      {
	  
		$this->__addProperty("code");
		$this->__addProperty("service");
		$this->__addProperty("alias");
	}	
}

?>