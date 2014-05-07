/*
YUI 3.16.0 (build 76f0e08)
Copyright 2014 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/

YUI.add("attribute-observable",function(e,t){function s(){this._ATTR_E_FACADE={},n.call(this,{emitFacade:!0})}var n=e.EventTarget,r="Change",i="broadcast";s._ATTR_CFG=[i],s.prototype={set:function(e,t,n){return this._setAttr(e,t,n)},_set:function(e,t,n){return this._setAttr(e,t,n,!0)},setAttrs:function(e,t){return this._setAttrs(e,t)},_setAttrs:function(e,t){var n;for(n in e)e.hasOwnProperty(n)&&this.set(n,e[n],t);return this},_fireAttrChange:function(t,n,i,s,o,u){var a=this,f=this._getFullType(t+r),l=a._state,c,h,p;u||(u=l.data[t]||{}),u.published||(p=a._publish(f),p.emitFacade=!0,p.defaultTargetOnly=!0,p.defaultFn=a._defAttrChangeFn,h=u.broadcast,h!==undefined&&(p.broadcast=h),u.published=!0),o?(c=e.merge(o),c._attrOpts=o):c=a._ATTR_E_FACADE,c.attrName=t,c.subAttrName=n,c.prevVal=i,c.newVal=s,a._hasPotentialSubscribers(f)?a.fire(f,c):this._setAttrVal(t,n,i,s,o,u)},_defAttrChangeFn:function(e,t){var n=e._attrOpts;n&&delete e._attrOpts,this._setAttrVal(e.attrName,e.subAttrName,e.prevVal,e.newVal,n)?t||(e.newVal=this.get(e.attrName)):t||e.stopImmediatePropagation()}},e.mix(s,n,!1,null,1),e.AttributeObservable=s,e.AttributeEvents=s},"3.16.0",{requires:["event-custom"]});
