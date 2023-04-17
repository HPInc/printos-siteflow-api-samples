const Base = require('./Base');
const Item = require("./Item");

class OrderData extends Base {
    constructor(config) {
        super(config);
        if (!this.items) this.items = [];
        if (!this.shipments) this.shipments = [];
    }

    addItem(item) {
        const newItem = new Item(item);
        this.items.push(newItem);
        return newItem;
    }

    addStockItem(stockItem) {
        if (!this.stockItems) this.stockItems = [];
        this.stockItems.push(stockItem);
    }

    addShipment(shipment) {
        this.shipments.push(shipment);
    }
}

module.exports = OrderData;

