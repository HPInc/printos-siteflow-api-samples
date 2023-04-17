<?php

require_once "error/error.php";
require_once "item/item.php";
require_once "stockItem/stockItem.php";
require_once "shipment/shipment.php";

/**
 * OneFlowOrderData class.
 *
 * @extends OneFlowBase
 */
class OneFlowOrderData extends OneFlowBase	{

	/**
	 * init function.
	 *
	 * @access public
	 * @return void
	 */
	public function init()      {

		$this->__addList("printType", Array("digital", "litho", "largeformat"));

		$this->__addArray("shipments","Shipment");
		$this->__addArray("items","Item");
		$this->__addArray("stockItems","stockItems");
		$this->__addArray("error","Error");
		$this->__addArray("extraData",null);

		$this->__addProperty("printType", "digital", true);
		$this->__addProperty("sourceOrderId", "", true);
		$this->__addProperty("status");
		$this->__addProperty("read");
		$this->__addProperty("inqueue");
		$this->__addProperty("counter");
		$this->__addProperty("slaTimestamp");
		$this->__addProperty("date");
		$this->__addProperty("clientTimeZoneMinuts");
		$this->__addProperty("email","");
		$this->__addProperty("misCode");
		$this->__addProperty("amount", 0, "numeric"); 	//check this validation
		$this->__addProperty("currency");
		$this->__addProperty("deliveryDate");			//check whether required in SKU Orders
		$this->__addProperty("shipbyDate");			//check whether required in SKU Orders
		$this->__addProperty("customerName","");
		$this->__addProperty("purchaseOrderNumber");
		$this->__addProperty("consolidatedInvoice");
		$this->__addProperty("tax");
		$this->__addProperty("instructions");

    }

	/**
	 * newItem function.
	 *
	 * @access public
	 * @return OneFlowItem
	 */
	public function newItem()	{
		$this->items[] = new OneFlowItem();
		return end($this->items);
	}

	/**
	 * newSKUItem function.
	 *
	 * @access public
	 * @param mixed $skuCode
	 * @param mixed $sourceItemId
	 * @param mixed $quantity
	 * @return OneFlowItem
	 */
	public function newSKUItem($skuCode, $sourceItemId, $quantity = 1)	{
		$item = new OneFlowItem();
		$item->setSourceItemId($sourceItemId);
		$item->setQuantity($quantity);
		$item->setSKU($skuCode);

		$this->items[] = $item;
		return $item;
	}

	/**
	 * newSourceProductIdItem function.
	 *
	 * @access public
	 * @param string $sourceProductId
	 * @param mixed $sourceItemId
	 * @param integer $quantity
	 * @return OneFlowItem
	 */
	public function newSourceProductIdItem($sourceProductId, $sourceItemId, $quantity = 1)	{
		$item = new OneFlowItem();
		$item->setSourceItemId($sourceItemId);
		$item->setQuantity($quantity);
		$item->setSourceProductId($sourceProductId);

		$this->items[] = $item;
		return $item;
	}

	/**
	 * newStockItem function.
	 *
	 * @access public
	 * @param mixed $stockCode
	 * @param mixed $quantity
	 * @param mixed $shipmentIndex
	 * @return OneFlowStockItem
	 */
	public function newStockItem($stockCode, $quantity = 1)	{
		$stockItem = new OneFlowStockItem();
		$stockItem->setCode($stockCode);
		$stockItem->setQuantity($quantity);

		$this->stockItems[] = $stockItem;
		return $stockItem;
	}

	/**
	 * newShipment function.
	 *
	 * @access public
	 * @return OneFlowShipment
	 */
	public function newShipment()	{
		$count = count($this->shipments);

		$this->shipments[$count] = new OneFlowShipment();
		$this->shipments[$count]->setShipmentIndex($count);
		return end($this->shipments);
	}

	/**
	 * setPrintType function.
	 *
	 * @access public
	 * @param mixed $printType
	 * @return boolean
	 */
	public function setPrintType($printType)	{
		if (in_array($printType, $this->printTypes))	{
			$this->printType = $printType;
		}	else	{
			return false;
		}
		return true;
	}

	/**
	 * setDeliveryDate function.
	 *
	 * @access public
	 * @param mixed $deliveryDate
	 * @return void
	 */
	public function setDeliveryDate($deliveryDate)	{
		$date = new DateTime($deliveryDate);
		$this->deliveryDate = $date->format('Y-m-d\TH:i:sO');
		$date->add(new DateInterval('P1D'));
		$this->shipbyDate = $date->format('Y-m-d\TH:i:sO');
	}

	/**
	 * setShipbyDate function.
	 *
	 * @access public
	 * @param mixed $shipbyDate
	 * @return void
	 */
	public function setShipbyDate($shipbyDate)	{
		$date = new DateTime($shipbyDate);
		$this->shipbyDate = $date->format('Y-m-dTH:i:sO');
	}

	/**
	 * setSourceOrderId function.
	 *
	 * @access public
	 * @param mixed $sourceOrderId
	 * @return void
	 */
	public function setSourceOrderId($sourceOrderId)	{
		$this->sourceOrderId = $sourceOrderId;
	}

	/**
	 * setCustomerName function.
	 *
	 * @access public
	 * @param mixed $customerName
	 * @return void
	 */
	public function setCustomerName($customerName)	{
		$this->customerName = $customerName;
	}

	/**
	 * setEmail function.
	 *
	 * @access public
	 * @param mixed $email
	 * @return void
	 */
	public function setEmail($email)	{
		$this->email = $email;
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

	/**
	 * setInstructions function.
	 *
	 * @access public
	 * @param string $instructions
	 * @return void
	 */
	public function setInstructions($instructions)
	{
		$this->instructions = $instructions;
	}
}

?>
