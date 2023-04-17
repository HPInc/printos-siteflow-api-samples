<?php

use PHPUnit\Framework\TestCase;

final class OneflowOrderTest extends TestCase
{
	public function testCanBuildASingleItemOrder()
	{
		// Arrange
		$order = new OneFlowOrder();
		$order->setDestination('destinationName');
		$orderData = new OneFlowOrderData();
		$orderData->setSourceOrderId('uniqueSourceOrderId');
		$orderData->setCustomerName('customerName');
		$orderData->setEmail('customerEmail');
		$orderData->setInstructions('some instructions');
		$orderData->setExtraData([
			'param1' => 'value1',
			'param2' => 'value2'
		]);

		$item = $orderData->newSKUItem('skuCode', 'itemId', 5);
		$item->setPrintQuantity(1);
		$item->setBarcode('customItemBarcode');
		$item->setDispatchAlert('my item dispatch alert');
		$item->setUnitCost(3.3);
		$item->setUnitPrice(4.4);
		$item->setUnitWeight(5.5);
		$item->setExtraData([
			'param3' => 'value3',
			'param4' => 'value4'
		]);

		$component = $item->newComponent('componentCode');
		$component->setFetchUrl('http://site.com/file.pdf');
		$component->setLocalFile(false);
		$component->setBarcode('customComponentBarcode');
		$component->setPreflight(true);
		$component->setPreflightProfile('custom');
		$component->setPreflightProfileId('preflightProfileId');
		$component->setBarcode('customComponentBarcode');
		$component->setDuplicate(5);
		$component->addAttribute('fooString', 'bar');
		$component->addAttribute('fooNumber', 123);
		$component->setExtraData([
			'param5' => 'value5',
			'param6' => 'value6'
		]);

		$sourceProductItem = $orderData->newSourceProductIdItem('isbn', 'itemId2', 2);

		$textComponent = $sourceProductItem->addTextComponent();
		$textComponent->addAttribute('custom', 'value');

		$coverComponent = $sourceProductItem->addCoverComponent();
		$coverComponent->addAttribute('additional', 'value');

		$stockItem = $orderData->newStockItem('stockCode', 100);
		$stockItem->setName('my name');
		$stockItem->setUnitPrice(2.2);

		$shipment = $orderData->newShipment();
		$shipment->setShipTo(
			'name',
			'companyName',
			'address1',
			'address2',
			'address3',
			'town',
			'state',
			'postcode',
			'isoCountryCode',
			'countryName',
			'phone',
			'email'
		);
		$shipment->setReturnAddress(
			'name',
			'companyName',
			'address1',
			'address2',
			'address3',
			'town',
			'state',
			'postcode',
			'isoCountryCode',
			'country',
			'phone'
		);
		$shipment->setCarrier('carrierCode', 'carrierService');
		$shipment->setCarrierByAlias('carrierAlias');
		$shipment->newAttachment(
			'http://site.com/attachment.pdf',
			'insert',
			'application/pdf',
			false
		);
		$shipment->setDispatchAlert('my shipment dispatch alert');
		$shipment->setLabelName('labelName');
		$shipment->setSlaDays(5);
		$shipment->setCanShipEarly(false);
		$shipment->setShipByDate('2019-04-01');
		$shipment->setCarrierFields([
			'carrierField1' => 'carrierValue1',
			'carrierField2' => 'carrierValue2'
		]);


		$item->setShipment($shipment);
		$stockItem->setShipment($shipment);

		$order->setOrderData($orderData);

		// Execute
		$json = $order->toJSON();
		$result = json_decode($json);

		// Assert
		$validationOutput = $order->validateOrder();
		$this->assertEquals("Valid Order\n", $validationOutput);

		// Order
		$this->assertObjectHasAttribute('orderData', $result);
		$this->assertObjectHasAttribute('destination', $result);
		$this->assertObjectHasAttribute('extraData', $result->orderData);
		$this->assertEquals('destinationName', $result->destination->name);
		$this->assertEquals('uniqueSourceOrderId', $result->orderData->sourceOrderId);
		$this->assertEquals('customerName', $result->orderData->customerName);
		$this->assertEquals('customerEmail', $result->orderData->email);
		$this->assertEquals('some instructions', $result->orderData->instructions);
		$this->assertEquals('value1', $result->orderData->extraData->param1);
		$this->assertEquals('value2', $result->orderData->extraData->param2);

		// Items
		$this->assertEquals(2, count($result->orderData->items));
		$outputItem = $result->orderData->items[0];
		$this->assertEquals('skuCode', $outputItem->sku);
		$this->assertEquals('itemId', $outputItem->sourceItemId);
		$this->assertEquals('customItemBarcode', $outputItem->barcode);
		$this->assertEquals(5, $outputItem->quantity);
		$this->assertEquals(1, $outputItem->printQuantity);
		$this->assertEquals(0, $outputItem->shipmentIndex);
		$this->assertEquals(3.3, $outputItem->unitCost);
		$this->assertEquals(4.4, $outputItem->unitPrice);
		$this->assertEquals(5.5, $outputItem->unitWeight);
		$this->assertEquals('my item dispatch alert', $outputItem->dispatchAlert);
		$this->assertObjectHasAttribute('extraData', $outputItem);
		$this->assertEquals('value3', $outputItem->extraData->param3);
		$this->assertEquals('value4', $outputItem->extraData->param4);

		$this->assertEquals(1, count($outputItem->components));
		$outputComponent = $outputItem->components[0];
		$this->assertEquals('componentCode', $outputComponent->code);
		$this->assertEquals('http://site.com/file.pdf', $outputComponent->path);
		$this->assertEquals('customComponentBarcode', $outputComponent->barcode);
		$this->assertEquals(true, $outputComponent->fetch);
		$this->assertEquals(false, $outputComponent->localFile);
		$this->assertEquals(5, $outputComponent->duplicate);
		$this->assertObjectHasAttribute('attributes', $outputComponent);
		$this->assertEquals('bar', $outputComponent->attributes->fooString);
		$this->assertEquals(123, $outputComponent->attributes->fooNumber);
		$this->assertEquals(true, $outputComponent->preflight);
		$this->assertEquals('custom', $outputComponent->preflightProfile);
		$this->assertEquals('preflightProfileId', $outputComponent->preflightProfileId);
		$this->assertObjectHasAttribute('extraData', $outputComponent);
		$this->assertEquals('value5', $outputComponent->extraData->param5);
		$this->assertEquals('value6', $outputComponent->extraData->param6);

		// Source Product Item
		$outputSourceProductItem = $result->orderData->items[1];
		$this->assertEmpty($outputSourceProductItem->sku);
		$this->assertEquals('isbn', $outputSourceProductItem->sourceProductId);
		$this->assertEquals('itemId2', $outputSourceProductItem->sourceItemId);
		$this->assertEquals('2', $outputSourceProductItem->quantity);

		$this->assertEquals(2, count($outputSourceProductItem->components));
		$outputTextComponent = $outputSourceProductItem->components[0];
		$outputCoverComponent = $outputSourceProductItem->components[1];
		$this->assertEquals('text', $outputTextComponent->code);
		$this->assertObjectHasAttribute('custom', $outputTextComponent->attributes);
		$this->assertEquals('cover', $outputCoverComponent->code);
		$this->assertObjectHasAttribute('additional', $outputCoverComponent->attributes);

		// Stock items
		$this->assertEquals(1, count($result->orderData->stockItems));
		$outputStockItem = $result->orderData->stockItems[0];
		$this->assertEquals('stockCode', $outputStockItem->code);
		$this->assertEquals('my name', $outputStockItem->name);
		$this->assertEquals(2.2, $outputStockItem->unitPrice);
		$this->assertEquals(100, $outputStockItem->quantity);
		$this->assertEquals(0, $outputStockItem->shipmentIndex);


		// Shipments
		$this->assertEquals(1, count($result->orderData->shipments));
		$outputShipment = $result->orderData->shipments[0];
		$this->assertEquals(0, $outputShipment->shipmentIndex);
		$this->assertEquals('my shipment dispatch alert', $outputShipment->dispatchAlert);
		$this->assertEquals('labelName', $outputShipment->labelName);
		$this->assertEquals(false, $outputShipment->canShipEarly);
		$this->assertEquals('2019-04-01', $outputShipment->shipByDate);

		$this->assertObjectHasAttribute('shipTo', $outputShipment);
		$this->assertEquals('name', $outputShipment->shipTo->name);
		$this->assertEquals('companyName', $outputShipment->shipTo->companyName);
		$this->assertEquals('address1', $outputShipment->shipTo->address1);
		$this->assertEquals('address2', $outputShipment->shipTo->address2);
		$this->assertEquals('address3', $outputShipment->shipTo->address3);
		$this->assertEquals('town', $outputShipment->shipTo->town);
		$this->assertEquals('state', $outputShipment->shipTo->state);
		$this->assertEquals('postcode', $outputShipment->shipTo->postcode);
		$this->assertEquals('isoCountryCode', $outputShipment->shipTo->isoCountry);
		$this->assertEquals('countryName', $outputShipment->shipTo->country);
		$this->assertEquals('phone', $outputShipment->shipTo->phone);
		$this->assertEquals('email', $outputShipment->shipTo->email);

		$this->assertObjectHasAttribute('returnAddress', $outputShipment);
		$this->assertEquals('name', $outputShipment->returnAddress->name);
		$this->assertEquals('companyName', $outputShipment->returnAddress->companyName);
		$this->assertEquals('address1', $outputShipment->returnAddress->address1);
		$this->assertEquals('address2', $outputShipment->returnAddress->address2);
		$this->assertEquals('address3', $outputShipment->returnAddress->address3);
		$this->assertEquals('town', $outputShipment->returnAddress->town);
		$this->assertEquals('state', $outputShipment->returnAddress->state);
		$this->assertEquals('postcode', $outputShipment->returnAddress->postcode);
		$this->assertEquals('isoCountryCode', $outputShipment->returnAddress->isoCountry);
		$this->assertEquals('country', $outputShipment->returnAddress->country);
		$this->assertEquals('phone', $outputShipment->returnAddress->phone);

		$this->assertObjectHasAttribute('carrier', $outputShipment);
		$this->assertEquals('carrierCode', $outputShipment->carrier->code);
		$this->assertEquals('carrierService', $outputShipment->carrier->service);
		$this->assertEquals('carrierAlias', $outputShipment->carrier->alias);
		$this->assertEquals(5, $outputShipment->slaDays);

		$this->assertEquals(1, count($outputShipment->attachments));
		$outputAttachment = $outputShipment->attachments[0];
		$this->assertEquals('http://site.com/attachment.pdf', $outputAttachment->path);
		$this->assertEquals('insert', $outputAttachment->type);
		$this->assertEquals('application/pdf', $outputAttachment->contentType);
		$this->assertEquals(false, $outputAttachment->fetch);

		$this->assertObjectHasAttribute('carrierFields', $outputShipment);
		$this->assertEquals('carrierValue1', $outputShipment->carrierFields->carrierField1);
		$this->assertEquals('carrierValue2', $outputShipment->carrierFields->carrierField2);

	}
}
