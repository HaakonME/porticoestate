/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.3.0
build: 3167
*/
YUI.add('node-deprecated', function(Y) {

var Y_Node = Y.Node;

/*
 * Flat data store for off-DOM usage 
 * @config data
 * @type any
 * @deprecated Use getData/setData
 */
Y_Node.ATTRS.data = {
    getter: function() { 
        return this._dataVal; 
    },
    setter: function(val) { 
        this._dataVal = val;
        return val;
    },
    value: null
};

/**
 * Returns a single Node instance bound to the node or the
 * first element matching the given selector.
 * @method Y.get
 * @deprecated Use Y.one
 * @static
 * @param {String | HTMLElement} node a node or Selector 
 * @param {Y.Node || HTMLElement} doc an optional document to scan. Defaults to Y.config.doc. 
 */
Y.get = Y_Node.get = function() {
    return Y_Node.one.apply(Y_Node, arguments);
};


Y.mix(Y_Node.prototype, {
    /**
     * Retrieves a Node instance of nodes based on the given CSS selector. 
     * @method query
     * @deprecated Use one()
     * @param {string} selector The CSS selector to test against.
     * @return {Node} A Node instance for the matching HTMLElement.
     */
    query: function(selector) {
        return this.one(selector);
    },

    /**
     * Retrieves a nodeList based on the given CSS selector. 
     * @method queryAll
     * @deprecated Use all()
     * @param {string} selector The CSS selector to test against.
     * @return {NodeList} A NodeList instance for the matching HTMLCollection/Array.
     */
    queryAll: function(selector) {
        return this.all(selector);
    },

    /**
     * Applies the given function to each Node in the NodeList.
     * @method each
     * @deprecated Use NodeList
     * @param {Function} fn The function to apply 
     * @param {Object} context optional An optional context to apply the function with
     * Default context is the NodeList instance
     * @chainable
     */
    each: function(fn, context) {
        context = context || this;
        return fn.call(context, this);
    },

    /**
     * Retrieves the Node instance at the given index. 
     * @method item
     * @deprecated Use NodeList
     *
     * @param {Number} index The index of the target Node.
     * @return {Node} The Node instance at the given index.
     */
    item: function(index) {
        return this;
    },

    /**
     * Returns the current number of items in the Node.
     * @method size
     * @deprecated Use NodeList
     * @return {Int} The number of items in the Node. 
     */
    size: function() {
        return this._node ? 1 : 0;
    }

});




}, '3.3.0' ,{requires:['node-base']});
