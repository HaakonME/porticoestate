/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.3.0
build: 3167
*/
YUI.add('loader-base', function(Y) {

/**
 * The YUI loader core
 * @module loader
 * @submodule loader-base
 */

if (!YUI.Env[Y.version]) {

    (function() {
        var VERSION = Y.version,
            BUILD = '/build/',
            ROOT = VERSION + BUILD,
            CDN_BASE = Y.Env.base,
            GALLERY_VERSION = 'gallery-2010.12.16-18-24',
            TNT = '2in3',
            TNT_VERSION = '4',
            YUI2_VERSION = '2.8.2',
            COMBO_BASE = CDN_BASE + 'combo?',
            META = { version: VERSION,
                              root: ROOT,
                              base: Y.Env.base,
                              comboBase: COMBO_BASE,
                              skin: { defaultSkin: 'sam',
                                           base: 'assets/skins/',
                                           path: 'skin.css',
                                           after: ['cssreset',
                                                          'cssfonts',
                                                          'cssgrids',
                                                          'cssbase',
                                                          'cssreset-context',
                                                          'cssfonts-context']},
                              groups: {},
                              patterns: {} },
            groups = META.groups,
            yui2Update = function(tnt, yui2) {
                                  var root = TNT + '.' +
                                            (tnt || TNT_VERSION) + '/' +
                                            (yui2 || YUI2_VERSION) + BUILD;
                                  groups.yui2.base = CDN_BASE + root;
                                  groups.yui2.root = root;
                              },
            galleryUpdate = function(tag) {
                                  var root = (tag || GALLERY_VERSION) + BUILD;
                                  groups.gallery.base = CDN_BASE + root;
                                  groups.gallery.root = root;
                              };

        groups[VERSION] = {};

        groups.gallery = {
            ext: false,
            combine: true,
            comboBase: COMBO_BASE,
            update: galleryUpdate,
            patterns: { 'gallery-': { },
                        'gallerycss-': { type: 'css' } }
        };

        groups.yui2 = {
            combine: true,
            ext: false,
            comboBase: COMBO_BASE,
            update: yui2Update,
            patterns: {
                'yui2-': {
                    configFn: function(me) {
                        if (/-skin|reset|fonts|grids|base/.test(me.name)) {
                            me.type = 'css';
                            me.path = me.path.replace(/\.js/, '.css');
                            // this makes skins in builds earlier than
                            // 2.6.0 work as long as combine is false
                            me.path = me.path.replace(/\/yui2-skin/,
                                             '/assets/skins/sam/yui2-skin');
                        }
                    }
                }
            }
        };

        galleryUpdate();
        yui2Update();

        YUI.Env[VERSION] = META;
    }());
}



/**
 * Loader dynamically loads script and css files.  It includes the dependency
 * info for the version of the library in use, and will automatically pull in
 * dependencies for the modules requested.  It supports rollup files and will
 * automatically use these when appropriate in order to minimize the number of
 * http connections required to load all of the dependencies.  It can load the
 * files from the Yahoo! CDN, and it can utilize the combo service provided on
 * this network to reduce the number of http connections required to download
 * YUI files.
 *
 * @module loader
 * @submodule loader-base
 */

var NOT_FOUND = {},
    NO_REQUIREMENTS = [],
    MAX_URL_LENGTH = (Y.UA.ie) ? 2048 : 8192,
    GLOBAL_ENV = YUI.Env,
    GLOBAL_LOADED = GLOBAL_ENV._loaded,
    CSS = 'css',
    JS = 'js',
    INTL = 'intl',
    VERSION = Y.version,
    ROOT_LANG = '',
    YObject = Y.Object,
    oeach = YObject.each,
    YArray = Y.Array,
    _queue = GLOBAL_ENV._loaderQueue,
    META = GLOBAL_ENV[VERSION],
    SKIN_PREFIX = 'skin-',
    L = Y.Lang,
    ON_PAGE = GLOBAL_ENV.mods,
    modulekey,
    cache,
    _path = function(dir, file, type, nomin) {
                        var path = dir + '/' + file;
                        if (!nomin) {
                            path += '-min';
                        }
                        path += '.' + (type || CSS);

                        return path;
                    };

/**
 * The component metadata is stored in Y.Env.meta.
 * Part of the loader module.
 * @property Env.meta
 * @for YUI
 */
Y.Env.meta = META;

/**
 * Loader dynamically loads script and css files.  It includes the dependency
 * info for the version of the library in use, and will automatically pull in
 * dependencies for the modules requested.  It supports rollup files and will
 * automatically use these when appropriate in order to minimize the number of
 * http connections required to load all of the dependencies.  It can load the
 * files from the Yahoo! CDN, and it can utilize the combo service provided on
 * this network to reduce the number of http connections required to download
 * YUI files.
 *
 * While the loader can be instantiated by the end user, it normally is not.
 * @see YUI.use for the normal use case.  The use function automatically will
 * pull in missing dependencies.
 *
 * @constructor
 * @class Loader
 * @param {object} o an optional set of configuration options.  Valid options:
 * <ul>
 *  <li>base:
 *  The base dir</li>
 *  <li>comboBase:
 *  The YUI combo service base dir. Ex: http://yui.yahooapis.com/combo?</li>
 *  <li>root:
 *  The root path to prepend to module names for the combo service.
 *  Ex: 2.5.2/build/</li>
 *  <li>filter:.
 *
 * A filter to apply to result urls.  This filter will modify the default
 * path for all modules.  The default path for the YUI library is the
 * minified version of the files (e.g., event-min.js).  The filter property
 * can be a predefined filter or a custom filter.  The valid predefined
 * filters are:
 * <dl>
 *  <dt>DEBUG</dt>
 *  <dd>Selects the debug versions of the library (e.g., event-debug.js).
 *      This option will automatically include the Logger widget</dd>
 *  <dt>RAW</dt>
 *  <dd>Selects the non-minified version of the library (e.g., event.js).
 *  </dd>
 * </dl>
 * You can also define a custom filter, which must be an object literal
 * containing a search expression and a replace string:
 * <pre>
 *  myFilter: &#123;
 *      'searchExp': "-min\\.js",
 *      'replaceStr': "-debug.js"
 *  &#125;
 * </pre>
 *
 *  </li>
 *  <li>filters: per-component filter specification.  If specified
 *  for a given component, this overrides the filter config</li>
 *  <li>combine:
 *  Use the YUI combo service to reduce the number of http connections
 *  required to load your dependencies</li>
 *  <li>ignore:
 *  A list of modules that should never be dynamically loaded</li>
 *  <li>force:
 *  A list of modules that should always be loaded when required, even if
 *  already present on the page</li>
 *  <li>insertBefore:
 *  Node or id for a node that should be used as the insertion point for
 *  new nodes</li>
 *  <li>charset:
 *  charset for dynamic nodes (deprecated, use jsAttributes or cssAttributes)
 *  </li>
 *  <li>jsAttributes: object literal containing attributes to add to script
 *  nodes</li>
 *  <li>cssAttributes: object literal containing attributes to add to link
 *  nodes</li>
 *  <li>timeout:
 *  The number of milliseconds before a timeout occurs when dynamically
 *  loading nodes.  If not set, there is no timeout</li>
 *  <li>context:
 *  execution context for all callbacks</li>
 *  <li>onSuccess:
 *  callback for the 'success' event</li>
 *  <li>onFailure: callback for the 'failure' event</li>
 *  <li>onCSS: callback for the 'CSSComplete' event.  When loading YUI
 *  components with CSS the CSS is loaded first, then the script.  This
 *  provides a moment you can tie into to improve
 *  the presentation of the page while the script is loading.</li>
 *  <li>onTimeout:
 *  callback for the 'timeout' event</li>
 *  <li>onProgress:
 *  callback executed each time a script or css file is loaded</li>
 *  <li>modules:
 *  A list of module definitions.  See Loader.addModule for the supported
 *  module metadata</li>
 *  <li>groups:
 *  A list of group definitions.  Each group can contain specific definitions
 *  for base, comboBase, combine, and accepts a list of modules.  See above
 *  for the description of these properties.</li>
 *  <li>2in3: the version of the YUI 2 in 3 wrapper to use.  The intrinsic
 *  support for YUI 2 modules in YUI 3 relies on versions of the YUI 2
 *  components inside YUI 3 module wrappers.  These wrappers
 *  change over time to accomodate the issues that arise from running YUI 2
 *  in a YUI 3 sandbox.</li>
 *  <li>yui2: when using the 2in3 project, you can select the version of
 *  YUI 2 to use.  Valid values *  are 2.2.2, 2.3.1, 2.4.1, 2.5.2, 2.6.0,
 *  2.7.0, 2.8.0, and 2.8.1 [default] -- plus all versions of YUI 2
 *  going forward.</li>
 * </ul>
 */
Y.Loader = function(o) {

    var defaults = META.modules,
        self = this;

    modulekey = META.md5;

    /**
     * Internal callback to handle multiple internal insert() calls
     * so that css is inserted prior to js
     * @property _internalCallback
     * @private
     */
    // self._internalCallback = null;

    /**
     * Callback that will be executed when the loader is finished
     * with an insert
     * @method onSuccess
     * @type function
     */
    // self.onSuccess = null;

    /**
     * Callback that will be executed if there is a failure
     * @method onFailure
     * @type function
     */
    // self.onFailure = null;

    /**
     * Callback for the 'CSSComplete' event.  When loading YUI components
     * with CSS the CSS is loaded first, then the script.  This provides
     * a moment you can tie into to improve the presentation of the page
     * while the script is loading.
     * @method onCSS
     * @type function
     */
    // self.onCSS = null;

    /**
     * Callback executed each time a script or css file is loaded
     * @method onProgress
     * @type function
     */
    // self.onProgress = null;

    /**
     * Callback that will be executed if a timeout occurs
     * @method onTimeout
     * @type function
     */
    // self.onTimeout = null;

    /**
     * The execution context for all callbacks
     * @property context
     * @default {YUI} the YUI instance
     */
    self.context = Y;

    /**
     * Data that is passed to all callbacks
     * @property data
     */
    // self.data = null;

    /**
     * Node reference or id where new nodes should be inserted before
     * @property insertBefore
     * @type string|HTMLElement
     */
    // self.insertBefore = null;

    /**
     * The charset attribute for inserted nodes
     * @property charset
     * @type string
     * @deprecated , use cssAttributes or jsAttributes.
     */
    // self.charset = null;

    /**
     * An object literal containing attributes to add to link nodes
     * @property cssAttributes
     * @type object
     */
    // self.cssAttributes = null;

    /**
     * An object literal containing attributes to add to script nodes
     * @property jsAttributes
     * @type object
     */
    // self.jsAttributes = null;

    /**
     * The base directory.
     * @property base
     * @type string
     * @default http://yui.yahooapis.com/[YUI VERSION]/build/
     */
    self.base = Y.Env.meta.base;

    /**
     * Base path for the combo service
     * @property comboBase
     * @type string
     * @default http://yui.yahooapis.com/combo?
     */
    self.comboBase = Y.Env.meta.comboBase;

    /*
     * Base path for language packs.
     */
    // self.langBase = Y.Env.meta.langBase;
    // self.lang = "";

    /**
     * If configured, the loader will attempt to use the combo
     * service for YUI resources and configured external resources.
     * @property combine
     * @type boolean
     * @default true if a base dir isn't in the config
     */
    self.combine = o.base &&
        (o.base.indexOf(self.comboBase.substr(0, 20)) > -1);

    /**
     * Max url length for combo urls.  The default is 2048 for
     * internet explorer, and 8192 otherwise.  This is the URL
     * limit for the Yahoo! hosted combo servers.  If consuming
     * a different combo service that has a different URL limit
     * it is possible to override this default by supplying
     * the maxURLLength config option.  The config option will
     * only take effect if lower than the default.
     *
     * Browsers:
     *    IE: 2048
     *    Other A-Grade Browsers: Higher that what is typically supported
     *    'capable' mobile browsers:
     *
     * Servers:
     *    Apache: 8192
     *
     * @property maxURLLength
     * @type int
     */
    self.maxURLLength = MAX_URL_LENGTH;

    /**
     * Ignore modules registered on the YUI global
     * @property ignoreRegistered
     * @default false
     */
    // self.ignoreRegistered = false;

    /**
     * Root path to prepend to module path for the combo
     * service
     * @property root
     * @type string
     * @default [YUI VERSION]/build/
     */
    self.root = Y.Env.meta.root;

    /**
     * Timeout value in milliseconds.  If set, self value will be used by
     * the get utility.  the timeout event will fire if
     * a timeout occurs.
     * @property timeout
     * @type int
     */
    self.timeout = 0;

    /**
     * A list of modules that should not be loaded, even if
     * they turn up in the dependency tree
     * @property ignore
     * @type string[]
     */
    // self.ignore = null;

    /**
     * A list of modules that should always be loaded, even
     * if they have already been inserted into the page.
     * @property force
     * @type string[]
     */
    // self.force = null;

    self.forceMap = {};

    /**
     * Should we allow rollups
     * @property allowRollup
     * @type boolean
     * @default true
     */
    self.allowRollup = true;

    /**
     * A filter to apply to result urls.  This filter will modify the default
     * path for all modules.  The default path for the YUI library is the
     * minified version of the files (e.g., event-min.js).  The filter property
     * can be a predefined filter or a custom filter.  The valid predefined
     * filters are:
     * <dl>
     *  <dt>DEBUG</dt>
     *  <dd>Selects the debug versions of the library (e.g., event-debug.js).
     *      This option will automatically include the Logger widget</dd>
     *  <dt>RAW</dt>
     *  <dd>Selects the non-minified version of the library (e.g., event.js).
     *  </dd>
     * </dl>
     * You can also define a custom filter, which must be an object literal
     * containing a search expression and a replace string:
     * <pre>
     *  myFilter: &#123;
     *      'searchExp': "-min\\.js",
     *      'replaceStr': "-debug.js"
     *  &#125;
     * </pre>
     * @property filter
     * @type string| {searchExp: string, replaceStr: string}
     */
    // self.filter = null;

    /**
     * per-component filter specification.  If specified for a given
     * component, this overrides the filter config.
     * @property filters
     * @type object
     */
    self.filters = {};

    /**
     * The list of requested modules
     * @property required
     * @type {string: boolean}
     */
    self.required = {};

    /**
     * If a module name is predefined when requested, it is checked againsts
     * the patterns provided in this property.  If there is a match, the
     * module is added with the default configuration.
     *
     * At the moment only supporting module prefixes, but anticipate
     * supporting at least regular expressions.
     * @property patterns
     * @type Object
     */
    // self.patterns = Y.merge(Y.Env.meta.patterns);
    self.patterns = {};

    /**
     * The library metadata
     * @property moduleInfo
     */
    // self.moduleInfo = Y.merge(Y.Env.meta.moduleInfo);
    self.moduleInfo = {};

    self.groups = Y.merge(Y.Env.meta.groups);

    /**
     * Provides the information used to skin the skinnable components.
     * The following skin definition would result in 'skin1' and 'skin2'
     * being loaded for calendar (if calendar was requested), and
     * 'sam' for all other skinnable components:
     *
     *   <code>
     *   skin: {
     *
     *      // The default skin, which is automatically applied if not
     *      // overriden by a component-specific skin definition.
     *      // Change this in to apply a different skin globally
     *      defaultSkin: 'sam',
     *
     *      // This is combined with the loader base property to get
     *      // the default root directory for a skin. ex:
     *      // http://yui.yahooapis.com/2.3.0/build/assets/skins/sam/
     *      base: 'assets/skins/',
     *
     *      // Any component-specific overrides can be specified here,
     *      // making it possible to load different skins for different
     *      // components.  It is possible to load more than one skin
     *      // for a given component as well.
     *      overrides: {
     *          calendar: ['skin1', 'skin2']
     *      }
     *   }
     *   </code>
     *   @property skin
     */
    self.skin = Y.merge(Y.Env.meta.skin);

    /*
     * Map of conditional modules
     * @since 3.2.0
     */
    self.conditions = {};

    // map of modules with a hash of modules that meet the requirement
    // self.provides = {};

    self.config = o;
    self._internal = true;


    cache = GLOBAL_ENV._renderedMods;

    if (cache) {
        oeach(cache, function(v, k) {
            self.moduleInfo[k] = Y.merge(v);
        });

        cache = GLOBAL_ENV._conditions;

        oeach(cache, function(v, k) {
            self.conditions[k] = Y.merge(v);
        });

    } else {
        oeach(defaults, self.addModule, self);
    }

    if (!GLOBAL_ENV._renderedMods) {
        GLOBAL_ENV._renderedMods = Y.merge(self.moduleInfo);
        GLOBAL_ENV._conditions = Y.merge(self.conditions);
    }

    self._inspectPage();

    self._internal = false;

    self._config(o);

    /**
     * List of rollup files found in the library metadata
     * @property rollups
     */
    // self.rollups = null;

    /**
     * Whether or not to load optional dependencies for
     * the requested modules
     * @property loadOptional
     * @type boolean
     * @default false
     */
    // self.loadOptional = false;

    /**
     * All of the derived dependencies in sorted order, which
     * will be populated when either calculate() or insert()
     * is called
     * @property sorted
     * @type string[]
     */
    self.sorted = [];

    /**
     * Set when beginning to compute the dependency tree.
     * Composed of what YUI reports to be loaded combined
     * with what has been loaded by any instance on the page
     * with the version number specified in the metadata.
     * @property loaded
     * @type {string: boolean}
     */
    self.loaded = GLOBAL_LOADED[VERSION];

    /*
     * A list of modules to attach to the YUI instance when complete.
     * If not supplied, the sorted list of dependencies are applied.
     * @property attaching
     */
    // self.attaching = null;

    /**
     * Flag to indicate the dependency tree needs to be recomputed
     * if insert is called again.
     * @property dirty
     * @type boolean
     * @default true
     */
    self.dirty = true;

    /**
     * List of modules inserted by the utility
     * @property inserted
     * @type {string: boolean}
     */
    self.inserted = {};

    /**
     * List of skipped modules during insert() because the module
     * was not defined
     * @property skipped
     */
    self.skipped = {};

    // Y.on('yui:load', self.loadNext, self);

    self.tested = {};

    /*
     * Cached sorted calculate results
     * @property results
     * @since 3.2.0
     */
    //self.results = {};

};

Y.Loader.prototype = {

    FILTER_DEFS: {
        RAW: {
            'searchExp': '-min\\.js',
            'replaceStr': '.js'
        },
        DEBUG: {
            'searchExp': '-min\\.js',
            'replaceStr': '-debug.js'
        }
    },

   _inspectPage: function() {
       oeach(ON_PAGE, function(v, k) {
           if (v.details) {
               var m = this.moduleInfo[k],
                   req = v.details.requires,
                   mr = m && m.requires;
               if (m) {
                   if (!m._inspected && req && mr.length != req.length) {
                       // console.log('deleting ' + m.name);
                       delete m.expanded;
                   }
               } else {
                   m = this.addModule(v.details, k);
               }
               m._inspected = true;
           }
       }, this);
   },

// returns true if b is not loaded, and is required
// directly or by means of modules it supersedes.
   _requires: function(mod1, mod2) {

        var i, rm, after_map, s,
            info = this.moduleInfo,
            m = info[mod1],
            other = info[mod2];
            // key = mod1 + mod2;

        // if (this.tested[key]) {
            // return this.tested[key];
        // }

        // if (loaded[mod2] || !m || !other) {
        if (!m || !other) {
            return false;
        }

        rm = m.expanded_map;
        after_map = m.after_map;

        // check if this module should be sorted after the other
        // do this first to short circut circular deps
        if (after_map && (mod2 in after_map)) {
            return true;
        }

        after_map = other.after_map;

        // and vis-versa
        if (after_map && (mod1 in after_map)) {
            return false;
        }

        // check if this module requires one the other supersedes
        s = info[mod2] && info[mod2].supersedes;
        if (s) {
            for (i = 0; i < s.length; i++) {
                if (this._requires(mod1, s[i])) {
                    return true;
                }
            }
        }

        s = info[mod1] && info[mod1].supersedes;
        if (s) {
            for (i = 0; i < s.length; i++) {
                if (this._requires(mod2, s[i])) {
                    return false;
                }
            }
        }

        // check if this module requires the other directly
        // if (r && YArray.indexOf(r, mod2) > -1) {
        if (rm && (mod2 in rm)) {
            return true;
        }

        // external css files should be sorted below yui css
        if (m.ext && m.type == CSS && !other.ext && other.type == CSS) {
            return true;
        }

        return false;
    },

    _config: function(o) {
        var i, j, val, f, group, groupName, self = this;
        // apply config values
        if (o) {
            for (i in o) {
                if (o.hasOwnProperty(i)) {
                    val = o[i];
                    if (i == 'require') {
                        self.require(val);
                    } else if (i == 'skin') {
                        Y.mix(self.skin, o[i], true);
                    } else if (i == 'groups') {
                        for (j in val) {
                            if (val.hasOwnProperty(j)) {
                                groupName = j;
                                group = val[j];
                                self.addGroup(group, groupName);
                            }
                        }

                    } else if (i == 'modules') {
                        // add a hash of module definitions
                        oeach(val, self.addModule, self);
                    } else if (i == 'gallery') {
                        this.groups.gallery.update(val);
                    } else if (i == 'yui2' || i == '2in3') {
                        this.groups.yui2.update(o['2in3'], o.yui2);
                    } else if (i == 'maxURLLength') {
                        self[i] = Math.min(MAX_URL_LENGTH, val);
                    } else {
                        self[i] = val;
                    }
                }
            }
        }

        // fix filter
        f = self.filter;

        if (L.isString(f)) {
            f = f.toUpperCase();
            self.filterName = f;
            self.filter = self.FILTER_DEFS[f];
            if (f == 'DEBUG') {
                self.require('yui-log', 'dump');
            }
        }

    },

    /**
     * Returns the skin module name for the specified skin name.  If a
     * module name is supplied, the returned skin module name is
     * specific to the module passed in.
     * @method formatSkin
     * @param {string} skin the name of the skin.
     * @param {string} mod optional: the name of a module to skin.
     * @return {string} the full skin module name.
     */
    formatSkin: function(skin, mod) {
        var s = SKIN_PREFIX + skin;
        if (mod) {
            s = s + '-' + mod;
        }

        return s;
    },

    /**
     * Adds the skin def to the module info
     * @method _addSkin
     * @param {string} skin the name of the skin.
     * @param {string} mod the name of the module.
     * @param {string} parent parent module if this is a skin of a
     * submodule or plugin.
     * @return {string} the module name for the skin.
     * @private
     */
    _addSkin: function(skin, mod, parent) {
        var mdef, pkg, name,
            info = this.moduleInfo,
            sinf = this.skin,
            ext = info[mod] && info[mod].ext;

        // Add a module definition for the module-specific skin css
        if (mod) {
            name = this.formatSkin(skin, mod);
            if (!info[name]) {
                mdef = info[mod];
                pkg = mdef.pkg || mod;
                this.addModule({
                    name: name,
                    group: mdef.group,
                    type: 'css',
                    after: sinf.after,
                    path: (parent || pkg) + '/' + sinf.base + skin +
                          '/' + mod + '.css',
                    ext: ext
                });

            }
        }

        return name;
    },

    /** Add a new module group
     * <dl>
     *   <dt>name:</dt>      <dd>required, the group name</dd>
     *   <dt>base:</dt>      <dd>The base dir for this module group</dd>
     *   <dt>root:</dt>      <dd>The root path to add to each combo
     *   resource path</dd>
     *   <dt>combine:</dt>   <dd>combo handle</dd>
     *   <dt>comboBase:</dt> <dd>combo service base path</dd>
     *   <dt>modules:</dt>   <dd>the group of modules</dd>
     * </dl>
     * @method addGroup
     * @param {object} o An object containing the module data.
     * @param {string} name the group name.
     */
    addGroup: function(o, name) {
        var mods = o.modules,
            self = this;
        name = name || o.name;
        o.name = name;
        self.groups[name] = o;

        if (o.patterns) {
            oeach(o.patterns, function(v, k) {
                v.group = name;
                self.patterns[k] = v;
            });
        }

        if (mods) {
            oeach(mods, function(v, k) {
                v.group = name;
                self.addModule(v, k);
            }, self);
        }
    },

    /** Add a new module to the component metadata.
     * <dl>
     *     <dt>name:</dt>       <dd>required, the component name</dd>
     *     <dt>type:</dt>       <dd>required, the component type (js or css)
     *     </dd>
     *     <dt>path:</dt>       <dd>required, the path to the script from
     *     "base"</dd>
     *     <dt>requires:</dt>   <dd>array of modules required by this
     *     component</dd>
     *     <dt>optional:</dt>   <dd>array of optional modules for this
     *     component</dd>
     *     <dt>supersedes:</dt> <dd>array of the modules this component
     *     replaces</dd>
     *     <dt>after:</dt>      <dd>array of modules the components which, if
     *     present, should be sorted above this one</dd>
     *     <dt>after_map:</dt>  <dd>faster alternative to 'after' -- supply
     *     a hash instead of an array</dd>
     *     <dt>rollup:</dt>     <dd>the number of superseded modules required
     *     for automatic rollup</dd>
     *     <dt>fullpath:</dt>   <dd>If fullpath is specified, this is used
     *     instead of the configured base + path</dd>
     *     <dt>skinnable:</dt>  <dd>flag to determine if skin assets should
     *     automatically be pulled in</dd>
     *     <dt>submodules:</dt> <dd>a hash of submodules</dd>
     *     <dt>group:</dt>      <dd>The group the module belongs to -- this
     *     is set automatically when it is added as part of a group
     *     configuration.</dd>
     *     <dt>lang:</dt>
     *       <dd>array of BCP 47 language tags of languages for which this
     *           module has localized resource bundles,
     *           e.g., ["en-GB","zh-Hans-CN"]</dd>
     *     <dt>condition:</dt>
     *       <dd>Specifies that the module should be loaded automatically if
     *           a condition is met.  This is an object with up to three fields:
     *           [trigger] - the name of a module that can trigger the auto-load
     *           [test] - a function that returns true when the module is to be
     *           loaded.
     *           [when] - specifies the load order of the conditional module
     *           with regard to the position of the trigger module.
     *           This should be one of three values: 'before', 'after', or
     *           'instead'.  The default is 'after'.
     *       </dd>
     * </dl>
     * @method addModule
     * @param {object} o An object containing the module data.
     * @param {string} name the module name (optional), required if not
     * in the module data.
     * @return {object} the module definition or null if
     * the object passed in did not provide all required attributes.
     */
    addModule: function(o, name) {

        name = name || o.name;
        o.name = name;

        if (!o || !o.name) {
            return null;
        }

        if (!o.type) {
            o.type = JS;
        }

        if (!o.path && !o.fullpath) {
            o.path = _path(name, name, o.type);
        }

        o.supersedes = o.supersedes || o.use;

        o.ext = ('ext' in o) ? o.ext : (this._internal) ? false : true;
        o.requires = o.requires || [];

        // Handle submodule logic
        var subs = o.submodules, i, l, sup, s, smod, plugins, plug,
            j, langs, packName, supName, flatSup, flatLang, lang, ret,
            overrides, skinname, when,
            conditions = this.conditions, trigger;
            // , existing = this.moduleInfo[name], newr;

        this.moduleInfo[name] = o;

        if (!o.langPack && o.lang) {
            langs = YArray(o.lang);
            for (j = 0; j < langs.length; j++) {
                lang = langs[j];
                packName = this.getLangPackName(lang, name);
                smod = this.moduleInfo[packName];
                if (!smod) {
                    smod = this._addLangPack(lang, o, packName);
                }
            }
        }

        if (subs) {
            sup = o.supersedes || [];
            l = 0;

            for (i in subs) {
                if (subs.hasOwnProperty(i)) {
                    s = subs[i];

                    s.path = s.path || _path(name, i, o.type);
                    s.pkg = name;
                    s.group = o.group;

                    if (s.supersedes) {
                        sup = sup.concat(s.supersedes);
                    }

                    smod = this.addModule(s, i);
                    sup.push(i);

                    if (smod.skinnable) {
                        o.skinnable = true;
                        overrides = this.skin.overrides;
                        if (overrides && overrides[i]) {
                            for (j = 0; j < overrides[i].length; j++) {
                                skinname = this._addSkin(overrides[i][j],
                                         i, name);
                                sup.push(skinname);
                            }
                        }
                        skinname = this._addSkin(this.skin.defaultSkin,
                                        i, name);
                        sup.push(skinname);
                    }

                    // looks like we are expected to work out the metadata
                    // for the parent module language packs from what is
                    // specified in the child modules.
                    if (s.lang && s.lang.length) {

                        langs = YArray(s.lang);
                        for (j = 0; j < langs.length; j++) {
                            lang = langs[j];
                            packName = this.getLangPackName(lang, name);
                            supName = this.getLangPackName(lang, i);
                            smod = this.moduleInfo[packName];

                            if (!smod) {
                                smod = this._addLangPack(lang, o, packName);
                            }

                            flatSup = flatSup || YArray.hash(smod.supersedes);

                            if (!(supName in flatSup)) {
                                smod.supersedes.push(supName);
                            }

                            o.lang = o.lang || [];

                            flatLang = flatLang || YArray.hash(o.lang);

                            if (!(lang in flatLang)) {
                                o.lang.push(lang);
                            }

// Add rollup file, need to add to supersedes list too

                            // default packages
                            packName = this.getLangPackName(ROOT_LANG, name);
                            supName = this.getLangPackName(ROOT_LANG, i);

                            smod = this.moduleInfo[packName];

                            if (!smod) {
                                smod = this._addLangPack(lang, o, packName);
                            }

                            if (!(supName in flatSup)) {
                                smod.supersedes.push(supName);
                            }

// Add rollup file, need to add to supersedes list too

                        }
                    }

                    l++;
                }
            }
            o.supersedes = YObject.keys(YArray.hash(sup));
            o.rollup = (l < 4) ? l : Math.min(l - 1, 4);
        }

        plugins = o.plugins;
        if (plugins) {
            for (i in plugins) {
                if (plugins.hasOwnProperty(i)) {
                    plug = plugins[i];
                    plug.pkg = name;
                    plug.path = plug.path || _path(name, i, o.type);
                    plug.requires = plug.requires || [];
                    plug.group = o.group;
                    this.addModule(plug, i);
                    if (o.skinnable) {
                        this._addSkin(this.skin.defaultSkin, i, name);
                    }

                }
            }
        }

        if (o.condition) {
            trigger = o.condition.trigger;
            when = o.condition.when;
            conditions[trigger] = conditions[trigger] || {};
            conditions[trigger][name] = o.condition;
            // the 'when' attribute can be 'before', 'after', or 'instead'
            // the default is after.
            if (when && when != 'after') {
                if (when == 'instead') { // replace the trigger
                    o.supersedes = o.supersedes || [];
                    o.supersedes.push(trigger);
                } else { // before the trigger
                    // the trigger requires the conditional mod,
                    // so it should appear before the conditional
                    // mod if we do not intersede.
                }
            } else { // after the trigger
                o.after = o.after || [];
                o.after.push(trigger);
            }
        }

        if (o.after) {
            o.after_map = YArray.hash(o.after);
        }

        // this.dirty = true;

        if (o.configFn) {
            ret = o.configFn(o);
            if (ret === false) {
                delete this.moduleInfo[name];
                o = null;
            }
        }

        return o;
    },

    /**
     * Add a requirement for one or more module
     * @method require
     * @param {string[] | string*} what the modules to load.
     */
    require: function(what) {
        var a = (typeof what === 'string') ? arguments : what;
        this.dirty = true;
        Y.mix(this.required, YArray.hash(a));
    },

    /**
     * Returns an object containing properties for all modules required
     * in order to load the requested module
     * @method getRequires
     * @param {object}  mod The module definition from moduleInfo.
     * @return {array} the expanded requirement list.
     */
    getRequires: function(mod) {

        if (!mod || mod._parsed) {
            return NO_REQUIREMENTS;
        }

        var i, m, j, add, packName, lang,
            name = mod.name, cond, go,
            adddef = ON_PAGE[name] && ON_PAGE[name].details,
            d,
            r, old_mod,
            o, skinmod, skindef,
            intl = mod.lang || mod.intl,
            info = this.moduleInfo,
            hash;

        // pattern match leaves module stub that needs to be filled out
        if (mod.temp && adddef) {
            old_mod = mod;
            mod = this.addModule(adddef, name);
            mod.group = old_mod.group;
            mod.pkg = old_mod.pkg;
            delete mod.expanded;
        }

        // if (mod.expanded && (!mod.langCache || mod.langCache == this.lang)) {
        if (mod.expanded && (!this.lang || mod.langCache === this.lang)) {
            return mod.expanded;
        }

        d = [];
        hash = {};

        r = mod.requires;
        o = mod.optional;


        mod._parsed = true;


        for (i = 0; i < r.length; i++) {
            if (!hash[r[i]]) {
                d.push(r[i]);
                hash[r[i]] = true;
                m = this.getModule(r[i]);
                if (m) {
                    add = this.getRequires(m);
                    intl = intl || (m.expanded_map &&
                        (INTL in m.expanded_map));
                    for (j = 0; j < add.length; j++) {
                        d.push(add[j]);
                    }
                }
            }
        }

        // get the requirements from superseded modules, if any
        r = mod.supersedes;
        if (r) {
            for (i = 0; i < r.length; i++) {
                if (!hash[r[i]]) {
                    // if this module has submodules, the requirements list is
                    // expanded to include the submodules.  This is so we can
                    // prevent dups when a submodule is already loaded and the
                    // parent is requested.
                    if (mod.submodules) {
                        d.push(r[i]);
                    }

                    hash[r[i]] = true;
                    m = this.getModule(r[i]);

                    if (m) {
                        add = this.getRequires(m);
                        intl = intl || (m.expanded_map &&
                            (INTL in m.expanded_map));
                        for (j = 0; j < add.length; j++) {
                            d.push(add[j]);
                        }
                    }
                }
            }
        }

        if (o && this.loadOptional) {
            for (i = 0; i < o.length; i++) {
                if (!hash[o[i]]) {
                    d.push(o[i]);
                    hash[o[i]] = true;
                    m = info[o[i]];
                    if (m) {
                        add = this.getRequires(m);
                        intl = intl || (m.expanded_map &&
                            (INTL in m.expanded_map));
                        for (j = 0; j < add.length; j++) {
                            d.push(add[j]);
                        }
                    }
                }
            }
        }

        cond = this.conditions[name];

        if (cond) {
            oeach(cond, function(def, condmod) {

                if (!hash[condmod]) {
                    go = def && ((def.ua && Y.UA[def.ua]) ||
                                 (def.test && def.test(Y, r)));
                    if (go) {
                        hash[condmod] = true;
                        d.push(condmod);
                        m = this.getModule(condmod);
                        if (m) {
                            add = this.getRequires(m);
                            for (j = 0; j < add.length; j++) {
                                d.push(add[j]);
                            }
                        }
                    }
                }
            }, this);
        }

        // Create skin modules
        if (mod.skinnable) {
            skindef = this.skin.overrides;
            if (skindef && skindef[name]) {
                for (i = 0; i < skindef[name].length; i++) {
                    skinmod = this._addSkin(skindef[name][i], name);
                    d.push(skinmod);
                }
            } else {
                skinmod = this._addSkin(this.skin.defaultSkin, name);
                d.push(skinmod);
            }
        }

        mod._parsed = false;

        if (intl) {

            if (mod.lang && !mod.langPack && Y.Intl) {
                lang = Y.Intl.lookupBestLang(this.lang || ROOT_LANG, mod.lang);
                mod.langCache = this.lang;
                packName = this.getLangPackName(lang, name);
                if (packName) {
                    d.unshift(packName);
                }
            }

            d.unshift(INTL);
        }

        mod.expanded_map = YArray.hash(d);

        mod.expanded = YObject.keys(mod.expanded_map);

        return mod.expanded;
    },


    /**
     * Returns a hash of module names the supplied module satisfies.
     * @method getProvides
     * @param {string} name The name of the module.
     * @return {object} what this module provides.
     */
    getProvides: function(name) {
        var m = this.getModule(name), o, s;
            // supmap = this.provides;

        if (!m) {
            return NOT_FOUND;
        }

        if (m && !m.provides) {
            o = {};
            s = m.supersedes;

            if (s) {
                YArray.each(s, function(v) {
                    Y.mix(o, this.getProvides(v));
                }, this);
            }

            o[name] = true;
            m.provides = o;

        }

        return m.provides;
    },

    /**
     * Calculates the dependency tree, the result is stored in the sorted
     * property.
     * @method calculate
     * @param {object} o optional options object.
     * @param {string} type optional argument to prune modules.
     */
    calculate: function(o, type) {
        if (o || type || this.dirty) {

            if (o) {
                this._config(o);
            }

            if (!this._init) {
                this._setup();
            }

            this._explode();

            if (this.allowRollup) {
                this._rollup();
            }
            this._reduce();
            this._sort();
        }
    },

    _addLangPack: function(lang, m, packName) {
        var name = m.name,
            packPath,
            existing = this.moduleInfo[packName];

        if (!existing) {

            packPath = _path((m.pkg || name), packName, JS, true);

            this.addModule({ path: packPath,
                             intl: true,
                             langPack: true,
                             ext: m.ext,
                             group: m.group,
                             supersedes: [] }, packName, true);

            if (lang) {
                Y.Env.lang = Y.Env.lang || {};
                Y.Env.lang[lang] = Y.Env.lang[lang] || {};
                Y.Env.lang[lang][name] = true;
            }
        }

        return this.moduleInfo[packName];
    },

    /**
     * Investigates the current YUI configuration on the page.  By default,
     * modules already detected will not be loaded again unless a force
     * option is encountered.  Called by calculate()
     * @method _setup
     * @private
     */
    _setup: function() {
        var info = this.moduleInfo, name, i, j, m, l,
            packName;

        for (name in info) {
            if (info.hasOwnProperty(name)) {
                m = info[name];
                if (m) {

                    // remove dups
                    m.requires = YObject.keys(YArray.hash(m.requires));

                    // Create lang pack modules
                    if (m.lang && m.lang.length) {
                        // Setup root package if the module has lang defined,
                        // it needs to provide a root language pack
                        packName = this.getLangPackName(ROOT_LANG, name);
                        this._addLangPack(null, m, packName);
                    }

                }
            }
        }


        //l = Y.merge(this.inserted);
        l = {};

        // available modules
        if (!this.ignoreRegistered) {
            Y.mix(l, GLOBAL_ENV.mods);
        }

        // add the ignore list to the list of loaded packages
        if (this.ignore) {
            Y.mix(l, YArray.hash(this.ignore));
        }

        // expand the list to include superseded modules
        for (j in l) {
            if (l.hasOwnProperty(j)) {
                Y.mix(l, this.getProvides(j));
            }
        }

        // remove modules on the force list from the loaded list
        if (this.force) {
            for (i = 0; i < this.force.length; i++) {
                if (this.force[i] in l) {
                    delete l[this.force[i]];
                }
            }
        }

        Y.mix(this.loaded, l);

        this._init = true;
    },

    /**
     * Builds a module name for a language pack
     * @method getLangPackName
     * @param {string} lang the language code.
     * @param {string} mname the module to build it for.
     * @return {string} the language pack module name.
     */
    getLangPackName: function(lang, mname) {
        return ('lang/' + mname + ((lang) ? '_' + lang : ''));
    },

    /**
     * Inspects the required modules list looking for additional
     * dependencies.  Expands the required list to include all
     * required modules.  Called by calculate()
     * @method _explode
     * @private
     */
    _explode: function() {
        var r = this.required, m, reqs, done = {},
            self = this;

        // the setup phase is over, all modules have been created
        self.dirty = false;

        oeach(r, function(v, name) {
            if (!done[name]) {
                done[name] = true;
                m = self.getModule(name);
                if (m) {
                    var expound = m.expound;

                    if (expound) {
                        r[expound] = self.getModule(expound);
                        reqs = self.getRequires(r[expound]);
                        Y.mix(r, YArray.hash(reqs));
                    }

                    reqs = self.getRequires(m);
                    Y.mix(r, YArray.hash(reqs));
                }
            }
        });

    },

    getModule: function(mname) {
        //TODO: Remove name check - it's a quick hack to fix pattern WIP
        if (!mname) {
            return null;
        }

        var p, found, pname,
            m = this.moduleInfo[mname],
            patterns = this.patterns;

        // check the patterns library to see if we should automatically add
        // the module with defaults
        if (!m) {
            for (pname in patterns) {
                if (patterns.hasOwnProperty(pname)) {
                    p = patterns[pname];

                    // use the metadata supplied for the pattern
                    // as the module definition.
                    if (mname.indexOf(pname) > -1) {
                        found = p;
                        break;
                    }
                }
            }

            if (found) {
                if (p.action) {
                    p.action.call(this, mname, pname);
                } else {
                    // ext true or false?
                    m = this.addModule(Y.merge(found), mname);
                    m.temp = true;
                }
            }
        }

        return m;
    },

    // impl in rollup submodule
    _rollup: function() { },

    /**
     * Remove superceded modules and loaded modules.  Called by
     * calculate() after we have the mega list of all dependencies
     * @method _reduce
     * @return {object} the reduced dependency hash.
     * @private
     */
    _reduce: function(r) {

        r = r || this.required;

        var i, j, s, m, type = this.loadType;
        for (i in r) {
            if (r.hasOwnProperty(i)) {
                m = this.getModule(i);
                // remove if already loaded
                if (((this.loaded[i] || ON_PAGE[i]) &&
                        !this.forceMap[i] && !this.ignoreRegistered) ||
                        (type && m && m.type != type)) {
                    delete r[i];
                }
                // remove anything this module supersedes
                s = m && m.supersedes;
                if (s) {
                    for (j = 0; j < s.length; j++) {
                        if (s[j] in r) {
                            delete r[s[j]];
                        }
                    }
                }
            }
        }

        return r;
    },

    _finish: function(msg, success) {

        _queue.running = false;

        var onEnd = this.onEnd;
        if (onEnd) {
            onEnd.call(this.context, {
                msg: msg,
                data: this.data,
                success: success
            });
        }
        this._continue();
    },

    _onSuccess: function() {
        var self = this, skipped = Y.merge(self.skipped), fn,
            failed = [], rreg = self.requireRegistration,
            success, msg;

        oeach(skipped, function(k) {
            delete self.inserted[k];
        });

        self.skipped = {};

        oeach(self.inserted, function(v, k) {
            var mod = self.getModule(k);
            if (mod && rreg && mod.type == JS && !(k in YUI.Env.mods)) {
                failed.push(k);
            } else {
                Y.mix(self.loaded, self.getProvides(k));
            }
        });

        fn = self.onSuccess;
        msg = (failed.length) ? 'notregistered' : 'success';
        success = !(failed.length);
        if (fn) {
            fn.call(self.context, {
                msg: msg,
                data: self.data,
                success: success,
                failed: failed,
                skipped: skipped
            });
        }
        self._finish(msg, success);
    },
    _onFailure: function(o) {
        var f = this.onFailure, msg = 'failure: ' + o.msg;
        if (f) {
            f.call(this.context, {
                msg: msg,
                data: this.data,
                success: false
            });
        }
        this._finish(msg, false);
    },

    _onTimeout: function() {
        var f = this.onTimeout;
        if (f) {
            f.call(this.context, {
                msg: 'timeout',
                data: this.data,
                success: false
            });
        }
        this._finish('timeout', false);
    },

    /**
     * Sorts the dependency tree.  The last step of calculate()
     * @method _sort
     * @private
     */
    _sort: function() {

        // create an indexed list
        var s = YObject.keys(this.required),
            // loaded = this.loaded,
            done = {},
            p = 0, l, a, b, j, k, moved, doneKey;


        // keep going until we make a pass without moving anything
        for (;;) {

            l = s.length;
            moved = false;

            // start the loop after items that are already sorted
            for (j = p; j < l; j++) {

                // check the next module on the list to see if its
                // dependencies have been met
                a = s[j];

                // check everything below current item and move if we
                // find a requirement for the current item
                for (k = j + 1; k < l; k++) {
                    doneKey = a + s[k];

                    if (!done[doneKey] && this._requires(a, s[k])) {

                        // extract the dependency so we can move it up
                        b = s.splice(k, 1);

                        // insert the dependency above the item that
                        // requires it
                        s.splice(j, 0, b[0]);

                        // only swap two dependencies once to short circut
                        // circular dependencies
                        done[doneKey] = true;

                        // keep working
                        moved = true;

                        break;
                    }
                }

                // jump out of loop if we moved something
                if (moved) {
                    break;
                // this item is sorted, move our pointer and keep going
                } else {
                    p++;
                }
            }

            // when we make it here and moved is false, we are
            // finished sorting
            if (!moved) {
                break;
            }

        }

        this.sorted = s;

    },

    partial: function(partial, o, type) {
        this.sorted = partial;
        this.insert(o, type, true);
    },

    _insert: function(source, o, type, skipcalc) {


        // restore the state at the time of the request
        if (source) {
            this._config(source);
        }

        // build the dependency list
        // don't include type so we can process CSS and script in
        // one pass when the type is not specified.
        if (!skipcalc) {
            this.calculate(o);
        }

        this.loadType = type;

        if (!type) {

            var self = this;

            this._internalCallback = function() {

                var f = self.onCSS, n, p, sib;

                // IE hack for style overrides that are not being applied
                if (this.insertBefore && Y.UA.ie) {
                    n = Y.config.doc.getElementById(this.insertBefore);
                    p = n.parentNode;
                    sib = n.nextSibling;
                    p.removeChild(n);
                    if (sib) {
                        p.insertBefore(n, sib);
                    } else {
                        p.appendChild(n);
                    }
                }

                if (f) {
                    f.call(self.context, Y);
                }
                self._internalCallback = null;

                self._insert(null, null, JS);
            };

            this._insert(null, null, CSS);

            return;
        }

        // set a flag to indicate the load has started
        this._loading = true;

        // flag to indicate we are done with the combo service
        // and any additional files will need to be loaded
        // individually
        this._combineComplete = {};

        // start the load
        this.loadNext();

    },

    // Once a loader operation is completely finished, process
    // any additional queued items.
    _continue: function() {
        if (!(_queue.running) && _queue.size() > 0) {
            _queue.running = true;
            _queue.next()();
        }
    },

    /**
     * inserts the requested modules and their dependencies.
     * <code>type</code> can be "js" or "css".  Both script and
     * css are inserted if type is not provided.
     * @method insert
     * @param {object} o optional options object.
     * @param {string} type the type of dependency to insert.
     */
    insert: function(o, type, skipsort) {
        var self = this, copy = Y.merge(this);
        delete copy.require;
        delete copy.dirty;
        _queue.add(function() {
            self._insert(copy, o, type, skipsort);
        });
        this._continue();
    },

    /**
     * Executed every time a module is loaded, and if we are in a load
     * cycle, we attempt to load the next script.  Public so that it
     * is possible to call this if using a method other than
     * Y.register to determine when scripts are fully loaded
     * @method loadNext
     * @param {string} mname optional the name of the module that has
     * been loaded (which is usually why it is time to load the next
     * one).
     */
    loadNext: function(mname) {
        // It is possible that this function is executed due to something
        // else one the page loading a YUI module.  Only react when we
        // are actively loading something
        if (!this._loading) {
            return;
        }

        var s, len, i, m, url, fn, msg, attr, group, groupName, j, frag,
            comboSource, comboSources, mods, combining, urls, comboBase,
            self = this,
            type = self.loadType,
            handleSuccess = function(o) {
                self.loadNext(o.data);
            },
            handleCombo = function(o) {
                self._combineComplete[type] = true;
                var i, len = combining.length;

                for (i = 0; i < len; i++) {
                    self.inserted[combining[i]] = true;
                }

                handleSuccess(o);
            };

        if (self.combine && (!self._combineComplete[type])) {

            combining = [];

            self._combining = combining;
            s = self.sorted;
            len = s.length;

            // the default combo base
            comboBase = self.comboBase;

            url = comboBase;
            urls = [];

            comboSources = {};

            for (i = 0; i < len; i++) {
                comboSource = comboBase;
                m = self.getModule(s[i]);
                groupName = m && m.group;
                if (groupName) {

                    group = self.groups[groupName];

                    if (!group.combine) {
                        m.combine = false;
                        continue;
                    }
                    m.combine = true;
                    if (group.comboBase) {
                        comboSource = group.comboBase;
                    }

                    if (group.root) {
                        m.root = group.root;
                    }

                }

                comboSources[comboSource] = comboSources[comboSource] || [];
                comboSources[comboSource].push(m);
            }

            for (j in comboSources) {
                if (comboSources.hasOwnProperty(j)) {
                    url = j;
                    mods = comboSources[j];
                    len = mods.length;

                    for (i = 0; i < len; i++) {
                        // m = self.getModule(s[i]);
                        m = mods[i];

                        // Do not try to combine non-yui JS unless combo def
                        // is found
                        if (m && (m.type === type) && (m.combine || !m.ext)) {

                            frag = (m.root || self.root) + m.path;

                            if ((url !== j) && (i < (len - 1)) &&
                            ((frag.length + url.length) > self.maxURLLength)) {
                                urls.push(self._filter(url));
                                url = j;
                            }

                            url += frag;
                            if (i < (len - 1)) {
                                url += '&';
                            }

                            combining.push(m.name);
                        }

                    }

                    if (combining.length && (url != j)) {
                        urls.push(self._filter(url));
                    }
                }
            }

            if (combining.length) {


                // if (m.type === CSS) {
                if (type === CSS) {
                    fn = Y.Get.css;
                    attr = self.cssAttributes;
                } else {
                    fn = Y.Get.script;
                    attr = self.jsAttributes;
                }

                fn(urls, {
                    data: self._loading,
                    onSuccess: handleCombo,
                    onFailure: self._onFailure,
                    onTimeout: self._onTimeout,
                    insertBefore: self.insertBefore,
                    charset: self.charset,
                    attributes: attr,
                    timeout: self.timeout,
                    autopurge: false,
                    context: self
                });

                return;

            } else {
                self._combineComplete[type] = true;
            }
        }

        if (mname) {

            // if the module that was just loaded isn't what we were expecting,
            // continue to wait
            if (mname !== self._loading) {
                return;
            }


            // The global handler that is called when each module is loaded
            // will pass that module name to this function.  Storing this
            // data to avoid loading the same module multiple times
            // centralize this in the callback
            self.inserted[mname] = true;
            // self.loaded[mname] = true;

            // provided = self.getProvides(mname);
            // Y.mix(self.loaded, provided);
            // Y.mix(self.inserted, provided);

            if (self.onProgress) {
                self.onProgress.call(self.context, {
                        name: mname,
                        data: self.data
                    });
            }
        }

        s = self.sorted;
        len = s.length;

        for (i = 0; i < len; i = i + 1) {
            // this.inserted keeps track of what the loader has loaded.
            // move on if this item is done.
            if (s[i] in self.inserted) {
                continue;
            }

            // Because rollups will cause multiple load notifications
            // from Y, loadNext may be called multiple times for
            // the same module when loading a rollup.  We can safely
            // skip the subsequent requests
            if (s[i] === self._loading) {
                return;
            }

            // log("inserting " + s[i]);
            m = self.getModule(s[i]);

            if (!m) {
                if (!self.skipped[s[i]]) {
                    msg = 'Undefined module ' + s[i] + ' skipped';
                    // self.inserted[s[i]] = true;
                    self.skipped[s[i]] = true;
                }
                continue;

            }

            group = (m.group && self.groups[m.group]) || NOT_FOUND;

            // The load type is stored to offer the possibility to load
            // the css separately from the script.
            if (!type || type === m.type) {
                self._loading = s[i];

                if (m.type === CSS) {
                    fn = Y.Get.css;
                    attr = self.cssAttributes;
                } else {
                    fn = Y.Get.script;
                    attr = self.jsAttributes;
                }

                url = (m.fullpath) ? self._filter(m.fullpath, s[i]) :
                      self._url(m.path, s[i], group.base || m.base);

                fn(url, {
                    data: s[i],
                    onSuccess: handleSuccess,
                    insertBefore: self.insertBefore,
                    charset: self.charset,
                    attributes: attr,
                    onFailure: self._onFailure,
                    onTimeout: self._onTimeout,
                    timeout: self.timeout,
                    autopurge: false,
                    context: self
                });

                return;
            }
        }

        // we are finished
        self._loading = null;

        fn = self._internalCallback;

        // internal callback for loading css first
        if (fn) {
            self._internalCallback = null;
            fn.call(self);
        } else {
            self._onSuccess();
        }
    },

    /**
     * Apply filter defined for this instance to a url/path
     * method _filter
     * @param {string} u the string to filter.
     * @param {string} name the name of the module, if we are processing
     * a single module as opposed to a combined url.
     * @return {string} the filtered string.
     * @private
     */
    _filter: function(u, name) {
        var f = this.filter,
            hasFilter = name && (name in this.filters),
            modFilter = hasFilter && this.filters[name];

        if (u) {
            if (hasFilter) {
                f = (L.isString(modFilter)) ?
                    this.FILTER_DEFS[modFilter.toUpperCase()] || null :
                    modFilter;
            }
            if (f) {
                u = u.replace(new RegExp(f.searchExp, 'g'), f.replaceStr);
            }
        }

        return u;
    },

    /**
     * Generates the full url for a module
     * method _url
     * @param {string} path the path fragment.
     * @return {string} the full url.
     * @private
     */
    _url: function(path, name, base) {
        return this._filter((base || this.base || '') + path, name);
    }
};




}, '3.3.0' ,{requires:['get']});

