import java.io.IOException;
import java.security.InvalidKeyException;
import java.security.NoSuchAlgorithmException;
import java.util.Random;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.ParseException;
import org.apache.http.util.EntityUtils;
import org.json.JSONObject;

public class SiteFlow_Example {

	//Access Credentials
	private static String key = "";
	private static String secret = "";
	private static SiteFlow siteFlow;

	public static void main(String[] args) throws IOException, InvalidKeyException, NoSuchAlgorithmException {
		siteFlow = new SiteFlow(key, secret, "HmacSHA1");

		printInfo( siteFlow.ValidateOrder(createOrder()), true );
//		printInfo( siteFlow.SubmitOrder(createOrder()), true );
//		printInfo( siteFlow.GetAllOrders(), true );
//		printInfo( siteFlow.GetOrder("OrderId"), true );
//		printInfo( siteFlow.CancelOrder("sourceAccount", "sourceOrderId"), true );
	}

	/**
	 * Creates an order for submission or validation
	 * 
	 * @return JSON string of an example order
	 */
	private static String createOrder() {
		String componentCode = "Content";
		String destination = "hp.jpeng";
		String fetchPath = "https://Server/Path/business_cards.pdf";
		String itemId = "";
		String orderId = "";
		String postbackAddress = "http://postback.genesis.com";
		int quantity = 1;
		String sku = "Business Cards";
		
		//Generate random OrderId and itemId
		Random rand = new Random();
		char[] values = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789".toCharArray();
		for(int i = 0; i < 10; i++) {
			orderId += values[rand.nextInt(values.length)];
			itemId += values[rand.nextInt(values.length)];
		}
		
		//Shipping Information
		JSONObject shipTo = new JSONObject();
		shipTo.put("name", "John Doe");
		shipTo.put("address1", "5th Avenue");
		shipTo.put("town", "New York");
		shipTo.put("postcode", "12345");
		shipTo.put("state", "New York");
		shipTo.put("isoCountry", "US");
		shipTo.put("email", "johnd@acme.com");
		shipTo.put("phone", "0123456789");
		
		//Courier Services
		JSONObject carrier = new JSONObject();
		carrier.put("code", "customer");
		carrier.put("service", "shipping");
		
		//Create an Item
		JSONObject item = new JSONObject();
		item.put("sourceItemId", itemId);
		item.put("sku", sku);
		item.put("quantity", quantity);
		
		JSONObject components = new JSONObject();
		components.put("code", componentCode);
		components.put("path", fetchPath);
		components.put("fetch", "true");
		
		item.append("components", components);		
		
		//Create a shipment
		JSONObject shipment = new JSONObject();
		shipment.put("shipTo", shipTo);
		shipment.put("carrier", carrier);
		
		//Put the complete order together
		JSONObject dest = new JSONObject();
		dest.put("name", destination);
		
		JSONObject orderData = new JSONObject();
		orderData.put("sourceOrderId", orderId);
		orderData.put("postbackAddress", postbackAddress);
		orderData.append("items", item);
		orderData.append("shipments", shipment);
		
		JSONObject order = new JSONObject();
		order.put("destination", dest);
		order.put("orderData", orderData);
		
		return order.toString();
	}
	
	/**
	 * Prints the body of a HttpResponse in a "pretty" format. Information printed is determined
	 * on whether the body is expected to be JSON or regular format.
	 * 
	 * @param response - Response with information to output
	 * @param JsonResponse - Determines if response entity is expected to have JSON output 
	 * @throws ParseException
	 * @throws IOException
	 */
	private static void printInfo(HttpResponse response, boolean JsonResponse) throws ParseException, IOException {
		System.out.println(response.getStatusLine().getStatusCode() + " : " + response.getStatusLine().getReasonPhrase());
		HttpEntity entity = response.getEntity();
		String body = EntityUtils.toString(entity, "UTF-8");
		if(JsonResponse) {
			JSONObject formatted = new JSONObject(body);
			System.out.print("RESPONSE: ");
			System.out.println(formatted.toString(4));
		} else {
			System.out.println(body);
		}
	}

}
