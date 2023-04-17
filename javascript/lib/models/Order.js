const Base = require('./Base');
const OrderData = require('./OrderData');

class Order extends Base {
    constructor(name, config) {
        super();
        this.destination = { name };
        this.orderData = new OrderData(config);
    }
    addItem(item) {
        return this.orderData.addItem(item);
    }
    addShipment(shipment) {
        return this.orderData.addShipment(shipment);
    }
    addStockItem(item) {
        return this.orderData.addStockItem(item);
    }
}

module.exports = Order;

