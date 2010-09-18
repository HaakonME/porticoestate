/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 2.8.1
*/
(function(){var I=document,B=I.createElement("p"),D=B.style,C=YAHOO.lang,L={},H={},E=0,J=("cssFloat" in D)?"cssFloat":"styleFloat",F,A,K;A=("opacity" in D)?function(M){M.opacity="";}:function(M){M.filter="";};D.border="1px solid red";D.border="";K=D.borderLeft?function(M,O){var N;if(O!==J&&O.toLowerCase().indexOf("float")!=-1){O=J;}if(typeof M[O]==="string"){switch(O){case"opacity":case"filter":A(M);break;case"font":M.font=M.fontStyle=M.fontVariant=M.fontWeight=M.fontSize=M.lineHeight=M.fontFamily="";break;default:for(N in M){if(N.indexOf(O)===0){M[N]="";}}}}}:function(M,N){if(N!==J&&N.toLowerCase().indexOf("float")!=-1){N=J;}if(C.isString(M[N])){if(N==="opacity"){A(M);}else{M[N]="";}}};function G(T,O){var W,R,V,U={},N,X,Q,S,M,P;if(!(this instanceof G)){return new G(T,O);}R=T&&(T.nodeName?T:I.getElementById(T));if(T&&H[T]){return H[T];}else{if(R&&R.yuiSSID&&H[R.yuiSSID]){return H[R.yuiSSID];}}if(!R||!/^(?:style|link)$/i.test(R.nodeName)){R=I.createElement("style");R.type="text/css";}if(C.isString(T)){if(T.indexOf("{")!=-1){if(R.styleSheet){R.styleSheet.cssText=T;}else{R.appendChild(I.createTextNode(T));}}else{if(!O){O=T;}}}if(!R.parentNode||R.parentNode.nodeName.toLowerCase()!=="head"){W=(R.ownerDocument||I).getElementsByTagName("head")[0];W.appendChild(R);}V=R.sheet||R.styleSheet;N=V&&("cssRules" in V)?"cssRules":"rules";Q=("deleteRule" in V)?function(Y){V.deleteRule(Y);}:function(Y){V.removeRule(Y);};X=("insertRule" in V)?function(a,Z,Y){V.insertRule(a+" {"+Z+"}",Y);}:function(a,Z,Y){V.addRule(a,Z,Y);};for(S=V[N].length-1;S>=0;--S){M=V[N][S];P=M.selectorText;if(U[P]){U[P].style.cssText+=";"+M.style.cssText;Q(S);}else{U[P]=M;}}R.yuiSSID="yui-stylesheet-"+(E++);G.register(R.yuiSSID,this);if(O){G.register(O,this);}C.augmentObject(this,{getId:function(){return R.yuiSSID;},node:R,enable:function(){V.disabled=false;return this;},disable:function(){V.disabled=true;return this;},isEnabled:function(){return !V.disabled;},set:function(b,a){var d=U[b],c=b.split(/\s*,\s*/),Z,Y;if(c.length>1){for(Z=c.length-1;Z>=0;--Z){this.set(c[Z],a);}return this;}if(!G.isValidSelector(b)){return this;}if(d){d.style.cssText=G.toCssText(a,d.style.cssText);}else{Y=V[N].length;a=G.toCssText(a);if(a){X(b,a,Y);U[b]=V[N][Y];}}return this;},unset:function(b,a){var d=U[b],c=b.split(/\s*,\s*/),Y=!a,e,Z;if(c.length>1){for(Z=c.length-1;Z>=0;--Z){this.unset(c[Z],a);}return this;}if(d){if(!Y){if(!C.isArray(a)){a=[a];}D.cssText=d.style.cssText;for(Z=a.length-1;Z>=0;--Z){K(D,a[Z]);}if(D.cssText){d.style.cssText=D.cssText;}else{Y=true;}}if(Y){e=V[N];for(Z=e.length-1;Z>=0;--Z){if(e[Z]===d){delete U[b];Q(Z);break;}}}}return this;},getCssText:function(Z){var a,Y;if(C.isString(Z)){a=U[Z.split(/\s*,\s*/)[0]];return a?a.style.cssText:null;}else{Y=[];for(Z in U){if(U.hasOwnProperty(Z)){a=U[Z];Y.push(a.selectorText+" {"+a.style.cssText+"}");}}return Y.join("\n");}}},true);}F=function(M,O){var N=M.styleFloat||M.cssFloat||M["float"],Q;D.cssText=O||"";if(C.isString(M)){D.cssText+=";"+M;}else{if(N&&!M[J]){M=C.merge(M);delete M.styleFloat;delete M.cssFloat;delete M["float"];M[J]=N;}for(Q in M){if(M.hasOwnProperty(Q)){try{D[Q]=C.trim(M[Q]);}catch(P){}}}}return D.cssText;};C.augmentObject(G,{toCssText:(("opacity" in D)?F:function(M,N){if(C.isObject(M)&&"opacity" in M){M=C.merge(M,{filter:"alpha(opacity="+(M.opacity*100)+")"});delete M.opacity;}return F(M,N);}),register:function(M,N){return !!(M&&N instanceof G&&!H[M]&&(H[M]=N));},isValidSelector:function(N){var M=false;if(N&&C.isString(N)){if(!L.hasOwnProperty(N)){L[N]=!/\S/.test(N.replace(/\s+|\s*[+~>]\s*/g," ").replace(/([^ ])\[.*?\]/g,"$1").replace(/([^ ])::?[a-z][a-z\-]+[a-z](?:\(.*?\))?/ig,"$1").replace(/(?:^| )[a-z0-6]+/ig," ").replace(/\\./g,"").replace(/[.#]\w[\w\-]*/g,""));}M=L[N];}return M;}},true);YAHOO.util.StyleSheet=G;})();YAHOO.register("stylesheet",YAHOO.util.StyleSheet,{version:"2.8.1",build:"19"});