// © Copyright 2016 HP Development Company, L.P.
// SPDX-License-Identifier: MIT

var SiteFlowApi = require('./siteFlowApi');

var key = '';
var secret = '';
var proxy = parseArgs();

new SiteFlowApiTest().testAll();

function SiteFlowApiTest() {

	//var baseUrl = 'https://printos.api.hp.com/siteflow'; //use for account on production server
	//var baseUrl = 'https://stage.printos.api.hp.com/siteflow'; //use for account on staging server
	var siteFlowApi = new SiteFlowApi(baseUrl, key, secret, proxy);

	this.testCancelOrder = function(account, id) {
		siteFlowApi.cancelOrder(account, id)
		.then(success, failure);
	}

	this.testGetOrder = function(id) {
		siteFlowApi.getOrder(id)
		.then(success, failure);
	}
	
	this.testGetOrders = function() {
		siteFlowApi.getOrders()
		.then(success, failure);
	}

	this.testGetProducts = function() {
		siteFlowApi.getProducts()
		.then(success, failure);
	}
	
	this.testGetSkus = function() {
		siteFlowApi.getSkus()
		.then(success, failure);
	}

	this.testGetUploadUrls = function(mime_type) {
		siteFlowApi.getUploadUrls(mime_type)
		.then(success, failure);
	}
	
	this.testSubmitOrder = function(order) {
		siteFlowApi.submitOrder(order)
		.then(success, failure);
	}

	this.testValidateOrder = function(order) {
		siteFlowApi.validateOrder(order)
		.then(success, failure);
	}
	
	this.testAll = function() {
		this.testValidateOrder(this.buildOrder());
		// this.testSubmitOrder(this.buildOrder());
		// this.testGetProducts();
		// this.testGetSkus();
		// this.testGetUploadUrls("application/pdf")
		// this.testGetOrders();
		// this.testGetOrder('orderId');
		// this.testCancelOrder('sourceAccount', 'sourceOrderId');
	}
	
	this.buildOrder = function() {

		var orderId = getRandomOrderId();

		var order = {
			destination: {
				name: "hp.jpeng"
			},
			orderData: {
				sourceOrderId: orderId,
				postbackAddress: "apjohnsto@gmail.com",
				items: [{
					description: "Test Product",
					sku: "Flat",
					sourceItemId: orderId + "-I-01",
					components: [{
						path: "https://Server/Path/business_cards.pdf",
						code: "Content",
						fetch: true,
						// route: [{
						// 	"name": "Print",
						// 	"eventTypeId": ""		//eventTypeId found within Site Flow -> Events
						// }, {
						// 	"name": "Cut",
						// 	"eventTypeId": ""
						// }, {
						// 	"name": "Laminate",
						// 	"eventTypeId": ""
						// }, {
						// 	"name": "Finish",
						// 	"eventTypeId": ""
						// }]
					}]
				}],
				shipments: [{
					shipTo: {
						name: "Peter Pan",
						address1: "17 Disney Way",
						town: "Los Angeles",
						postcode: "34757",
						state: "California",
						isoCountry: "US",
						email: "Peter@Pan.com",
						phone: "0123456789"
					},
					carrier: {
						code: "customer",
						service: "shipping"
					}
				}]
			}
		}
		return order;
	}
	
	//Returns a random integer between min (included) and max (included)
	function getRandomIntInclusive(min, max) {
		return Math.floor(Math.random() * (max - min + 1)) + min;
	}

	function getRandomOrderId() {
		var table = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		var orderId = '';
		for (var i = 0; i < 12; i++) {
			var index = getRandomIntInclusive(0, table.length - 1);
			orderId = orderId + table.charAt(index);
		}
		return orderId;
	};
}

//Handle 'proxy' cmd line arg: -p=<proxy>
function parseArgs() {
	var args = process.argv.slice(2);
	for (var i = 0; i < args.length; i++) {
		var arg = args[i];
		if (arg.startsWith('-p')) {
			var strs = arg.split('=');
			if (strs[1].length > 0) {
				proxy = strs[1];
				return proxy;
			}
		}
	}
	return undefined;
}

function success(response) {
	console.log(JSON.stringify({status: response.statusCode, body: response.body}, null, "  "));
}

function failure(error)	{
	console.log(JSON.stringify({error: error}, null, "  "));
}