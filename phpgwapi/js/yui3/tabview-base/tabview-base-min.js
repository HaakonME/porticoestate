/*
YUI 3.7.3 (build 5687)
Copyright 2012 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/
YUI.add("tabview-base",function(e,t){var n=e.ClassNameManager.getClassName,r="tabview",i="tab",s="content",o="panel",u="selected",a={},f=".",l={tabview:n(r),tabviewPanel:n(r,o),tabviewList:n(r,"list"),tab:n(i),tabLabel:n(i,"label"),tabPanel:n(i,o),selectedTab:n(i,u),selectedPanel:n(i,o,u)},c={tabview:f+l.tabview,tabviewList:"> ul",tab:"> ul > li",tabLabel:"> ul > li > a",tabviewPanel:"> div",tabPanel:"> div > div",selectedTab:"> ul > "+f+l.selectedTab,selectedPanel:"> div "+f+l.selectedPanel},h=function(e){this.init.apply(this,arguments)};h.NAME="tabviewBase",h._queries=c,h._classNames=l,e.mix(h.prototype,{init:function(t){t=t||a,this._node=t.host||e.one(t.node),this.refresh()},initClassNames:function(t){e.Object.each(c,function(e,n){if(l[n]){var r=this.all(e);t!==undefined&&(r=r.item(t)),r&&r.addClass(l[n])}},this._node),this._node.addClass(l.tabview)},_select:function(e){var t=this._node,n=t.one(c.selectedTab),r=t.one(c.selectedPanel),i=t.all(c.tab).item(e),s=t.all(c.tabPanel).item(e);n&&n.removeClass(l.selectedTab),r&&r.removeClass(l.selectedPanel),i&&i.addClass(l.selectedTab),s&&s.addClass(l.selectedPanel)},initState:function(){var e=this._node,t=e.one(c.selectedTab),n=t?e.all(c.tab).indexOf(t):0;this._select(n)},_scrubTextNodes:function(){this._node.one(c.tabviewList).get("childNodes").each(function(e){e.get("nodeType")===3&&e.remove()})},refresh:function(){this._scrubTextNodes(),this.initClassNames(),this.initState(),this.initEvents()},tabEventName:"click",initEvents:function(){this._node.delegate(this.tabEventName,this.onTabEvent,c.tab,this)},onTabEvent:function(e){e.preventDefault(),this._select(this._node.all(c.tab).indexOf(e.currentTarget))},destroy:function(){this._node.detach(this.tabEventName)}}),e.TabviewBase=h},"3.7.3",{requires:["node-event-delegate","classnamemanager","skin-sam-tabview"]});
