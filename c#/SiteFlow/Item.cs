using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace SiteFlow
{
    class Item
    {
        public string sourceItemId { get; set; }
        public string sku { get; set; }
        public int quantity { get; set; }
        public Component[] components { get; set; }

        public Item(Component component)
        {
            components = new Component[1];
            components[0] = component;
        }
    }
}
