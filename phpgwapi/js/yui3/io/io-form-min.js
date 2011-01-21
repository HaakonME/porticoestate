/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.3.0
build: 3167
*/
YUI.add("io-form",function(B){var A=encodeURIComponent;B.mix(B.io,{_serialize:function(M,R){var I=[],N=M.useDisabled||false,Q=0,C=(typeof M.id==="string")?M.id:M.id.getAttribute("id"),K,J,E,P,L,H,O,F,G,D;if(!C){C=B.guid("io:");M.id.setAttribute("id",C);}J=B.config.doc.getElementById(C);for(H=0,O=J.elements.length;H<O;++H){K=J.elements[H];L=K.disabled;E=K.name;if(N?E:E&&!L){E=A(E)+"=";P=A(K.value);switch(K.type){case"select-one":if(K.selectedIndex>-1){D=K.options[K.selectedIndex];I[Q++]=E+A(D.attributes.value&&D.attributes.value.specified?D.value:D.text);}break;case"select-multiple":if(K.selectedIndex>-1){for(F=K.selectedIndex,G=K.options.length;F<G;++F){D=K.options[F];if(D.selected){I[Q++]=E+A(D.attributes.value&&D.attributes.value.specified?D.value:D.text);}}}break;case"radio":case"checkbox":if(K.checked){I[Q++]=E+P;}break;case"file":case undefined:case"reset":case"button":break;case"submit":default:I[Q++]=E+P;}}}return R?I.join("&")+"&"+R:I.join("&");}},true);},"3.3.0",{requires:["io-base","node-base"]});