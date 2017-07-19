// © Copyright 2016 HP Development Company, L.P.
// SPDX-License-Identifier: MIT

namespace SiteFlow
{
    class Route
    {
        public string name { get; set; }
        public string eventTypeId { get; set; }

        public Route(string name, string eventTypeId)
        {
            this.name = name;
            this.eventTypeId = eventTypeId;
        }
    }
}
