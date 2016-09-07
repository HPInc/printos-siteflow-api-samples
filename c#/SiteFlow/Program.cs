using Newtonsoft.Json;
using Newtonsoft.Json.Linq;
using System;
using System.Net.Http;
using System.Net.Http.Headers;
using System.Security.Cryptography;
using System.Text;
using System.Threading.Tasks;

namespace SiteFlow
{
    class Program
    {
        //Access Credentials
        //private static string baseUrl = "https://printos.api.hp.com/siteflow";		//use for production server account
        private static string baseUrl = "https://stage.printos.api.hp.com/siteflow";    //use for staging server account
        private static string key = "";
        private static string secret = "";

        static void Main(string[] args)
        {
            RunAsync().Wait();

            Console.WriteLine("All Tasks Complete. Press any key to stop...");
            Console.ReadKey();
        }

        static async Task RunAsync()
        {
            await ValidateOrder(); WaitBeforeProceeding();
            //await SubmitOrder(); WaitBeforeProceeding();
            //await GetAllOrders(); WaitBeforeProceeding();
            //await GetSingleOrder("OrderId"); WaitBeforeProceeding();
            //await CancelOrder("sourceAccount", "sourceOrderId"); WaitBeforeProceeding();
        }


        //Helper methods below
        /*------------------------------------------------------------------------------------*/

        private static void CreateHmacHeaders(string method, string path, HttpClient client)
        {
            string timeStamp = DateTime.UtcNow.ToString("yyyy-MM-ddTHH:mm:ssZ");

            string stringToSign = method + " " + path + timeStamp;
            HMACSHA1 hmac = new HMACSHA1(Encoding.UTF8.GetBytes(secret));
            byte[] bytes = hmac.ComputeHash(Encoding.UTF8.GetBytes(stringToSign));
            string signature = BitConverter.ToString(bytes).Replace("-", string.Empty).ToLower();
            string auth = key + ":" + signature;

            client.DefaultRequestHeaders.Add("x-hp-hmac-authentication", auth);
            client.DefaultRequestHeaders.Add("x-hp-hmac-date", timeStamp);
            client.DefaultRequestHeaders.Add("x-hp-hmac-algorithm", "SHA1");
        }

        private static Order CreateOrder()
        {
            //Variables required for a single item order
            //Not the full set of fields available
            string componentCode = "Content";
            string destination = "hp.jpeng";
            string fetchPath = "https://Server/Path/business_cards.pdf";
            string itemId = "ORDERID";
            string orderId = "ORDERID";
            string postbackAddress = "http://postback.genesis.com";
            string sku = "Business Cards";
            int quantity = 1;

            //Shipping Information
            ShipTo shipTo = new ShipTo();
            shipTo.name = "John Doe";
            shipTo.address1 = "5th Avenue";
	        shipTo.town = "New York";
            shipTo.postcode = "12345";
	        shipTo.state = "New York";
	        shipTo.isoCountry = "US";
	        shipTo.email = "johnd@acme.com";
	        shipTo.phone = "01234567890";

            //Courier Information
            Carrier carrier = new Carrier();
            carrier.code = "customer";
            carrier.service = "shipping";

            //Component Information
            Component component = new Component();
            component.code = componentCode;
            component.path = fetchPath;
            component.fetch = "true";

            //Create an Item
            Item item = new Item(component);
            item.sourceItemId = itemId;
            item.sku = sku;
            item.quantity = quantity;

            //Create a Shipment
            Shipment shipment = new Shipment();
            shipment.shipTo = shipTo;
            shipment.carrier = carrier;

            //Put the complete order together
            Order order = new Order(item, shipment);
            order.destination.name = destination;
            order.orderData.sourceOrderId = orderId;
            order.orderData.postbackAddress = postbackAddress;

            //Console.WriteLine(JsonConvert.SerializeObject(order, Formatting.Indented));

            return order;
        }

        private static void WaitBeforeProceeding()
        {
            Console.WriteLine("Press any key to continue...");
            Console.ReadKey();
        }


        //Methods for APIs Below
        /*------------------------------------------------------------------------------------*/