YUI.add('loader-rollup', function(Y) {

/**
 * Optional automatic rollup logic for reducing http connections
 * when not using a combo service.
 * @module loader
 * @submodule rollup
 */

/**
 * Look for rollup packages to determine if all of the modules a
 * rollup supersedes are required.  If so, include the rollup to
 * help reduce the total number of connections required.  Called
 * by calculate().  This is an optional feature, and requires the
 * appropriate submodule to function.
 * @method _rollup
 * @for Loader
 * @private
 */
Y.Loader.prototype._rollup = function() {
    var i, j, m, s, r = this.required, roll,
        info = this.moduleInfo, rolled, c, smod;

    // find and cache rollup modules
    if (this.dirty || !this.rollups) {
        this.rollups = {};
        for (i in info) {
            if (info.hasOwnProperty(i)) {
                m = this.getModule(i);
                // if (m && m.rollup && m.supersedes) {
                if (m && m.rollup) {
                    this.rollups[i] = m;
                }
            }
        }

        this.forceMap = (this.force) ? Y.Array.hash(this.force) : {};
    }

    // make as many passes as needed to pick up rollup rollups
    for (;;) {
        rolled = false;

        // go through the rollup candidates
        for (i in this.rollups) {
            if (this.rollups.hasOwnProperty(i)) {
                // there can be only one, unless forced
                if (!r[i] && ((!this.loaded[i]) || this.forceMap[i])) {
                    m = this.getModule(i);
                    s = m.supersedes || [];
                    roll = false;

                    // @TODO remove continue
                    if (!m.rollup) {
                        continue;
                    }

                    c = 0;

                    // check the threshold
                    for (j = 0; j < s.length; j++) {
                        smod = info[s[j]];

                        // if the superseded module is loaded, we can't
                        // load the rollup unless it has been forced.
                        if (this.loaded[s[j]] && !this.forceMap[s[j]]) {
                            roll = false;
                            break;
                        // increment the counter if this module is required.
                        // if we are beyond the rollup threshold, we will
                        // use the rollup module
                        } else if (r[s[j]] && m.type == smod.type) {
                            c++;
                            roll = (c >= m.rollup);
                            if (roll) {
                                break;
                            }
                        }
                    }

                    if (roll) {
                        // add the rollup
                        r[i] = true;
                        rolled = true;

                        // expand the rollup's dependencies
                        this.getRequires(m);
                    }
                }
            }
        }

        // if we made it here w/o rolling up something, we are done
        if (!rolled) {
            break;
        }
    }
};



}, '3.3.0' ,{requires:['loader-base']});

