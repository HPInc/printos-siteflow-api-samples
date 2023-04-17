<?php

/**
 * OneFlowAddress class.
 *
 * @extends OneFlowBase
 */
class OneFlowAttachment extends OneFlowBase {

	/**
	 * init function.
	 *
	 * @access public
	 * @return void
	 */
	public function init()      {
		$this->__addProperty("path", "", true);
		$this->__addProperty("type", "", false);
		$this->__addProperty("contentType");
		$this->__addProperty("fetch");
	}

	/**
	 * setPath function.
	 *
	 * @access public
	 * @param string $path
	 * @return void
	 */
	public function setPath($path)      {
		$this->path = $path;
	}

	/**
	 * setType function.
	 *
	 * @access public
	 * @param string $type
	 * @return void
	 */
	public function setType($type)      {
		$this->type = $type;
	}

	/**
	 * setContentType function.
	 *
	 * @access public
	 * @param string $contentType
	 * @return void
	 */
	public function setContentType($contentType)      {
		$this->contentType = $contentType;
	}

	/**
	 * setFetch function.
	 *
	 * @access public
	 * @param boolean $fetch
	 * @return void
	 */
	public function setFetch($fetch)      {
		$this->fetch = $fetch;
	}
}

?>
