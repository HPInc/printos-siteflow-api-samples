class Base {
	constructor(options) {
		Object.assign(this, options);
	}
	toJson() {
		return JSON.stringify(this, null, 2);
	}
}

module.exports = Base;

