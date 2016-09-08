var crypto = require("crypto")
var superagent = require("superagent")
require('superagent-proxy')(superagent);

var PrintOSClient = function(baseUrl, key, secret, proxy) {
	
	this.get = function(path, queryParams) {
		return sendRequest('get', path, undefined, queryParams);
	}

	this.post = function(path, payload, queryParams) {
		return sendRequest('post', path, payload, queryParams);
	}
	
	this.put = function(path, payload, queryParams) {
		return sendRequest('put', path, payload, queryParams);
	}
	
	function createHeaders(method, path) {
	    var timestamp = (new Date()).toISOString()
	    var toSign = method.toUpperCase() + " " + path + timestamp
	    var hash = crypto.createHmac("SHA1", secret)
	    hash.update(toSign)
	    var sig = hash.digest("hex");
	    var headers = {
	      "X-HP-HMAC-Authentication": key + ":" + sig,
	      "X-HP-HMAC-Date":  timestamp,
	      "X-HP-HMAC-Algorithm": "SHA1"
	    }
	    return headers;
	}
	
	function sendRequest(method, path, payload, queryParams) {
		return new Promise(function(resolve, reject) {
			function responseHandler(error, response) {
				if (error) { 
					reject(error);
				} else {
					resolve(response);
				}
			}

			var request = superagent[method.toLowerCase()](baseUrl + path);
			request.set('Accept', "application/json");
			request.set('Cache-Control', "no-cache");

			var headers = createHeaders(method, path);
			Object.keys(headers).forEach(function(k) {
				request.set(k, headers[k])
			})

			request.proxyWrapper = function(proxy) {
				if (proxy == undefined)
					return this;
				return this.proxy(proxy);
			}

			return request.send(payload)
				.query(queryParams)
				.proxyWrapper(proxy)
				.end(responseHandler);
		});
	}
}

module.exports = PrintOSClient;