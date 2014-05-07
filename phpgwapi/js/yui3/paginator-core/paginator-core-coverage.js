/*
YUI 3.16.0 (build 76f0e08)
Copyright 2014 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/

if (typeof __coverage__ === 'undefined') { __coverage__ = {}; }
if (!__coverage__['build/paginator-core/paginator-core.js']) {
   __coverage__['build/paginator-core/paginator-core.js'] = {"path":"build/paginator-core/paginator-core.js","s":{"1":0,"2":0,"3":0,"4":0,"5":0,"6":0,"7":0,"8":0,"9":0,"10":0,"11":0,"12":0,"13":0,"14":0},"b":{"1":[0,0],"2":[0,0],"3":[0,0],"4":[0,0]},"f":{"1":0,"2":0,"3":0,"4":0,"5":0,"6":0,"7":0},"fnMap":{"1":{"name":"(anonymous_1)","line":1,"loc":{"start":{"line":1,"column":26},"end":{"line":1,"column":45}}},"2":{"name":"(anonymous_2)","line":25,"loc":{"start":{"line":25,"column":52},"end":{"line":25,"column":64}}},"3":{"name":"(anonymous_3)","line":81,"loc":{"start":{"line":81,"column":14},"end":{"line":81,"column":26}}},"4":{"name":"(anonymous_4)","line":95,"loc":{"start":{"line":95,"column":14},"end":{"line":95,"column":26}}},"5":{"name":"(anonymous_5)","line":109,"loc":{"start":{"line":109,"column":17},"end":{"line":109,"column":29}}},"6":{"name":"(anonymous_6)","line":121,"loc":{"start":{"line":121,"column":17},"end":{"line":121,"column":29}}},"7":{"name":"(anonymous_7)","line":137,"loc":{"start":{"line":137,"column":22},"end":{"line":137,"column":34}}}},"statementMap":{"1":{"start":{"line":1,"column":0},"end":{"line":146,"column":37}},"2":{"start":{"line":25,"column":0},"end":{"line":25,"column":67}},"3":{"start":{"line":27,"column":0},"end":{"line":73,"column":2}},"4":{"start":{"line":75,"column":0},"end":{"line":142,"column":3}},"5":{"start":{"line":82,"column":8},"end":{"line":84,"column":9}},"6":{"start":{"line":83,"column":12},"end":{"line":83,"column":51}},"7":{"start":{"line":86,"column":8},"end":{"line":86,"column":20}},"8":{"start":{"line":96,"column":8},"end":{"line":98,"column":9}},"9":{"start":{"line":97,"column":12},"end":{"line":97,"column":51}},"10":{"start":{"line":100,"column":8},"end":{"line":100,"column":20}},"11":{"start":{"line":110,"column":8},"end":{"line":110,"column":36}},"12":{"start":{"line":122,"column":8},"end":{"line":122,"column":86}},"13":{"start":{"line":138,"column":8},"end":{"line":138,"column":52}},"14":{"start":{"line":140,"column":8},"end":{"line":140,"column":89}}},"branchMap":{"1":{"line":82,"type":"if","locations":[{"start":{"line":82,"column":8},"end":{"line":82,"column":8}},{"start":{"line":82,"column":8},"end":{"line":82,"column":8}}]},"2":{"line":96,"type":"if","locations":[{"start":{"line":96,"column":8},"end":{"line":96,"column":8}},{"start":{"line":96,"column":8},"end":{"line":96,"column":8}}]},"3":{"line":122,"type":"binary-expr","locations":[{"start":{"line":122,"column":16},"end":{"line":122,"column":39}},{"start":{"line":122,"column":43},"end":{"line":122,"column":84}}]},"4":{"line":140,"type":"cond-expr","locations":[{"start":{"line":140,"column":36},"end":{"line":140,"column":37}},{"start":{"line":140,"column":40},"end":{"line":140,"column":88}}]}},"code":["(function () { YUI.add('paginator-core', function (Y, NAME) {","","/**"," Paginator's core functionality consists of keeping track of the current page"," being displayed and providing information for previous and next pages.",""," @module paginator"," @submodule paginator-core"," @since 3.11.0"," */","","/**"," _API docs for this extension are included in the Paginator class._",""," Class extension providing the core API and structure for the Paginator module.",""," Use this class extension with Widget or another Base-based superclass to"," create the basic Paginator model API and composing class structure.",""," @class Paginator.Core"," @for Paginator"," @since 3.11.0"," */","","var PaginatorCore = Y.namespace('Paginator').Core = function () {};","","PaginatorCore.ATTRS = {","    /**","     Current page count. First page is 1.","","     @attribute page","     @type Number","     @default 1","     **/","    page: {","        value: 1","    },","","    /**","     Total number of pages to display","","     @readOnly","     @attribute totalPages","     @type Number","     **/","    totalPages: {","        readOnly: true,","        getter: '_getTotalPagesFn'","    },","","    /**","     Maximum number of items per page. A value of negative one (-1) indicates","         all items on one page.","","     @attribute itemsPerPage","     @type Number","     @default 10","     **/","    itemsPerPage: {","        value: 10","    },","","    /**","     Total number of items in all pages.","","     @attribute totalItems","     @type Number","     @default 0","     **/","    totalItems: {","        value: 0","    }","};","","Y.mix(PaginatorCore.prototype, {","    /**","     Sets the page to the previous page in the set, if there is a previous page.","     @method prevPage","     @chainable","     */","    prevPage: function () {","        if (this.hasPrevPage()) {","            this.set('page', this.get('page') - 1);","        }","","        return this;","    },","","    /**","     Sets the page to the next page in the set, if there is a next page.","","     @method nextPage","     @chainable","     */","    nextPage: function () {","        if (this.hasNextPage()) {","            this.set('page', this.get('page') + 1);","        }","","        return this;","    },","","    /**","     Returns True if there is a previous page in the set.","","     @method hasPrevPage","     @return {Boolean} `true` if there is a previous page, `false` otherwise.","     */","    hasPrevPage: function () {","        return this.get('page') > 1;","    },","","    /**","     Returns True if there is a next page in the set.","","     If totalItems isn't set, assume there is always next page.","","     @method hasNextPage","     @return {Boolean} `true` if there is a next page, `false` otherwise.","     */","    hasNextPage: function () {","        return (!this.get('totalItems') || this.get('page') < this.get('totalPages'));","    },","","","    //--- P R O T E C T E D","","    /**","     Returns the total number of pages based on the total number of","       items provided and the number of items per page","","     @protected","     @method _getTotalPagesFn","     @return {Number} Total number of pages based on total number of items and","       items per page or one if itemsPerPage is less than one","     */","    _getTotalPagesFn: function () {","        var itemsPerPage = this.get('itemsPerPage');","","        return (itemsPerPage < 1) ? 1 : Math.ceil(this.get('totalItems') / itemsPerPage);","    }","});","","","","}, '3.16.0', {\"requires\": [\"base\"]});","","}());"]};
}
var __cov_s65kVCjUVYd4xLZimuejTQ = __coverage__['build/paginator-core/paginator-core.js'];
__cov_s65kVCjUVYd4xLZimuejTQ.s['1']++;YUI.add('paginator-core',function(Y,NAME){__cov_s65kVCjUVYd4xLZimuejTQ.f['1']++;__cov_s65kVCjUVYd4xLZimuejTQ.s['2']++;var PaginatorCore=Y.namespace('Paginator').Core=function(){__cov_s65kVCjUVYd4xLZimuejTQ.f['2']++;};__cov_s65kVCjUVYd4xLZimuejTQ.s['3']++;PaginatorCore.ATTRS={page:{value:1},totalPages:{readOnly:true,getter:'_getTotalPagesFn'},itemsPerPage:{value:10},totalItems:{value:0}};__cov_s65kVCjUVYd4xLZimuejTQ.s['4']++;Y.mix(PaginatorCore.prototype,{prevPage:function(){__cov_s65kVCjUVYd4xLZimuejTQ.f['3']++;__cov_s65kVCjUVYd4xLZimuejTQ.s['5']++;if(this.hasPrevPage()){__cov_s65kVCjUVYd4xLZimuejTQ.b['1'][0]++;__cov_s65kVCjUVYd4xLZimuejTQ.s['6']++;this.set('page',this.get('page')-1);}else{__cov_s65kVCjUVYd4xLZimuejTQ.b['1'][1]++;}__cov_s65kVCjUVYd4xLZimuejTQ.s['7']++;return this;},nextPage:function(){__cov_s65kVCjUVYd4xLZimuejTQ.f['4']++;__cov_s65kVCjUVYd4xLZimuejTQ.s['8']++;if(this.hasNextPage()){__cov_s65kVCjUVYd4xLZimuejTQ.b['2'][0]++;__cov_s65kVCjUVYd4xLZimuejTQ.s['9']++;this.set('page',this.get('page')+1);}else{__cov_s65kVCjUVYd4xLZimuejTQ.b['2'][1]++;}__cov_s65kVCjUVYd4xLZimuejTQ.s['10']++;return this;},hasPrevPage:function(){__cov_s65kVCjUVYd4xLZimuejTQ.f['5']++;__cov_s65kVCjUVYd4xLZimuejTQ.s['11']++;return this.get('page')>1;},hasNextPage:function(){__cov_s65kVCjUVYd4xLZimuejTQ.f['6']++;__cov_s65kVCjUVYd4xLZimuejTQ.s['12']++;return(__cov_s65kVCjUVYd4xLZimuejTQ.b['3'][0]++,!this.get('totalItems'))||(__cov_s65kVCjUVYd4xLZimuejTQ.b['3'][1]++,this.get('page')<this.get('totalPages'));},_getTotalPagesFn:function(){__cov_s65kVCjUVYd4xLZimuejTQ.f['7']++;__cov_s65kVCjUVYd4xLZimuejTQ.s['13']++;var itemsPerPage=this.get('itemsPerPage');__cov_s65kVCjUVYd4xLZimuejTQ.s['14']++;return itemsPerPage<1?(__cov_s65kVCjUVYd4xLZimuejTQ.b['4'][0]++,1):(__cov_s65kVCjUVYd4xLZimuejTQ.b['4'][1]++,Math.ceil(this.get('totalItems')/ itemsPerPage));}});},'3.16.0',{'requires':['base']});
