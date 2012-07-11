/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.3.0
build: 3167
*/
YUI.add('autocomplete-base', function(Y) {

/**
 * Provides automatic input completion or suggestions for text input fields and
 * textareas.
 *
 * @module autocomplete
 * @since 3.3.0
 */

/**
 * <code>Y.Base</code> extension that provides core autocomplete logic (but no
 * UI implementation) for a text input field or textarea. Must be mixed into a
 * <code>Y.Base</code>-derived class to be useful.
 *
 * @module autocomplete
 * @submodule autocomplete-base
 */

/**
 * <p>
 * Extension that provides core autocomplete logic (but no UI implementation)
 * for a text input field or textarea.
 * </p>
 *
 * <p>
 * The <code>AutoCompleteBase</code> class provides events and attributes that
 * abstract away core autocomplete logic and configuration, but does not provide
 * a widget implementation or suggestion UI. For a prepackaged autocomplete
 * widget, see <code>AutoCompleteList</code>.
 * </p>
 *
 * <p>
 * This extension cannot be instantiated directly, since it doesn't provide an
 * actual implementation. It's intended to be mixed into a
 * <code>Y.Base</code>-based class or widget.
 * </p>
 *
 * <p>
 * <code>Y.Widget</code>-based example:
 * </p>
 *
 * <pre>
 * YUI().use('autocomplete-base', 'widget', function (Y) {
 * &nbsp;&nbsp;var MyAC = Y.Base.create('myAC', Y.Widget, [Y.AutoCompleteBase], {
 * &nbsp;&nbsp;&nbsp;&nbsp;// Custom prototype methods and properties.
 * &nbsp;&nbsp;}, {
 * &nbsp;&nbsp;&nbsp;&nbsp;// Custom static methods and properties.
 * &nbsp;&nbsp;});
 * &nbsp;
 * &nbsp;&nbsp;// Custom implementation code.
 * });
 * </pre>
 *
 * <p>
 * <code>Y.Base</code>-based example:
 * </p>
 *
 * <pre>
 * YUI().use('autocomplete-base', function (Y) {
 * &nbsp;&nbsp;var MyAC = Y.Base.create('myAC', Y.Base, [Y.AutoCompleteBase], {
 * &nbsp;&nbsp;&nbsp;&nbsp;initializer: function () {
 * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;this._bindUIACBase();
 * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;this._syncUIACBase();
 * &nbsp;&nbsp;&nbsp;&nbsp;},
 * &nbsp;
 * &nbsp;&nbsp;&nbsp;&nbsp;// Custom prototype methods and properties.
 * &nbsp;&nbsp;}, {
 * &nbsp;&nbsp;&nbsp;&nbsp;// Custom static methods and properties.
 * &nbsp;&nbsp;});
 * &nbsp;
 * &nbsp;&nbsp;// Custom implementation code.
 * });
 * </pre>
 *
 * @class AutoCompleteBase
 */

var Escape  = Y.Escape,
    Lang    = Y.Lang,
    YArray  = Y.Array,
    YObject = Y.Object,

    isFunction = Lang.isFunction,
    isString   = Lang.isString,
    trim       = Lang.trim,

    INVALID_VALUE = Y.Attribute.INVALID_VALUE,

    _FUNCTION_VALIDATOR = '_functionValidator',
    _SOURCE_SUCCESS     = '_sourceSuccess',

    ALLOW_BROWSER_AC    = 'allowBrowserAutocomplete',
    INPUT_NODE          = 'inputNode',
    QUERY               = 'query',
    QUERY_DELIMITER     = 'queryDelimiter',
    REQUEST_TEMPLATE    = 'requestTemplate',
    RESULTS             = 'results',
    RESULT_LIST_LOCATOR = 'resultListLocator',
    VALUE               = 'value',
    VALUE_CHANGE        = 'valueChange',

    EVT_CLEAR   = 'clear',
    EVT_QUERY   = QUERY,
    EVT_RESULTS = RESULTS;

function AutoCompleteBase() {
    // AOP bindings.
    Y.before(this._bindUIACBase, this, 'bindUI');
    Y.before(this._destructorACBase, this, 'destructor');
    Y.before(this._syncUIACBase, this, 'syncUI');

    // -- Public Events --------------------------------------------------------

    /**
     * Fires after the query has been completely cleared or no longer meets the
     * minimum query length requirement.
     *
     * @event clear
     * @param {EventFacade} e Event facade with the following additional
     *   properties:
     *
     * <dl>
     *   <dt>prevVal (String)</dt>
     *   <dd>
     *     Value of the query before it was cleared.
     *   </dd>
     * </dl>
     *
     * @preventable _defClearFn
     */
    this.publish(EVT_CLEAR, {
        defaultFn: this._defClearFn
    });

    /**
     * Fires when the contents of the input field have changed and the input
     * value meets the criteria necessary to generate an autocomplete query.
     *
     * @event query
     * @param {EventFacade} e Event facade with the following additional
     *   properties:
     *
     * <dl>
     *   <dt>inputValue (String)</dt>
     *   <dd>
     *     Full contents of the text input field or textarea that generated
     *     the query.
     *   </dd>
     *
     *   <dt>query (String)</dt>
     *   <dd>
     *     Autocomplete query. This is the string that will be used to
     *     request completion results. It may or may not be the same as
     *     <code>inputValue</code>.
     *   </dd>
     * </dl>
     *
     * @preventable _defQueryFn
     */
    this.publish(EVT_QUERY, {
        defaultFn: this._defQueryFn
    });

    /**
     * Fires after query results are received from the <code>source</code>. If
     * no source has been set, this event will not fire.
     *
     * @event results
     * @param {EventFacade} e Event facade with the following additional
     *   properties:
     *
     * <dl>
     *   <dt>data (Array|Object)</dt>
     *   <dd>
     *     Raw, unfiltered result data (if available).
     *   </dd>
     *
     *   <dt>query (String)</dt>
     *   <dd>
     *     Query that generated these results.
     *   </dd>
     *
     *   <dt>results (Array)</dt>
     *   <dd>
     *     Array of filtered, formatted, and highlighted results. Each item in
     *     the array is an object with the following properties:
     *
     *     <dl>
     *       <dt>display (Node|HTMLElement|String)</dt>
     *       <dd>
     *         Formatted result HTML suitable for display to the user. If no
     *         custom formatter is set, this will be an HTML-escaped version of
     *         the string in the <code>text</code> property.
     *       </dd>
     *
     *       <dt>highlighted (String)</dt>
     *       <dd>
     *         Highlighted (but not formatted) result text. This property will
     *         only be set if a highlighter is in use.
     *       </dd>
     *
     *       <dt>raw (mixed)</dt>
     *       <dd>
     *         Raw, unformatted result in whatever form it was provided by the
     *         <code>source</code>.
     *       </dd>
     *
     *       <dt>text (String)</dt>
     *       <dd>
     *         Plain text version of the result, suitable for being inserted
     *         into the value of a text input field or textarea when the result
     *         is selected by a user. This value is not HTML-escaped and should
     *         not be inserted into the page using innerHTML.
     *       </dd>
     *     </dl>
     *   </dd>
     * </dl>
     *
     * @preventable _defResultsFn
     */
    this.publish(EVT_RESULTS, {
        defaultFn: this._defResultsFn
    });
}

// -- Public Static Properties -------------------------------------------------
AutoCompleteBase.ATTRS = {
    /**
     * Whether or not to enable the browser's built-in autocomplete
     * functionality for input fields.
     *
     * @attribute allowBrowserAutocomplete
     * @type Boolean
     * @default false
     */
    allowBrowserAutocomplete: {
        value: false
    },

    /**
     * When a <code>queryDelimiter</code> is set, trailing delimiters will
     * automatically be stripped from the input value by default when the
     * input node loses focus. Set this to <code>true</code> to allow trailing
     * delimiters.
     *
     * @attribute allowTrailingDelimiter
     * @type Boolean
     * @default false
     */
    allowTrailingDelimiter: {
        value: false
    },

    /**
     * Node to monitor for changes, which will generate <code>query</code>
     * events when appropriate. May be either an input field or a textarea.
     *
     * @attribute inputNode
     * @type Node|HTMLElement|String
     * @writeonce
     */
    inputNode: {
        setter: Y.one,
        writeOnce: 'initOnly'
    },

    /**
     * Maximum number of results to return. A value of <code>0</code> or less
     * will allow an unlimited number of results.
     *
     * @attribute maxResults
     * @type Number
     * @default 0
     */
    maxResults: {
        value: 0
    },

    /**
     * Minimum number of characters that must be entered before a
     * <code>query</code> event will be fired. A value of <code>0</code>
     * allows empty queries; a negative value will effectively disable all
     * <code>query</code> events.
     *
     * @attribute minQueryLength
     * @type Number
     * @default 1
     */
    minQueryLength: {
        value: 1
    },

    /**
     * <p>
     * Current query, or <code>null</code> if there is no current query.
     * </p>
     *
     * <p>
     * The query might not be the same as the current value of the input
     * node, both for timing reasons (due to <code>queryDelay</code>) and
     * because when one or more <code>queryDelimiter</code> separators are
     * in use, only the last portion of the delimited input string will be
     * used as the query value.
     * </p>
     *
     * @attribute query
     * @type String|null
     * @default null
     * @readonly
     */
    query: {
        readOnly: true,
        value: null
    },

    /**
     * <p>
     * Number of milliseconds to delay after input before triggering a
     * <code>query</code> event. If new input occurs before this delay is
     * over, the previous input event will be ignored and a new delay will
     * begin.
     * </p>
     *
     * <p>
     * This can be useful both to throttle queries to a remote data source
     * and to avoid distracting the user by showing them less relevant
     * results before they've paused their typing.
     * </p>
     *
     * @attribute queryDelay
     * @type Number
     * @default 100
     */
    queryDelay: {
        value: 100
    },

    /**
     * Query delimiter string. When a delimiter is configured, the input value
     * will be split on the delimiter, and only the last portion will be used in
     * autocomplete queries and updated when the <code>query</code> attribute is
     * modified.
     *
     * @attribute queryDelimiter
     * @type String|null
     * @default null
     */
    queryDelimiter: {
        value: null
    },

    /**
     * <p>
     * Source request template. This can be a function that accepts a query as a
     * parameter and returns a request string, or it can be a string containing
     * the placeholder "{query}", which will be replaced with the actual
     * URI-encoded query. In either case, the resulting string will be appended
     * to the request URL when the <code>source</code> attribute is set to a
     * remote DataSource, JSONP URL, or XHR URL (it will not be appended to YQL
     * URLs).
     * </p>
     *
     * <p>
     * While <code>requestTemplate</code> may be set to either a function or
     * a string, it will always be returned as a function that accepts a
     * query argument and returns a string.
     * </p>
     *
     * @attribute requestTemplate
     * @type Function|String|null
     * @default null
     */
    requestTemplate: {
        setter: '_setRequestTemplate',
        value: null
    },

    /**
     * <p>
     * Array of local result filter functions. If provided, each filter
     * will be called with two arguments when results are received: the query
     * and an array of result objects. See the documentation for the
     * <code>results</code> event for a list of the properties available on each
     * result object.
     * </p>
     *
     * <p>
     * Each filter is expected to return a filtered or modified version of the
     * results array, which will then be passed on to subsequent filters, then
     * the <code>resultHighlighter</code> function (if set), then the
     * <code>resultFormatter</code> function (if set), and finally to
     * subscribers to the <code>results</code> event.
     * </p>
     *
     * <p>
     * If no <code>source</code> is set, result filters will not be called.
     * </p>
     *
     * <p>
     * Prepackaged result filters provided by the autocomplete-filters and
     * autocomplete-filters-accentfold modules can be used by specifying the
     * filter name as a string, such as <code>'phraseMatch'</code> (assuming
     * the necessary filters module is loaded).
     * </p>
     *
     * @attribute resultFilters
     * @type Array
     * @default []
     */
    resultFilters: {
        setter: '_setResultFilters',
        value: []
    },

    /**
     * <p>
     * Function which will be used to format results. If provided, this function
     * will be called with two arguments after results have been received and
     * filtered: the query and an array of result objects. The formatter is
     * expected to return an array of HTML strings or Node instances containing
     * the desired HTML for each result.
     * </p>
     *
     * <p>
     * See the documentation for the <code>results</code> event for a list of
     * the properties available on each result object.
     * </p>
     *
     * <p>
     * If no <code>source</code> is set, the formatter will not be called.
     * </p>
     *
     * @attribute resultFormatter
     * @type Function|null
     */
    resultFormatter: {
        validator: _FUNCTION_VALIDATOR
    },

    /**
     * <p>
     * Function which will be used to highlight results. If provided, this
     * function will be called with two arguments after results have been
     * received and filtered: the query and an array of filtered result objects.
     * The highlighter is expected to return an array of highlighted result
     * text in the form of HTML strings.
     * </p>
     *
     * <p>
     * See the documentation for the <code>results</code> event for a list of
     * the properties available on each result object.
     * </p>
     *
     * <p>
     * If no <code>source</code> is set, the highlighter will not be called.
     * </p>
     *
     * @attribute resultHighlighter
     * @type Function|null
     */
    resultHighlighter: {
        setter: '_setResultHighlighter'
    },

    /**
     * <p>
     * Locator that should be used to extract an array of results from a
     * non-array response.
     * </p>
     *
     * <p>
     * By default, no locator is applied, and all responses are assumed to be
     * arrays by default. If all responses are already arrays, you don't need to
     * define a locator.
     * </p>
     *
     * <p>
     * The locator may be either a function (which will receive the raw response
     * as an argument and must return an array) or a string representing an
     * object path, such as "foo.bar.baz" (which would return the value of
     * <code>result.foo.bar.baz</code> if the response is an object).
     * </p>
     *
     * <p>
     * While <code>resultListLocator</code> may be set to either a function or a
     * string, it will always be returned as a function that accepts a response
     * argument and returns an array.
     * </p>
     *
     * @attribute resultListLocator
     * @type Function|String|null
     */
    resultListLocator: {
        setter: '_setLocator'
    },

    /**
     * Current results, or an empty array if there are no results.
     *
     * @attribute results
     * @type Array
     * @default []
     * @readonly
     */
    results: {
        readOnly: true,
        value: []
    },

    /**
     * <p>
     * Locator that should be used to extract a plain text string from a
     * non-string result item. The resulting text value will typically be the
     * value that ends up being inserted into an input field or textarea when
     * the user of an autocomplete implementation selects a result.
     * </p>
     *
     * <p>
     * By default, no locator is applied, and all results are assumed to be
     * plain text strings. If all results are already plain text strings, you
     * don't need to define a locator.
     * </p>
     *
     * <p>
     * The locator may be either a function (which will receive the raw result
     * as an argument and must return a string) or a string representing an
     * object path, such as "foo.bar.baz" (which would return the value of
     * <code>result.foo.bar.baz</code> if the result is an object).
     * </p>
     *
     * <p>
     * While <code>resultTextLocator</code> may be set to either a function or a
     * string, it will always be returned as a function that accepts a result
     * argument and returns a string.
     * </p>
     *
     * @attribute resultTextLocator
     * @type Function|String|null
     */
    resultTextLocator: {
        setter: '_setLocator'
    },

    /**
     * <p>
     * Source for autocomplete results. The following source types are
     * supported:
     * </p>
     *
     * <dl>
     *   <dt>Array</dt>
     *   <dd>
     *     <p>
     *     <i>Example:</i> <code>['first result', 'second result', 'etc']</code>
     *     </p>
     *
     *     <p>
     *     The full array will be provided to any configured filters for each
     *     query. This is an easy way to create a fully client-side autocomplete
     *     implementation.
     *     </p>
     *   </dd>
     *
     *   <dt>DataSource</dt>
     *   <dd>
     *     <p>
     *     A <code>DataSource</code> instance or other object that provides a
     *     DataSource-like <code>sendRequest</code> method. See the
     *     <code>DataSource</code> documentation for details.
     *     </p>
     *   </dd>
     *
     *   <dt>Function</dt>
     *   <dd>
     *     <p>
     *     <i>Example:</i> <code>function (query) { return ['foo', 'bar']; }</code>
     *     </p>
     *
     *     <p>
     *     A function source will be called with the current query as a
     *     parameter, and should return an array of results.
     *     </p>
     *   </dd>
     *
     *   <dt>Object</dt>
     *   <dd>
     *     <p>
     *     <i>Example:</i> <code>{foo: ['foo result 1', 'foo result 2'], bar: ['bar result']}</code>
     *     </p>
     *
     *     <p>
     *     An object will be treated as a query hashmap. If a property on the
     *     object matches the current query, the value of that property will be
     *     used as the response.
     *     </p>
     *
     *     <p>
     *     The response is assumed to be an array of results by default. If the
     *     response is not an array, provide a <code>resultListLocator</code> to
     *     process the response and return an array.
     *     </p>
     *   </dd>
     * </dl>
     *
     * <p>
     * If the optional <code>autocomplete-sources</code> module is loaded, then
     * the following additional source types will be supported as well:
     * </p>
     *
     * <dl>
     *   <dt>String (JSONP URL)</dt>
     *   <dd>
     *     <p>
     *     <i>Example:</i> <code>'http://example.com/search?q={query}&callback={callback}'</code>
     *     </p>
     *
     *     <p>
     *     If a URL with a <code>{callback}</code> placeholder is provided, it
     *     will be used to make a JSONP request. The <code>{query}</code>
     *     placeholder will be replaced with the current query, and the
     *     <code>{callback}</code> placeholder will be replaced with an
     *     internally-generated JSONP callback name. Both placeholders must
     *     appear in the URL, or the request will fail. An optional
     *     <code>{maxResults}</code> placeholder may also be provided, and will
     *     be replaced with the value of the maxResults attribute (or 1000 if
     *     the maxResults attribute is 0 or less).
     *     </p>
     *
     *     <p>
     *     The response is assumed to be an array of results by default. If the
     *     response is not an array, provide a <code>resultListLocator</code> to
     *     process the response and return an array.
     *     </p>
     *
     *     <p>
     *     <strong>The <code>jsonp</code> module must be loaded in order for
     *     JSONP URL sources to work.</strong> If the <code>jsonp</code> module
     *     is not already loaded, it will be loaded on demand if possible.
     *     </p>
     *   </dd>
     *
     *   <dt>String (XHR URL)</dt>
     *   <dd>
     *     <p>
     *     <i>Example:</i> <code>'http://example.com/search?q={query}'</code>
     *     </p>
     *
     *     <p>
     *     If a URL without a <code>{callback}</code> placeholder is provided,
     *     it will be used to make a same-origin XHR request. The
     *     <code>{query}</code> placeholder will be replaced with the current
     *     query. An optional <code>{maxResults}</code> placeholder may also be
     *     provided, and will be replaced with the value of the maxResults
     *     attribute (or 1000 if the maxResults attribute is 0 or less).
     *     </p>
     *
     *     <p>
     *     The response is assumed to be a JSON array of results by default. If
     *     the response is a JSON object and not an array, provide a
     *     <code>resultListLocator</code> to process the response and return an
     *     array. If the response is in some form other than JSON, you will
     *     need to use a custom DataSource instance as the source.
     *     </p>
     *
     *     <p>
     *     <strong>The <code>io-base</code> and <code>json-parse</code> modules
     *     must be loaded in order for XHR URL sources to work.</strong> If
     *     these modules are not already loaded, they will be loaded on demand
     *     if possible.
     *     </p>
     *   </dd>
     *
     *   <dt>String (YQL query)</dt>
     *   <dd>
     *     <p>
     *     <i>Example:</i> <code>'select * from search.suggest where query="{query}"'</code>
     *     </p>
     *
     *     <p>
     *     If a YQL query is provided, it will be used to make a YQL request.
     *     The <code>{query}</code> placeholder will be replaced with the
     *     current autocomplete query. This placeholder must appear in the YQL
     *     query, or the request will fail. An optional
     *     <code>{maxResults}</code> placeholder may also be provided, and will
     *     be replaced with the value of the maxResults attribute (or 1000 if
     *     the maxResults attribute is 0 or less).
     *     </p>
     *
     *     <p>
     *     <strong>The <code>yql</code> module must be loaded in order for YQL
     *     sources to work.</strong> If the <code>yql</code> module is not
     *     already loaded, it will be loaded on demand if possible.
     *     </p>
     *   </dd>
     * </dl>
     *
     * <p>
     * As an alternative to providing a source, you could simply listen for
     * <code>query</code> events and handle them any way you see fit. Providing
     * a source is optional, but will usually be simpler.
     * </p>
     *
     * @attribute source
     * @type Array|DataSource|Function|Object|String|null
     */
    source: {
        setter: '_setSource'
    },

    /**
     * If the <code>inputNode</code> specified at instantiation time has a
     * <code>node-tokeninput</code> plugin attached to it, this attribute will
     * be a reference to the <code>Y.Plugin.TokenInput</code> instance.
     *
     * @attribute tokenInput
     * @type Plugin.TokenInput
     * @readonly
     */
    tokenInput: {
        readOnly: true
    },

    /**
     * Current value of the input node.
     *
     * @attribute value
     * @type String
     * @default ''
     */
    value: {
        // Why duplicate this._inputNode.get('value')? Because we need a
        // reliable way to track the source of value changes. We want to perform
        // completion when the user changes the value, but not when we change
        // the value.
        value: ''
    }
};

AutoCompleteBase.CSS_PREFIX = 'ac';
AutoCompleteBase.UI_SRC = (Y.Widget && Y.Widget.UI_SRC) || 'ui';

AutoCompleteBase.prototype = {
    // -- Public Prototype Methods ---------------------------------------------

    /**
     * <p>
     * Sends a request to the configured source. If no source is configured,
     * this method won't do anything.
     * </p>
     *
     * <p>
     * Usually there's no reason to call this method manually; it will be
     * called automatically when user input causes a <code>query</code> event to
     * be fired. The only time you'll need to call this method manually is if
     * you want to force a request to be sent when no user input has occurred.
     * </p>
     *
     * @method sendRequest
     * @param {String} query (optional) Query to send. If specified, the
     *   <code>query</code> attribute will be set to this query. If not
     *   specified, the current value of the <code>query</code> attribute will
     *   be used.
     * @param {Function} requestTemplate (optional) Request template function.
     *   If not specified, the current value of the <code>requestTemplate</code>
     *   attribute will be used.
     * @chainable
     */
    sendRequest: function (query, requestTemplate) {
        var request,
            source = this.get('source');

        if (query || query === '') {
            this._set(QUERY, query);
        } else {
            query = this.get(QUERY);
        }

        if (source) {
            if (!requestTemplate) {
                requestTemplate = this.get(REQUEST_TEMPLATE);
            }

            request = requestTemplate ? requestTemplate(query) : query;


            source.sendRequest({
                request: request,
                callback: {
                    success: Y.bind(this._onResponse, this, query)
                }
            });
        }

        return this;
    },

    // -- Protected Lifecycle Methods ------------------------------------------

    /**
     * Attaches event listeners and behaviors.
     *
     * @method _bindUIACBase
     * @protected
     */
    _bindUIACBase: function () {
        var inputNode  = this.get(INPUT_NODE),
            tokenInput = inputNode && inputNode.tokenInput;

        // If the inputNode has a node-tokeninput plugin attached, bind to the
        // plugin's inputNode instead.
        if (tokenInput) {
            inputNode = tokenInput.get(INPUT_NODE);
            this._set('tokenInput', tokenInput);
        }

        if (!inputNode) {
            Y.error('No inputNode specified.');
            return;
        }

        this._inputNode = inputNode;

        this._acBaseEvents = [
            // This is the valueChange event on the inputNode, provided by the
            // event-valuechange module, not our own valueChange.
            inputNode.on(VALUE_CHANGE, this._onInputValueChange, this),

            inputNode.on('blur', this._onInputBlur, this),

            this.after(ALLOW_BROWSER_AC + 'Change', this._syncBrowserAutocomplete),
            this.after(VALUE_CHANGE, this._afterValueChange)
        ];
    },

    /**
     * Detaches AutoCompleteBase event listeners.
     *
     * @method _destructorACBase
     * @protected
     */
    _destructorACBase: function () {
        var events = this._acBaseEvents;

        while (events && events.length) {
            events.pop().detach();
        }
    },

    /**
     * Synchronizes the UI state of the <code>inputNode</code>.
     *
     * @method _syncUIACBase
     * @protected
     */
    _syncUIACBase: function () {
        this._syncBrowserAutocomplete();
        this.set(VALUE, this.get(INPUT_NODE).get(VALUE));
    },

    // -- Protected Prototype Methods ------------------------------------------

    /**
     * Creates a DataSource-like object that simply returns the specified array
     * as a response. See the <code>source</code> attribute for more details.
     *
     * @method _createArraySource
     * @param {Array} source
     * @return {Object} DataSource-like object.
     * @protected
     */
    _createArraySource: function (source) {
        var that = this;

        return {sendRequest: function (request) {
            that[_SOURCE_SUCCESS](source.concat(), request);
        }};
    },

    /**
     * Creates a DataSource-like object that passes the query to a
     * custom-defined function, which is expected to return an array as a
     * response. See the <code>source</code> attribute for more details.
     *
     * @method _createFunctionSource
     * @param {Function} source Function that accepts a query parameter and
     *   returns an array of results.
     * @return {Object} DataSource-like object.
     * @protected
     */
    _createFunctionSource: function (source) {
        var that = this;

        return {sendRequest: function (request) {
            that[_SOURCE_SUCCESS](source(request.request) || [], request);
        }};
    },

    /**
     * Creates a DataSource-like object that looks up queries as properties on
     * the specified object, and returns the found value (if any) as a response.
     * See the <code>source</code> attribute for more details.
     *
     * @method _createObjectSource
     * @param {Object} source
     * @return {Object} DataSource-like object.
     * @protected
     */
    _createObjectSource: function (source) {
        var that = this;

        return {sendRequest: function (request) {
            var query = request.request;

            that[_SOURCE_SUCCESS](
                YObject.owns(source, query) ? source[query] : [],
                request
            );
        }};
    },

    /**
     * Returns <code>true</code> if <i>value</i> is either a function or
     * <code>null</code>.
     *
     * @method _functionValidator
     * @param {Function|null} value Value to validate.
     * @protected
     */
    _functionValidator: function (value) {
        return value === null || isFunction(value);
    },

    /**
     * Faster and safer alternative to Y.Object.getValue(). Doesn't bother
     * casting the path to an array (since we already know it's an array) and
     * doesn't throw an error if a value in the middle of the object hierarchy
     * is neither <code>undefined</code> nor an object.
     *
     * @method _getObjectValue
     * @param {Object} obj
     * @param {Array} path
     * @return {mixed} Located value, or <code>undefined</code> if the value was
     *   not found at the specified path.
     * @protected
     */
    _getObjectValue: function (obj, path) {
        if (!obj) {
            return;
        }

        for (var i = 0, len = path.length; obj && i < len; i++) {
            obj = obj[path[i]];
        }

        return obj;
    },

    /**
     * Parses result responses, performs filtering and highlighting, and fires
     * the <code>results</code> event.
     *
     * @method _parseResponse
     * @param {String} query Query that generated these results.
     * @param {Object} response Response containing results.
     * @param {Object} data Raw response data.
     * @protected
     */
    _parseResponse: function (query, response, data) {
        var facade = {
                data   : data,
                query  : query,
                results: []
            },

            listLocator = this.get(RESULT_LIST_LOCATOR),
            results     = [],
            unfiltered  = response && response.results,

            filters,
            formatted,
            formatter,
            highlighted,
            highlighter,
            i,
            len,
            maxResults,
            result,
            text,
            textLocator;

        if (unfiltered && listLocator) {
            unfiltered = listLocator(unfiltered);
        }

        if (unfiltered && unfiltered.length) {
            filters     = this.get('resultFilters');
            textLocator = this.get('resultTextLocator');

            // Create a lightweight result object for each result to make them
            // easier to work with. The various properties on the object
            // represent different formats of the result, and will be populated
            // as we go.
            for (i = 0, len = unfiltered.length; i < len; ++i) {
                result = unfiltered[i];
                text   = textLocator ? textLocator(result) : result.toString();

                results.push({
                    display: Escape.html(text),
                    raw    : result,
                    text   : text
                });
            }

            // Run the results through all configured result filters. Each
            // filter returns an array of (potentially fewer) result objects,
            // which is then passed to the next filter, and so on.
            for (i = 0, len = filters.length; i < len; ++i) {
                results = filters[i](query, results.concat());

                if (!results) {
                    return;
                }

                if (!results.length) {
                    break;
                }
            }

            if (results.length) {
                formatter   = this.get('resultFormatter');
                highlighter = this.get('resultHighlighter');
                maxResults  = this.get('maxResults');

                // If maxResults is set and greater than 0, limit the number of
                // results.
                if (maxResults && maxResults > 0 &&
                        results.length > maxResults) {
                    results.length = maxResults;
                }

                // Run the results through the configured highlighter (if any).
                // The highlighter returns an array of highlighted strings (not
                // an array of result objects), and these strings are then added
                // to each result object.
                if (highlighter) {
                    highlighted = highlighter(query, results.concat());

                    if (!highlighted) {
                        return;
                    }

                    for (i = 0, len = highlighted.length; i < len; ++i) {
                        result = results[i];
                        result.highlighted = highlighted[i];
                        result.display     = result.highlighted;
                    }
                }

                // Run the results through the configured formatter (if any) to
                // produce the final formatted results. The formatter returns an
                // array of strings or Node instances (not an array of result
                // objects), and these strings/Nodes are then added to each
                // result object.
                if (formatter) {
                    formatted = formatter(query, results.concat());

                    if (!formatted) {
                        return;
                    }

                    for (i = 0, len = formatted.length; i < len; ++i) {
                        results[i].display = formatted[i];
                    }
                }
            }
        }

        facade.results = results;
        this.fire(EVT_RESULTS, facade);
    },

    /**
     * <p>
     * Returns the query portion of the specified input value, or
     * <code>null</code> if there is no suitable query within the input value.
     * </p>
     *
     * <p>
     * If a query delimiter is defined, the query will be the last delimited
     * part of of the string.
     * </p>
     *
     * @method _parseValue
     * @param {String} value Input value from which to extract the query.
     * @return {String|null} query
     * @protected
     */
    _parseValue: function (value) {
        var delim = this.get(QUERY_DELIMITER);

        if (delim) {
            value = value.split(delim);
            value = value[value.length - 1];
        }

        return Lang.trimLeft(value);
    },

    /**
     * Setter for locator attributes.
     *
     * @method _setLocator
     * @param {Function|String|null} locator
     * @return {Function|null}
     * @protected
     */
    _setLocator: function (locator) {
        if (this[_FUNCTION_VALIDATOR](locator)) {
            return locator;
        }

        var that = this;

        locator = locator.toString().split('.');

        return function (result) {
            return result && that._getObjectValue(result, locator);
        };
    },

    /**
     * Setter for the <code>requestTemplate</code> attribute.
     *
     * @method _setRequestTemplate
     * @param {Function|String|null} template
     * @return {Function|null}
     * @protected
     */
    _setRequestTemplate: function (template) {
        if (this[_FUNCTION_VALIDATOR](template)) {
            return template;
        }

        template = template.toString();

        return function (query) {
            return Lang.sub(template, {query: encodeURIComponent(query)});
        };
    },

    /**
     * Setter for the <code>resultFilters</code> attribute.
     *
     * @method _setResultFilters
     * @param {Array|Function|String|null} filters <code>null</code>, a filter
     *   function, an array of filter functions, or a string or array of strings
     *   representing the names of methods on
     *   <code>Y.AutoCompleteFilters</code>.
     * @return {Array} Array of filter functions (empty if <i>filters</i> is
     *   <code>null</code>).
     * @protected
     */
    _setResultFilters: function (filters) {
        var acFilters, getFilterFunction;

        if (filters === null) {
            return [];
        }

        acFilters = Y.AutoCompleteFilters;

        getFilterFunction = function (filter) {
            if (isFunction(filter)) {
                return filter;
            }

            if (isString(filter) && acFilters &&
                    isFunction(acFilters[filter])) {
                return acFilters[filter];
            }

            return false;
        };

        if (Lang.isArray(filters)) {
            filters = YArray.map(filters, getFilterFunction);
            return YArray.every(filters, function (f) { return !!f; }) ?
                    filters : INVALID_VALUE;
        } else {
            filters = getFilterFunction(filters);
            return filters ? [filters] : INVALID_VALUE;
        }
    },

    /**
     * Setter for the <code>resultHighlighter</code> attribute.
     *
     * @method _setResultHighlighter
     * @param {Function|String|null} highlighter <code>null</code>, a
     *   highlighter function, or a string representing the name of a method on
     *   <code>Y.AutoCompleteHighlighters</code>.
     * @return {Function|null}
     * @protected
     */
    _setResultHighlighter: function (highlighter) {
        var acHighlighters;

        if (this._functionValidator(highlighter)) {
            return highlighter;
        }

        acHighlighters = Y.AutoCompleteHighlighters;

        if (isString(highlighter) && acHighlighters &&
                isFunction(acHighlighters[highlighter])) {
            return acHighlighters[highlighter];
        }

        return INVALID_VALUE;
    },

    /**
     * Setter for the <code>source</code> attribute. Returns a DataSource or
     * a DataSource-like object depending on the type of <i>source</i>.
     *
     * @method _setSource
     * @param {Array|DataSource|Object|String} source AutoComplete source. See
     *   the <code>source</code> attribute for details.
     * @return {DataSource|Object}
     * @protected
     */
    _setSource: function (source) {
        var sourcesNotLoaded = 'autocomplete-sources module not loaded';

        if ((source && isFunction(source.sendRequest)) || source === null) {
            // Quacks like a DataSource instance (or null). Make it so!
            return source;
        }

        switch (Lang.type(source)) {
        case 'string':
            if (this._createStringSource) {
                return this._createStringSource(source);
            }

            Y.error(sourcesNotLoaded);
            return INVALID_VALUE;

        case 'array':
            // Wrap the array in a teensy tiny fake DataSource that just returns
            // the array itself for each request. Filters will do the rest.
            return this._createArraySource(source);

        case 'function':
            return this._createFunctionSource(source);

        case 'object':
            // If the object is a JSONPRequest instance, use it as a JSONP
            // source.
            if (Y.JSONPRequest && source instanceof Y.JSONPRequest) {
                if (this._createJSONPSource) {
                    return this._createJSONPSource(source);
                }

                Y.error(sourcesNotLoaded);
                return INVALID_VALUE;
            }

            // Not a JSONPRequest instance. Wrap the object in a teensy tiny
            // fake DataSource that looks for the request as a property on the
            // object and returns it if it exists, or an empty array otherwise.
            return this._createObjectSource(source);
        }

        return INVALID_VALUE;
    },

    /**
     * Shared success callback for non-DataSource sources.
     *
     * @method _sourceSuccess
     * @param {mixed} data Response data.
     * @param {Object} request Request object.
     * @protected
     */
    _sourceSuccess: function (data, request) {
        request.callback.success({
            data: data,
            response: {results: data}
        });
    },

    /**
     * Synchronizes the UI state of the <code>allowBrowserAutocomplete</code>
     * attribute.
     *
     * @method _syncBrowserAutocomplete
     * @protected
     */
    _syncBrowserAutocomplete: function () {
        var inputNode = this.get(INPUT_NODE);

        if (inputNode.get('nodeName').toLowerCase() === 'input') {
            inputNode.setAttribute('autocomplete',
                    this.get(ALLOW_BROWSER_AC) ? 'on' : 'off');
        }
    },

    /**
     * <p>
     * Updates the query portion of the <code>value</code> attribute.
     * </p>
     *
     * <p>
     * If a query delimiter is defined, the last delimited portion of the input
     * value will be replaced with the specified <i>value</i>.
     * </p>
     *
     * @method _updateValue
     * @param {String} newVal New value.
     * @protected
     */
    _updateValue: function (newVal) {
        var delim = this.get(QUERY_DELIMITER),
            insertDelim,
            len,
            prevVal;

        newVal = Lang.trimLeft(newVal);

        if (delim) {
            insertDelim = trim(delim); // so we don't double up on spaces
            prevVal     = YArray.map(trim(this.get(VALUE)).split(delim), trim);
            len         = prevVal.length;

            if (len > 1) {
                prevVal[len - 1] = newVal;
                newVal = prevVal.join(insertDelim + ' ');
            }

            newVal = newVal + insertDelim + ' ';
        }

        this.set(VALUE, newVal);
    },

    // -- Protected Event Handlers ---------------------------------------------

    /**
     * Handles change events for the <code>value</code> attribute.
     *
     * @method _afterValueChange
     * @param {EventFacade} e
     * @protected
     */
    _afterValueChange: function (e) {
        var delay,
            fire,
            minQueryLength,
            newVal = e.newVal,
            query,
            that;

        // Don't query on value changes that didn't come from the user.
        if (e.src !== AutoCompleteBase.UI_SRC) {
            this._inputNode.set(VALUE, newVal);
            return;
        }


        minQueryLength = this.get('minQueryLength');
        query          = this._parseValue(newVal) || '';

        if (minQueryLength >= 0 && query.length >= minQueryLength) {
            delay = this.get('queryDelay');
            that  = this;

            fire = function () {
                that.fire(EVT_QUERY, {
                    inputValue: newVal,
                    query     : query
                });
            };

            if (delay) {
                clearTimeout(this._delay);
                this._delay = setTimeout(fire, delay);
            } else {
                fire();
            }
        } else {
            clearTimeout(this._delay);

            this.fire(EVT_CLEAR, {
                prevVal: e.prevVal ? this._parseValue(e.prevVal) : null
            });
        }
    },

    /**
     * Handles <code>blur</code> events on the input node.
     *
     * @method _onInputBlur
     * @param {EventFacade} e
     * @protected
     */
    _onInputBlur: function (e) {
        var delim = this.get(QUERY_DELIMITER),
            delimPos,
            newVal,
            value;

        // If a query delimiter is set and the input's value contains one or
        // more trailing delimiters, strip them.
        if (delim && !this.get('allowTrailingDelimiter')) {
            delim = Lang.trimRight(delim);
            value = newVal = this._inputNode.get(VALUE);

            if (delim) {
                while ((newVal = Lang.trimRight(newVal)) &&
                        (delimPos = newVal.length - delim.length) &&
                        newVal.lastIndexOf(delim) === delimPos) {

                    newVal = newVal.substring(0, delimPos);
                }
            } else {
                // Delimiter is one or more space characters, so just trim the
                // value.
                newVal = Lang.trimRight(newVal);
            }

            if (newVal !== value) {
                this.set(VALUE, newVal);
            }
        }
    },

    /**
     * Handles <code>valueChange</code> events on the input node and fires a
     * <code>query</code> event when the input value meets the configured
     * criteria.
     *
     * @method _onInputValueChange
     * @param {EventFacade} e
     * @protected
     */
    _onInputValueChange: function (e) {
        var newVal = e.newVal;

        // Don't query if the internal value is the same as the new value
        // reported by valueChange.
        if (newVal === this.get(VALUE)) {
            return;
        }

        this.set(VALUE, newVal, {src: AutoCompleteBase.UI_SRC});
    },

    /**
     * Handles source responses and fires the <code>results</code> event.
     *
     * @method _onResponse
     * @param {EventFacade} e
     * @protected
     */
    _onResponse: function (query, e) {
        // Ignore stale responses that aren't for the current query.
        if (query === this.get(QUERY)) {
            this._parseResponse(query, e.response, e.data);
        }
    },

    // -- Protected Default Event Handlers -------------------------------------

    /**
     * Default <code>clear</code> event handler. Sets the <code>results</code>
     * property to an empty array and <code>query</code> to null.
     *
     * @method _defClearFn
     * @protected
     */
    _defClearFn: function () {
        this._set(QUERY, null);
        this._set(RESULTS, []);
    },

    /**
     * Default <code>query</code> event handler. Sets the <code>query</code>
     * property and sends a request to the source if one is configured.
     *
     * @method _defQueryFn
     * @param {EventFacade} e
     * @protected
     */
    _defQueryFn: function (e) {
        var query = e.query;

        this.sendRequest(query); // sendRequest will set the 'query' attribute
    },

    /**
     * Default <code>results</code> event handler. Sets the <code>results</code>
     * property to the latest results.
     *
     * @method _defResultsFn
     * @param {EventFacade} e
     * @protected
     */
    _defResultsFn: function (e) {
        this._set(RESULTS, e[RESULTS]);
    }
};

Y.AutoCompleteBase = AutoCompleteBase;


}, '3.3.0' ,{optional:['autocomplete-sources'], requires:['array-extras', 'base-build', 'escape', 'event-valuechange', 'node-base']});
