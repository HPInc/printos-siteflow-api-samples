using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace SiteFlow
{
    class Order
    {
        public Destination destination { get; set; }
        public OrderData orderData { get; set; }

        public Order(Item item, Shipment shipment)
        {
            destination = new Destination();
            orderData = new OrderData();

            orderData.items[0] = item;
            orderData.shipments[0] = shipment;
        }

        public class Destination
        {
            public string name { get; set; }
        }

        public class OrderData
        {
            public string sourceOrderId { get; set; }
            public string postbackAddress { get; set; }
            public Item[] items = new Item[1];
            public Shipment[] shipments = new Shipment[1];
        }
    }
}
