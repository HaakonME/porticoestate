/*
YUI 3.7.3 (build 5687)
Copyright 2012 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/
YUI.add("widget-child",function(e,t){function r(){e.after(this._syncUIChild,this,"syncUI"),e.after(this._bindUIChild,this,"bindUI")}var n=e.Lang;r.ATTRS={selected:{value:0,validator:n.isNumber},index:{readOnly:!0,getter:function(){var e=this.get("parent"),t=-1;return e&&(t=e.indexOf(this)),t}},parent:{readOnly:!0},depth:{readOnly:!0,getter:function(){var e=this.get("parent"),t=this.get("root"),n=-1;while(e){n+=1;if(e==t)break;e=e.get("parent")}return n}},root:{readOnly:!0,getter:function(){var t=function(n){var r=n.get("parent"),i=n.ROOT_TYPE,s=r;return i&&(s=r&&e.instanceOf(r,i)),s?t(r):n};return t(this)}}},r.prototype={ROOT_TYPE:null,_getUIEventNode:function(){var e=this.get("root"),t;return e&&(t=e.get("boundingBox")),t},next:function(e){var t=this.get("parent"),n;return t&&(n=t.item(this.get("index")+1)),!n&&e&&(n=t.item(0)),n},previous:function(e){var t=this.get("parent"),n=this.get("index"),r;return t&&n>0&&(r=t.item([n-1])),!r&&e&&(r=t.item(t.size()-1)),r},remove:function(t){var r,i;return n.isNumber(t)?i=e.WidgetParent.prototype.remove.apply(this,arguments):(r=this.get("parent"),r&&(i=r.remove(this.get("index")))),i},isRoot:function(){return this==this.get("root")},ancestor:function(e){var t=this.get("root"),n;if(this.get("depth")>e){n=this.get("parent");while(n!=t&&n.get("depth")>e)n=n.get("parent")}return n},_uiSetChildSelected:function(e){var t=this.get("boundingBox"),n=this.getClassName("selected");e===0?t.removeClass(n):t.addClass(n)},_afterChildSelectedChange:function(e){this._uiSetChildSelected(e.newVal)},_syncUIChild:function(){this._uiSetChildSelected(this.get("selected"))},_bindUIChild:function(){this.after("selectedChange",this._afterChildSelectedChange)}},e.WidgetChild=r},"3.7.3",{requires:["base-build","widget"]});
