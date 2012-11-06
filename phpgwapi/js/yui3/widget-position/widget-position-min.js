/*
YUI 3.7.3 (build 5687)
Copyright 2012 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/
YUI.add("widget-position",function(e,t){function d(t){this._posNode=this.get(u),e.after(this._renderUIPosition,this,f),e.after(this._syncUIPosition,this,c),e.after(this._bindUIPosition,this,l)}var n=e.Lang,r=e.Widget,i="xy",s="position",o="positioned",u="boundingBox",a="relative",f="renderUI",l="bindUI",c="syncUI",h=r.UI_SRC,p="xyChange";d.ATTRS={x:{setter:function(e){this._setX(e)},getter:function(){return this._getX()},lazyAdd:!1},y:{setter:function(e){this._setY(e)},getter:function(){return this._getY()},lazyAdd:!1},xy:{value:[0,0],validator:function(e){return this._validateXY(e)}}},d.POSITIONED_CLASS_NAME=r.getClassName(o),d.prototype={_renderUIPosition:function(){this._posNode.addClass(d.POSITIONED_CLASS_NAME)},_syncUIPosition:function(){var e=this._posNode;e.getStyle(s)===a&&this.syncXY(),this._uiSetXY(this.get(i))},_bindUIPosition:function(){this.after(p,this._afterXYChange)},move:function(){var e=arguments,t=n.isArray(e[0])?e[0]:[e[0],e[1]];this.set(i,t)},syncXY:function(){this.set(i,this._posNode.getXY(),{src:h})},_validateXY:function(e){return n.isArray(e)&&n.isNumber(e[0])&&n.isNumber(e[1])},_setX:function(e){this.set(i,[e,this.get(i)[1]])},_setY:function(e){this.set(i,[this.get(i)[0],e])},_getX:function(){return this.get(i)[0]},_getY:function(){return this.get(i)[1]},_afterXYChange:function(e){e.src!=h&&this._uiSetXY(e.newVal)},_uiSetXY:function(e){this._posNode.setXY(e)}},e.WidgetPosition=d},"3.7.3",{requires:["base-build","node-screen","widget"]});
