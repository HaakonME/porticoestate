/*
YUI 3.16.0 (build 76f0e08)
Copyright 2014 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/

if (typeof __coverage__ === 'undefined') { __coverage__ = {}; }
if (!__coverage__['build/classnamemanager/classnamemanager.js']) {
   __coverage__['build/classnamemanager/classnamemanager.js'] = {"path":"build/classnamemanager/classnamemanager.js","s":{"1":0,"2":0,"3":0,"4":0,"5":0,"6":0,"7":0,"8":0,"9":0,"10":0,"11":0,"12":0},"b":{"1":[0,0],"2":[0,0],"3":[0,0]},"f":{"1":0,"2":0,"3":0},"fnMap":{"1":{"name":"(anonymous_1)","line":1,"loc":{"start":{"line":1,"column":28},"end":{"line":1,"column":47}}},"2":{"name":"(anonymous_2)","line":49,"loc":{"start":{"line":49,"column":21},"end":{"line":49,"column":33}}},"3":{"name":"(anonymous_3)","line":66,"loc":{"start":{"line":66,"column":25},"end":{"line":66,"column":37}}}},"statementMap":{"1":{"start":{"line":1,"column":0},"end":{"line":84,"column":41}},"2":{"start":{"line":22,"column":0},"end":{"line":24,"column":22}},"3":{"start":{"line":36,"column":0},"end":{"line":36,"column":64}},"4":{"start":{"line":47,"column":0},"end":{"line":47,"column":67}},"5":{"start":{"line":49,"column":0},"end":{"line":81,"column":4}},"6":{"start":{"line":51,"column":1},"end":{"line":52,"column":44}},"7":{"start":{"line":54,"column":1},"end":{"line":79,"column":3}},"8":{"start":{"line":68,"column":12},"end":{"line":68,"column":42}},"9":{"start":{"line":70,"column":12},"end":{"line":74,"column":13}},"10":{"start":{"line":71,"column":16},"end":{"line":71,"column":38}},"11":{"start":{"line":73,"column":16},"end":{"line":73,"column":27}},"12":{"start":{"line":76,"column":3},"end":{"line":76,"column":32}}},"branchMap":{"1":{"line":36,"type":"binary-expr","locations":[{"start":{"line":36,"column":28},"end":{"line":36,"column":53}},{"start":{"line":36,"column":57},"end":{"line":36,"column":63}}]},"2":{"line":47,"type":"binary-expr","locations":[{"start":{"line":47,"column":31},"end":{"line":47,"column":59}},{"start":{"line":47,"column":63},"end":{"line":47,"column":66}}]},"3":{"line":70,"type":"if","locations":[{"start":{"line":70,"column":12},"end":{"line":70,"column":12}},{"start":{"line":70,"column":12},"end":{"line":70,"column":12}}]}},"code":["(function () { YUI.add('classnamemanager', function (Y, NAME) {","","/**","* Contains a singleton (ClassNameManager) that enables easy creation and caching of","* prefixed class names.","* @module classnamemanager","*/","","/**"," * A singleton class providing:"," *"," * <ul>"," *    <li>Easy creation of prefixed class names</li>"," *    <li>Caching of previously created class names for improved performance.</li>"," * </ul>"," *"," * @class ClassNameManager"," * @static"," */","","// String constants","var CLASS_NAME_PREFIX = 'classNamePrefix',","\tCLASS_NAME_DELIMITER = 'classNameDelimiter',","    CONFIG = Y.config;","","// Global config","","/**"," * Configuration property indicating the prefix for all CSS class names in this YUI instance."," *"," * @property classNamePrefix"," * @type {String}"," * @default \"yui\""," * @static"," */","CONFIG[CLASS_NAME_PREFIX] = CONFIG[CLASS_NAME_PREFIX] || 'yui3';","","/**"," * Configuration property indicating the delimiter used to compose all CSS class names in"," * this YUI instance."," *"," * @property classNameDelimiter"," * @type {String}"," * @default \"-\""," * @static"," */","CONFIG[CLASS_NAME_DELIMITER] = CONFIG[CLASS_NAME_DELIMITER] || '-';","","Y.ClassNameManager = function () {","","\tvar sPrefix    = CONFIG[CLASS_NAME_PREFIX],","\t\tsDelimiter = CONFIG[CLASS_NAME_DELIMITER];","","\treturn {","","\t\t/**","\t\t * Returns a class name prefixed with the value of the","\t\t * <code>Y.config.classNamePrefix</code> attribute + the provided strings.","\t\t * Uses the <code>Y.config.classNameDelimiter</code> attribute to delimit the","\t\t * provided strings. E.g. Y.ClassNameManager.getClassName('foo','bar'); // yui-foo-bar","\t\t *","\t\t * @method getClassName","\t\t * @param {String} [classnameSection*] one or more classname sections to be joined","\t\t * @param {Boolean} skipPrefix If set to true, the classname will not be prefixed with the default Y.config.classNameDelimiter value.","\t\t */","\t\tgetClassName: Y.cached(function () {","","            var args = Y.Array(arguments);","","            if (args[args.length-1] !== true) {","                args.unshift(sPrefix);","            } else {","                args.pop();","            }","","\t\t\treturn args.join(sDelimiter);","\t\t})","","\t};","","}();","","","}, '3.16.0', {\"requires\": [\"yui-base\"]});","","}());"]};
}
var __cov_ER6FT_nrhVfJNEFu65EmIA = __coverage__['build/classnamemanager/classnamemanager.js'];
__cov_ER6FT_nrhVfJNEFu65EmIA.s['1']++;YUI.add('classnamemanager',function(Y,NAME){__cov_ER6FT_nrhVfJNEFu65EmIA.f['1']++;__cov_ER6FT_nrhVfJNEFu65EmIA.s['2']++;var CLASS_NAME_PREFIX='classNamePrefix',CLASS_NAME_DELIMITER='classNameDelimiter',CONFIG=Y.config;__cov_ER6FT_nrhVfJNEFu65EmIA.s['3']++;CONFIG[CLASS_NAME_PREFIX]=(__cov_ER6FT_nrhVfJNEFu65EmIA.b['1'][0]++,CONFIG[CLASS_NAME_PREFIX])||(__cov_ER6FT_nrhVfJNEFu65EmIA.b['1'][1]++,'yui3');__cov_ER6FT_nrhVfJNEFu65EmIA.s['4']++;CONFIG[CLASS_NAME_DELIMITER]=(__cov_ER6FT_nrhVfJNEFu65EmIA.b['2'][0]++,CONFIG[CLASS_NAME_DELIMITER])||(__cov_ER6FT_nrhVfJNEFu65EmIA.b['2'][1]++,'-');__cov_ER6FT_nrhVfJNEFu65EmIA.s['5']++;Y.ClassNameManager=function(){__cov_ER6FT_nrhVfJNEFu65EmIA.f['2']++;__cov_ER6FT_nrhVfJNEFu65EmIA.s['6']++;var sPrefix=CONFIG[CLASS_NAME_PREFIX],sDelimiter=CONFIG[CLASS_NAME_DELIMITER];__cov_ER6FT_nrhVfJNEFu65EmIA.s['7']++;return{getClassName:Y.cached(function(){__cov_ER6FT_nrhVfJNEFu65EmIA.f['3']++;__cov_ER6FT_nrhVfJNEFu65EmIA.s['8']++;var args=Y.Array(arguments);__cov_ER6FT_nrhVfJNEFu65EmIA.s['9']++;if(args[args.length-1]!==true){__cov_ER6FT_nrhVfJNEFu65EmIA.b['3'][0]++;__cov_ER6FT_nrhVfJNEFu65EmIA.s['10']++;args.unshift(sPrefix);}else{__cov_ER6FT_nrhVfJNEFu65EmIA.b['3'][1]++;__cov_ER6FT_nrhVfJNEFu65EmIA.s['11']++;args.pop();}__cov_ER6FT_nrhVfJNEFu65EmIA.s['12']++;return args.join(sDelimiter);})};}();},'3.16.0',{'requires':['yui-base']});
