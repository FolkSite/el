var el = function (config) {
	config = config || {};
	el.superclass.constructor.call(this, config);
};
Ext.extend(el, Ext.Component, {
	page: {}, window: {}, grid: {}, tree: {}, panel: {}, combo: {}, config: {}, view: {}, utils: {}
});
Ext.reg('el', el);

el = new el();