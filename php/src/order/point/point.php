<?php

/**
 * OneFlowPoint class.
 * 
 * @extends OneFlowBase
 */
class OneFlowPoint extends OneFlowBase	{

	/**
	 * init function.
	 * 
	 * @access public
	 * @return void
	 */
	public function init()      {  

		$this->__addProperty("id");
		$this->__addProperty("name");
		
    }
	
}

?>