/*
YUI 3.16.0 (build 76f0e08)
Copyright 2014 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/

if (typeof __coverage__ === 'undefined') { __coverage__ = {}; }
if (!__coverage__['build/axis-time/axis-time.js']) {
   __coverage__['build/axis-time/axis-time.js'] = {"path":"build/axis-time/axis-time.js","s":{"1":0,"2":0,"3":0,"4":0,"5":0,"6":0,"7":0,"8":0,"9":0,"10":0,"11":0,"12":0,"13":0,"14":0,"15":0,"16":0,"17":0,"18":0},"b":{"1":[0,0],"2":[0,0],"3":[0,0]},"f":{"1":0,"2":0,"3":0},"fnMap":{"1":{"name":"(anonymous_1)","line":1,"loc":{"start":{"line":1,"column":21},"end":{"line":1,"column":40}}},"2":{"name":"(anonymous_2)","line":30,"loc":{"start":{"line":30,"column":22},"end":{"line":31,"column":4}}},"3":{"name":"(anonymous_3)","line":55,"loc":{"start":{"line":55,"column":19},"end":{"line":56,"column":4}}}},"statementMap":{"1":{"start":{"line":1,"column":0},"end":{"line":91,"column":55}},"2":{"start":{"line":19,"column":0},"end":{"line":87,"column":3}},"3":{"start":{"line":32,"column":8},"end":{"line":35,"column":18}},"4":{"start":{"line":36,"column":12},"end":{"line":36,"column":19}},"5":{"start":{"line":37,"column":8},"end":{"line":37,"column":37}},"6":{"start":{"line":38,"column":8},"end":{"line":38,"column":29}},"7":{"start":{"line":39,"column":8},"end":{"line":39,"column":21}},"8":{"start":{"line":57,"column":8},"end":{"line":62,"column":32}},"9":{"start":{"line":63,"column":8},"end":{"line":63,"column":79}},"10":{"start":{"line":64,"column":8},"end":{"line":81,"column":9}},"11":{"start":{"line":66,"column":12},"end":{"line":66,"column":55}},"12":{"start":{"line":67,"column":12},"end":{"line":80,"column":13}},"13":{"start":{"line":69,"column":16},"end":{"line":69,"column":27}},"14":{"start":{"line":70,"column":16},"end":{"line":70,"column":49}},"15":{"start":{"line":71,"column":16},"end":{"line":77,"column":18}},"16":{"start":{"line":78,"column":16},"end":{"line":78,"column":35}},"17":{"start":{"line":79,"column":16},"end":{"line":79,"column":39}},"18":{"start":{"line":82,"column":8},"end":{"line":85,"column":10}}},"branchMap":{"1":{"line":63,"type":"binary-expr","locations":[{"start":{"line":63,"column":21},"end":{"line":63,"column":31}},{"start":{"line":63,"column":35},"end":{"line":63,"column":78}}]},"2":{"line":67,"type":"if","locations":[{"start":{"line":67,"column":12},"end":{"line":67,"column":12}},{"start":{"line":67,"column":12},"end":{"line":67,"column":12}}]},"3":{"line":67,"type":"binary-expr","locations":[{"start":{"line":67,"column":15},"end":{"line":67,"column":31}},{"start":{"line":67,"column":35},"end":{"line":67,"column":51}}]}},"code":["(function () { YUI.add('axis-time', function (Y, NAME) {","","/**"," * Provides functionality for drawing a time axis for use with a chart."," *"," * @module charts"," * @submodule axis-time"," */","/**"," * TimeAxis draws a time-based axis for a chart."," *"," * @class TimeAxis"," * @constructor"," * @extends Axis"," * @uses TimeImpl"," * @param {Object} config (optional) Configuration parameters."," * @submodule axis-time"," */","Y.TimeAxis = Y.Base.create(\"timeAxis\", Y.Axis, [Y.TimeImpl], {","    /**","     * Calculates and returns a value based on the number of labels and the index of","     * the current label.","     *","     * @method _getLabelByIndex","     * @param {Number} i Index of the label.","     * @param {Number} l Total number of labels.","     * @return String","     * @private","     */","    _getLabelByIndex: function(i, l)","    {","        var min = this.get(\"minimum\"),","            max = this.get(\"maximum\"),","            increm,","            label;","            l -= 1;","        increm = ((max - min)/l) * i;","        label = min + increm;","        return label;","    },","","    /**","     * Returns an object literal containing and array of label values and an array of points.","     *","     * @method _getLabelData","     * @param {Object} startPoint An object containing x and y values.","     * @param {Number} edgeOffset Distance to offset coordinates.","     * @param {Number} layoutLength Distance that the axis spans.","     * @param {Number} count Number of labels.","     * @param {String} direction Indicates whether the axis is horizontal or vertical.","     * @param {Array} Array containing values for axis labels.","     * @return Array","     * @private","     */","    _getLabelData: function(constantVal, staticCoord, dynamicCoord, min, max, edgeOffset, layoutLength, count, dataValues)","    {","        var dataValue,","            i,","            points = [],","            values = [],","            point,","            offset = edgeOffset;","        dataValues = dataValues || this._getDataValuesByCount(count, min, max);","        for(i = 0; i < count; i = i + 1)","        {","            dataValue = this._getNumber(dataValues[i]);","            if(dataValue <= max && dataValue >= min)","            {","                point = {};","                point[staticCoord] = constantVal;","                point[dynamicCoord] = this._getCoordFromValue(","                    min,","                    max,","                    layoutLength,","                    dataValue,","                    offset","                );","                points.push(point);","                values.push(dataValue);","            }","        }","        return {","            points: points,","            values: values","        };","    }","});","","","","}, '3.16.0', {\"requires\": [\"axis\", \"axis-time-base\"]});","","}());"]};
}
var __cov_NIoKAV1h0fULE_RLRoh6Ig = __coverage__['build/axis-time/axis-time.js'];
__cov_NIoKAV1h0fULE_RLRoh6Ig.s['1']++;YUI.add('axis-time',function(Y,NAME){__cov_NIoKAV1h0fULE_RLRoh6Ig.f['1']++;__cov_NIoKAV1h0fULE_RLRoh6Ig.s['2']++;Y.TimeAxis=Y.Base.create('timeAxis',Y.Axis,[Y.TimeImpl],{_getLabelByIndex:function(i,l){__cov_NIoKAV1h0fULE_RLRoh6Ig.f['2']++;__cov_NIoKAV1h0fULE_RLRoh6Ig.s['3']++;var min=this.get('minimum'),max=this.get('maximum'),increm,label;__cov_NIoKAV1h0fULE_RLRoh6Ig.s['4']++;l-=1;__cov_NIoKAV1h0fULE_RLRoh6Ig.s['5']++;increm=(max-min)/l*i;__cov_NIoKAV1h0fULE_RLRoh6Ig.s['6']++;label=min+increm;__cov_NIoKAV1h0fULE_RLRoh6Ig.s['7']++;return label;},_getLabelData:function(constantVal,staticCoord,dynamicCoord,min,max,edgeOffset,layoutLength,count,dataValues){__cov_NIoKAV1h0fULE_RLRoh6Ig.f['3']++;__cov_NIoKAV1h0fULE_RLRoh6Ig.s['8']++;var dataValue,i,points=[],values=[],point,offset=edgeOffset;__cov_NIoKAV1h0fULE_RLRoh6Ig.s['9']++;dataValues=(__cov_NIoKAV1h0fULE_RLRoh6Ig.b['1'][0]++,dataValues)||(__cov_NIoKAV1h0fULE_RLRoh6Ig.b['1'][1]++,this._getDataValuesByCount(count,min,max));__cov_NIoKAV1h0fULE_RLRoh6Ig.s['10']++;for(i=0;i<count;i=i+1){__cov_NIoKAV1h0fULE_RLRoh6Ig.s['11']++;dataValue=this._getNumber(dataValues[i]);__cov_NIoKAV1h0fULE_RLRoh6Ig.s['12']++;if((__cov_NIoKAV1h0fULE_RLRoh6Ig.b['3'][0]++,dataValue<=max)&&(__cov_NIoKAV1h0fULE_RLRoh6Ig.b['3'][1]++,dataValue>=min)){__cov_NIoKAV1h0fULE_RLRoh6Ig.b['2'][0]++;__cov_NIoKAV1h0fULE_RLRoh6Ig.s['13']++;point={};__cov_NIoKAV1h0fULE_RLRoh6Ig.s['14']++;point[staticCoord]=constantVal;__cov_NIoKAV1h0fULE_RLRoh6Ig.s['15']++;point[dynamicCoord]=this._getCoordFromValue(min,max,layoutLength,dataValue,offset);__cov_NIoKAV1h0fULE_RLRoh6Ig.s['16']++;points.push(point);__cov_NIoKAV1h0fULE_RLRoh6Ig.s['17']++;values.push(dataValue);}else{__cov_NIoKAV1h0fULE_RLRoh6Ig.b['2'][1]++;}}__cov_NIoKAV1h0fULE_RLRoh6Ig.s['18']++;return{points:points,values:values};}});},'3.16.0',{'requires':['axis','axis-time-base']});
