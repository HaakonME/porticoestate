/*
YUI 3.16.0 (build 76f0e08)
Copyright 2014 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/

if (typeof __coverage__ === 'undefined') { __coverage__ = {}; }
if (!__coverage__['build/paginator-url/paginator-url.js']) {
   __coverage__['build/paginator-url/paginator-url.js'] = {"path":"build/paginator-url/paginator-url.js","s":{"1":0,"2":0,"3":0,"4":0,"5":0,"6":0,"7":0,"8":0,"9":0,"10":0,"11":0,"12":0},"b":{"1":[0,0,0],"2":[0,0,0],"3":[0,0],"4":[0,0]},"f":{"1":0,"2":0,"3":0,"4":0,"5":0},"fnMap":{"1":{"name":"(anonymous_1)","line":1,"loc":{"start":{"line":1,"column":25},"end":{"line":1,"column":44}}},"2":{"name":"PaginatorUrl","line":12,"loc":{"start":{"line":12,"column":0},"end":{"line":12,"column":25}}},"3":{"name":"(anonymous_3)","line":32,"loc":{"start":{"line":32,"column":17},"end":{"line":32,"column":29}}},"4":{"name":"(anonymous_4)","line":41,"loc":{"start":{"line":41,"column":17},"end":{"line":41,"column":29}}},"5":{"name":"(anonymous_5)","line":51,"loc":{"start":{"line":51,"column":19},"end":{"line":51,"column":35}}}},"statementMap":{"1":{"start":{"line":1,"column":0},"end":{"line":67,"column":42}},"2":{"start":{"line":12,"column":0},"end":{"line":12,"column":27}},"3":{"start":{"line":14,"column":0},"end":{"line":24,"column":2}},"4":{"start":{"line":26,"column":0},"end":{"line":60,"column":2}},"5":{"start":{"line":33,"column":8},"end":{"line":33,"column":88}},"6":{"start":{"line":42,"column":8},"end":{"line":42,"column":88}},"7":{"start":{"line":52,"column":8},"end":{"line":52,"column":42}},"8":{"start":{"line":53,"column":8},"end":{"line":57,"column":9}},"9":{"start":{"line":54,"column":12},"end":{"line":56,"column":15}},"10":{"start":{"line":58,"column":8},"end":{"line":58,"column":20}},"11":{"start":{"line":62,"column":0},"end":{"line":62,"column":44}},"12":{"start":{"line":64,"column":0},"end":{"line":64,"column":40}}},"branchMap":{"1":{"line":33,"type":"binary-expr","locations":[{"start":{"line":33,"column":16},"end":{"line":33,"column":34}},{"start":{"line":33,"column":38},"end":{"line":33,"column":78}},{"start":{"line":33,"column":83},"end":{"line":33,"column":87}}]},"2":{"line":42,"type":"binary-expr","locations":[{"start":{"line":42,"column":16},"end":{"line":42,"column":34}},{"start":{"line":42,"column":38},"end":{"line":42,"column":78}},{"start":{"line":42,"column":83},"end":{"line":42,"column":87}}]},"3":{"line":53,"type":"if","locations":[{"start":{"line":53,"column":8},"end":{"line":53,"column":8}},{"start":{"line":53,"column":8},"end":{"line":53,"column":8}}]},"4":{"line":55,"type":"binary-expr","locations":[{"start":{"line":55,"column":22},"end":{"line":55,"column":26}},{"start":{"line":55,"column":30},"end":{"line":55,"column":46}}]}},"code":["(function () { YUI.add('paginator-url', function (Y, NAME) {","","/**"," Adds in URL options for paginator links.",""," @module paginator"," @submodule paginator-url"," @class Paginator.Url"," @since 3.10.0"," */","","function PaginatorUrl () {}","","PaginatorUrl.ATTRS = {","    /**","    URL to return formatted with the page number. URL uses `Y.Lang.sub` for page number stubstitutions.","","    For example, if the page number is `3`, setting the `pageUrl` to `\"?pg={page}\"`, will result in `?pg=3`","","    @attribute pageUrl","    @type String","    **/","    pageUrl: {}","};","","PaginatorUrl.prototype = {","    /**","     Returns a formated URL for the previous page.","     @method prevPageUrl","     @return {String | null} Formatted URL for the previous page, or `null` if there is no previous page.","     */","    prevPageUrl: function () {","        return (this.hasPrevPage() && this.formatPageUrl(this.get('page') - 1)) || null;","    },","","    /**","     Returns a formated URL for the next page.","     @method nextPageUrl","     @return {String | null} Formatted URL for the next page or `null` if there is no next page.","     */","    nextPageUrl: function () {","        return (this.hasNextPage() && this.formatPageUrl(this.get('page') + 1)) || null;","    },","","    /**","     Returns a formated URL for the provided page number.","     @method formatPageUrl","     @param {Number} [page] Page value to be used in the formatted URL. If empty, page will be the value of the `page` ATTRS.","     @return {String | null} Formatted URL for the page or `null` if there is not a `pageUrl` set.","     */","    formatPageUrl: function (page) {","        var pageUrl = this.get('pageUrl');","        if (pageUrl) {","            return Y.Lang.sub(pageUrl, {","                page: page || this.get('page')","            });","        }","        return null;","    }","};","","Y.namespace('Paginator').Url = PaginatorUrl;","","Y.Base.mix(Y.Paginator, [PaginatorUrl]);","","","}, '3.16.0', {\"requires\": [\"paginator\"]});","","}());"]};
}
var __cov_Qqnavx_mTqPXxV_VZJBMtQ = __coverage__['build/paginator-url/paginator-url.js'];
__cov_Qqnavx_mTqPXxV_VZJBMtQ.s['1']++;YUI.add('paginator-url',function(Y,NAME){__cov_Qqnavx_mTqPXxV_VZJBMtQ.f['1']++;__cov_Qqnavx_mTqPXxV_VZJBMtQ.s['2']++;function PaginatorUrl(){__cov_Qqnavx_mTqPXxV_VZJBMtQ.f['2']++;}__cov_Qqnavx_mTqPXxV_VZJBMtQ.s['3']++;PaginatorUrl.ATTRS={pageUrl:{}};__cov_Qqnavx_mTqPXxV_VZJBMtQ.s['4']++;PaginatorUrl.prototype={prevPageUrl:function(){__cov_Qqnavx_mTqPXxV_VZJBMtQ.f['3']++;__cov_Qqnavx_mTqPXxV_VZJBMtQ.s['5']++;return(__cov_Qqnavx_mTqPXxV_VZJBMtQ.b['1'][0]++,this.hasPrevPage())&&(__cov_Qqnavx_mTqPXxV_VZJBMtQ.b['1'][1]++,this.formatPageUrl(this.get('page')-1))||(__cov_Qqnavx_mTqPXxV_VZJBMtQ.b['1'][2]++,null);},nextPageUrl:function(){__cov_Qqnavx_mTqPXxV_VZJBMtQ.f['4']++;__cov_Qqnavx_mTqPXxV_VZJBMtQ.s['6']++;return(__cov_Qqnavx_mTqPXxV_VZJBMtQ.b['2'][0]++,this.hasNextPage())&&(__cov_Qqnavx_mTqPXxV_VZJBMtQ.b['2'][1]++,this.formatPageUrl(this.get('page')+1))||(__cov_Qqnavx_mTqPXxV_VZJBMtQ.b['2'][2]++,null);},formatPageUrl:function(page){__cov_Qqnavx_mTqPXxV_VZJBMtQ.f['5']++;__cov_Qqnavx_mTqPXxV_VZJBMtQ.s['7']++;var pageUrl=this.get('pageUrl');__cov_Qqnavx_mTqPXxV_VZJBMtQ.s['8']++;if(pageUrl){__cov_Qqnavx_mTqPXxV_VZJBMtQ.b['3'][0]++;__cov_Qqnavx_mTqPXxV_VZJBMtQ.s['9']++;return Y.Lang.sub(pageUrl,{page:(__cov_Qqnavx_mTqPXxV_VZJBMtQ.b['4'][0]++,page)||(__cov_Qqnavx_mTqPXxV_VZJBMtQ.b['4'][1]++,this.get('page'))});}else{__cov_Qqnavx_mTqPXxV_VZJBMtQ.b['3'][1]++;}__cov_Qqnavx_mTqPXxV_VZJBMtQ.s['10']++;return null;}};__cov_Qqnavx_mTqPXxV_VZJBMtQ.s['11']++;Y.namespace('Paginator').Url=PaginatorUrl;__cov_Qqnavx_mTqPXxV_VZJBMtQ.s['12']++;Y.Base.mix(Y.Paginator,[PaginatorUrl]);},'3.16.0',{'requires':['paginator']});
