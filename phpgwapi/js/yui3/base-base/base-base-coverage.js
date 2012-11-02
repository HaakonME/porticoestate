/*
YUI 3.7.3 (build 5687)
Copyright 2012 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/
if (typeof _yuitest_coverage == "undefined"){
    _yuitest_coverage = {};
    _yuitest_coverline = function(src, line){
        var coverage = _yuitest_coverage[src];
        if (!coverage.lines[line]){
            coverage.calledLines++;
        }
        coverage.lines[line]++;
    };
    _yuitest_coverfunc = function(src, name, line){
        var coverage = _yuitest_coverage[src],
            funcId = name + ":" + line;
        if (!coverage.functions[funcId]){
            coverage.calledFunctions++;
        }
        coverage.functions[funcId]++;
    };
}
_yuitest_coverage["build/base-base/base-base.js"] = {
    lines: {},
    functions: {},
    coveredLines: 0,
    calledLines: 0,
    coveredFunctions: 0,
    calledFunctions: 0,
    path: "build/base-base/base-base.js",
    code: []
};
_yuitest_coverage["build/base-base/base-base.js"].code=["YUI.add('base-base', function (Y, NAME) {","","    /**","     * The base module provides the Base class, which objects requiring attribute and custom event support can extend. ","     * The module also provides two ways to reuse code - It augments Base with the Plugin.Host interface which provides ","     * plugin support and also provides the BaseCore.build method which provides a way to build custom classes using extensions.","     *","     * @module base","     */","","    /**","     * The base-base submodule provides the Base class without the Plugin support, provided by Plugin.Host, ","     * and without the extension support provided by BaseCore.build.","     *","     * @module base","     * @submodule base-base","     */","","    /**","     * The base module provides the Base class, which objects requiring attribute and custom event support can extend. ","     * The module also provides two ways to reuse code - It augments Base with the Plugin.Host interface which provides ","     * plugin support and also provides the Base.build method which provides a way to build custom classes using extensions.","     *","     * @module base","     */","","    /**","     * The base-base submodule provides the Base class without the Plugin support, provided by Plugin.Host, ","     * and without the extension support provided by Base.build.","     *","     * @module base","     * @submodule base-base","     */","    var L = Y.Lang,","","        DESTROY = \"destroy\",","        INIT = \"init\",","","        BUBBLETARGETS = \"bubbleTargets\",","        _BUBBLETARGETS = \"_bubbleTargets\",","","        BaseCore = Y.BaseCore,","        AttributeCore = Y.AttributeCore,","        Attribute = Y.Attribute;","","    /**","     * <p>","     * A base class which objects requiring attributes and custom event support can ","     * extend. Base also handles the chaining of initializer and destructor methods across ","     * the hierarchy as part of object construction and destruction. Additionally, attributes configured ","     * through the static <a href=\"#property_ATTRS\">ATTRS</a> property for each class ","     * in the hierarchy will be initialized by Base.","     * </p>","     *","     * <p>","     * The static <a href=\"#property_NAME\">NAME</a> property of each class extending ","     * from Base will be used as the identifier for the class, and is used by Base to prefix ","     * all events fired by instances of that class.","     * </p>","     *","     * @class Base","     * @constructor","     * @uses BaseCore","     * @uses Attribute","     * @uses AttributeCore","     * @uses AttributeEvents","     * @uses AttributeExtras","     * @uses EventTarget","     *","     * @param {Object} config Object with configuration property name/value pairs. The object can be ","     * used to provide default values for the objects published attributes.","     *","     * <p>","     * The config object can also contain the following non-attribute properties, providing a convenient ","     * way to configure events listeners and plugins for the instance, as part of the constructor call:","     * </p>","     *","     * <dl>","     *     <dt>on</dt>","     *     <dd>An event name to listener function map, to register event listeners for the \"on\" moment of the event. A constructor convenience property for the <a href=\"Base.html#method_on\">on</a> method.</dd>","     *     <dt>after</dt>","     *     <dd>An event name to listener function map, to register event listeners for the \"after\" moment of the event. A constructor convenience property for the <a href=\"Base.html#method_after\">after</a> method.</dd>","     *     <dt>bubbleTargets</dt>","     *     <dd>An object, or array of objects, to register as bubble targets for bubbled events fired by this instance. A constructor convenience property for the <a href=\"EventTarget.html#method_addTarget\">addTarget</a> method.</dd>","     *     <dt>plugins</dt>","     *     <dd>A plugin, or array of plugins to be plugged into the instance (see PluginHost's plug method for signature details). A constructor convenience property for the <a href=\"Plugin.Host.html#method_plug\">plug</a> method.</dd>","     * </dl>","     */","    function Base() {","        BaseCore.apply(this, arguments);","    }","","    /**","     * The list of properties which can be configured for ","     * each attribute (e.g. setter, getter, writeOnce, readOnly etc.)","     *","     * @property _ATTR_CFG","     * @type Array","     * @static","     * @private","     */","    Base._ATTR_CFG = Attribute._ATTR_CFG.concat(\"cloneDefaultValue\");","    Base._ATTR_CFG_HASH = Y.Array.hash(Base._ATTR_CFG);","","    /**","     * The array of non-attribute configuration properties supported by this class. ","     * ","     * `Base` supports \"on\", \"after\", \"plugins\" and \"bubbleTargets\" properties, ","     * which are not set up as attributes. ","     *","     * This property is primarily required so that when ","     * <a href=\"#property__allowAdHocAttrs\">`_allowAdHocAttrs`</a> is enabled by","     * a class, non-attribute configurations don't get added as ad-hoc attributes.  ","     *","     * @property _NON_ATTRS_CFG","     * @type Array","     * @static","     * @private","     */","    Base._NON_ATTRS_CFG = BaseCore._NON_ATTRS_CFG.concat([\"on\", \"after\", \"bubbleTargets\"]);","","    /**","     * <p>","     * The string to be used to identify instances of ","     * this class, for example in prefixing events.","     * </p>","     * <p>","     * Classes extending Base, should define their own","     * static NAME property, which should be camelCase by","     * convention (e.g. MyClass.NAME = \"myClass\";).","     * </p>","     * @property NAME","     * @type String","     * @static","     */","    Base.NAME = \"base\";","","    /**","     * The default set of attributes which will be available for instances of this class, and ","     * their configuration. In addition to the configuration properties listed by ","     * Attribute's <a href=\"Attribute.html#method_addAttr\">addAttr</a> method, the attribute ","     * can also be configured with a \"cloneDefaultValue\" property, which defines how the statically","     * defined value field should be protected (\"shallow\", \"deep\" and false are supported values). ","     *","     * By default if the value is an object literal or an array it will be \"shallow\" cloned, to ","     * protect the default value.","     *","     * @property ATTRS","     * @type Object","     * @static","     */","    Base.ATTRS = AttributeCore.prototype._protectAttrs(BaseCore.ATTRS);","","    Base.prototype = {","","        /**","         * Internal construction logic for Base.","         *","         * @method _initBase","         * @param {Object} config The constructor configuration object","         * @private","         */","        _initBase: function(cfg) {","","            this._eventPrefix = this.constructor.EVENT_PREFIX || this.constructor.NAME;","","            Y.BaseCore.prototype._initBase.call(this, cfg);","        },","","        /**","         * Initializes Attribute ","         * ","         * @method _initAttribute","         * @private","         */","        _initAttribute: function(cfg) {","            Attribute.call(this);","            this._yuievt.config.prefix = this._eventPrefix;","        },","","        /**","         * Utility method to define the attribute hash used to filter/whitelist property mixes for ","         * this class. ","         * ","         * @method _attrCfgHash","         * @private","         */","        _attrCfgHash: function() {","            return Base._ATTR_CFG_HASH;","        },","","        /**","         * Init lifecycle method, invoked during construction.","         * Fires the init event prior to setting up attributes and ","         * invoking initializers for the class hierarchy.","         *","         * @method init","         * @chainable","         * @param {Object} config Object with configuration property name/value pairs","         * @return {Base} A reference to this object","         */","        init: function(config) {","            /**","             * <p>","             * Lifecycle event for the init phase, fired prior to initialization. ","             * Invoking the preventDefault() method on the event object provided ","             * to subscribers will prevent initialization from occuring.","             * </p>","             * <p>","             * Subscribers to the \"after\" momemt of this event, will be notified","             * after initialization of the object is complete (and therefore","             * cannot prevent initialization).","             * </p>","             *","             * @event init","             * @preventable _defInitFn","             * @param {EventFacade} e Event object, with a cfg property which ","             * refers to the configuration object passed to the constructor.","             */","            this.publish(INIT, {","                queuable:false,","                fireOnce:true,","                defaultTargetOnly:true,","                defaultFn:this._defInitFn","            });","","            this._preInitEventCfg(config);","","            this.fire(INIT, {cfg: config});","","            return this;","        },","","        /**","         * Handles the special on, after and target properties which allow the user to","         * easily configure on and after listeners as well as bubble targets during ","         * construction, prior to init.","         *","         * @private","         * @method _preInitEventCfg","         * @param {Object} config The user configuration object","         */","        _preInitEventCfg : function(config) {","            if (config) {","                if (config.on) {","                    this.on(config.on);","                }","                if (config.after) {","                    this.after(config.after);","                }","            }","","            var i, l, target,","                userTargets = (config && BUBBLETARGETS in config);","","            if (userTargets || _BUBBLETARGETS in this) {","                target = userTargets ? (config && config.bubbleTargets) : this._bubbleTargets;","                if (L.isArray(target)) {","                    for (i = 0, l = target.length; i < l; i++) { ","                        this.addTarget(target[i]);","                    }","                } else if (target) {","                    this.addTarget(target);","                }","            }","        },","","        /**","         * <p>","         * Destroy lifecycle method. Fires the destroy","         * event, prior to invoking destructors for the","         * class hierarchy.","         * </p>","         * <p>","         * Subscribers to the destroy","         * event can invoke preventDefault on the event object, to prevent destruction","         * from proceeding.","         * </p>","         * @method destroy","         * @return {Base} A reference to this object","         * @chainable","         */","        destroy: function() {","","            /**","             * <p>","             * Lifecycle event for the destroy phase, ","             * fired prior to destruction. Invoking the preventDefault ","             * method on the event object provided to subscribers will ","             * prevent destruction from proceeding.","             * </p>","             * <p>","             * Subscribers to the \"after\" moment of this event, will be notified","             * after destruction is complete (and as a result cannot prevent","             * destruction).","             * </p>","             * @event destroy","             * @preventable _defDestroyFn","             * @param {EventFacade} e Event object","             */","            this.publish(DESTROY, {","                queuable:false,","                fireOnce:true,","                defaultTargetOnly:true,","                defaultFn: this._defDestroyFn","            });","            this.fire(DESTROY);","","            this.detachAll();","            return this;","        },","","        /**","         * Default init event handler","         *","         * @method _defInitFn","         * @param {EventFacade} e Event object, with a cfg property which ","         * refers to the configuration object passed to the constructor.","         * @protected","         */","        _defInitFn : function(e) {","            this._baseInit(e.cfg);","        },","","        /**","         * Default destroy event handler","         *","         * @method _defDestroyFn","         * @param {EventFacade} e Event object","         * @protected","         */","        _defDestroyFn : function(e) {","            this._baseDestroy(e.cfg);","        }","    };","","    Y.mix(Base, Attribute, false, null, 1);","    Y.mix(Base, BaseCore, false, null, 1);","","    // Fix constructor","    Base.prototype.constructor = Base;","","    Y.Base = Base;","","","}, '3.7.3', {\"requires\": [\"base-core\", \"attribute-base\"]});"];
_yuitest_coverage["build/base-base/base-base.js"].lines = {"1":0,"34":0,"89":0,"90":0,"102":0,"103":0,"120":0,"136":0,"152":0,"154":0,"165":0,"167":0,"177":0,"178":0,"189":0,"220":0,"227":0,"229":0,"231":0,"244":0,"245":0,"246":0,"248":0,"249":0,"253":0,"256":0,"257":0,"258":0,"259":0,"260":0,"262":0,"263":0,"301":0,"307":0,"309":0,"310":0,"322":0,"333":0,"337":0,"338":0,"341":0,"343":0};
_yuitest_coverage["build/base-base/base-base.js"].functions = {"Base:89":0,"_initBase:163":0,"_initAttribute:176":0,"_attrCfgHash:188":0,"init:202":0,"_preInitEventCfg:243":0,"destroy:283":0,"_defInitFn:321":0,"_defDestroyFn:332":0,"(anonymous 1):1":0};
_yuitest_coverage["build/base-base/base-base.js"].coveredLines = 42;
_yuitest_coverage["build/base-base/base-base.js"].coveredFunctions = 10;
_yuitest_coverline("build/base-base/base-base.js", 1);
YUI.add('base-base', function (Y, NAME) {

    /**
     * The base module provides the Base class, which objects requiring attribute and custom event support can extend. 
     * The module also provides two ways to reuse code - It augments Base with the Plugin.Host interface which provides 
     * plugin support and also provides the BaseCore.build method which provides a way to build custom classes using extensions.
     *
     * @module base
     */

    /**
     * The base-base submodule provides the Base class without the Plugin support, provided by Plugin.Host, 
     * and without the extension support provided by BaseCore.build.
     *
     * @module base
     * @submodule base-base
     */

    /**
     * The base module provides the Base class, which objects requiring attribute and custom event support can extend. 
     * The module also provides two ways to reuse code - It augments Base with the Plugin.Host interface which provides 
     * plugin support and also provides the Base.build method which provides a way to build custom classes using extensions.
     *
     * @module base
     */

    /**
     * The base-base submodule provides the Base class without the Plugin support, provided by Plugin.Host, 
     * and without the extension support provided by Base.build.
     *
     * @module base
     * @submodule base-base
     */
    _yuitest_coverfunc("build/base-base/base-base.js", "(anonymous 1)", 1);
_yuitest_coverline("build/base-base/base-base.js", 34);
var L = Y.Lang,

        DESTROY = "destroy",
        INIT = "init",

        BUBBLETARGETS = "bubbleTargets",
        _BUBBLETARGETS = "_bubbleTargets",

        BaseCore = Y.BaseCore,
        AttributeCore = Y.AttributeCore,
        Attribute = Y.Attribute;

    /**
     * <p>
     * A base class which objects requiring attributes and custom event support can 
     * extend. Base also handles the chaining of initializer and destructor methods across 
     * the hierarchy as part of object construction and destruction. Additionally, attributes configured 
     * through the static <a href="#property_ATTRS">ATTRS</a> property for each class 
     * in the hierarchy will be initialized by Base.
     * </p>
     *
     * <p>
     * The static <a href="#property_NAME">NAME</a> property of each class extending 
     * from Base will be used as the identifier for the class, and is used by Base to prefix 
     * all events fired by instances of that class.
     * </p>
     *
     * @class Base
     * @constructor
     * @uses BaseCore
     * @uses Attribute
     * @uses AttributeCore
     * @uses AttributeEvents
     * @uses AttributeExtras
     * @uses EventTarget
     *
     * @param {Object} config Object with configuration property name/value pairs. The object can be 
     * used to provide default values for the objects published attributes.
     *
     * <p>
     * The config object can also contain the following non-attribute properties, providing a convenient 
     * way to configure events listeners and plugins for the instance, as part of the constructor call:
     * </p>
     *
     * <dl>
     *     <dt>on</dt>
     *     <dd>An event name to listener function map, to register event listeners for the "on" moment of the event. A constructor convenience property for the <a href="Base.html#method_on">on</a> method.</dd>
     *     <dt>after</dt>
     *     <dd>An event name to listener function map, to register event listeners for the "after" moment of the event. A constructor convenience property for the <a href="Base.html#method_after">after</a> method.</dd>
     *     <dt>bubbleTargets</dt>
     *     <dd>An object, or array of objects, to register as bubble targets for bubbled events fired by this instance. A constructor convenience property for the <a href="EventTarget.html#method_addTarget">addTarget</a> method.</dd>
     *     <dt>plugins</dt>
     *     <dd>A plugin, or array of plugins to be plugged into the instance (see PluginHost's plug method for signature details). A constructor convenience property for the <a href="Plugin.Host.html#method_plug">plug</a> method.</dd>
     * </dl>
     */
    _yuitest_coverline("build/base-base/base-base.js", 89);
function Base() {
        _yuitest_coverfunc("build/base-base/base-base.js", "Base", 89);
_yuitest_coverline("build/base-base/base-base.js", 90);
BaseCore.apply(this, arguments);
    }

    /**
     * The list of properties which can be configured for 
     * each attribute (e.g. setter, getter, writeOnce, readOnly etc.)
     *
     * @property _ATTR_CFG
     * @type Array
     * @static
     * @private
     */
    _yuitest_coverline("build/base-base/base-base.js", 102);
Base._ATTR_CFG = Attribute._ATTR_CFG.concat("cloneDefaultValue");
    _yuitest_coverline("build/base-base/base-base.js", 103);
Base._ATTR_CFG_HASH = Y.Array.hash(Base._ATTR_CFG);

    /**
     * The array of non-attribute configuration properties supported by this class. 
     * 
     * `Base` supports "on", "after", "plugins" and "bubbleTargets" properties, 
     * which are not set up as attributes. 
     *
     * This property is primarily required so that when 
     * <a href="#property__allowAdHocAttrs">`_allowAdHocAttrs`</a> is enabled by
     * a class, non-attribute configurations don't get added as ad-hoc attributes.  
     *
     * @property _NON_ATTRS_CFG
     * @type Array
     * @static
     * @private
     */
    _yuitest_coverline("build/base-base/base-base.js", 120);
Base._NON_ATTRS_CFG = BaseCore._NON_ATTRS_CFG.concat(["on", "after", "bubbleTargets"]);

    /**
     * <p>
     * The string to be used to identify instances of 
     * this class, for example in prefixing events.
     * </p>
     * <p>
     * Classes extending Base, should define their own
     * static NAME property, which should be camelCase by
     * convention (e.g. MyClass.NAME = "myClass";).
     * </p>
     * @property NAME
     * @type String
     * @static
     */
    _yuitest_coverline("build/base-base/base-base.js", 136);
Base.NAME = "base";

    /**
     * The default set of attributes which will be available for instances of this class, and 
     * their configuration. In addition to the configuration properties listed by 
     * Attribute's <a href="Attribute.html#method_addAttr">addAttr</a> method, the attribute 
     * can also be configured with a "cloneDefaultValue" property, which defines how the statically
     * defined value field should be protected ("shallow", "deep" and false are supported values). 
     *
     * By default if the value is an object literal or an array it will be "shallow" cloned, to 
     * protect the default value.
     *
     * @property ATTRS
     * @type Object
     * @static
     */
    _yuitest_coverline("build/base-base/base-base.js", 152);
Base.ATTRS = AttributeCore.prototype._protectAttrs(BaseCore.ATTRS);

    _yuitest_coverline("build/base-base/base-base.js", 154);
Base.prototype = {

        /**
         * Internal construction logic for Base.
         *
         * @method _initBase
         * @param {Object} config The constructor configuration object
         * @private
         */
        _initBase: function(cfg) {

            _yuitest_coverfunc("build/base-base/base-base.js", "_initBase", 163);
_yuitest_coverline("build/base-base/base-base.js", 165);
this._eventPrefix = this.constructor.EVENT_PREFIX || this.constructor.NAME;

            _yuitest_coverline("build/base-base/base-base.js", 167);
Y.BaseCore.prototype._initBase.call(this, cfg);
        },

        /**
         * Initializes Attribute 
         * 
         * @method _initAttribute
         * @private
         */
        _initAttribute: function(cfg) {
            _yuitest_coverfunc("build/base-base/base-base.js", "_initAttribute", 176);
_yuitest_coverline("build/base-base/base-base.js", 177);
Attribute.call(this);
            _yuitest_coverline("build/base-base/base-base.js", 178);
this._yuievt.config.prefix = this._eventPrefix;
        },

        /**
         * Utility method to define the attribute hash used to filter/whitelist property mixes for 
         * this class. 
         * 
         * @method _attrCfgHash
         * @private
         */
        _attrCfgHash: function() {
            _yuitest_coverfunc("build/base-base/base-base.js", "_attrCfgHash", 188);
_yuitest_coverline("build/base-base/base-base.js", 189);
return Base._ATTR_CFG_HASH;
        },

        /**
         * Init lifecycle method, invoked during construction.
         * Fires the init event prior to setting up attributes and 
         * invoking initializers for the class hierarchy.
         *
         * @method init
         * @chainable
         * @param {Object} config Object with configuration property name/value pairs
         * @return {Base} A reference to this object
         */
        init: function(config) {
            /**
             * <p>
             * Lifecycle event for the init phase, fired prior to initialization. 
             * Invoking the preventDefault() method on the event object provided 
             * to subscribers will prevent initialization from occuring.
             * </p>
             * <p>
             * Subscribers to the "after" momemt of this event, will be notified
             * after initialization of the object is complete (and therefore
             * cannot prevent initialization).
             * </p>
             *
             * @event init
             * @preventable _defInitFn
             * @param {EventFacade} e Event object, with a cfg property which 
             * refers to the configuration object passed to the constructor.
             */
            _yuitest_coverfunc("build/base-base/base-base.js", "init", 202);
_yuitest_coverline("build/base-base/base-base.js", 220);
this.publish(INIT, {
                queuable:false,
                fireOnce:true,
                defaultTargetOnly:true,
                defaultFn:this._defInitFn
            });

            _yuitest_coverline("build/base-base/base-base.js", 227);
this._preInitEventCfg(config);

            _yuitest_coverline("build/base-base/base-base.js", 229);
this.fire(INIT, {cfg: config});

            _yuitest_coverline("build/base-base/base-base.js", 231);
return this;
        },

        /**
         * Handles the special on, after and target properties which allow the user to
         * easily configure on and after listeners as well as bubble targets during 
         * construction, prior to init.
         *
         * @private
         * @method _preInitEventCfg
         * @param {Object} config The user configuration object
         */
        _preInitEventCfg : function(config) {
            _yuitest_coverfunc("build/base-base/base-base.js", "_preInitEventCfg", 243);
_yuitest_coverline("build/base-base/base-base.js", 244);
if (config) {
                _yuitest_coverline("build/base-base/base-base.js", 245);
if (config.on) {
                    _yuitest_coverline("build/base-base/base-base.js", 246);
this.on(config.on);
                }
                _yuitest_coverline("build/base-base/base-base.js", 248);
if (config.after) {
                    _yuitest_coverline("build/base-base/base-base.js", 249);
this.after(config.after);
                }
            }

            _yuitest_coverline("build/base-base/base-base.js", 253);
var i, l, target,
                userTargets = (config && BUBBLETARGETS in config);

            _yuitest_coverline("build/base-base/base-base.js", 256);
if (userTargets || _BUBBLETARGETS in this) {
                _yuitest_coverline("build/base-base/base-base.js", 257);
target = userTargets ? (config && config.bubbleTargets) : this._bubbleTargets;
                _yuitest_coverline("build/base-base/base-base.js", 258);
if (L.isArray(target)) {
                    _yuitest_coverline("build/base-base/base-base.js", 259);
for (i = 0, l = target.length; i < l; i++) { 
                        _yuitest_coverline("build/base-base/base-base.js", 260);
this.addTarget(target[i]);
                    }
                } else {_yuitest_coverline("build/base-base/base-base.js", 262);
if (target) {
                    _yuitest_coverline("build/base-base/base-base.js", 263);
this.addTarget(target);
                }}
            }
        },

        /**
         * <p>
         * Destroy lifecycle method. Fires the destroy
         * event, prior to invoking destructors for the
         * class hierarchy.
         * </p>
         * <p>
         * Subscribers to the destroy
         * event can invoke preventDefault on the event object, to prevent destruction
         * from proceeding.
         * </p>
         * @method destroy
         * @return {Base} A reference to this object
         * @chainable
         */
        destroy: function() {

            /**
             * <p>
             * Lifecycle event for the destroy phase, 
             * fired prior to destruction. Invoking the preventDefault 
             * method on the event object provided to subscribers will 
             * prevent destruction from proceeding.
             * </p>
             * <p>
             * Subscribers to the "after" moment of this event, will be notified
             * after destruction is complete (and as a result cannot prevent
             * destruction).
             * </p>
             * @event destroy
             * @preventable _defDestroyFn
             * @param {EventFacade} e Event object
             */
            _yuitest_coverfunc("build/base-base/base-base.js", "destroy", 283);
_yuitest_coverline("build/base-base/base-base.js", 301);
this.publish(DESTROY, {
                queuable:false,
                fireOnce:true,
                defaultTargetOnly:true,
                defaultFn: this._defDestroyFn
            });
            _yuitest_coverline("build/base-base/base-base.js", 307);
this.fire(DESTROY);

            _yuitest_coverline("build/base-base/base-base.js", 309);
this.detachAll();
            _yuitest_coverline("build/base-base/base-base.js", 310);
return this;
        },

        /**
         * Default init event handler
         *
         * @method _defInitFn
         * @param {EventFacade} e Event object, with a cfg property which 
         * refers to the configuration object passed to the constructor.
         * @protected
         */
        _defInitFn : function(e) {
            _yuitest_coverfunc("build/base-base/base-base.js", "_defInitFn", 321);
_yuitest_coverline("build/base-base/base-base.js", 322);
this._baseInit(e.cfg);
        },

        /**
         * Default destroy event handler
         *
         * @method _defDestroyFn
         * @param {EventFacade} e Event object
         * @protected
         */
        _defDestroyFn : function(e) {
            _yuitest_coverfunc("build/base-base/base-base.js", "_defDestroyFn", 332);
_yuitest_coverline("build/base-base/base-base.js", 333);
this._baseDestroy(e.cfg);
        }
    };

    _yuitest_coverline("build/base-base/base-base.js", 337);
Y.mix(Base, Attribute, false, null, 1);
    _yuitest_coverline("build/base-base/base-base.js", 338);
Y.mix(Base, BaseCore, false, null, 1);

    // Fix constructor
    _yuitest_coverline("build/base-base/base-base.js", 341);
Base.prototype.constructor = Base;

    _yuitest_coverline("build/base-base/base-base.js", 343);
Y.Base = Base;


}, '3.7.3', {"requires": ["base-core", "attribute-base"]});
