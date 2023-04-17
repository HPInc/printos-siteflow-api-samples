<?php

error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

global $OneFlowLevel;
$OneFlowFilenames = Array();

/**
 * OneFlow class.
 */
class OneFlow {

}

/**
 * OneFlowBase class.
 */
class OneFlowBase	{

	private $__children = Array();
	private $__required = Array();
	private $__lists = Array();
	private $__validation = Array();

	/**
	 * __construct function.
	 *
	 * @access public
	 * @param mixed $values (default: null)
	 */
	public function __construct($values=null) {
		$this->init();
		$this->setValues($values);
    }

	/**
	 * init function.
	 *
	 * @access public
	 * @return void
	 */
	public function init()	{
		//this is to be overwritten if needed
	}


	/**
	 * __addList function.
	 *
	 * @access public
	 * @param mixed $name
	 * @param mixed $list
	 * @return void
	 */
	public function __addList($name, $list)	{
		$this->__lists[$name] = $list;
	}

	/**
	 * __addProperty function.
	 *
	 * @access public
	 * @param mixed $name
	 * @param string $default (default: "")
	 * @param bool $required (default: false)
	 * @return void
	 */
	public function __addProperty($name, $default = null, $required=false)	{
		if ($default !== null) $this->$name = $default;
		if ($required)	$this->__required[] = $name;
	}

	/**
	 * __addObject function.
	 *
	 * @access public
	 * @param mixed $collection
	 * @param mixed $class
	 * @return void
	 */
	public function __addObject($collection, $class){
		$this->__children[$collection] = $class;
		$classname = "OneFlow".$class;
		$this->$collection = new $classname();
	}

	/**
	 * __addArray function.
	 *
	 * @access public
	 * @param mixed $collection
	 * @param mixed $class
	 * @return void
	 */
	public function __addArray($collection, $class){
		$this->__children[$collection] = $class;
		$this->$collection = Array();
	}

	/**
	 * getValidationMessages function.
	 *
	 * @access public
	 * @return array
	 */
	public function getValidationMessages()	{
		return $this->__validation;
	}

	/**
	 * isValid function.
	 *
	 * @access public
	 * @return array
	 */
	public function isValid()	{
		global $OneFlowFilenames;

		$this->__validation = Array();

		foreach ($this->__required as $fieldName)	{
			$value = $this->$fieldName;

			$set = true;
			if ($value==="")	{
				$set=false;
			}

			if ($value==="0")	{
				$set=true;
			}

			if ($value===null)		$set = false;

			if (!$set) {
				$this->__validation[] = get_class($this).".".$fieldName." is required";
			}
		}

		foreach ($this->__lists as $listName=>$list)	{
			$list = $this->__lists[$listName];
			$value = $this->$listName;

			if (!in_array($value, $list)) {
				$this->__validation[] = "'$value' is not a valid selection for ".get_class($this).".$listName";
			}
		}

		$children = $this->__children;

		foreach ($this as $key=>$value)	{
			$type = gettype($value);

			if ($type=="array")	{

				if (isset($children[$key]))	{
					//cycle through the array
					foreach ($value as $arrayKey=>$arrayItem)	{
						$arrayItemType = gettype($value[$arrayKey]);
						if ($arrayItemType=="object"){
							$this->__validation = array_merge($this->__validation, $value[$arrayKey]->isValid());
						}
					}
				}

			} else if ($type=="object")	{

				if (isset($children[$key]))	{
					$this->__validation = array_merge($this->__validation, $value->isValid());
				}

			}	else {

				if ($key=="path")	{

					$uniqueCount = count(array_unique($OneFlowFilenames));
					if ($uniqueCount!=count($OneFlowFilenames))	{
						$this->__validation = array_merge($this->__validation, Array("Unique filenames are required"));
					}

				}
			}
		}

		//remove fields if empty
		foreach ($this as $key=>$value)	{
			if (!in_array($key, $this->__required))	{
				//not required then remove if empty
				$this->removeIfEmpty($key);
			}
		}

		return $this->__validation;
	}

	/**
	 * removeIfEmpty function.
	 *
	 * @access public
	 * @param mixed $key
	 * @return void
	 */
	public function removeIfEmpty($key)	{
		if (substr($key,0,2)!="__")	{
			if ($this->$key===Array()) {
				unset($this->$key);
			}
			elseif ($this->$key==="") {
				unset($this->$key);
			}
			elseif (is_object($this->$key) && (count(get_object_vars($this->$key))== 4)) {
				unset($this->$key);
			}
		}
	}

	/**
	 * setValue function.
	 *
	 * @access public
	 * @param mixed $name
	 * @param mixed $value
	 * @return void
	 */
	public function setValue($name, $value)	{
		if (property_exists($this, $name))	$this->$name = $value;
	}

