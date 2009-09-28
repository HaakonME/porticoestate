/*
Copyright (c) 2009, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 2.8.0r4
*/
YAHOO.namespace("util");YAHOO.util.Cookie={_createCookieString:function(B,D,C,A){var F=YAHOO.lang,E=encodeURIComponent(B)+"="+(C?encodeURIComponent(D):D);if(F.isObject(A)){if(A.expires instanceof Date){E+="; expires="+A.expires.toUTCString();}if(F.isString(A.path)&&A.path!==""){E+="; path="+A.path;}if(F.isString(A.domain)&&A.domain!==""){E+="; domain="+A.domain;}if(A.secure===true){E+="; secure";}}return E;},_createCookieHashString:function(B){var D=YAHOO.lang;if(!D.isObject(B)){throw new TypeError("Cookie._createCookieHashString(): Argument must be an object.");}var C=[];for(var A in B){if(D.hasOwnProperty(B,A)&&!D.isFunction(B[A])&&!D.isUndefined(B[A])){C.push(encodeURIComponent(A)+"="+encodeURIComponent(String(B[A])));}}return C.join("&");},_parseCookieHash:function(E){var D=E.split("&"),F=null,C={};if(E.length>0){for(var B=0,A=D.length;B<A;B++){F=D[B].split("=");C[decodeURIComponent(F[0])]=decodeURIComponent(F[1]);}}return C;},_parseCookieString:function(J,A){var K={};if(YAHOO.lang.isString(J)&&J.length>0){var B=(A===false?function(L){return L;}:decodeURIComponent);var H=J.split(/;\s/g),I=null,C=null,E=null;for(var D=0,F=H.length;D<F;D++){E=H[D].match(/([^=]+)=/i);if(E instanceof Array){try{I=decodeURIComponent(E[1]);C=B(H[D].substring(E[1].length+1));}catch(G){}}else{I=decodeURIComponent(H[D]);C="";}K[I]=C;}}return K;},exists:function(A){if(!YAHOO.lang.isString(A)||A===""){throw new TypeError("Cookie.exists(): Cookie name must be a non-empty string.");}var B=this._parseCookieString(document.cookie,true);return B.hasOwnProperty(A);},get:function(B,A){var E=YAHOO.lang,C;if(E.isFunction(A)){C=A;A={};}else{if(E.isObject(A)){C=A.converter;}else{A={};}}var D=this._parseCookieString(document.cookie,!A.raw);if(!E.isString(B)||B===""){throw new TypeError("Cookie.get(): Cookie name must be a non-empty string.");}if(E.isUndefined(D[B])){return null;}if(!E.isFunction(C)){return D[B];}else{return C(D[B]);}},getSub:function(A,C,B){var E=YAHOO.lang,D=this.getSubs(A);if(D!==null){if(!E.isString(C)||C===""){throw new TypeError("Cookie.getSub(): Subcookie name must be a non-empty string.");}if(E.isUndefined(D[C])){return null;}if(!E.isFunction(B)){return D[C];}else{return B(D[C]);}}else{return null;}},getSubs:function(B){var A=YAHOO.lang.isString;if(!A(B)||B===""){throw new TypeError("Cookie.getSubs(): Cookie name must be a non-empty string.");}var C=this._parseCookieString(document.cookie,false);if(A(C[B])){return this._parseCookieHash(C[B]);}return null;},remove:function(B,A){if(!YAHOO.lang.isString(B)||B===""){throw new TypeError("Cookie.remove(): Cookie name must be a non-empty string.");}A=YAHOO.lang.merge(A||{},{expires:new Date(0)});return this.set(B,"",A);},removeSub:function(B,E,A){var F=YAHOO.lang;A=A||{};if(!F.isString(B)||B===""){throw new TypeError("Cookie.removeSub(): Cookie name must be a non-empty string.");}if(!F.isString(E)||E===""){throw new TypeError("Cookie.removeSub(): Subcookie name must be a non-empty string.");}var D=this.getSubs(B);if(F.isObject(D)&&F.hasOwnProperty(D,E)){delete D[E];if(!A.removeIfEmpty){return this.setSubs(B,D,A);}else{for(var C in D){if(F.hasOwnProperty(D,C)&&!F.isFunction(D[C])&&!F.isUndefined(D[C])){return this.setSubs(B,D,A);}}return this.remove(B,A);}}else{return"";}},set:function(B,C,A){var E=YAHOO.lang;A=A||{};if(!E.isString(B)){throw new TypeError("Cookie.set(): Cookie name must be a string.");}if(E.isUndefined(C)){throw new TypeError("Cookie.set(): Value cannot be undefined.");}var D=this._createCookieString(B,C,!A.raw,A);document.cookie=D;return D;},setSub:function(B,D,C,A){var F=YAHOO.lang;if(!F.isString(B)||B===""){throw new TypeError("Cookie.setSub(): Cookie name must be a non-empty string.");}if(!F.isString(D)||D===""){throw new TypeError("Cookie.setSub(): Subcookie name must be a non-empty string.");}if(F.isUndefined(C)){throw new TypeError("Cookie.setSub(): Subcookie value cannot be undefined.");}var E=this.getSubs(B);if(!F.isObject(E)){E={};}E[D]=C;return this.setSubs(B,E,A);},setSubs:function(B,C,A){var E=YAHOO.lang;if(!E.isString(B)){throw new TypeError("Cookie.setSubs(): Cookie name must be a string.");}if(!E.isObject(C)){throw new TypeError("Cookie.setSubs(): Cookie value must be an object.");}var D=this._createCookieString(B,this._createCookieHashString(C),false,A);document.cookie=D;return D;}};YAHOO.register("cookie",YAHOO.util.Cookie,{version:"2.8.0r4",build:"2449"});