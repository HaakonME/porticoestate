/*
YUI 3.16.0 (build 76f0e08)
Copyright 2014 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/

if (typeof __coverage__ === 'undefined') { __coverage__ = {}; }
if (!__coverage__['build/series-spline-stacked/series-spline-stacked.js']) {
   __coverage__['build/series-spline-stacked/series-spline-stacked.js'] = {"path":"build/series-spline-stacked/series-spline-stacked.js","s":{"1":0,"2":0,"3":0,"4":0},"b":{},"f":{"1":0,"2":0},"fnMap":{"1":{"name":"(anonymous_1)","line":1,"loc":{"start":{"line":1,"column":33},"end":{"line":1,"column":52}}},"2":{"name":"(anonymous_2)","line":28,"loc":{"start":{"line":28,"column":17},"end":{"line":29,"column":4}}}},"statementMap":{"1":{"start":{"line":1,"column":0},"end":{"line":50,"column":64}},"2":{"start":{"line":20,"column":0},"end":{"line":46,"column":3}},"3":{"start":{"line":30,"column":8},"end":{"line":30,"column":65}},"4":{"start":{"line":31,"column":8},"end":{"line":31,"column":43}}},"branchMap":{},"code":["(function () { YUI.add('series-spline-stacked', function (Y, NAME) {","","/**"," * Provides functionality for creating a stacked spline series."," *"," * @module charts"," * @submodule series-spline-stacked"," */","/**"," * StackedSplineSeries creates spline graphs in which the different series are stacked along a value axis"," * to indicate their contribution to a cumulative total."," *"," * @class StackedSplineSeries"," * @constructor"," * @extends SplineSeries"," * @uses StackingUtil"," * @param {Object} config (optional) Configuration parameters."," * @submodule series-spline-stacked"," */","Y.StackedSplineSeries = Y.Base.create(\"stackedSplineSeries\", Y.SplineSeries, [Y.StackingUtil], {","    /**","     * @protected","     *","     * Calculates the coordinates for the series. Overrides base implementation.","     *","     * @method setAreaData","     */","    setAreaData: function()","    {","        Y.StackedSplineSeries.superclass.setAreaData.apply(this);","        this._stackCoordinates.apply(this);","    }","}, {","    ATTRS: {","        /**","         * Read-only attribute indicating the type of series.","         *","         * @attribute type","         * @type String","         * @default stackedSpline","         */","        type: {","            value:\"stackedSpline\"","        }","    }","});","","","","}, '3.16.0', {\"requires\": [\"series-stacked\", \"series-spline\"]});","","}());"]};
}
var __cov_RFzVsgp0P_kwJ4a1E76vlA = __coverage__['build/series-spline-stacked/series-spline-stacked.js'];
__cov_RFzVsgp0P_kwJ4a1E76vlA.s['1']++;YUI.add('series-spline-stacked',function(Y,NAME){__cov_RFzVsgp0P_kwJ4a1E76vlA.f['1']++;__cov_RFzVsgp0P_kwJ4a1E76vlA.s['2']++;Y.StackedSplineSeries=Y.Base.create('stackedSplineSeries',Y.SplineSeries,[Y.StackingUtil],{setAreaData:function(){__cov_RFzVsgp0P_kwJ4a1E76vlA.f['2']++;__cov_RFzVsgp0P_kwJ4a1E76vlA.s['3']++;Y.StackedSplineSeries.superclass.setAreaData.apply(this);__cov_RFzVsgp0P_kwJ4a1E76vlA.s['4']++;this._stackCoordinates.apply(this);}},{ATTRS:{type:{value:'stackedSpline'}}});},'3.16.0',{'requires':['series-stacked','series-spline']});