        private static async Task CancelOrder(string sourceAccount, string orderId)
        {
            Console.WriteLine("Canceling Order from account " + sourceAccount + " with ID: " + orderId);
            using (var client = new HttpClient())
            {
                string path = "/api/order/" + sourceAccount + "/" + orderId + "/cancel";
                CreateHmacHeaders("PUT", path, client);

                HttpResponseMessage response = await client.PutAsync(baseUrl + path, null);

                if (response.IsSuccessStatusCode)
                {
                    Console.WriteLine("Success. Order " + orderId + " was canceled.");
                    string info = await response.Content.ReadAsStringAsync();
                    JObject json = JObject.Parse(info);
                    string infoFormatted = json.ToString();
                    Console.WriteLine(infoFormatted);
                }
                else
                {
                    Console.WriteLine("Failure. Unable to cancel order " + orderId);
                    Console.WriteLine(response.ReasonPhrase); //Bad Request here may be caused by the order already being canceled.
                }
            }
        }

        private static async Task GetAllOrders()
        {
            Console.WriteLine("Getting All Orders");
            using (var client = new HttpClient())
            {
                CreateHmacHeaders("GET", "/api/order", client);

                HttpResponseMessage response = await client.GetAsync(baseUrl + "/api/order");

                if (response.IsSuccessStatusCode)
                {
                    Console.WriteLine("Success. Orders were obtained.");
                    string info = await response.Content.ReadAsStringAsync();
                    JObject json = JObject.Parse(info);
                    string infoFormatted = json.ToString();
                    Console.WriteLine(infoFormatted);
                }
                else
                {
                    Console.WriteLine("Failure. Unable to obtain orders.");
                    Console.WriteLine(response.ReasonPhrase);
                }
            }
        }

        private static async Task GetSingleOrder(string orderId)
        {
            Console.WriteLine("Getting Order with ID: " + orderId);
            using (var client = new HttpClient())
            {
                CreateHmacHeaders("GET", "/api/order/" + orderId, client);

                HttpResponseMessage response = await client.GetAsync(baseUrl + "/api/order/" + orderId);

                if (response.IsSuccessStatusCode)
                {
                    Console.WriteLine("Success. Order " + orderId + " was obtained.");
                    string info = await response.Content.ReadAsStringAsync();
                    JObject json = JObject.Parse(info);
                    string infoFormatted = json.ToString();
                    Console.WriteLine(infoFormatted);
                }
                else
                {
                    Console.WriteLine("Failure. Unable to obtain order " + orderId);
                    Console.WriteLine(response.ReasonPhrase);
                }
            }
        }

        private static async Task SubmitOrder()
        {
            Console.WriteLine("Submitting Order.");
            Order order = CreateOrder();
            string orderJson = JsonConvert.SerializeObject(order);

            using (var client = new HttpClient())
            {
                CreateHmacHeaders("POST", "/api/order", client);
                client.DefaultRequestHeaders.Accept.Add(new MediaTypeWithQualityHeaderValue("application/json"));

                HttpResponseMessage response = await client.PostAsync(baseUrl + "/api/order", new StringContent(orderJson, Encoding.UTF8, "application/json"));

                if (response.IsSuccessStatusCode)
                {
                    Console.WriteLine("Success. Order has been submitted.");
                    string info = await response.Content.ReadAsStringAsync();
                    JObject json = JObject.Parse(info);
                    string infoFormatted = json.ToString();
                    Console.WriteLine(infoFormatted);
                }
                else
                {
                    Console.WriteLine("Failure. Order was rejected.");
                    Console.WriteLine(response.ReasonPhrase);
                }
            }
        }

        private static async Task ValidateOrder()
        {
            Console.WriteLine("Validating Order.");
            Order order = CreateOrder();
            string orderJson = JsonConvert.SerializeObject(order);

            using (var client = new HttpClient())
            {
                CreateHmacHeaders("POST", "/api/order/validate", client);
                client.DefaultRequestHeaders.Accept.Add(new MediaTypeWithQualityHeaderValue("application/json"));

                HttpResponseMessage response = await client.PostAsync(baseUrl + "/api/order/validate", new StringContent(orderJson, Encoding.UTF8, "application/json"));

                if (response.IsSuccessStatusCode)
                {
                    Console.WriteLine("Success. Order is valid.");
                    string info = await response.Content.ReadAsStringAsync();
                    JObject json = JObject.Parse(info);
                    string infoFormatted = json.ToString();
                    Console.WriteLine(infoFormatted);
                }
                else
                {
                    Console.WriteLine("Failure. Order is invalid.");
                    Console.WriteLine(response.ReasonPhrase); //Bad Request here may be caused by an order with an existing OrderID
                }
            }
        }

    }
}