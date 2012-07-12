/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.3.0
build: 3167
*/
YUI.add("widget-locale",function(f){var c=true,g="locale",e="initValue",b="-",a="",d=f.Widget;d.ATTRS[g]={value:"en"};d.ATTRS.strings.lazyAdd=false;f.mix(d.prototype,{_setStrings:function(i,h){var j=this._strs;h=h.toLowerCase();if(!j[h]){j[h]={};}f.aggregate(j[h],i,c);return j[h];},_getStrings:function(h){return this._strs[h.toLowerCase()];},getStrings:function(r){r=(r||this.get(g)).toLowerCase();var p=this.getDefaultLocale().toLowerCase(),j=this._getStrings(p),q=(j)?f.merge(j):{},n=r.split(b),o,m,k,h;if(r!==p||n.length>1){h=a;for(m=0,k=n.length;m<k;++m){h+=n[m];o=this._getStrings(h);if(o){f.aggregate(q,o,c);}h+=b;}}return q;},getString:function(j,i){i=(i||this.get(g)).toLowerCase();var k=(this.getDefaultLocale()).toLowerCase(),l=this._getStrings(k)||{},m=l[j],h=i.lastIndexOf(b);if(i!==k||h!=-1){do{l=this._getStrings(i);if(l&&j in l){m=l[j];break;}h=i.lastIndexOf(b);if(h!=-1){i=i.substring(0,h);}}while(h!=-1);}return m;},getDefaultLocale:function(){return this._state.get(g,e);},_strSetter:function(h){return this._setStrings(h,this.get(g));},_strGetter:function(h){return this._getStrings(this.get(g));}},true);},"3.3.0",{requires:["widget-base"]});