	/**
	 * setValues function.
	 *
	 * @access public
	 * @param mixed $values
	 * @return void
	 */
	public function setValues($values)	{
		if ($values==null) return;
		//go through each of the properties of the incoming value
		foreach ($values as $name=>$value)	{
			$type = gettype($value);

			if ($type=="array")	{
				if (isset($this->__children[$name]))	{
					//cycle through the array
					foreach ($value as $element)	{
						$baseClassname = $this->__children[$name];

						if ($baseClassname=="String")	{
							$this->{$name}[] = $element;
						}	else	{
							@class_alias("OneFlowAttachment", "OneFlowAttachments", true);
							$classname = "OneFlow".$this->__children[$name];
							$this->{$name}[] = new $classname($element);
						}
					}

				}	else	{
					foreach ($value as $element)	{
						$this->{$name}[] = $element;
					}
				}

			} else if ($type=="object")	{
				if (isset($this->__children[$name]))	{
					$baseClassname = $this->__children[$name];

					$classname = "OneFlow".$baseClassname;
					$this->{$name} = new $classname($value);
				}

			}	else	{
				$this->setValue($name, $value);
			}
		}
	}

	/**
	 * objectToXML function.
	 *
	 * @access public
	 * @return string
	 */
	public function objectToXML()	{
		global $OneFlowLevel;

		$OneFlowLevel++;
		$xml = "";

		foreach ($this as $key=>$value)	{
			$type = gettype($this->$key);
			$spacer = str_repeat("  ",$OneFlowLevel);

			if (substr($key,0,2)!="__")	{

				if ($type=="array")	{

					$xml .= "$spacer<$key>\n";

					$array = $this->$key;
					$OneFlowLevel++;
					$spacer = str_repeat("  ",$OneFlowLevel);

					$index=0;
					foreach ($array as $element)	{

						$arrayType = gettype($element);
						if ($arrayType=="object")	{
							$xml .= "$spacer<arrayItem index=\"$index\">\n";
							$xml .= $element->objectToXML();
							$xml .= "$spacer</arrayItem>\n";
						}	else	{
							$xml .= "$spacer<arrayItem index=\"$index\">$element</arrayItem>\n";
						}
					}

					$OneFlowLevel--;
					$spacer = str_repeat("  ",$OneFlowLevel);

					$xml .= "$spacer</$key>\n";
				}	else if ($type=="object")	{

					$xml .= "$spacer<$key>\n";
					$xml .= $this->$key->objectToXML();
					$xml .= "$spacer</$key>\n";

				} 	else	{
					$xml .= "$spacer<$key>$value</$key>\n";
				}
			}
		}

		$OneFlowLevel--;

		return $xml;
	}

	/**
	 * toJSON function.
	 *
	 * @access public
	 * @return mixed
	 */
	public function toJSON()	{
		return json_encode($this);
	}

	/**
	 * toPrettyJSON function.
	 *
	 * @access public
	 * @return string
	 */
	public function toPrettyJSON()	{
		return $this->prettyPrint(json_encode($this))."\n";
	}

	/**
	 * prettyPrint function.
	 *
	 * @access private
	 * @param mixed $json
	 * @return string
	 */
	private function prettyPrint($json)	{
	    $result = '';
	    $level = 0;
	    $prev_char = '';
	    $in_quotes = false;
	    $ends_line_level = NULL;
	    $json_length = strlen( $json );

	    for( $i = 0; $i < $json_length; $i++ ) {
	        $char = $json[$i];
	        $new_line_level = NULL;
	        $post = "";
	        if( $ends_line_level !== NULL ) {
	            $new_line_level = $ends_line_level;
	            $ends_line_level = NULL;
	        }
	        if( $char === '"' && $prev_char != '\\' ) {
	            $in_quotes = !$in_quotes;
	        } else if( ! $in_quotes ) {
	            switch( $char ) {
	                case '}': case ']':
	                    $level--;
	                    $ends_line_level = NULL;
	                    $new_line_level = $level;
	                    break;

	                case '{': case '[':
	                    $level++;
	                case ',':
	                    $ends_line_level = $level;
	                    break;

	                case ':':
	                    $post = " ";
	                    break;

	                case " ": case "\t": case "\n": case "\r":
	                    $char = "";
	                    $ends_line_level = $new_line_level;
	                    $new_line_level = NULL;
	                    break;
	            }
	        }
	        if( $new_line_level !== NULL ) {
	            $result .= "\n".str_repeat( "\t", $new_line_level );
	        }
	        $result .= $char.$post;
	        $prev_char = $char;
	    }

	    return $result;
	}
}

?>