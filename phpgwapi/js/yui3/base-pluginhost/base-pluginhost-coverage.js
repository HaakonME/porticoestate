/*
YUI 3.16.0 (build 76f0e08)
Copyright 2014 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/

if (typeof __coverage__ === 'undefined') { __coverage__ = {}; }
if (!__coverage__['build/base-pluginhost/base-pluginhost.js']) {
   __coverage__['build/base-pluginhost/base-pluginhost.js'] = {"path":"build/base-pluginhost/base-pluginhost.js","s":{"1":0,"2":0,"3":0,"4":0,"5":0},"b":{},"f":{"1":0},"fnMap":{"1":{"name":"(anonymous_1)","line":1,"loc":{"start":{"line":1,"column":27},"end":{"line":1,"column":46}}}},"statementMap":{"1":{"start":{"line":1,"column":0},"end":{"line":36,"column":56}},"2":{"start":{"line":12,"column":4},"end":{"line":13,"column":35}},"3":{"start":{"line":15,"column":4},"end":{"line":15,"column":44}},"4":{"start":{"line":24,"column":4},"end":{"line":24,"column":32}},"5":{"start":{"line":33,"column":4},"end":{"line":33,"column":36}}},"branchMap":{},"code":["(function () { YUI.add('base-pluginhost', function (Y, NAME) {","","    /**","     * The base-pluginhost submodule adds Plugin support to Base, by augmenting Base with","     * Plugin.Host and setting up static (class level) Base.plug and Base.unplug methods.","     *","     * @module base","     * @submodule base-pluginhost","     * @for Base","     */","","    var Base = Y.Base,","        PluginHost = Y.Plugin.Host;","","    Y.mix(Base, PluginHost, false, null, 1);","","    /**","     * Alias for <a href=\"Plugin.Host.html#method_Plugin.Host.plug\">Plugin.Host.plug</a>. See aliased","     * method for argument and return value details.","     *","     * @method plug","     * @static","     */","    Base.plug = PluginHost.plug;","","    /**","     * Alias for <a href=\"Plugin.Host.html#method_Plugin.Host.unplug\">Plugin.Host.unplug</a>. See the","     * aliased method for argument and return value details.","     *","     * @method unplug","     * @static","     */","    Base.unplug = PluginHost.unplug;","","","}, '3.16.0', {\"requires\": [\"base-base\", \"pluginhost\"]});","","}());"]};
}
var __cov_N_CvTArdk7dnpFqQRnHT5g = __coverage__['build/base-pluginhost/base-pluginhost.js'];
__cov_N_CvTArdk7dnpFqQRnHT5g.s['1']++;YUI.add('base-pluginhost',function(Y,NAME){__cov_N_CvTArdk7dnpFqQRnHT5g.f['1']++;__cov_N_CvTArdk7dnpFqQRnHT5g.s['2']++;var Base=Y.Base,PluginHost=Y.Plugin.Host;__cov_N_CvTArdk7dnpFqQRnHT5g.s['3']++;Y.mix(Base,PluginHost,false,null,1);__cov_N_CvTArdk7dnpFqQRnHT5g.s['4']++;Base.plug=PluginHost.plug;__cov_N_CvTArdk7dnpFqQRnHT5g.s['5']++;Base.unplug=PluginHost.unplug;},'3.16.0',{'requires':['base-base','pluginhost']});
