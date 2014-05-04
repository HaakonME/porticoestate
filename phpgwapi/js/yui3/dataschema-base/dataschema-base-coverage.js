/*
YUI 3.16.0 (build 76f0e08)
Copyright 2014 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/
    
if (typeof __coverage__ === 'undefined') { __coverage__ = {}; }
if (!__coverage__['build/dataschema-base/dataschema-base.js']) {
   __coverage__['build/dataschema-base/dataschema-base.js'] = {"path":"build/dataschema-base/dataschema-base.js","s":{"1":0,"2":0,"3":0,"4":0,"5":0,"6":0,"7":0,"8":0,"9":0,"10":0},"b":{"1":[0,0],"2":[0,0],"3":[0,0]},"f":{"1":0,"2":0,"3":0},"fnMap":{"1":{"name":"(anonymous_1)","line":1,"loc":{"start":{"line":1,"column":27},"end":{"line":1,"column":46}}},"2":{"name":"(anonymous_2)","line":36,"loc":{"start":{"line":36,"column":11},"end":{"line":36,"column":34}}},"3":{"name":"(anonymous_3)","line":48,"loc":{"start":{"line":48,"column":11},"end":{"line":48,"column":34}}}},"statementMap":{"1":{"start":{"line":1,"column":0},"end":{"line":66,"column":37}},"2":{"start":{"line":20,"column":0},"end":{"line":60,"column":2}},"3":{"start":{"line":37,"column":8},"end":{"line":37,"column":20}},"4":{"start":{"line":49,"column":8},"end":{"line":57,"column":9}},"5":{"start":{"line":50,"column":12},"end":{"line":51,"column":54}},"6":{"start":{"line":52,"column":12},"end":{"line":56,"column":13}},"7":{"start":{"line":53,"column":16},"end":{"line":53,"column":49}},"8":{"start":{"line":58,"column":8},"end":{"line":58,"column":21}},"9":{"start":{"line":62,"column":0},"end":{"line":62,"column":44}},"10":{"start":{"line":63,"column":0},"end":{"line":63,"column":23}}},"branchMap":{"1":{"line":49,"type":"if","locations":[{"start":{"line":49,"column":8},"end":{"line":49,"column":8}},{"start":{"line":49,"column":8},"end":{"line":49,"column":8}}]},"2":{"line":50,"type":"cond-expr","locations":[{"start":{"line":51,"column":12},"end":{"line":51,"column":24}},{"start":{"line":51,"column":27},"end":{"line":51,"column":53}}]},"3":{"line":52,"type":"if","locations":[{"start":{"line":52,"column":12},"end":{"line":52,"column":12}},{"start":{"line":52,"column":12},"end":{"line":52,"column":12}}]}},"code":["(function () { YUI.add('dataschema-base', function (Y, NAME) {","","/**"," * The DataSchema utility provides a common configurable interface for widgets to"," * apply a given schema to a variety of data."," *"," * @module dataschema"," * @main dataschema"," */","","/**"," * Provides the base DataSchema implementation, which can be extended to"," * create DataSchemas for specific data formats, such XML, JSON, text and"," * arrays."," *"," * @module dataschema"," * @submodule dataschema-base"," */","","var LANG = Y.Lang,","/**"," * Base class for the YUI DataSchema Utility."," * @class DataSchema.Base"," * @static"," */","    SchemaBase = {","    /**","     * Overridable method returns data as-is.","     *","     * @method apply","     * @param schema {Object} Schema to apply.","     * @param data {Object} Data.","     * @return {Object} Schema-parsed data.","     * @static","     */","    apply: function(schema, data) {","        return data;","    },","","    /**","     * Applies field parser, if defined","     *","     * @method parse","     * @param value {Object} Original value.","     * @param field {Object} Field.","     * @return {Object} Type-converted value.","     */","    parse: function(value, field) {","        if(field.parser) {","            var parser = (LANG.isFunction(field.parser)) ?","            field.parser : Y.Parsers[field.parser+''];","            if(parser) {","                value = parser.call(this, value);","            }","            else {","            }","        }","        return value;","    }","};","","Y.namespace(\"DataSchema\").Base = SchemaBase;","Y.namespace(\"Parsers\");","","","}, '3.16.0', {\"requires\": [\"base\"]});","","}());"]};
}
var __cov_ogg_CNcIcpOXPnOEm1joVQ = __coverage__['build/dataschema-base/dataschema-base.js'];
__cov_ogg_CNcIcpOXPnOEm1joVQ.s['1']++;YUI.add('dataschema-base',function(Y,NAME){__cov_ogg_CNcIcpOXPnOEm1joVQ.f['1']++;__cov_ogg_CNcIcpOXPnOEm1joVQ.s['2']++;var LANG=Y.Lang,SchemaBase={apply:function(schema,data){__cov_ogg_CNcIcpOXPnOEm1joVQ.f['2']++;__cov_ogg_CNcIcpOXPnOEm1joVQ.s['3']++;return data;},parse:function(value,field){__cov_ogg_CNcIcpOXPnOEm1joVQ.f['3']++;__cov_ogg_CNcIcpOXPnOEm1joVQ.s['4']++;if(field.parser){__cov_ogg_CNcIcpOXPnOEm1joVQ.b['1'][0]++;__cov_ogg_CNcIcpOXPnOEm1joVQ.s['5']++;var parser=LANG.isFunction(field.parser)?(__cov_ogg_CNcIcpOXPnOEm1joVQ.b['2'][0]++,field.parser):(__cov_ogg_CNcIcpOXPnOEm1joVQ.b['2'][1]++,Y.Parsers[field.parser+'']);__cov_ogg_CNcIcpOXPnOEm1joVQ.s['6']++;if(parser){__cov_ogg_CNcIcpOXPnOEm1joVQ.b['3'][0]++;__cov_ogg_CNcIcpOXPnOEm1joVQ.s['7']++;value=parser.call(this,value);}else{__cov_ogg_CNcIcpOXPnOEm1joVQ.b['3'][1]++;}}else{__cov_ogg_CNcIcpOXPnOEm1joVQ.b['1'][1]++;}__cov_ogg_CNcIcpOXPnOEm1joVQ.s['8']++;return value;}};__cov_ogg_CNcIcpOXPnOEm1joVQ.s['9']++;Y.namespace('DataSchema').Base=SchemaBase;__cov_ogg_CNcIcpOXPnOEm1joVQ.s['10']++;Y.namespace('Parsers');},'3.16.0',{'requires':['base']});
