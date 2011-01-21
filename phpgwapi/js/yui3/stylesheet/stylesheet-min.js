/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.3.0
build: 3167
*/
YUI.add("stylesheet",function(b){var k=b.config.doc,c=k.createElement("p"),g=c.style,e=b.Lang.isString,n={},j={},l=("cssFloat" in g)?"cssFloat":"styleFloat",h,a,m,o="opacity",q="float",f="";a=(o in g)?function(d){d.opacity=f;}:function(d){d.filter=f;};g.border="1px solid red";g.border=f;m=g.borderLeft?function(d,s){var r;if(s!==l&&s.toLowerCase().indexOf(q)!=-1){s=l;}if(e(d[s])){switch(s){case o:case"filter":a(d);break;case"font":d.font=d.fontStyle=d.fontVariant=d.fontWeight=d.fontSize=d.lineHeight=d.fontFamily=f;break;default:for(r in d){if(r.indexOf(s)===0){d[r]=f;}}}}}:function(d,p){if(p!==l&&p.toLowerCase().indexOf(q)!=-1){p=l;}if(e(d[p])){if(p===o){a(d);}else{d[p]=f;}}};function i(x,s){var A,v,z,y={},p,B,u,w,d,t;if(!(b.instanceOf(this,i))){return new i(x,s);}if(x){if(b.Node&&x instanceof b.Node){v=x._node;}else{if(x.nodeName){v=x;}else{if(e(x)){if(x&&j[x]){return j[x];}v=k.getElementById(x.replace(/^#/,f));}}}if(v&&j[b.stamp(v)]){return j[b.stamp(v)];}}if(!v||!/^(?:style|link)$/i.test(v.nodeName)){v=k.createElement("style");v.type="text/css";}if(e(x)){if(x.indexOf("{")!=-1){if(v.styleSheet){v.styleSheet.cssText=x;}else{v.appendChild(k.createTextNode(x));}}else{if(!s){s=x;}}}if(!v.parentNode||v.parentNode.nodeName.toLowerCase()!=="head"){A=(v.ownerDocument||k).getElementsByTagName("head")[0];A.appendChild(v);}z=v.sheet||v.styleSheet;p=z&&("cssRules" in z)?"cssRules":"rules";u=("deleteRule" in z)?function(r){z.deleteRule(r);}:function(r){z.removeRule(r);};B=("insertRule" in z)?function(D,C,r){z.insertRule(D+" {"+C+"}",r);}:function(D,C,r){z.addRule(D,C,r);};for(w=z[p].length-1;w>=0;--w){d=z[p][w];t=d.selectorText;if(y[t]){y[t].style.cssText+=";"+d.style.cssText;u(w);}else{y[t]=d;}}i.register(b.stamp(v),this);if(s){i.register(s,this);}b.mix(this,{getId:function(){return b.stamp(v);},enable:function(){z.disabled=false;return this;},disable:function(){z.disabled=true;return this;},isEnabled:function(){return !z.disabled;},set:function(E,D){var G=y[E],F=E.split(/\s*,\s*/),C,r;if(F.length>1){for(C=F.length-1;C>=0;--C){this.set(F[C],D);}return this;}if(!i.isValidSelector(E)){return this;}if(G){G.style.cssText=i.toCssText(D,G.style.cssText);}else{r=z[p].length;D=i.toCssText(D);if(D){B(E,D,r);y[E]=z[p][r];}}return this;},unset:function(E,D){var G=y[E],F=E.split(/\s*,\s*/),r=!D,H,C;if(F.length>1){for(C=F.length-1;C>=0;--C){this.unset(F[C],D);}return this;}if(G){if(!r){D=b.Array(D);g.cssText=G.style.cssText;for(C=D.length-1;C>=0;--C){m(g,D[C]);}if(g.cssText){G.style.cssText=g.cssText;}else{r=true;}}if(r){H=z[p];for(C=H.length-1;C>=0;--C){if(H[C]===G){delete y[E];u(C);break;}}}}return this;},getCssText:function(D){var E,C,r;if(e(D)){E=y[D.split(/\s*,\s*/)[0]];return E?E.style.cssText:null;}else{C=[];for(r in y){if(y.hasOwnProperty(r)){E=y[r];C.push(E.selectorText+" {"+E.style.cssText+"}");}}return C.join("\n");}}});}h=function(r,t){var s=r.styleFloat||r.cssFloat||r[q],d=b.Lang.trim,v;try{g.cssText=t||f;}catch(u){c=k.createElement("p");g=c.style;g.cssText=t||f;}if(s&&!r[l]){r=b.merge(r);delete r.styleFloat;delete r.cssFloat;delete r[q];r[l]=s;}for(v in r){if(r.hasOwnProperty(v)){try{g[v]=d(r[v]);}catch(p){}}}return g.cssText;};b.mix(i,{toCssText:((o in g)?h:function(d,p){if(o in d){d=b.merge(d,{filter:"alpha(opacity="+(d.opacity*100)+")"});delete d.opacity;}return h(d,p);}),register:function(d,p){return !!(d&&p instanceof i&&!j[d]&&(j[d]=p));},isValidSelector:function(p){var d=false;if(p&&e(p)){if(!n.hasOwnProperty(p)){n[p]=!/\S/.test(p.replace(/\s+|\s*[+~>]\s*/g," ").replace(/([^ ])\[.*?\]/g,"$1").replace(/([^ ])::?[a-z][a-z\-]+[a-z](?:\(.*?\))?/ig,"$1").replace(/(?:^| )[a-z0-6]+/ig," ").replace(/\\./g,f).replace(/[.#]\w[\w\-]*/g,f));}d=n[p];}return d;}},true);b.StyleSheet=i;},"3.3.0");