/*
Copyright (c) 2007, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 2.3.1
*/
YAHOO.util.Attribute=function(B,A){if(A){this.owner=A;this.configure(B,true);}};YAHOO.util.Attribute.prototype={name:undefined,value:null,owner:null,readOnly:false,writeOnce:false,_initialConfig:null,_written:false,method:null,validator:null,getValue:function(){return this.value;},setValue:function(F,B){var E;var A=this.owner;var C=this.name;var D={type:C,prevValue:this.getValue(),newValue:F};if(this.readOnly||(this.writeOnce&&this._written)){return false;}if(this.validator&&!this.validator.call(A,F)){return false;}if(!B){E=A.fireBeforeChangeEvent(D);if(E===false){return false;}}if(this.method){this.method.call(A,F);}this.value=F;this._written=true;D.type=C;if(!B){this.owner.fireChangeEvent(D);}return true;},configure:function(B,C){B=B||{};this._written=false;this._initialConfig=this._initialConfig||{};for(var A in B){if(A&&YAHOO.lang.hasOwnProperty(B,A)){this[A]=B[A];if(C){this._initialConfig[A]=B[A];}}}},resetValue:function(){return this.setValue(this._initialConfig.value);},resetConfig:function(){this.configure(this._initialConfig);},refresh:function(A){this.setValue(this.value,A);}};(function(){var A=YAHOO.util.Lang;YAHOO.util.AttributeProvider=function(){};YAHOO.util.AttributeProvider.prototype={_configs:null,get:function(C){this._configs=this._configs||{};var B=this._configs[C];if(!B){return undefined;}return B.value;},set:function(D,E,B){this._configs=this._configs||{};var C=this._configs[D];if(!C){return false;}return C.setValue(E,B);},getAttributeKeys:function(){this._configs=this._configs;var D=[];var B;for(var C in this._configs){B=this._configs[C];if(A.hasOwnProperty(this._configs,C)&&!A.isUndefined(B)){D[D.length]=C;}}return D;},setAttributes:function(D,B){for(var C in D){if(A.hasOwnProperty(D,C)){this.set(C,D[C],B);}}},resetValue:function(C,B){this._configs=this._configs||{};if(this._configs[C]){this.set(C,this._configs[C]._initialConfig.value,B);return true;}return false;},refresh:function(E,C){this._configs=this._configs;E=((A.isString(E))?[E]:E)||this.getAttributeKeys();for(var D=0,B=E.length;D<B;++D){if(this._configs[E[D]]&&!A.isUndefined(this._configs[E[D]].value)&&!A.isNull(this._configs[E[D]].value)){this._configs[E[D]].refresh(C);}}},register:function(B,C){this.setAttributeConfig(B,C);},getAttributeConfig:function(C){this._configs=this._configs||{};var B=this._configs[C]||{};var D={};for(C in B){if(A.hasOwnProperty(B,C)){D[C]=B[C];}}return D;},setAttributeConfig:function(B,C,D){this._configs=this._configs||{};C=C||{};if(!this._configs[B]){C.name=B;this._configs[B]=this.createAttribute(C);}else{this._configs[B].configure(C,D);}},configureAttribute:function(B,C,D){this.setAttributeConfig(B,C,D);},resetAttributeConfig:function(B){this._configs=this._configs||{};this._configs[B].resetConfig();},subscribe:function(B,C){this._events=this._events||{};if(!(B in this._events)){this._events[B]=this.createEvent(B);}YAHOO.util.EventProvider.prototype.subscribe.apply(this,arguments);},on:function(){this.subscribe.apply(this,arguments);},addListener:function(){this.subscribe.apply(this,arguments);},fireBeforeChangeEvent:function(C){var B="before";B+=C.type.charAt(0).toUpperCase()+C.type.substr(1)+"Change";C.type=B;return this.fireEvent(C.type,C);},fireChangeEvent:function(B){B.type+="Change";return this.fireEvent(B.type,B);},createAttribute:function(B){return new YAHOO.util.Attribute(B,this);}};YAHOO.augment(YAHOO.util.AttributeProvider,YAHOO.util.EventProvider);})();(function(){var D=YAHOO.util.Dom,F=YAHOO.util.AttributeProvider;YAHOO.util.Element=function(G,H){if(arguments.length){this.init(G,H);}};YAHOO.util.Element.prototype={DOM_EVENTS:null,appendChild:function(G){G=G.get?G.get("element"):G;this.get("element").appendChild(G);},getElementsByTagName:function(G){return this.get("element").getElementsByTagName(G);},hasChildNodes:function(){return this.get("element").hasChildNodes();},insertBefore:function(G,H){G=G.get?G.get("element"):G;H=(H&&H.get)?H.get("element"):H;this.get("element").insertBefore(G,H);},removeChild:function(G){G=G.get?G.get("element"):G;this.get("element").removeChild(G);return true;},replaceChild:function(G,H){G=G.get?G.get("element"):G;H=H.get?H.get("element"):H;return this.get("element").replaceChild(G,H);},initAttributes:function(G){},addListener:function(K,J,L,I){var H=this.get("element");I=I||this;H=this.get("id")||H;var G=this;if(!this._events[K]){if(this.DOM_EVENTS[K]){YAHOO.util.Event.addListener(H,K,function(M){if(M.srcElement&&!M.target){M.target=M.srcElement;}G.fireEvent(K,M);},L,I);}this.createEvent(K,this);}YAHOO.util.EventProvider.prototype.subscribe.apply(this,arguments);},on:function(){this.addListener.apply(this,arguments);},subscribe:function(){this.addListener.apply(this,arguments);},removeListener:function(H,G){this.unsubscribe.apply(this,arguments);},addClass:function(G){D.addClass(this.get("element"),G);},getElementsByClassName:function(H,G){return D.getElementsByClassName(H,G,this.get("element"));},hasClass:function(G){return D.hasClass(this.get("element"),G);},removeClass:function(G){return D.removeClass(this.get("element"),G);},replaceClass:function(H,G){return D.replaceClass(this.get("element"),H,G);},setStyle:function(I,H){var G=this.get("element");if(!G){return this._queue[this._queue.length]=["setStyle",arguments];}return D.setStyle(G,I,H);},getStyle:function(G){return D.getStyle(this.get("element"),G);},fireQueue:function(){var H=this._queue;for(var I=0,G=H.length;I<G;++I){this[H[I][0]].apply(this,H[I][1]);}},appendTo:function(H,I){H=(H.get)?H.get("element"):D.get(H);this.fireEvent("beforeAppendTo",{type:"beforeAppendTo",target:H});I=(I&&I.get)?I.get("element"):D.get(I);var G=this.get("element");if(!G){return false;}if(!H){return false;}if(G.parent!=H){if(I){H.insertBefore(G,I);}else{H.appendChild(G);}}this.fireEvent("appendTo",{type:"appendTo",target:H});},get:function(G){var I=this._configs||{};var H=I.element;if(H&&!I[G]&&!YAHOO.lang.isUndefined(H.value[G])){return H.value[G];}return F.prototype.get.call(this,G);},setAttributes:function(L,H){var K=this.get("element");
for(var J in L){if(!this._configs[J]&&!YAHOO.lang.isUndefined(K[J])){this.setAttributeConfig(J);}}for(var I=0,G=this._configOrder.length;I<G;++I){if(L[this._configOrder[I]]){this.set(this._configOrder[I],L[this._configOrder[I]],H);}}},set:function(H,J,G){var I=this.get("element");if(!I){this._queue[this._queue.length]=["set",arguments];if(this._configs[H]){this._configs[H].value=J;}return ;}if(!this._configs[H]&&!YAHOO.lang.isUndefined(I[H])){C.call(this,H);}return F.prototype.set.apply(this,arguments);},setAttributeConfig:function(G,I,J){var H=this.get("element");if(H&&!this._configs[G]&&!YAHOO.lang.isUndefined(H[G])){C.call(this,G,I);}else{F.prototype.setAttributeConfig.apply(this,arguments);}this._configOrder.push(G);},getAttributeKeys:function(){var H=this.get("element");var I=F.prototype.getAttributeKeys.call(this);for(var G in H){if(!this._configs[G]){I[G]=I[G]||H[G];}}return I;},createEvent:function(H,G){this._events[H]=true;F.prototype.createEvent.apply(this,arguments);},init:function(H,G){A.apply(this,arguments);}};var A=function(H,G){this._queue=this._queue||[];this._events=this._events||{};this._configs=this._configs||{};this._configOrder=[];G=G||{};G.element=G.element||H||null;this.DOM_EVENTS={"click":true,"dblclick":true,"keydown":true,"keypress":true,"keyup":true,"mousedown":true,"mousemove":true,"mouseout":true,"mouseover":true,"mouseup":true,"focus":true,"blur":true,"submit":true};var I=false;if(YAHOO.lang.isString(H)){C.call(this,"id",{value:G.element});}if(D.get(H)){I=true;E.call(this,G);B.call(this,G);}YAHOO.util.Event.onAvailable(G.element,function(){if(!I){E.call(this,G);}this.fireEvent("available",{type:"available",target:G.element});},this,true);YAHOO.util.Event.onContentReady(G.element,function(){if(!I){B.call(this,G);}this.fireEvent("contentReady",{type:"contentReady",target:G.element});},this,true);};var E=function(G){this.setAttributeConfig("element",{value:D.get(G.element),readOnly:true});};var B=function(G){this.initAttributes(G);this.setAttributes(G,true);this.fireQueue();};var C=function(G,I){var H=this.get("element");I=I||{};I.name=G;I.method=I.method||function(J){H[G]=J;};I.value=I.value||H[G];this._configs[G]=new YAHOO.util.Attribute(I,this);};YAHOO.augment(YAHOO.util.Element,F);})();YAHOO.register("element",YAHOO.util.Element,{version:"2.3.1",build:"541"});