YUI.add('loader-yui3', function(Y) {

/* This file is auto-generated by src/loader/meta_join.py */

/**
 * YUI 3 module metadata
 * @module loader
 * @submodule yui3
 */
YUI.Env[Y.version].modules = YUI.Env[Y.version].modules || {
    "anim": {
        "submodules": {
            "anim-base": {
                "requires": [
                    "base-base", 
                    "node-style"
                ]
            }, 
            "anim-color": {
                "requires": [
                    "anim-base"
                ]
            }, 
            "anim-curve": {
                "requires": [
                    "anim-xy"
                ]
            }, 
            "anim-easing": {
                "requires": [
                    "anim-base"
                ]
            }, 
            "anim-node-plugin": {
                "requires": [
                    "node-pluginhost", 
                    "anim-base"
                ]
            }, 
            "anim-scroll": {
                "requires": [
                    "anim-base"
                ]
            }, 
            "anim-xy": {
                "requires": [
                    "anim-base", 
                    "node-screen"
                ]
            }
        }
    }, 
    "arraysort": {
        "requires": [
            "yui-base"
        ]
    }, 
    "async-queue": {
        "requires": [
            "event-custom"
        ]
    }, 
    "attribute": {
        "submodules": {
            "attribute-base": {
                "requires": [
                    "event-custom"
                ]
            }, 
            "attribute-complex": {
                "requires": [
                    "attribute-base"
                ]
            }
        }
    }, 
    "autocomplete": {
        "submodules": {
            "autocomplete-base": {
                "optional": [
                    "autocomplete-sources"
                ], 
                "plugins": {
                    "autocomplete-filters": {
                        "path": "autocomplete/autocomplete-filters-min.js", 
                        "requires": [
                            "array-extras", 
                            "text-wordbreak"
                        ]
                    }, 
                    "autocomplete-filters-accentfold": {
                        "path": "autocomplete/autocomplete-filters-accentfold-min.js", 
                        "requires": [
                            "array-extras", 
                            "text-accentfold", 
                            "text-wordbreak"
                        ]
                    }, 
                    "autocomplete-highlighters": {
                        "path": "autocomplete/autocomplete-highlighters-min.js", 
                        "requires": [
                            "array-extras", 
                            "highlight-base"
                        ]
                    }, 
                    "autocomplete-highlighters-accentfold": {
                        "path": "autocomplete/autocomplete-highlighters-accentfold-min.js", 
                        "requires": [
                            "array-extras", 
                            "highlight-accentfold"
                        ]
                    }
                }, 
                "requires": [
                    "array-extras", 
                    "base-build", 
                    "escape", 
                    "event-valuechange", 
                    "node-base"
                ]
            }, 
            "autocomplete-list": {
                "after": "autocomplete-sources", 
                "lang": [
                    "en"
                ], 
                "plugins": {
                    "autocomplete-list-keys": {
                        "condition": {
                            "test": function (Y) {
    // Only add keyboard support to autocomplete-list if this doesn't appear to
    // be an iOS or Android-based mobile device.
    //
    // There's currently no feasible way to actually detect whether a device has
    // a hardware keyboard, so this sniff will have to do. It can easily be
    // overridden by manually loading the autocomplete-list-keys module.
    //
    // Worth noting: even though iOS supports bluetooth keyboards, Mobile Safari
    // doesn't fire the keyboard events used by AutoCompleteList, so there's
    // no point loading the -keys module even when a bluetooth keyboard may be
    // available.
    return !(Y.UA.ios || Y.UA.android);
}, 
                            "trigger": "autocomplete-list"
                        }, 
                        "path": "autocomplete/autocomplete-list-keys-min.js", 
                        "requires": [
                            "autocomplete-list", 
                            "base-build"
                        ]
                    }, 
                    "autocomplete-plugin": {
                        "path": "autocomplete/autocomplete-plugin-min.js", 
                        "requires": [
                            "autocomplete-list", 
                            "node-pluginhost"
                        ]
                    }
                }, 
                "requires": [
                    "autocomplete-base", 
                    "selector-css3", 
                    "widget", 
                    "widget-position", 
                    "widget-position-align", 
                    "widget-stack"
                ], 
                "skinnable": true
            }, 
            "autocomplete-sources": {
                "optional": [
                    "io-base", 
                    "json-parse", 
                    "jsonp", 
                    "yql"
                ], 
                "requires": [
                    "autocomplete-base"
                ]
            }
        }
    }, 
    "base": {
        "submodules": {
            "base-base": {
                "after": [
                    "attribute-complex"
                ], 
                "requires": [
                    "attribute-base"
                ]
            }, 
            "base-build": {
                "requires": [
                    "base-base"
                ]
            }, 
            "base-pluginhost": {
                "requires": [
                    "base-base", 
                    "pluginhost"
                ]
            }
        }
    }, 
    "cache": {
        "submodules": {
            "cache-base": {
                "requires": [
                    "base"
                ]
            }, 
            "cache-offline": {
                "requires": [
                    "cache-base", 
                    "json"
                ]
            }, 
            "cache-plugin": {
                "requires": [
                    "plugin", 
                    "cache-base"
                ]
            }
        }
    }, 
    "charts": {
        "requires": [
            "dom", 
            "datatype", 
            "event-custom", 
            "event-mouseenter", 
            "widget", 
            "widget-position", 
            "widget-stack"
        ]
    }, 
    "classnamemanager": {
        "requires": [
            "yui-base"
        ]
    }, 
    "collection": {
        "submodules": {
            "array-extras": {}, 
            "array-invoke": {}, 
            "arraylist": {}, 
            "arraylist-add": {
                "requires": [
                    "arraylist"
                ]
            }, 
            "arraylist-filter": {
                "requires": [
                    "arraylist"
                ]
            }
        }
    }, 
    "compat": {
        "requires": [
            "event-base", 
            "dom", 
            "dump", 
            "substitute"
        ]
    }, 
    "console": {
        "lang": [
            "en", 
            "es"
        ], 
        "plugins": {
            "console-filters": {
                "requires": [
                    "plugin", 
                    "console"
                ], 
                "skinnable": true
            }
        }, 
        "requires": [
            "yui-log", 
            "widget", 
            "substitute"
        ], 
        "skinnable": true
    }, 
    "cookie": {
        "requires": [
            "yui-base"
        ]
    }, 
    "cssbase": {
        "after": [
            "cssreset", 
            "cssfonts", 
            "cssgrids", 
            "cssreset-context", 
            "cssfonts-context", 
            "cssgrids-context"
        ], 
        "path": "cssbase/base-min.css", 
        "type": "css"
    }, 
    "cssbase-context": {
        "after": [
            "cssreset", 
            "cssfonts", 
            "cssgrids", 
            "cssreset-context", 
            "cssfonts-context", 
            "cssgrids-context"
        ], 
        "path": "cssbase/base-context-min.css", 
        "type": "css"
    }, 
    "cssfonts": {
        "path": "cssfonts/fonts-min.css", 
        "type": "css"
    }, 
    "cssfonts-context": {
        "path": "cssfonts/fonts-context-min.css", 
        "type": "css"
    }, 
    "cssgrids": {
        "optional": [
            "cssreset", 
            "cssfonts"
        ], 
        "path": "cssgrids/grids-min.css", 
        "type": "css"
    }, 
    "cssgrids-context-deprecated": {
        "optional": [
            "cssreset-context"
        ], 
        "path": "cssgrids-deprecated/grids-context-min.css", 
        "requires": [
            "cssfonts-context"
        ], 
        "type": "css"
    }, 
    "cssgrids-deprecated": {
        "optional": [
            "cssreset"
        ], 
        "path": "cssgrids-deprecated/grids-min.css", 
        "requires": [
            "cssfonts"
        ], 
        "type": "css"
    }, 
    "cssreset": {
        "path": "cssreset/reset-min.css", 
        "type": "css"
    }, 
    "cssreset-context": {
        "path": "cssreset/reset-context-min.css", 
        "type": "css"
    }, 
    "dataschema": {
        "submodules": {
            "dataschema-array": {
                "requires": [
                    "dataschema-base"
                ]
            }, 
            "dataschema-base": {
                "requires": [
                    "base"
                ]
            }, 
            "dataschema-json": {
                "requires": [
                    "dataschema-base", 
                    "json"
                ]
            }, 
            "dataschema-text": {
                "requires": [
                    "dataschema-base"
                ]
            }, 
            "dataschema-xml": {
                "requires": [
                    "dataschema-base"
                ]
            }
        }
    }, 
    "datasource": {
        "submodules": {
            "datasource-arrayschema": {
                "requires": [
                    "datasource-local", 
                    "plugin", 
                    "dataschema-array"
                ]
            }, 
            "datasource-cache": {
                "requires": [
                    "datasource-local", 
                    "cache-base"
                ]
            }, 
            "datasource-function": {
                "requires": [
                    "datasource-local"
                ]
            }, 
            "datasource-get": {
                "requires": [
                    "datasource-local", 
                    "get"
                ]
            }, 
            "datasource-io": {
                "requires": [
                    "datasource-local", 
                    "io-base"
                ]
            }, 
            "datasource-jsonschema": {
                "requires": [
                    "datasource-local", 
                    "plugin", 
                    "dataschema-json"
                ]
            }, 
            "datasource-local": {
                "requires": [
                    "base"
                ]
            }, 
            "datasource-polling": {
                "requires": [
                    "datasource-local"
                ]
            }, 
            "datasource-textschema": {
                "requires": [
                    "datasource-local", 
                    "plugin", 
                    "dataschema-text"
                ]
            }, 
            "datasource-xmlschema": {
                "requires": [
                    "datasource-local", 
                    "plugin", 
                    "dataschema-xml"
                ]
            }
        }
    }, 
    "datatable": {
        "submodules": {
            "datatable-base": {
                "requires": [
                    "recordset-base", 
                    "widget", 
                    "substitute", 
                    "event-mouseenter"
                ], 
                "skinnable": true
            }, 
            "datatable-datasource": {
                "requires": [
                    "datatable-base", 
                    "plugin", 
                    "datasource-local"
                ]
            }, 
            "datatable-scroll": {
                "requires": [
                    "datatable-base", 
                    "plugin", 
                    "stylesheet"
                ]
            }, 
            "datatable-sort": {
                "lang": [
                    "en"
                ], 
                "requires": [
                    "datatable-base", 
                    "plugin", 
                    "recordset-sort"
                ]
            }
        }
    }, 
    "datatype": {
        "submodules": {
            "datatype-date": {
                "lang": [
                    "ar", 
                    "ar-JO", 
                    "ca", 
                    "ca-ES", 
                    "da", 
                    "da-DK", 
                    "de", 
                    "de-AT", 
                    "de-DE", 
                    "el", 
                    "el-GR", 
                    "en", 
                    "en-AU", 
                    "en-CA", 
                    "en-GB", 
                    "en-IE", 
                    "en-IN", 
                    "en-JO", 
                    "en-MY", 
                    "en-NZ", 
                    "en-PH", 
                    "en-SG", 
                    "en-US", 
                    "es", 
                    "es-AR", 
                    "es-BO", 
                    "es-CL", 
                    "es-CO", 
                    "es-EC", 
                    "es-ES", 
                    "es-MX", 
                    "es-PE", 
                    "es-PY", 
                    "es-US", 
                    "es-UY", 
                    "es-VE", 
                    "fi", 
                    "fi-FI", 
                    "fr", 
                    "fr-BE", 
                    "fr-CA", 
                    "fr-FR", 
                    "hi", 
                    "hi-IN", 
                    "id", 
                    "id-ID", 
                    "it", 
                    "it-IT", 
                    "ja", 
                    "ja-JP", 
                    "ko", 
                    "ko-KR", 
                    "ms", 
                    "ms-MY", 
                    "nb", 
                    "nb-NO", 
                    "nl", 
                    "nl-BE", 
                    "nl-NL", 
                    "pl", 
                    "pl-PL", 
                    "pt", 
                    "pt-BR", 
                    "ro", 
                    "ro-RO", 
                    "ru", 
                    "ru-RU", 
                    "sv", 
                    "sv-SE", 
                    "th", 
                    "th-TH", 
                    "tr", 
                    "tr-TR", 
                    "vi", 
                    "vi-VN", 
                    "zh-Hans", 
                    "zh-Hans-CN", 
                    "zh-Hant", 
                    "zh-Hant-HK", 
                    "zh-Hant-TW"
                ], 
                "requires": [
                    "yui-base"
                ], 
                "supersedes": [
                    "datatype-date-format"
                ]
            }, 
            "datatype-number": {
                "requires": [
                    "yui-base"
                ]
            }, 
            "datatype-xml": {
                "requires": [
                    "yui-base"
                ]
            }
        }
    }, 
    "datatype-date-format": {
        "path": "datatype/datatype-date-format-min.js"
    }, 
    "dd": {
        "plugins": {
            "dd-drop-plugin": {
                "requires": [
                    "dd-drop"
                ]
            }, 
            "dd-gestures": {
                "condition": {
                    "test": function(Y) {
    return (Y.config.win && ('ontouchstart' in Y.config.win && !Y.UA.chrome));
}, 
                    "trigger": "dd-drag"
                }, 
                "requires": [
                    "dd-drag", 
                    "event-move"
                ]
            }, 
            "dd-plugin": {
                "optional": [
                    "dd-constrain", 
                    "dd-proxy"
                ], 
                "requires": [
                    "dd-drag"
                ]
            }
        }, 
        "submodules": {
            "dd-constrain": {
                "requires": [
                    "dd-drag"
                ]
            }, 
            "dd-ddm": {
                "requires": [
                    "dd-ddm-base", 
                    "event-resize"
                ]
            }, 
            "dd-ddm-base": {
                "requires": [
                    "node", 
                    "base", 
                    "yui-throttle", 
                    "classnamemanager"
                ]
            }, 
            "dd-ddm-drop": {
                "requires": [
                    "dd-ddm"
                ]
            }, 
            "dd-delegate": {
                "requires": [
                    "dd-drag", 
                    "dd-drop-plugin", 
                    "event-mouseenter"
                ]
            }, 
            "dd-drag": {
                "requires": [
                    "dd-ddm-base"
                ]
            }, 
            "dd-drop": {
                "requires": [
                    "dd-ddm-drop"
                ]
            }, 
            "dd-proxy": {
                "requires": [
                    "dd-drag"
                ]
            }, 
            "dd-scroll": {
                "requires": [
                    "dd-drag"
                ]
            }
        }
    }, 
    "dial": {
        "lang": [
            "en", 
            "es"
        ], 
        "requires": [
            "widget", 
            "dd-drag", 
            "substitute", 
            "event-mouseenter", 
            "transition", 
            "intl"
        ], 
        "skinnable": true
    }, 
    "dom": {
        "plugins": {
            "dom-deprecated": {
                "requires": [
                    "dom-base"
                ]
            }, 
            "dom-style-ie": {
                "condition": {
                    "test": function (Y) {

    var testFeature = Y.Features.test,
        addFeature = Y.Features.add,
        WINDOW = Y.config.win,
        DOCUMENT = Y.config.doc,
        DOCUMENT_ELEMENT = 'documentElement',
        ret = false;

    addFeature('style', 'computedStyle', {
        test: function() {
            return WINDOW && 'getComputedStyle' in WINDOW;
        }
    });

    addFeature('style', 'opacity', {
        test: function() {
            return DOCUMENT && 'opacity' in DOCUMENT[DOCUMENT_ELEMENT].style;
        }
    });

    ret =  (!testFeature('style', 'opacity') &&
            !testFeature('style', 'computedStyle'));

    return ret;
}, 
                    "trigger": "dom-style"
                }, 
                "requires": [
                    "dom-style"
                ]
            }, 
            "selector-css3": {
                "requires": [
                    "selector-css2"
                ]
            }
        }, 
        "requires": [
            "oop"
        ], 
        "submodules": {
            "dom-base": {
                "requires": [
                    "oop"
                ]
            }, 
            "dom-screen": {
                "requires": [
                    "dom-base", 
                    "dom-style"
                ]
            }, 
            "dom-style": {
                "requires": [
                    "dom-base"
                ]
            }, 
            "selector": {
                "requires": [
                    "dom-base"
                ]
            }, 
            "selector-css2": {
                "requires": [
                    "selector-native"
                ]
            }, 
            "selector-native": {
                "requires": [
                    "dom-base"
                ]
            }
        }
    }, 
    "dump": {
        "requires": [
            "yui-base"
        ]
    }, 
    "editor": {
        "submodules": {
            "createlink-base": {
                "requires": [
                    "editor-base"
                ]
            }, 
            "editor-base": {
                "requires": [
                    "base", 
                    "frame", 
                    "node", 
                    "exec-command", 
                    "selection"
                ]
            }, 
            "editor-bidi": {
                "requires": [
                    "editor-base"
                ]
            }, 
            "editor-br": {
                "requires": [
                    "node"
                ]
            }, 
            "editor-lists": {
                "requires": [
                    "editor-base"
                ]
            }, 
            "editor-para": {
                "requires": [
                    "node"
                ]
            }, 
            "exec-command": {
                "requires": [
                    "frame"
                ]
            }, 
            "frame": {
                "requires": [
                    "base", 
                    "node", 
                    "selector-css3", 
                    "substitute"
                ]
            }, 
            "selection": {
                "requires": [
                    "node"
                ]
            }
        }
    }, 
    "escape": {}, 
    "event": {
        "after": "node-base", 
        "plugins": {
            "event-base-ie": {
                "after": [
                    "event-base"
                ], 
                "condition": {
                    "test": function(Y) {
    var imp = Y.config.doc && Y.config.doc.implementation;
    return (imp && (!imp.hasFeature('Events', '2.0')));
}, 
                    "trigger": "node-base"
                }, 
                "requires": [
                    "node-base"
                ]
            }, 
            "event-touch": {
                "requires": [
                    "node-base"
                ]
            }
        }, 
        "submodules": {
            "event-base": {
                "after": "node-base", 
                "requires": [
                    "event-custom-base"
                ]
            }, 
            "event-delegate": {
                "requires": [
                    "node-base"
                ]
            }, 
            "event-focus": {
                "requires": [
                    "event-synthetic"
                ]
            }, 
            "event-hover": {
                "requires": [
                    "event-synthetic"
                ]
            }, 
            "event-key": {
                "requires": [
                    "event-synthetic"
                ]
            }, 
            "event-mouseenter": {
                "requires": [
                    "event-synthetic"
                ]
            }, 
            "event-mousewheel": {
                "requires": [
                    "event-synthetic"
                ]
            }, 
            "event-resize": {
                "requires": [
                    "event-synthetic"
                ]
            }, 
            "event-synthetic": {
                "requires": [
                    "node-base", 
                    "event-custom-complex"
                ]
            }
        }
    }, 
    "event-custom": {
        "submodules": {
            "event-custom-base": {
                "requires": [
                    "oop"
                ]
            }, 
            "event-custom-complex": {
                "requires": [
                    "event-custom-base"
                ]
            }
        }
    }, 
    "event-gestures": {
        "submodules": {
            "event-flick": {
                "requires": [
                    "node-base", 
                    "event-touch", 
                    "event-synthetic"
                ]
            }, 
            "event-move": {
                "requires": [
                    "node-base", 
                    "event-touch", 
                    "event-synthetic"
                ]
            }
        }
    }, 
    "event-simulate": {
        "requires": [
            "event-base"
        ]
    }, 
    "event-valuechange": {
        "requires": [
            "event-focus", 
            "event-synthetic"
        ]
    }, 
    "highlight": {
        "submodules": {
            "highlight-accentfold": {
                "requires": [
                    "highlight-base", 
                    "text-accentfold"
                ]
            }, 
            "highlight-base": {
                "requires": [
                    "array-extras", 
                    "escape", 
                    "text-wordbreak"
                ]
            }
        }
    }, 
    "history": {
        "plugins": {
            "history-hash-ie": {
                "condition": {
                    "test": function (Y) {
    var docMode = Y.config.doc.documentMode;

    return Y.UA.ie && (!('onhashchange' in Y.config.win) ||
            !docMode || docMode < 8);
}, 
                    "trigger": "history-hash"
                }, 
                "requires": [
                    "history-hash", 
                    "node-base"
                ]
            }
        }, 
        "submodules": {
            "history-base": {
                "after": [
                    "history-deprecated"
                ], 
                "requires": [
                    "event-custom-complex"
                ]
            }, 
            "history-hash": {
                "after": [
                    "history-html5"
                ], 
                "requires": [
                    "event-synthetic", 
                    "history-base", 
                    "yui-later"
                ]
            }, 
            "history-html5": {
                "optional": [
                    "json"
                ], 
                "requires": [
                    "event-base", 
                    "history-base", 
                    "node-base"
                ]
            }
        }
    }, 
    "history-deprecated": {
        "requires": [
            "node"
        ]
    }, 
    "imageloader": {
        "requires": [
            "base-base", 
            "node-style", 
            "node-screen"
        ]
    }, 
    "intl": {
        "requires": [
            "intl-base", 
            "event-custom"
        ]
    }, 
    "io": {
        "submodules": {
            "io-base": {
                "optional": [
                    "querystring-stringify-simple"
                ], 
                "requires": [
                    "event-custom-base"
                ]
            }, 
            "io-form": {
                "requires": [
                    "io-base", 
                    "node-base", 
                    "node-style"
                ]
            }, 
            "io-queue": {
                "requires": [
                    "io-base", 
                    "queue-promote"
                ]
            }, 
            "io-upload-iframe": {
                "requires": [
                    "io-base", 
                    "node-base"
                ]
            }, 
            "io-xdr": {
                "requires": [
                    "io-base", 
                    "datatype-xml"
                ]
            }
        }
    }, 
    "json": {
        "submodules": {
            "json-parse": {
                "requires": [
                    "yui-base"
                ]
            }, 
            "json-stringify": {
                "requires": [
                    "yui-base"
                ]
            }
        }
    }, 
    "jsonp": {
        "plugins": {
            "jsonp-url": {
                "requires": [
                    "jsonp"
                ]
            }
        }, 
        "requires": [
            "get", 
            "oop"
        ]
    }, 
    "loader": {
        "submodules": {
            "loader-base": {
                "requires": [
                    "get"
                ]
            }, 
            "loader-rollup": {
                "requires": [
                    "loader-base"
                ]
            }, 
            "loader-yui3": {
                "requires": [
                    "loader-base"
                ]
            }
        }
    }, 
    "node": {
        "plugins": {
            "align-plugin": {
                "requires": [
                    "node-screen", 
                    "node-pluginhost"
                ]
            }, 
            "node-deprecated": {
                "requires": [
                    "node-base"
                ]
            }, 
            "node-event-simulate": {
                "requires": [
                    "node-base", 
                    "event-simulate"
                ]
            }, 
            "node-load": {
                "requires": [
                    "node-base", 
                    "io-base"
                ]
            }, 
            "shim-plugin": {
                "requires": [
                    "node-style", 
                    "node-pluginhost"
                ]
            }, 
            "transition": {
                "requires": [
                    "transition-native", 
                    "node-style"
                ]
            }, 
            "transition-native": {
                "requires": [
                    "node-base"
                ]
            }
        }, 
        "submodules": {
            "node-base": {
                "requires": [
                    "dom-base", 
                    "selector-css2", 
                    "event-base"
                ]
            }, 
            "node-event-delegate": {
                "requires": [
                    "node-base", 
                    "event-delegate"
                ]
            }, 
            "node-pluginhost": {
                "requires": [
                    "node-base", 
                    "pluginhost"
                ]
            }, 
            "node-screen": {
                "requires": [
                    "dom-screen", 
                    "node-base"
                ]
            }, 
            "node-style": {
                "requires": [
                    "dom-style", 
                    "node-base"
                ]
            }
        }
    }, 
    "node-flick": {
        "requires": [
            "classnamemanager", 
            "transition", 
            "event-flick", 
            "plugin"
        ], 
        "skinnable": true
    }, 
    "node-focusmanager": {
        "requires": [
            "attribute", 
            "node", 
            "plugin", 
            "node-event-simulate", 
            "event-key", 
            "event-focus"
        ]
    }, 
    "node-menunav": {
        "requires": [
            "node", 
            "classnamemanager", 
            "plugin", 
            "node-focusmanager"
        ], 
        "skinnable": true
    }, 
    "oop": {
        "requires": [
            "yui-base"
        ]
    }, 
    "overlay": {
        "requires": [
            "widget", 
            "widget-stdmod", 
            "widget-position", 
            "widget-position-align", 
            "widget-stack", 
            "widget-position-constrain"
        ], 
        "skinnable": true
    }, 
    "plugin": {
        "requires": [
            "base-base"
        ]
    }, 
    "pluginhost": {
        "submodules": {
            "pluginhost-base": {
                "requires": [
                    "yui-base"
                ]
            }, 
            "pluginhost-config": {
                "requires": [
                    "pluginhost-base"
                ]
            }
        }
    }, 
    "profiler": {
        "requires": [
            "yui-base"
        ]
    }, 
    "querystring": {
        "submodules": {
            "querystring-parse": {
                "requires": [
                    "yui-base", 
                    "array-extras"
                ]
            }, 
            "querystring-stringify": {
                "requires": [
                    "yui-base"
                ]
            }
        }
    }, 
    "querystring-parse-simple": {
        "path": "querystring/querystring-parse-simple-min.js", 
        "requires": [
            "yui-base"
        ]
    }, 
    "querystring-stringify-simple": {
        "path": "querystring/querystring-stringify-simple-min.js", 
        "requires": [
            "yui-base"
        ]
    }, 
    "queue-promote": {
        "requires": [
            "yui-base"
        ]
    }, 
    "queue-run": {
        "path": "async-queue/async-queue-min.js", 
        "requires": [
            "event-custom"
        ]
    }, 
    "recordset": {
        "submodules": {
            "recordset-base": {
                "requires": [
                    "base", 
                    "arraylist"
                ]
            }, 
            "recordset-filter": {
                "requires": [
                    "recordset-base", 
                    "array-extras", 
                    "plugin"
                ]
            }, 
            "recordset-indexer": {
                "requires": [
                    "recordset-base", 
                    "plugin"
                ]
            }, 
            "recordset-sort": {
                "requires": [
                    "arraysort", 
                    "recordset-base", 
                    "plugin"
                ]
            }
        }
    }, 
    "resize": {
        "submodules": {
            "resize-base": {
                "requires": [
                    "widget", 
                    "substitute", 
                    "event", 
                    "oop", 
                    "dd-drag", 
                    "dd-delegate", 
                    "dd-drop"
                ], 
                "skinnable": true
            }, 
            "resize-constrain": {
                "requires": [
                    "plugin", 
                    "resize-base"
                ]
            }, 
            "resize-proxy": {
                "requires": [
                    "plugin", 
                    "resize-base"
                ]
            }
        }
    }, 
    "scrollview": {
        "plugins": {
            "scrollview-base": {
                "path": "scrollview/scrollview-base-min.js", 
                "requires": [
                    "widget", 
                    "event-gestures", 
                    "transition"
                ], 
                "skinnable": true
            }, 
            "scrollview-base-ie": {
                "condition": {
                    "trigger": "scrollview-base", 
                    "ua": "ie"
                }, 
                "requires": [
                    "scrollview-base"
                ]
            }, 
            "scrollview-paginator": {
                "path": "scrollview/scrollview-paginator-min.js", 
                "requires": [
                    "plugin"
                ]
            }, 
            "scrollview-scrollbars": {
                "path": "scrollview/scrollview-scrollbars-min.js", 
                "requires": [
                    "plugin"
                ], 
                "skinnable": true
            }
        }, 
        "requires": [
            "scrollview-base", 
            "scrollview-scrollbars"
        ]
    }, 
    "slider": {
        "submodules": {
            "clickable-rail": {
                "requires": [
                    "slider-base"
                ]
            }, 
            "range-slider": {
                "requires": [
                    "slider-base", 
                    "slider-value-range", 
                    "clickable-rail"
                ]
            }, 
            "slider-base": {
                "requires": [
                    "widget", 
                    "dd-constrain", 
                    "substitute"
                ], 
                "skinnable": true
            }, 
            "slider-value-range": {
                "requires": [
                    "slider-base"
                ]
            }
        }
    }, 
    "sortable": {
        "plugins": {
            "sortable-scroll": {
                "requires": [
                    "dd-scroll"
                ]
            }
        }, 
        "requires": [
            "dd-delegate", 
            "dd-drop-plugin", 
            "dd-proxy"
        ]
    }, 
    "stylesheet": {
        "requires": [
            "yui-base"
        ]
    }, 
    "substitute": {
        "optional": [
            "dump"
        ]
    }, 
    "swf": {
        "requires": [
            "event-custom", 
            "node", 
            "swfdetect"
        ]
    }, 
    "swfdetect": {}, 
    "tabview": {
        "plugins": {
            "tabview-base": {
                "requires": [
                    "node-event-delegate", 
                    "classnamemanager", 
                    "skin-sam-tabview"
                ]
            }, 
            "tabview-plugin": {
                "requires": [
                    "tabview-base"
                ]
            }
        }, 
        "requires": [
            "widget", 
            "widget-parent", 
            "widget-child", 
            "tabview-base", 
            "node-pluginhost", 
            "node-focusmanager"
        ], 
        "skinnable": true
    }, 
    "test": {
        "requires": [
            "substitute", 
            "node", 
            "json", 
            "event-simulate"
        ], 
        "skinnable": true
    }, 
    "text": {
        "submodules": {
            "text-accentfold": {
                "requires": [
                    "array-extras", 
                    "text-data-accentfold"
                ]
            }, 
            "text-data-accentfold": {}, 
            "text-data-wordbreak": {}, 
            "text-wordbreak": {
                "requires": [
                    "array-extras", 
                    "text-data-wordbreak"
                ]
            }
        }
    }, 
    "transition": {
        "submodules": {
            "transition-native": {
                "requires": [
                    "node-base"
                ]
            }, 
            "transition-timer": {
                "requires": [
                    "transition-native", 
                    "node-style"
                ]
            }
        }
    }, 
    "uploader": {
        "requires": [
            "event-custom", 
            "node", 
            "base", 
            "swf"
        ]
    }, 
    "widget": {
        "plugins": {
            "widget-base-ie": {
                "condition": {
                    "trigger": "widget-base", 
                    "ua": "ie"
                }, 
                "requires": [
                    "widget-base"
                ]
            }, 
            "widget-child": {
                "requires": [
                    "base-build", 
                    "widget"
                ]
            }, 
            "widget-parent": {
                "requires": [
                    "base-build", 
                    "arraylist", 
                    "widget"
                ]
            }, 
            "widget-position": {
                "requires": [
                    "base-build", 
                    "node-screen", 
                    "widget"
                ]
            }, 
            "widget-position-align": {
                "requires": [
                    "widget-position"
                ]
            }, 
            "widget-position-constrain": {
                "requires": [
                    "widget-position"
                ]
            }, 
            "widget-stack": {
                "requires": [
                    "base-build", 
                    "widget"
                ], 
                "skinnable": true
            }, 
            "widget-stdmod": {
                "requires": [
                    "base-build", 
                    "widget"
                ]
            }
        }, 
        "skinnable": true, 
        "submodules": {
            "widget-base": {
                "requires": [
                    "attribute", 
                    "event-focus", 
                    "base-base", 
                    "base-pluginhost", 
                    "node-base", 
                    "node-style", 
                    "classnamemanager"
                ]
            }, 
            "widget-htmlparser": {
                "requires": [
                    "widget-base"
                ]
            }, 
            "widget-skin": {
                "requires": [
                    "widget-base"
                ]
            }, 
            "widget-uievents": {
                "requires": [
                    "widget-base", 
                    "node-event-delegate"
                ]
            }
        }
    }, 
    "widget-anim": {
        "requires": [
            "plugin", 
            "anim-base", 
            "widget"
        ]
    }, 
    "widget-locale": {
        "path": "widget/widget-locale-min.js", 
        "requires": [
            "widget-base"
        ]
    }, 
    "yql": {
        "requires": [
            "jsonp", 
            "jsonp-url"
        ]
    }, 
    "yui": {
        "submodules": {
            "features": {
                "requires": [
                    "yui-base"
                ]
            }, 
            "get": {
                "requires": [
                    "yui-base"
                ]
            }, 
            "intl-base": {
                "requires": [
                    "yui-base"
                ]
            }, 
            "rls": {
                "requires": [
                    "get", 
                    "features"
                ]
            }, 
            "yui-base": {}, 
            "yui-later": {
                "requires": [
                    "yui-base"
                ]
            }, 
            "yui-log": {
                "requires": [
                    "yui-base"
                ]
            }, 
            "yui-throttle": {
                "requires": [
                    "yui-base"
                ]
            }
        }
    }
};
YUI.Env[Y.version].md5 = 'faf08d27c01d7ab5575789a63b1e36fc';



}, '3.3.0' ,{requires:['loader-base']});



YUI.add('loader', function(Y){}, '3.3.0' ,{use:['loader-base', 'loader-rollup', 'loader-yui3' ]});

