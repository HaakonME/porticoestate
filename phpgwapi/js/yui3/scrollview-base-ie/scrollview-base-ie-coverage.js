/*
YUI 3.16.0 (build 76f0e08)
Copyright 2014 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/

if (typeof __coverage__ === 'undefined') { __coverage__ = {}; }
if (!__coverage__['build/scrollview-base-ie/scrollview-base-ie.js']) {
   __coverage__['build/scrollview-base-ie/scrollview-base-ie.js'] = {"path":"build/scrollview-base-ie/scrollview-base-ie.js","s":{"1":0,"2":0,"3":0,"4":0,"5":0,"6":0,"7":0,"8":0,"9":0,"10":0},"b":{},"f":{"1":0,"2":0,"3":0,"4":0,"5":0},"fnMap":{"1":{"name":"(anonymous_1)","line":1,"loc":{"start":{"line":1,"column":30},"end":{"line":1,"column":49}}},"2":{"name":"(anonymous_2)","line":20,"loc":{"start":{"line":20,"column":19},"end":{"line":20,"column":36}}},"3":{"name":"(anonymous_3)","line":24,"loc":{"start":{"line":24,"column":27},"end":{"line":24,"column":38}}},"4":{"name":"(anonymous_4)","line":38,"loc":{"start":{"line":38,"column":23},"end":{"line":38,"column":34}}},"5":{"name":"(anonymous_5)","line":49,"loc":{"start":{"line":49,"column":23},"end":{"line":49,"column":34}}}},"statementMap":{"1":{"start":{"line":1,"column":0},"end":{"line":55,"column":48}},"2":{"start":{"line":9,"column":0},"end":{"line":52,"column":9}},"3":{"start":{"line":21,"column":8},"end":{"line":21,"column":46}},"4":{"start":{"line":22,"column":8},"end":{"line":22,"column":73}},"5":{"start":{"line":24,"column":8},"end":{"line":28,"column":17}},"6":{"start":{"line":25,"column":12},"end":{"line":25,"column":63}},"7":{"start":{"line":26,"column":12},"end":{"line":26,"column":67}},"8":{"start":{"line":27,"column":12},"end":{"line":27,"column":69}},"9":{"start":{"line":39,"column":8},"end":{"line":39,"column":21}},"10":{"start":{"line":50,"column":8},"end":{"line":50,"column":59}}},"branchMap":{},"code":["(function () { YUI.add('scrollview-base-ie', function (Y, NAME) {","","/**"," * IE specific support for the scrollview-base module."," *"," * @module scrollview-base-ie"," */","","Y.mix(Y.ScrollView.prototype, {","","    /**","     * Internal method to fix text selection in IE","     *","     * @method _fixIESelect","     * @for ScrollView","     * @private","     * @param {Node} bb The bounding box","     * @param {Node} cb The content box","     */","    _fixIESelect : function(bb, cb) {","        this._cbDoc = cb.get(\"ownerDocument\");","        this._nativeBody = Y.Node.getDOMNode(Y.one(\"body\", this._cbDoc));","","        cb.on(\"mousedown\", function() {","            this._selectstart = this._nativeBody.onselectstart;","            this._nativeBody.onselectstart = this._iePreventSelect;","            this._cbDoc.once(\"mouseup\", this._ieRestoreSelect, this);","        }, this);","    },","","    /**","     * Native onselectstart handle to prevent selection in IE","     *","     * @method _iePreventSelect","     * @for ScrollView","     * @private","     */","    _iePreventSelect : function() {","        return false;","    },","","    /**","     * Restores native onselectstart handle, backed up to prevent selection in IE","     *","     * @method _ieRestoreSelect","     * @for ScrollView","     * @private","     */","    _ieRestoreSelect : function() {","        this._nativeBody.onselectstart = this._selectstart;","    }","}, true);","","","}, '3.16.0', {\"requires\": [\"scrollview-base\"]});","","}());"]};
}
var __cov_FbZDikkUxPaIpw3IhpK8zw = __coverage__['build/scrollview-base-ie/scrollview-base-ie.js'];
__cov_FbZDikkUxPaIpw3IhpK8zw.s['1']++;YUI.add('scrollview-base-ie',function(Y,NAME){__cov_FbZDikkUxPaIpw3IhpK8zw.f['1']++;__cov_FbZDikkUxPaIpw3IhpK8zw.s['2']++;Y.mix(Y.ScrollView.prototype,{_fixIESelect:function(bb,cb){__cov_FbZDikkUxPaIpw3IhpK8zw.f['2']++;__cov_FbZDikkUxPaIpw3IhpK8zw.s['3']++;this._cbDoc=cb.get('ownerDocument');__cov_FbZDikkUxPaIpw3IhpK8zw.s['4']++;this._nativeBody=Y.Node.getDOMNode(Y.one('body',this._cbDoc));__cov_FbZDikkUxPaIpw3IhpK8zw.s['5']++;cb.on('mousedown',function(){__cov_FbZDikkUxPaIpw3IhpK8zw.f['3']++;__cov_FbZDikkUxPaIpw3IhpK8zw.s['6']++;this._selectstart=this._nativeBody.onselectstart;__cov_FbZDikkUxPaIpw3IhpK8zw.s['7']++;this._nativeBody.onselectstart=this._iePreventSelect;__cov_FbZDikkUxPaIpw3IhpK8zw.s['8']++;this._cbDoc.once('mouseup',this._ieRestoreSelect,this);},this);},_iePreventSelect:function(){__cov_FbZDikkUxPaIpw3IhpK8zw.f['4']++;__cov_FbZDikkUxPaIpw3IhpK8zw.s['9']++;return false;},_ieRestoreSelect:function(){__cov_FbZDikkUxPaIpw3IhpK8zw.f['5']++;__cov_FbZDikkUxPaIpw3IhpK8zw.s['10']++;this._nativeBody.onselectstart=this._selectstart;}},true);},'3.16.0',{'requires':['scrollview-base']});
