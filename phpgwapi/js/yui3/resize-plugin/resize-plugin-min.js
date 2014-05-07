/*
YUI 3.16.0 (build 76f0e08)
Copyright 2014 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/

YUI.add("resize-plugin",function(e,t){var n=function(t){t.node=e.Widget&&t.host instanceof e.Widget?t.host.get("boundingBox"):t.host,t.host instanceof e.Widget?t.widget=t.host:t.widget=!1,n.superclass.constructor.call(this,t)};n.NAME="resize-plugin",n.NS="resize",n.ATTRS={node:{value:undefined},widget:{value:undefined}},e.extend(n,e.Resize,{initializer:function(e){this.set("node",e.node),this.set("widget",e.widget),this.on("resize:resize",function(e){this._correctDimensions(e)})},_correctDimensions:function(e){var t=this.get("node"),n={old:t.getX(),cur:e.currentTarget.info.left},r={old:t.getY(),cur:e.currentTarget.info.top};this.get("widget")&&this._setWidgetProperties(e,n,r),this._isDifferent(n.old,n.cur)&&t.set("x",n.cur),this._isDifferent(r.old,r.cur)&&t.set("y",r.cur)},_setWidgetProperties:function(t,n,r){var i=this.get("widget"),s=i.get("height"),o=i.get("width"),u=t.currentTarget.info.offsetWidth-t.currentTarget.totalHSurrounding,a=t.currentTarget.info.offsetHeight-t.currentTarget.totalVSurrounding;this._isDifferent(s,a)&&i.set("height",a),this._isDifferent(o,u)&&i.set("width",u),i.hasImpl&&i.hasImpl(e.WidgetPosition)&&(this._isDifferent(i.get("x"),n.cur)&&i.set("x",n.cur),this._isDifferent(i.get("y"),r.cur)&&i.set("y",r.cur))},_isDifferent:function(e,t){var n=!1;return e!==t&&(n=t),n}}),e.namespace("Plugin"),e.Plugin.Resize=n},"3.16.0",{requires:["resize-base","plugin"],optional:["resize-constrain"]});
