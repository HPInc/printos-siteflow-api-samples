using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace SiteFlow
{
    class Shipment
    {
        public ShipTo shipTo { get; set; }
        public Carrier carrier { get; set; }
    }
}
