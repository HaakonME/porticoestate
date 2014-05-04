/*
YUI 3.16.0 (build 76f0e08)
Copyright 2014 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/

YUI.add("series-cartesian",function(e,t){var n=e.Lang;e.CartesianSeries=e.Base.create("cartesianSeries",e.SeriesBase,[],{_xDisplayName:null,_yDisplayName:null,_leftOrigin:null,_bottomOrigin:null,addListeners:function(){var t=this.get("xAxis"),n=this.get("yAxis");t&&(this._xDataReadyHandle=t.after("dataReady",e.bind(this._xDataChangeHandler,this)),this._xDataUpdateHandle=t.after("dataUpdate",e.bind(this._xDataChangeHandler,this))),n&&(this._yDataReadyHandle=n.after("dataReady",e.bind(this._yDataChangeHandler,this)),this._yDataUpdateHandle=n.after("dataUpdate",e.bind(this._yDataChangeHandler,this))),this._xAxisChangeHandle=this.after("xAxisChange",this._xAxisChangeHandler),this._yAxisChangeHandle=this.after("yAxisChange",this._yAxisChangeHandler),this._stylesChangeHandle=this.after("stylesChange",function(){var e=this._updateAxisBase();e&&this.draw()}),this._widthChangeHandle=this.after("widthChange",function(){var e=this._updateAxisBase();e&&this.draw()}),this._heightChangeHandle=this.after("heightChange",function(){var e=this._updateAxisBase();e&&this.draw()}),this._visibleChangeHandle=this.after("visibleChange",this._handleVisibleChange)},_xAxisChangeHandler:function(){var t=this.get("xAxis");t.after("dataReady",e.bind(this._xDataChangeHandler,this)),t.after("dataUpdate",e.bind(this._xDataChangeHandler,this))},_yAxisChangeHandler:function(){var t=this.get("yAxis");t.after("dataReady",e.bind(this._yDataChangeHandler,this)),t.after("dataUpdate",e.bind(this._yDataChangeHandler,this))},GUID:"yuicartesianseries",_xDataChangeHandler:function(){var e=this._updateAxisBase();e&&this.draw()},_yDataChangeHandler:function(){var e=this._updateAxisBase();e&&this.draw()},_updateAxisBase:function(){var t=this.get("xAxis"),r=this.get("yAxis"),i=this.get("xKey"),s=this.get("yKey"),o,u,a,f,l;return!t||!r||!i||!s?l=!1:(u=t.getDataByKey(i),o=r.getDataByKey(s),n.isArray(i)?a=u&&e.Object.size(u)>0?this._checkForDataByKey(u,i):!1:a=u?!0:!1,n.isArray(s)?f=o&&e.Object.size(o)>0?this._checkForDataByKey(o,s):!1:f=o?!0:!1,l=a&&f,l&&(this.set("xData",u),this.set("yData",o))),l},_checkForDataByKey:function(e,t){var n,r=t.length,i=!1;for(n=0;n<r;n+=1)if(e[t[n]]){i=!0;break}return i},validate:function(){this.get("xData")&&this.get("yData")||this._updateAxisBase()?this.draw():this.fire("drawingComplete")},setAreaData:function(){var e=this.get("width"),t=this.get("height"),n=this.get("xAxis"),r=this.get("yAxis"),i=this._copyData(this.get("xData")),s=this._copyData(this.get("yData")),o=this.get("direction"),u=o==="vertical"?s.length:i.length,a=n.getEdgeOffset(n.getTotalMajorUnits(),e),f=r.getEdgeOffset(r.getTotalMajorUnits(),t),l=this.get("styles").padding,c=l.left,h=l.top,p=e-(c+l.right+a*2),d=t-(h+l.bottom+f*2),v=n.get("maximum"),m=n.get("minimum"),g=r.get("maximum"),y=r.get("minimum"),b=this.get("graphic"),w=r.get("type"),E=w==="numeric"||w==="stacked",S,x,T=n.getOrigin(),N=r.getOrigin();b.set("width",e),b.set("height",t),a+=c,f=E?f+d+h+l.bottom:h+f,this._leftOrigin=Math.round(n._getCoordFromValue(m,v,p,T,a,!1)),this._bottomOrigin=Math.round(r._getCoordFromValue(y,g,d,N,f,E)),S=this._getCoords(m,v,p,i,n,a,!1),x=this._getCoords(y,g,d,s,r,f,E),this.set("xcoords",S),this.set("ycoords",x),this._dataLength=u,this._setXMarkerPlane(S,u),this._setYMarkerPlane(x,u)},_getCoords:function(e,t,r,i,s,o,u){var a,f;if(n.isArray(i))a=s._getCoordsFromValues(e,t,r,i,o,u);else{a={};for(f in i)i.hasOwnProperty(f)&&(a[f]=this._getCoords.apply(this,[e,t,r,i[f],s,o,u]))}return a},_copyData:function(e){var t,r;if(n.isArray(e))t=e.concat();else{t={};for(r in e)e.hasOwnProperty(r)&&(t[r]=e[r].concat())}return t},_setXMarkerPlane:function(e,t){var r=0,i=[],s=this.get("xMarkerPlaneOffset"),o;if(n.isArray(e)){for(r=0;r<t;r+=1)o=e[r],i.push({start:o-s,end:o+s});this.set("xMarkerPlane",i)}},_setYMarkerPlane:function(e,t){var r=0,i=[],s=this.get("yMarkerPlaneOffset"),o;if(n.isArray(e)){for(r=0;r<t;r+=1)o=e[r],i.push({start:o-s,end:o+s});this.set("yMarkerPlane",i)}},_getFirstValidIndex:function(e){var t,r=-1,i=e.length;while(!n.isNumber(t)&&r<i)r+=1,t=e[r];return r},_getLastValidIndex:function(e){var t,r=e.length,i=-1;while(!n.isNumber(t)&&r>i)r-=1,t=e[r];return r},draw:function(){var e=this.get("width"),t=this.get("height"),n,r;if(this.get("rendered")&&isFinite(e)&&isFinite(t)&&e>0&&t>0&&(this.get("xData")&&this.get("yData")||this._updateAxisBase())){if(this._drawing){this._callLater=!0;return}this._drawing=!0,this._callLater=!1,this.setAreaData(),n=this.get("xcoords"),r=this.get("ycoords"),n&&r&&n.length>0&&this.drawSeries(),this._drawing=!1,this._callLater?this.draw():(this._toggleVisible(this.get("visible")),this.fire("drawingComplete"))}},_defaultPlaneOffset:4,destructor:function(){this.get("rendered")&&(this._xDataReadyHandle&&this._xDataReadyHandle.detach(),this._xDataUpdateHandle&&this._xDataUpdateHandle.detach(),this._yDataReadyHandle&&this._yDataReadyHandle.detach(),this._yDataUpdateHandle&&this._yDataUpdateHandle.detach(),this._xAxisChangeHandle&&this._xAxisChangeHandle.detach(),this._yAxisChangeHandle&&this._yAxisChangeHandle.detach())}},{ATTRS:{seriesTypeCollection:{},xDisplayName:{getter:function(){return this._xDisplayName||this.get("xKey")},setter:function(e){return this._xDisplayName=e.toString(),e}},yDisplayName:{getter:function(){return this._yDisplayName||this.get("yKey")},setter:function(e){return this._yDisplayName=e.toString(),e}},categoryDisplayName:{lazyAdd:!1,getter:function(){return this.get("direction")==="vertical"?this.get("yDisplayName"):this.get("xDisplayName")},setter:function(e){return this.get("direction")==="vertical"?this._yDisplayName=e:this._xDisplayName=e,e}},valueDisplayName:{lazyAdd:!1,getter:function(){return this.get("direction")==="vertical"?this.get("xDisplayName"):this.get("yDisplayName")},setter:function(e){return this.get("direction")==="vertical"?this._xDisplayName=e:this._yDisplayName=e,e}},type:{value:"cartesian"},order:{},graphOrder:{},xcoords:{},ycoords:{},xAxis:{},yAxis:{},xKey:{setter:function(e){return n
.isArray(e)?e:e.toString()}},yKey:{setter:function(e){return n.isArray(e)?e:e.toString()}},xData:{},yData:{},xMarkerPlane:{},yMarkerPlane:{},xMarkerPlaneOffset:{getter:function(){var e=this.get("styles").marker;return e&&e.width&&isFinite(e.width)?e.width*.5:this._defaultPlaneOffset}},yMarkerPlaneOffset:{getter:function(){var e=this.get("styles").marker;return e&&e.height&&isFinite(e.height)?e.height*.5:this._defaultPlaneOffset}},direction:{value:"horizontal"}}})},"3.16.0",{requires:["series-base"]});
