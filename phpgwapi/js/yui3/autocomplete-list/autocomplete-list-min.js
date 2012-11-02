/*
YUI 3.7.3 (build 5687)
Copyright 2012 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/
YUI.add("autocomplete-list",function(e,t){var n=e.Lang,r=e.Node,i=e.Array,s=e.UA.ie&&e.UA.ie<7,o=9,u="_CLASS_ITEM",a="_CLASS_ITEM_ACTIVE",f="_CLASS_ITEM_HOVER",l="_SELECTOR_ITEM",c="activeItem",h="alwaysShowList",p="circular",d="hoveredItem",v="id",m="item",g="list",y="result",b="results",w="visible",E="width",S="select",x=e.Base.create("autocompleteList",e.Widget,[e.AutoCompleteBase,e.WidgetPosition,e.WidgetPositionAlign],{ARIA_TEMPLATE:"<div/>",ITEM_TEMPLATE:"<li/>",LIST_TEMPLATE:"<ul/>",UI_EVENTS:function(){var t=e.merge(e.Node.DOM_EVENTS);return delete t.valuechange,delete t.valueChange,t}(),initializer:function(){var t=this.get("inputNode");if(!t){e.error("No inputNode specified.");return}this._inputNode=t,this._listEvents=[],this.DEF_PARENT_NODE=t.get("parentNode"),this[u]=this.getClassName(m),this[a]=this.getClassName(m,"active"),this[f]=this.getClassName(m,"hover"),this[l]="."+this[u],this.publish(S,{defaultFn:this._defSelectFn})},destructor:function(){while(this._listEvents.length)this._listEvents.pop().detach();this._ariaNode&&this._ariaNode.remove().destroy(!0)},bindUI:function(){this._bindInput(),this._bindList()},renderUI:function(){var t=this._createAriaNode(),n=this.get("boundingBox"),r=this.get("contentBox"),i=this._inputNode,o=this._createListNode(),u=i.get("parentNode");i.addClass(this.getClassName("input")).setAttrs({"aria-autocomplete":g,"aria-expanded":!1,"aria-owns":o.get("id")}),u.append(t),s&&n.plug(e.Plugin.Shim),n.setStyle("position","absolute"),this._ariaNode=t,this._boundingBox=n,this._contentBox=r,this._listNode=o,this._parentNode=u},syncUI:function(){this._syncResults(),this._syncVisibility()},hide:function(){return this.get(h)?this:this.set(w,!1)},selectItem:function(e,t){if(e){if(!e.hasClass(this[u]))return this}else{e=this.get(c);if(!e)return this}return this.fire(S,{itemNode:e,originEvent:t||null,result:e.getData(y)}),this},_activateNextItem:function(){var e=this.get(c),t;return e?t=e.next(this[l])||(this.get(p)?null:e):t=this._getFirstItemNode(),this.set(c,t),this},_activatePrevItem:function(){var e=this.get(c),t=e?e.previous(this[l]):this.get(p)&&this._getLastItemNode();return this.set(c,t||null),this},_add:function(t){var r=[];return i.each(n.isArray(t)?t:[t],function(e){r.push(this._createItemNode(e).setData(y,e))},this),r=e.all(r),this._listNode.append(r.toFrag()),r},_ariaSay:function(e,t){var r=this.get("strings."+e);this._ariaNode.set("text",t?n.sub(r,t):r)},_bindInput:function(){var e=this._inputNode,t,n,r;this.get("align")===null&&(r=this.get("tokenInput"),t=r&&r.get("boundingBox")||e,this.set("align",{node:t,points:["tl","bl"]}),!this.get(E)&&(n=t.get("offsetWidth"))&&this.set(E,n)),this._listEvents=this._listEvents.concat([e.after("blur",this._afterListInputBlur,this),e.after("focus",this._afterListInputFocus,this)])},_bindList:function(){this._listEvents=this._listEvents.concat([e.one("doc").after("click",this._afterDocClick,this),e.one("win").after("windowresize",this._syncPosition,this),this.after({mouseover:this._afterMouseOver,mouseout:this._afterMouseOut,activeItemChange:this._afterActiveItemChange,alwaysShowListChange:this._afterAlwaysShowListChange,hoveredItemChange:this._afterHoveredItemChange,resultsChange:this._afterResultsChange,visibleChange:this._afterVisibleChange}),this._listNode.delegate("click",this._onItemClick,this[l],this)])},_clear:function(){this.set(c,null),this._set(d,null),this._listNode.get("children").remove(!0)},_createAriaNode:function(){var e=r.create(this.ARIA_TEMPLATE);return e.addClass(this.getClassName("aria")).setAttrs({"aria-live":"polite",role:"status"})},_createItemNode:function(t){var n=r.create(this.ITEM_TEMPLATE);return n.addClass(this[u]).setAttrs({id:e.stamp(n),role:"option"}).setAttribute("data-text",t.text).append(t.display)},_createListNode:function(){var t=this.get("listNode")||r.create(this.LIST_TEMPLATE);return t.addClass(this.getClassName(g)).setAttrs({id:e.stamp(t),role:"listbox"}),this._set("listNode",t),this.get("contentBox").append(t),t},_getFirstItemNode:function(){return this._listNode.one(this[l])},_getLastItemNode:function(){return this._listNode.one(this[l]+":last-child")},_syncPosition:function(){this._syncUIPosAlign(),this._syncShim()},_syncResults:function(e){e||(e=this.get(b)),this._clear(),e.length&&(this._add(e),this._ariaSay("items_available")),this._syncPosition(),this.get("activateFirstItem")&&!this.get(c)&&this.set(c,this._getFirstItemNode())},_syncShim:s?function(){var e=this._boundingBox.shim;e&&e.sync()}:function(){},_syncVisibility:function(t){this.get(h)&&(t=!0,this.set(w,t)),typeof t=="undefined"&&(t=this.get(w)),this._inputNode.set("aria-expanded",t),this._boundingBox.set("aria-hidden",!t),t?this._syncPosition():(this.set(c,null),this._set(d,null),this._boundingBox.get("offsetWidth")),e.UA.ie===7&&e.one("body").addClass("yui3-ie7-sucks").removeClass("yui3-ie7-sucks")},_afterActiveItemChange:function(t){var n=this._inputNode,r=t.newVal,i=t.prevVal,s;i&&i._node&&i.removeClass(this[a]),r?(r.addClass(this[a]),n.set("aria-activedescendant",r.get(v))):n.removeAttribute("aria-activedescendant"),this.get("scrollIntoView")&&(s=r||n,(!s.inRegion(e.DOM.viewportRegion(),!0)||!s.inRegion(this._contentBox,!0))&&s.scrollIntoView())},_afterAlwaysShowListChange:function(e){this.set(w,e.newVal||this.get(b).length>0)},_afterDocClick:function(e){var t=this._boundingBox,n=e.target;n!==this._inputNode&&n!==t&&n.ancestor("#"+t.get("id"),!0)&&this.hide()},_afterHoveredItemChange:function(e){var t=e.newVal,n=e.prevVal;n&&n.removeClass(this[f]),t&&t.addClass(this[f])},_afterListInputBlur:function(){this._listInputFocused=!1,this.get(w)&&!this._mouseOverList&&(this._lastInputKey!==o||!this.get("tabSelect")||!this.get(c))&&this.hide()},_afterListInputFocus:function(){this._listInputFocused=!0},_afterMouseOver:function(e){var t=e.domEvent.target.ancestor(this[l],!0);this._mouseOverList=!0,t&&this._set(d,t)},_afterMouseOut:function(){this._mouseOverList=!1,this._set(d,null)},_afterResultsChange:function(e){this._syncResults(e.newVal),this.get(h)||this.set(w,!!e.newVal.length)},_afterVisibleChange:function(e){this._syncVisibility(!!e.newVal)},_onItemClick:function(e){var t=e.currentTarget;this.set(c,t),this.selectItem(t,e)},_defSelectFn:function(e){var t=e.result.text;this._inputNode.focus(),this._updateValue(t),this._ariaSay("item_selected",{item:t}),this.hide()}},{ATTRS:{activateFirstItem:{value:!1},activeItem:{setter:e.one,value:null},alwaysShowList:{value:!1},circular:{value:!0},hoveredItem:{readOnly:!0,value:null},listNode:{writeOnce:"initOnly",value:null},scrollIntoView:{value:!1},strings:{valueFn:function(){return e.Intl.get("autocomplete-list")}},tabSelect:{value:!0},visible:{value:!1}},CSS_PREFIX:e.ClassNameManager.getClassName("aclist")});e.AutoCompleteList=x,e.AutoComplete=x},"3.7.3",{lang:["en"],requires:["autocomplete-base","event-resize","node-screen","selector-css3","shim-plugin","widget","widget-position","widget-position-align"],skinnable:!0});
