/*
YUI 3.16.0 (build 76f0e08)
Copyright 2014 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/

YUI.add("cache-base",function(e,t){var n=e.Lang,r=e.Lang.isDate,i=function(){i.superclass.constructor.apply(this,arguments)};e.mix(i,{NAME:"cache",ATTRS:{max:{value:0,setter:"_setMax"},size:{readOnly:!0,getter:"_getSize"},uniqueKeys:{value:!1},expires:{value:0,validator:function(t){return e.Lang.isDate(t)||e.Lang.isNumber(t)&&t>=0}},entries:{readOnly:!0,getter:"_getEntries"}}}),e.extend(i,e.Base,{_entries:null,initializer:function(e){this.publish("add",{defaultFn:this._defAddFn}),this.publish("flush",{defaultFn:this._defFlushFn}),this._entries=[]},destructor:function(){this._entries=[]},_setMax:function(e){var t=this._entries;if(e>0){if(t)while(t.length>e)t.shift()}else e=0,this._entries=[];return e},_getSize:function(){return this._entries.length},_getEntries:function(){return this._entries},_defAddFn:function(e){var t=this._entries,r=e.entry,i=this.get("max"),s;this.get("uniqueKeys")&&(s=this._position(e.entry.request),n.isValue(s)&&t.splice(s,1));while(i&&t.length>=i)t.shift();t[t.length]=r},_defFlushFn:function(e){var t=this._entries,r=e.details[0],i;r&&n.isValue(r.request)?(i=this._position(r.request),n.isValue(i)&&t.splice(i,1)):this._entries=[]},_isMatch:function(e,t){return!t.expires||new Date<t.expires?e===t.request:!1},_position:function(e){var t=this._entries,n=t.length,r=n-1;if(this.get("max")===null||this.get("max")>0)for(;r>=0;r--)if(this._isMatch(e,t[r]))return r;return null},add:function(e,t){var i=this.get("expires");this.get("initialized")&&(this.get("max")===null||this.get("max")>0)&&(n.isValue(e)||n.isNull(e)||n.isUndefined(e))&&this.fire("add",{entry:{request:e,response:t,cached:new Date,expires:r(i)?i:i?new Date((new Date).getTime()+this.get("expires")):null}})},flush:function(e){this.fire("flush",{request:n.isValue(e)?e:null})},retrieve:function(e){var t=this._entries,r=t.length,i=null,s;if(r>0&&(this.get("max")===null||this.get("max")>0)){this.fire("request",{request:e}),s=this._position(e);if(n.isValue(s))return i=t[s],this.fire("retrieve",{entry:i}),s<r-1&&(t.splice(s,1),t[t.length]=i),i}return null}}),e.Cache=i},"3.16.0",{requires:["base"]});
