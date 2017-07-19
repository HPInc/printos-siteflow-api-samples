// © Copyright 2016 HP Development Company, L.P.
// SPDX-License-Identifier: MIT

namespace SiteFlow
{
    class Item
    {
        public string sourceItemId { get; set; }
        public string sku { get; set; }
        public int quantity { get; set; }
        public Component[] components { get; set; }

        public Item(Component[] components)
        {
            this.components = components;
        }
    }
}
