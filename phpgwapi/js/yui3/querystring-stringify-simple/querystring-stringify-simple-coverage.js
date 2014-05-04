/*
YUI 3.16.0 (build 76f0e08)
Copyright 2014 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/

if (typeof __coverage__ === 'undefined') { __coverage__ = {}; }
if (!__coverage__['build/querystring-stringify-simple/querystring-stringify-simple.js']) {
   __coverage__['build/querystring-stringify-simple/querystring-stringify-simple.js'] = {"path":"build/querystring-stringify-simple/querystring-stringify-simple.js","s":{"1":0,"2":0,"3":0,"4":0,"5":0,"6":0,"7":0,"8":0,"9":0,"10":0,"11":0},"b":{"1":[0,0],"2":[0,0],"3":[0,0],"4":[0,0],"5":[0,0]},"f":{"1":0,"2":0},"fnMap":{"1":{"name":"(anonymous_1)","line":1,"loc":{"start":{"line":1,"column":40},"end":{"line":1,"column":59}}},"2":{"name":"(anonymous_2)","line":21,"loc":{"start":{"line":21,"column":24},"end":{"line":21,"column":42}}}},"statementMap":{"1":{"start":{"line":1,"column":0},"end":{"line":44,"column":41}},"2":{"start":{"line":17,"column":0},"end":{"line":18,"column":29}},"3":{"start":{"line":21,"column":0},"end":{"line":41,"column":2}},"4":{"start":{"line":22,"column":4},"end":{"line":25,"column":18}},"5":{"start":{"line":27,"column":4},"end":{"line":38,"column":5}},"6":{"start":{"line":28,"column":8},"end":{"line":37,"column":9}},"7":{"start":{"line":29,"column":12},"end":{"line":36,"column":13}},"8":{"start":{"line":30,"column":16},"end":{"line":32,"column":17}},"9":{"start":{"line":31,"column":20},"end":{"line":31,"column":80}},"10":{"start":{"line":35,"column":16},"end":{"line":35,"column":56}},"11":{"start":{"line":40,"column":4},"end":{"line":40,"column":24}}},"branchMap":{"1":{"line":24,"type":"cond-expr","locations":[{"start":{"line":24,"column":30},"end":{"line":24,"column":34}},{"start":{"line":24,"column":37},"end":{"line":24,"column":42}}]},"2":{"line":24,"type":"binary-expr","locations":[{"start":{"line":24,"column":12},"end":{"line":24,"column":13}},{"start":{"line":24,"column":17},"end":{"line":24,"column":27}}]},"3":{"line":28,"type":"if","locations":[{"start":{"line":28,"column":8},"end":{"line":28,"column":8}},{"start":{"line":28,"column":8},"end":{"line":28,"column":8}}]},"4":{"line":29,"type":"if","locations":[{"start":{"line":29,"column":12},"end":{"line":29,"column":12}},{"start":{"line":29,"column":12},"end":{"line":29,"column":12}}]},"5":{"line":31,"type":"cond-expr","locations":[{"start":{"line":31,"column":36},"end":{"line":31,"column":46}},{"start":{"line":31,"column":49},"end":{"line":31,"column":52}}]}},"code":["(function () { YUI.add('querystring-stringify-simple', function (Y, NAME) {","","/*global Y */","/**"," * <p>Provides Y.QueryString.stringify method for converting objects to Query Strings."," * This is a subset implementation of the full querystring-stringify.</p>"," * <p>This module provides the bare minimum functionality (encoding a hash of simple values),"," * without the additional support for nested data structures.  Every key-value pair is"," * encoded by encodeURIComponent.</p>"," * <p>This module provides a minimalistic way for io to handle  single-level objects"," * as transaction data.</p>"," *"," * @module querystring"," * @submodule querystring-stringify-simple"," */","","var QueryString = Y.namespace(\"QueryString\"),","    EUC = encodeURIComponent;","","","QueryString.stringify = function (obj, c) {","    var qs = [],","        // Default behavior is false; standard key notation.","        s = c && c.arrayKey ? true : false,","        key, i, l;","","    for (key in obj) {","        if (obj.hasOwnProperty(key)) {","            if (Y.Lang.isArray(obj[key])) {","                for (i = 0, l = obj[key].length; i < l; i++) {","                    qs.push(EUC(s ? key + '[]' : key) + '=' + EUC(obj[key][i]));","                }","            }","            else {","                qs.push(EUC(key) + '=' + EUC(obj[key]));","            }","        }","    }","","    return qs.join('&');","};","","","}, '3.16.0', {\"requires\": [\"yui-base\"]});","","}());"]};
}
var __cov_zpIEHgyi_8LAMi_AnAL3Vg = __coverage__['build/querystring-stringify-simple/querystring-stringify-simple.js'];
__cov_zpIEHgyi_8LAMi_AnAL3Vg.s['1']++;YUI.add('querystring-stringify-simple',function(Y,NAME){__cov_zpIEHgyi_8LAMi_AnAL3Vg.f['1']++;__cov_zpIEHgyi_8LAMi_AnAL3Vg.s['2']++;var QueryString=Y.namespace('QueryString'),EUC=encodeURIComponent;__cov_zpIEHgyi_8LAMi_AnAL3Vg.s['3']++;QueryString.stringify=function(obj,c){__cov_zpIEHgyi_8LAMi_AnAL3Vg.f['2']++;__cov_zpIEHgyi_8LAMi_AnAL3Vg.s['4']++;var qs=[],s=(__cov_zpIEHgyi_8LAMi_AnAL3Vg.b['2'][0]++,c)&&(__cov_zpIEHgyi_8LAMi_AnAL3Vg.b['2'][1]++,c.arrayKey)?(__cov_zpIEHgyi_8LAMi_AnAL3Vg.b['1'][0]++,true):(__cov_zpIEHgyi_8LAMi_AnAL3Vg.b['1'][1]++,false),key,i,l;__cov_zpIEHgyi_8LAMi_AnAL3Vg.s['5']++;for(key in obj){__cov_zpIEHgyi_8LAMi_AnAL3Vg.s['6']++;if(obj.hasOwnProperty(key)){__cov_zpIEHgyi_8LAMi_AnAL3Vg.b['3'][0]++;__cov_zpIEHgyi_8LAMi_AnAL3Vg.s['7']++;if(Y.Lang.isArray(obj[key])){__cov_zpIEHgyi_8LAMi_AnAL3Vg.b['4'][0]++;__cov_zpIEHgyi_8LAMi_AnAL3Vg.s['8']++;for(i=0,l=obj[key].length;i<l;i++){__cov_zpIEHgyi_8LAMi_AnAL3Vg.s['9']++;qs.push(EUC(s?(__cov_zpIEHgyi_8LAMi_AnAL3Vg.b['5'][0]++,key+'[]'):(__cov_zpIEHgyi_8LAMi_AnAL3Vg.b['5'][1]++,key))+'='+EUC(obj[key][i]));}}else{__cov_zpIEHgyi_8LAMi_AnAL3Vg.b['4'][1]++;__cov_zpIEHgyi_8LAMi_AnAL3Vg.s['10']++;qs.push(EUC(key)+'='+EUC(obj[key]));}}else{__cov_zpIEHgyi_8LAMi_AnAL3Vg.b['3'][1]++;}}__cov_zpIEHgyi_8LAMi_AnAL3Vg.s['11']++;return qs.join('&');};},'3.16.0',{'requires':['yui-base']});
