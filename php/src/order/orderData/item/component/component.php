<?php

require_once "finish/finish.php";
require_once "colour/colour.php";

/**
 * OneFlowComponent class.
 *
 * @extends OneFlowBase
 */
class OneFlowComponent extends OneFlowBase	{

	/**
	 * init function.
	 *
	 * @access public
	 * @return void
	 */
	public function init()      {
		$this->__addProperty("componentId");
		$this->__addProperty("type");
		$this->__addProperty("code");
		$this->__addProperty("path");
		$this->__addProperty("barcode");
		$this->__addProperty("fetch", false);
		$this->__addProperty("localFile", false);
		$this->__addProperty("preflight");
		$this->__addProperty("preflightProfile");
		$this->__addProperty("preflightProfileId");
		$this->__addProperty("paper" );
		$this->__addProperty("weight" );
		$this->__addProperty("pages" );
		$this->__addProperty("width");
		$this->__addProperty("height");
		$this->__addProperty("duplicate");
		$this->__addArray("attributes", null);
		$this->__addArray("extraData", null);
    }

	/**
	 * setType function.
	 *
	 * @access public
	 * @param mixed $type
	 * @return void
	 */
	public function setType($type)	{
		$this->type = $type;
	}

	/**
	 * setCode function.
	 *
	 * @access public
	 * @param mixed $code
	 * @return void
	 */
	public function setCode($code)	{
		$this->code = $code;
	}

	/**
	 * setFetch function.
	 *
	 * @access public
	 * @param bool $fetch
	 * @return void
	 */
	public function setFetch($fetch)	{
		$this->fetch = $fetch;
	}


	/**
	 * setUploadFile function.
	 *
	 * @access public
	 * @param string $localPath
	 * @return void
	 */
	public function setUploadFile($localPath)	{
		$this->fetch = false;
		$this->localFile = false;
		$this->path = $localPath;
	}

	/**
	 * setFetchUrl function.
	 *
	 * @access public
	 * @param string $fetchUrl
	 * @return void
	 */
	public function setFetchUrl($fetchUrl)	{
		$this->fetch = true;
		$this->localFile = false;
		$this->path = $fetchUrl;
	}

	/**
	 * setLocalPath function.
	 *
	 * @access public
	 * @param mixed $path
	 * @return void
	 */
	public function setLocalPath($path){
		$this->fetch = false;
		$this->localFile = true;
		$this->path = basename($path);

		global $OneFlowFilenames;
		$OneFlowFilenames[] = $path;
	}


	/**
	 * setPath function.
	 *
	 * @access public
	 * @param mixed $path
	 * @return void
	 */
	public function setPath($path){
		global $OneFlowFilenames;

		if ($this->fetch)	{
			$this->path = $path;
		}	else	{
			$this->path = basename($path);
		}

		$OneFlowFilenames[] = $path;
	}

	/**
	 * setBarcode function.
	 *
	 * @access public
	 * @param mixed $barcode
	 * @return void
	 */
	public function setBarcode($barcode)	{
		$this->barcode = $barcode;
	}

	/**
	 * setPaper function.
	 *
	 * @access public
	 * @param mixed $paper
	 * @param mixed $weight
	 * @return void
	 */
	public function setPaper($paper, $weight)	{
		$this->paper = $paper;
		$this->weight = $weight;
	}

	/**
	 * setPages function.
	 *
	 * @access public
	 * @param mixed $pages
	 * @return void
	 */
	public function setPages($pages)	{
		$this->pages = $pages;
	}

	/**
	 * setFinish function.
	 *
	 * @access public
	 * @param string $side1 (default: "none")
	 * @param string $side2 (default: "none")
	 * @return void
	 */
	public function setFinish($side1="none", $side2="none")	{
		$this->__addObject("finish", "Finish");
		$this->finish->side1 = $side1;
		$this->finish->side2 = $side2;
	}

	/**
	 * setColour function.
	 *
	 * @access public
	 * @param string $side1 (default: "none")
	 * @param string $side2 (default: "none")
	 * @return void
	 */
	public function setColour($side1="none", $side2="none")	{
		$this->__addObject("colour", "Colour");
		$this->colour->side1 = $side1;
		$this->colour->side2 = $side2;
	}

	/**
	 * setLocalFile function.
	 *
	 * @access public
	 * @param bool $localFile (default: false)
	 * @return void
	 */
	public function setLocalFile($localFile=false)	{
		$this->localFile = $localFile;
		if ($localFile)	{
			$this->preflight = false;
			$this->fetch = false;
		}
	}

	/**
	 * setPreflight function.
	 *
	 * @access public
	 * @param bool $preflight (default: true)
	 * @return void
	 */
	public function setPreflight($preflight=true)	{
		$this->preflight = $preflight;
	}

	/**
	 * setPreflightProfile function.
	 *
	 * @access public
	 * @param string $preflightProfile (default: "pdfx4")
	 * @return void
	 */
	public function setPreflightProfile($preflightProfile="pdfx4")	{
		$this->preflightProfile = $preflightProfile;
	}

	/**
	 * setPreflightProfileId function.
	 *
	 * @access public
	 * @param string $preflightProfileId
	 * @return void
	 */
	public function setPreflightProfileId($preflightProfileId)	{
		$this->preflightProfileId = $preflightProfileId;
        if ($preflightProfileId) $this->setPreflightProfile("custom");
	}

    /**
     * setSize function.
     *
     * @access public
     * @param mixed $width
     * @param mixed $height
     * @return void
     */
    public function setSize($width, $height)	{
	    $this->width = $width;
	    $this->height = $height;
    }

    /**
     * setComponentId function.
     *
     * @access public
     * @param mixed $componentId
     * @return void
     */
    public function setComponentId($componentId)	{
	    $this->componentId = $componentId;
    }

    /**
     * addAttribute function.
     *
     * @access public
     * @param $name
     * @param $value
     * @return void
     */
    public function addAttribute($name, $value = null)	{
	    $this->attributes[$name] = $value;
    }

	/**
	 * setDuplicate function.
	 *
	 * @access public
	 * @param integer $duplicate
	 * @return void
	 */
	public function setDuplicate($duplicate)	{
		$this->duplicate = $duplicate;
	}

	/**
	 * setExtraData function.
	 *
	 * @access public
	 * @param mixed $extraData
	 * @return void
	 */
	public function setExtraData($extraData)	{
		$this->extraData = $extraData;
	}
}

?>
