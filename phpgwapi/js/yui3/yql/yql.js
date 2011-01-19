/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.3.0
build: 3167
*/
YUI.add('yql', function(Y) {

    /**
     * This class adds a sugar class to allow access to YQL (http://developer.yahoo.com/yql/).
     * @module yql
     */     
    /**
     * Utility Class used under the hood my the YQL class
     * @class YQLRequest
     * @constructor
     * @param {String} sql The SQL statement to execute
     * @param {Function/Object} callback The callback to execute after the query (Falls through to JSONP).
     * @param {Object} params An object literal of extra parameters to pass along (optional).
     * @param {Object} opts An object literal of configuration options (optional): proto (http|https), base (url)
     */
    var YQLRequest = function (sql, callback, params, opts) {
        
        if (!params) {
            params = {};
        }
        params.q = sql;
        //Allow format override.. JSON-P-X
        if (!params.format) {
            params.format = Y.YQLRequest.FORMAT;
        }
        if (!params.env) {
            params.env = Y.YQLRequest.ENV;
        }

        this._params = params;
        this._opts = opts;
        this._callback = callback;

    };
    
    YQLRequest.prototype = {
        /**
        * @private
        * @property _jsonp
        * @description Reference to the JSONP instance used to make the queries
        */
        _jsonp: null,
        /**
        * @private
        * @property _opts
        * @description Holder for the opts argument
        */
        _opts: null,
        /**
        * @private
        * @property _callback
        * @description Holder for the callback argument
        */
        _callback: null,
        /**
        * @private
        * @property _params
        * @description Holder for the params argument
        */
        _params: null,
        /**
        * @method send
        * @description The method that executes the YQL Request.
        * @chainable
        * @returns {YQLRequest}
        */
        send: function() {
            var qs = '', url = ((this._opts && this._opts.proto) ? this._opts.proto : Y.YQLRequest.PROTO);

            Y.each(this._params, function(v, k) {
                qs += k + '=' + encodeURIComponent(v) + '&';
            });
            
            url += ((this._opts && this._opts.base) ? this._opts.base : Y.YQLRequest.BASE_URL) + qs;
            
            var o = (!Y.Lang.isFunction(this._callback)) ? this._callback : { on: { success: this._callback } };
            if (o.allowCache !== false) {
                o.allowCache = true;
            }
            
            if (!this._jsonp) {
                this._jsonp = Y.jsonp(url, o);
            } else {
                this._jsonp.url = url;
                if (o.on && o.on.success) {
                    this._jsonp._config.on.success = o.on.success;
                }
                this._jsonp.send();
            }
            return this;
        }
    };

    /**
    * @static
    * @property FORMAT
    * @description Default format to use: json
    */
    YQLRequest.FORMAT = 'json';
    /**
    * @static
    * @property PROTO
    * @description Default protocol to use: http
    */
    YQLRequest.PROTO = 'http';
    /**
    * @static
    * @property BASE_URL
    * @description The base URL to query: query.yahooapis.com/v1/public/yql?
    */
    YQLRequest.BASE_URL = ':/'+'/query.yahooapis.com/v1/public/yql?';
    /**
    * @static
    * @property ENV
    * @description The environment file to load: http://datatables.org/alltables.env
    */
    YQLRequest.ENV = 'http:/'+'/datatables.org/alltables.env';
    
    Y.YQLRequest = YQLRequest;
	
    /**
     * This class adds a sugar class to allow access to YQL (http://developer.yahoo.com/yql/).
     * @class YQL
     * @constructor
     * @param {String} sql The SQL statement to execute
     * @param {Function} callback The callback to execute after the query (optional).
     * @param {Object} params An object literal of extra parameters to pass along (optional).
     */
	Y.YQL = function(sql, callback, params) {
        return new Y.YQLRequest(sql, callback, params).send();
    };



}, '3.3.0' ,{requires:['jsonp']});
