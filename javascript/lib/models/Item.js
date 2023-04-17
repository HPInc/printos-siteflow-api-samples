const Base = require('./Base');
const Component = require('./Component');

class Item extends Base {
	constructor(config) {
		super(config);
        if (!this.components) this.components = [];
	}
	addComponent(config) {
		const newComponent = new Component(config);
		this.components.push(newComponent);
		return newComponent;
	}
}

module.exports = Item;

