/*
YUI 3.16.0 (build 76f0e08)
Copyright 2014 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/

YUI.add("history-base",function(e,t){function p(){this._init.apply(this,arguments)}function d(e){return n.type(e)==="object"}var n=e.Lang,r=e.Object,i=YUI.namespace("Env.History"),s=e.Array,o=e.config.doc,u=o.documentMode,a=e.config.win,f={merge:!0},l="change",c="add",h="replace";e.augment(p,e.EventTarget,null,null,{emitFacade:!0,prefix:"history",preventable:!1,queueable:!0}),i._state||(i._state={}),p.NAME="historyBase",p.SRC_ADD=c,p.SRC_REPLACE=h,p.html5=!!(a.history&&a.history.pushState&&a.history.replaceState&&("onpopstate"in a||e.UA.gecko>=2)&&(!e.UA.android||e.UA.android>=2.4)),p.nativeHashChange=("onhashchange"in a||"onhashchange"in o)&&(!u||u>7),e.mix(p.prototype,{_init:function(e){var t;e=this._config=e||{},this.force=!!e.force,t=this._initialState=this._initialState||e.initialState||null,this.publish(l,{broadcast:2,defaultFn:this._defChangeFn}),t&&this.replace(t)},add:function(){var e=s(arguments,0,!0);return e.unshift(c),this._change.apply(this,e)},addValue:function(e,t,n){var r={};return r[e]=t,this._change(c,r,n)},get:function(t){var n=i._state,s=d(n);return t?s&&r.owns(n,t)?n[t]:undefined:s?e.mix({},n,!0):n},replace:function(){var e=s(arguments,0,!0);return e.unshift(h),this._change.apply(this,e)},replaceValue:function(e,t,n){var r={};return r[e]=t,this._change(h,r,n)},_change:function(t,n,r){return r=r?e.merge(f,r):f,r.merge&&d(n)&&d(i._state)&&(n=e.merge(i._state,n)),this._resolveChanges(t,n,r),this},_fireEvents:function(e,t,n){this.fire(l,{_options:n,changed:t.changed,newVal:t.newState,prevVal:t.prevState,removed:t.removed,src:e}),r.each(t.changed,function(t,n){this._fireChangeEvent(e,n,t)},this),r.each(t.removed,function(t,n){this._fireRemoveEvent(e,n,t)},this)},_fireChangeEvent:function(e,t,n){this.fire(t+"Change",{newVal:n.newVal,prevVal:n.prevVal,src:e})},_fireRemoveEvent:function(e,t,n){this.fire(t+"Remove",{prevVal:n,src:e})},_resolveChanges:function(e,t,n){var s={},o,u=i._state,a={};t||(t={}),n||(n={}),d(t)&&d(u)?(r.each(t,function(e,t){var n=u[t];e!==n&&(s[t]={newVal:e,prevVal:n},o=!0)},this),r.each(u,function(e,n){if(!r.owns(t,n)||t[n]===null)delete t[n],a[n]=e,o=!0},this)):o=t!==u,(o||this.force)&&this._fireEvents(e,{changed:s,newState:t,prevState:u,removed:a},n)},_storeState:function(e,t){i._state=t||{}},_defChangeFn:function(e){this._storeState(e.src,e.newVal,e._options)}},!0),e.HistoryBase=p},"3.16.0",{requires:["event-custom-complex"]});
