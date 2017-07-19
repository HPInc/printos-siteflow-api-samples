// © Copyright 2016 HP Development Company, L.P.
// SPDX-License-Identifier: MIT

namespace SiteFlow
{
    class Component
    {
        public string code { get; set; }
        public string path { get; set; }
        public string fetch { get; set; }
        public Route[] route { get; set; }
    }
}
