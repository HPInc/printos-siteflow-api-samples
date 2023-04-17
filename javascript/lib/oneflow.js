const Order = require('./models/Order');
const crypto = require('crypto');
const URL = require('url');
const axios = require('axios');
const axiosRetry = require('axios-retry');
const { isRetryableError, exponentialDelay } = require('./customRetry');

function OneFlowClient(baseUrl, token, secret, options = {}) {

	const client = axios.create();
	axiosRetry(client, {
		retries: options.retries || 3, 
		retryDelay: options.retryDelay || exponentialDelay,
		retryCondition: options.retryCondition || isRetryableError
	});

	let order;

	function request(method, resourcePath, data, options = {}) {
		const url = `${baseUrl}${resourcePath}`;
		const headers = makeHeaders(url, method, options);
		const axiosRetry = options['axios-retry'] || {};
		return client
			.request({ method, url, data: data === null ? {} : data, headers, 'axios-retry': axiosRetry })
			.then(res => res.data);
	}

	function makeHeaders(url, method, options) {
		const parsedUrl = URL.parse(url);
		const pathname = parsedUrl.pathname;
		const timestamp = new Date().toISOString();
		const headers = {
			'x-oneflow-authorization': makeToken(method, pathname, timestamp),
			'x-oneflow-date': timestamp,
			'x-oneflow-algorithm': 'SHA256',
			'content-type': 'application/json'
		};
		if (options.serviceUser && options.accountId) {
			headers['x-oneflow-account'] = options.accountId;
		}
		return headers;
	}

	function makeToken(method, path, timestamp) {
		const StringToSign = `${method.toUpperCase()} ${decodeURIComponent(path)} ${timestamp}`;
		const hmac = crypto.createHmac('SHA256', secret);
		hmac.update(StringToSign);
		const Signature = hmac.digest('hex');
		const localAuthorization = `${token}:${Signature}`;
		return localAuthorization;
	}

	function createOrder(destination, config) {
		order = new Order(destination, config);
		return order;
	}

	function orderToJSON() {
		return JSON.stringify(order, null, 2);
	}

	function validateOrder() {
		return this.request('POST', '/order/validate', order);
	}

	function submitOrder(options = {}) {
		let path = '/order'
		if (options.routingRule) {
			path = `/order/route/${options.routingRule}`
		}
		return this.request('POST', path, order, options)
			.then(r => {
				// connect submission API
				if (r.order) return r.order;

				// order service submission API
				return r;
			})
			.catch(err => {
				if (err.response && err.response.status === 400 && err.response.data) {
					if (err.response.data.error) throw err.response.data.error;
				}
				throw err;
			});
	}

	return {
		client,
		request,
		createOrder,
		orderToJSON,
		validateOrder,
		submitOrder
	};

}

module.exports = OneFlowClient;

