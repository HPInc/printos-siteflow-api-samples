// © Copyright 2016 HP Development Company, L.P.
// SPDX-License-Identifier: MIT

import java.io.IOException;
import java.security.InvalidKeyException;
import java.security.NoSuchAlgorithmException;

import org.apache.http.HttpRequest;
import org.apache.http.HttpResponse;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.client.methods.HttpPut;
import org.apache.http.entity.StringEntity;
import org.apache.http.impl.client.CloseableHttpClient;
import org.apache.http.impl.client.HttpClients;


public class SiteFlow {

	//Access Credentials
	//private static String baseUrl = "https://printos.api.hp.com/siteflow"; //use for account on production server
	//private static String baseUrl = "https://stage.printos.api.hp.com/siteflow"; //use for account on staging server
	private static HmacAuth auth;

	public SiteFlow(String key, String secret, String algorithm) {
		auth = new HmacAuth(key, secret, algorithm);
	}

	/**
	 * Cancel an order in Site Flow
	 * 
	 * @param sourceAccount - name of source account
	 * @param sourceOrderId - source order id of the order (user generated)
	 * @return HttpResponse of the PUT request
	 * @throws IOException
	 * @throws InvalidKeyException
	 * @throws NoSuchAlgorithmException
	 */
	public HttpResponse CancelOrder(String sourceAccount, String sourceOrderId) throws IOException, InvalidKeyException, NoSuchAlgorithmException {
		String path = "/api/order/" + sourceAccount + "/" + sourceOrderId + "/cancel";
		CloseableHttpClient client = HttpClients.createDefault();
		HttpPut request = new HttpPut(baseUrl + path);
		
		addHeaders(request, "PUT", path);

		System.out.println("Cancelling order: " + sourceOrderId + " from " + sourceAccount);
		return client.execute(request);
	}

	/**
	 * Gets a list of all orders in Site Flow
	 * 
	 * @return HttpResponse of the GET request
	 * @throws InvalidKeyException
	 * @throws NoSuchAlgorithmException
	 * @throws IOException
	 */
	public HttpResponse GetAllOrders() throws InvalidKeyException, NoSuchAlgorithmException, IOException {
		String path = "/api/order";
		CloseableHttpClient client = HttpClients.createDefault();
		HttpGet request = new HttpGet(baseUrl + path);
		
		addHeaders(request, "GET", path);
		
		System.out.println("Getting all orders");
		return client.execute(request);
	}

	/**
	 * Gets information of a specific order in Site Flow
	 * 
	 * @param orderId - id of the order (SiteFlow generated)
	 * @return HttpResponse of the PUT request
	 * @throws InvalidKeyException
	 * @throws NoSuchAlgorithmException
	 * @throws IOException
	 */
	public HttpResponse GetOrder(String orderId) throws InvalidKeyException, NoSuchAlgorithmException, IOException {
		String path = "/api/order/" + orderId;
		CloseableHttpClient client = HttpClients.createDefault();
		HttpGet request = new HttpGet(baseUrl + path);
		
		addHeaders(request, "GET", path);
		
		System.out.println("Getting order with ID: " + orderId);
		return client.execute(request);
	}

	/**
	 * Submits an order into Site Flow
	 * 
	 * @param order - order to submit
	 * @return HttpResponse of the POST request
	 * @throws InvalidKeyException
	 * @throws NoSuchAlgorithmException
	 * @throws IOException
	 */
	public HttpResponse SubmitOrder(String order) throws InvalidKeyException, NoSuchAlgorithmException, IOException {
		String path = "/api/order";
		CloseableHttpClient client = HttpClients.createDefault();
		HttpPost request = new HttpPost(baseUrl + path);
		
		addHeaders(request, "POST", path);
		request.setEntity(new StringEntity(order, "UTF-8"));
		
		System.out.println("Submitting Order");
		return client.execute(request);
	}

	/**
	 * Validates an order to see if its able to be submitted successfully
	 * 
	 * @param order - order to validate
	 * @return HttpResponse of the POST request
	 * @throws InvalidKeyException
	 * @throws NoSuchAlgorithmException
	 * @throws IOException
	 */
	public HttpResponse ValidateOrder(String order) throws InvalidKeyException, NoSuchAlgorithmException, IOException {
		String path = "/api/order/validate";
		CloseableHttpClient client = HttpClients.createDefault();
		HttpPost request = new HttpPost(baseUrl + path);
		
		addHeaders(request, "POST", path);
		request.setEntity(new StringEntity(order, "UTF-8"));
		
		System.out.println("Validating Order");
		return client.execute(request);
	}

	/**
	 * Adds the headers to an HttpRequest
	 * 
	 * @param request - request to add the headers to
	 * @param method - type of request (GET, POST, PUT)
	 * @param path - the path the request is sent to (doesn't include baseUrl)
	 * @throws InvalidKeyException
	 * @throws NoSuchAlgorithmException
	 */
	private void addHeaders(HttpRequest request, String method, String path) throws InvalidKeyException, NoSuchAlgorithmException {
		request.addHeader("Content-Type", "application/json");
		request.addHeader("x-hp-hmac-authentication", auth.getHmacAuthentication(method, path));
		request.addHeader("x-hp-hmac-date", auth.getTimestamp());
	}
}
