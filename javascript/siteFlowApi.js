// © Copyright 2016 HP Development Company, L.P.
// SPDX-License-Identifier: MIT

var printOSClientPath = printOSClientPath || './printOSClient'; 
var PrintOSClient = require(printOSClientPath);

function SiteFlowApi(baseUrl, key, secret, proxy) {

	var printOSClient = new PrintOSClient(baseUrl, key, secret, proxy);

	this.validateOrder = function(order) {
		console.log("Validating order", (order.orderData.sourceOrderId));
		return printOSClient.post('/api/order/validate', order);
	}
	
	this.submitOrder = function(order) {
		console.log("Creating order", (order.orderData.sourceOrderId));
		return printOSClient.post('/api/order', order);
	}
	
	this.cancelOrder = function(account, id) {
		console.log("Cancelling account:order " + account + ':' + id);
		return printOSClient.put('/api/order/' + account + '/' + id + '/cancel');
	}
	
	this.getOrder = function(id) {
		console.log("Getting order " + id);
		return printOSClient.get('/api/order/' + id);
	}
	
	this.getOrders = function() {
		console.log("Getting all orders");
		return printOSClient.get('/api/order');
	}
}

module.exports = SiteFlowApi;