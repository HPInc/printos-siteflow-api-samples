require('should');
const _ = require('lodash');
const sinon = require('sinon');
const OneflowSDK = require('../lib/oneflow');

describe('Oneflow', function () {
	let sdk;
	let order;
	let stubs = {};

	afterEach(() => {
		_.each(stubs, stub => stub.restore());
		stubs = {};
	});

	it('should successfully create an instance of the SDK', function () {
		sdk = OneflowSDK('http://fakedomain.local/api', 'token', 'secret');
		sdk.should.have.property('createOrder');
	});

	it('should successfully create an instance of the SDK with retry options', function () {
		const options = { retries: 1, retryDelay: () => 3, retryCondition: () => true };
		sdk = OneflowSDK('http://fakedomain.local/api', 'token', 'secret', options);
		sdk.should.have.property('createOrder');
	});

	describe('Order Builder', () => {

		it('should successfully create an order structure', function () {
			order = sdk.createOrder('my-account', { sourceOrderId: '1234' });

			order.should.have.property('destination');
			order.should.have.property('orderData');
			order.destination.should.have.property('name').equal('my-account');
			order.orderData.should.have.property('sourceOrderId').equal('1234');
		});

		it('should successfully add items to the order', function () {
			const item = order.addItem({
				sourceItemId: 'ABCD'
			});

			item.addComponent({
				barcode: 'XYZ'
			});

			order.addStockItem({
				code: '123',
				quantity: 10
			});

			order.should.have.property('destination');
			order.should.have.property('orderData');
			order.destination.should.have.property('name').equal('my-account');
			order.orderData.should.have.property('items').length(1);
			order.orderData.items[0].should.have.property('components').length(1);
			order.orderData.should.have.property('stockItems').length(1);
			order.orderData.stockItems[0].should.have.property('code').equal('123');
		});

		it('should successfully add shipments to the order', function () {
			order.addShipment({
				shipTo: {
					name: 'Nigel Watson',
					address1: '999 Letsbe Avenue',
					town: 'London',
					isoCountry: 'UK',
					postcode: 'EC2N 0BJ'
				},
				carrier: {
					code: 'royalmail',
					service: 'firstclass'
				}
			});

			order.orderData.should.have.property('shipments').length(1);
			order.orderData.shipments[0].should.have.property('shipTo');
			order.orderData.shipments[0].should.have.property('carrier');
			order.orderData.shipments[0].shipTo.should.have.property('name').equal('Nigel Watson');
			order.orderData.shipments[0].carrier.should.have.property('code').equal('royalmail');
		});

		it('should export an order to json', function () {
			const json = order.toJson();
			json.should.be.type('string');

			const orderObject = JSON.parse(json);
			orderObject.should.have.properties(['destination', 'orderData']);
		});

		it('should export an order to json using the SDK shortcut', function () {
			const json = sdk.orderToJSON();
			json.should.be.type('string');

			const orderObject = JSON.parse(json);
			orderObject.should.have.properties(['destination', 'orderData']);
		});
	});

	describe('request', () => {

		it('should perform an http request with the Oneflow auth headers', async () => {
			stubs.axiosRequest = sinon.stub(sdk.client, 'request').callsFake(a => Promise.resolve({ data: a }));

			const result = await sdk.request('post', '/order', { mock: true });

			result.should.have.properties(['method', 'url', 'data', 'headers']);

			result.method.should.be.equal('post');
			result.url.should.be.equal('http://fakedomain.local/api/order');
			result.data.should.have.property('mock').equal(true);
			result.headers.should.have.properties([
				'x-oneflow-authorization',
				'x-oneflow-algorithm',
				'x-oneflow-date',
				'content-type',
			]);
		});

		it('should perform an http request with data=null', async () => {
			stubs.axiosRequest = sinon.stub(sdk.client, 'request').callsFake(a => Promise.resolve({ data: a }));

			const result = await sdk.request('get', '/order', null);

			result.method.should.be.equal('get');
			result.data.should.be.eql({});
		});

		it('should perform an http request with data=undefined', async () => {
			stubs.axiosRequest = sinon.stub(sdk.client, 'request').callsFake(a => Promise.resolve({ data: a }));

			const result = await sdk.request('get', '/order', undefined);

			result.method.should.be.equal('get');
			(result.data === undefined).should.equal(true);
		});

		it('should perform an http request with data=""', async () => {
			stubs.axiosRequest = sinon.stub(sdk.client, 'request').callsFake(a => Promise.resolve({ data: a }));

			const result = await sdk.request('get', '/order', '');

			result.method.should.be.equal('get');
			result.data.should.be.equal('');
		});

		it('should support service user requests', async () => {
			stubs.axiosRequest = sinon.stub(sdk.client, 'request').callsFake(a => Promise.resolve({ data: a }));

			const options = { serviceUser: true, accountId: 'QWERTY' };
			const result = await sdk.request('post', '/order', { mock: true }, options);

			result.should.have.properties(['method', 'url', 'data', 'headers']);

			result.method.should.be.equal('post');
			result.url.should.be.equal('http://fakedomain.local/api/order');
			result.data.should.have.property('mock').equal(true);
			result.headers.should.have.properties([
				'x-oneflow-authorization',
				'x-oneflow-algorithm',
				'x-oneflow-date',
				'x-oneflow-account',
				'content-type',
			]);
		});

	});

	describe('validateOrder', () => {
		it('should call the validate order endpoint ', async () => {
			stubs.request = sinon.stub(sdk, 'request').resolves(true);

			await sdk.validateOrder();

			stubs.request.calledOnce.should.be.ok();
			const requestArgs = stubs.request.lastCall.args;
			requestArgs[0].should.be.equal('POST');
			requestArgs[1].should.be.equal('/order/validate');
			requestArgs[2].should.have.properties(['destination', 'orderData']);
		});
	});

	describe('submitOrder', () => {
		it('should call the create order endpoint and handle a connect response', async () => {
			stubs.request = sinon.stub(sdk, 'request').resolves({ order });

			const result = await sdk.submitOrder();
			result.should.have.properties(['destination', 'orderData']);

			stubs.request.calledOnce.should.be.ok();
			const requestArgs = stubs.request.lastCall.args;
			requestArgs[0].should.be.equal('POST');
			requestArgs[1].should.be.equal('/order');
			requestArgs[2].should.have.properties(['destination', 'orderData']);
		});

		it('should call the create order endpoint and handle a order service response', async () => {
			stubs.request = sinon.stub(sdk, 'request').resolves({ _id: 'abc', timestamp: '123' });

			const result = await sdk.submitOrder();
			result.should.have.properties(['_id', 'timestamp']);

			stubs.request.calledOnce.should.be.ok();
			const requestArgs = stubs.request.lastCall.args;
			requestArgs[0].should.be.equal('POST');
			requestArgs[1].should.be.equal('/order');
			requestArgs[2].should.have.properties(['destination', 'orderData']);
		});

		it('should handle an invalid submission', async () => {
			stubs.request = sinon.stub(sdk, 'request').callsFake(() => Promise.reject({
				response: {
					status: 400,
					data: {
						error: 'invalid order error'
					}
				}
			}));

			try {
				await sdk.submitOrder();
				return Promise.reject('it should throw an error');
			} catch (e) {
				e.should.be.equal('invalid order error');
			}

			stubs.request.calledOnce.should.be.ok();
			const requestArgs = stubs.request.lastCall.args;
			requestArgs[0].should.be.equal('POST');
			requestArgs[1].should.be.equal('/order');
			requestArgs[2].should.have.properties(['destination', 'orderData']);
		});

		it('should handle other errors', async () => {
			stubs.request = sinon.stub(sdk, 'request').callsFake(() => Promise.reject('my error'));

			try {
				await sdk.submitOrder();
				return Promise.reject('it should throw an error');
			} catch (e) {
				e.should.be.equal('my error');
			}

			stubs.request.calledOnce.should.be.ok();
			const requestArgs = stubs.request.lastCall.args;
			requestArgs[0].should.be.equal('POST');
			requestArgs[1].should.be.equal('/order');
			requestArgs[2].should.have.properties(['destination', 'orderData']);
		});

		it('should be able to submit an order to with a routing rule endpoint', async () => {
			stubs.request = sinon.stub(sdk, 'request').resolves({ order });

			const result = await sdk.submitOrder({ routingRule: '12ab34cd56ef' });
			result.should.have.properties(['destination', 'orderData']);
			stubs.request.calledOnce.should.be.ok();
			const requestArgs = stubs.request.lastCall.args;
			requestArgs[0].should.be.equal('POST');
			requestArgs[1].should.be.equal('/order/route/12ab34cd56ef');
			requestArgs[2].should.have.properties(['destination', 'orderData']);
		});
	});
});

