/*
Copyright (c) 2008, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 2.5.1
*/
(function(){var C=YAHOO.util.Dom,A=YAHOO.util.Event,D=YAHOO.lang;var B=function(F,E){var G={element:F,attributes:E||{}};B.superclass.constructor.call(this,G.element,G.attributes);};B._instances={};B.getCropperById=function(E){if(B._instances[E]){return B._instances[E];}return false;};YAHOO.extend(B,YAHOO.util.Element,{CSS_MAIN:"yui-crop",CSS_MASK:"yui-crop-mask",CSS_RESIZE_MASK:"yui-crop-resize-mask",_image:null,_active:null,_resize:null,_resizeEl:null,_resizeMaskEl:null,_wrap:null,_mask:null,_createWrap:function(){this._wrap=document.createElement("div");this._wrap.id=this.get("element").id+"_wrap";this._wrap.className=this.CSS_MAIN;var F=this.get("element");this._wrap.style.width=F.width?F.width+"px":C.getStyle(F,"width");this._wrap.style.height=F.height?F.height+"px":C.getStyle(F,"height");var E=this.get("element").parentNode;E.replaceChild(this._wrap,this.get("element"));this._wrap.appendChild(this.get("element"));A.on(this._wrap,"mouseover",this._handleMouseOver,this,true);A.on(this._wrap,"mouseout",this._handleMouseOut,this,true);A.on(this._wrap,"click",function(G){A.stopEvent(G);},this,true);},_createMask:function(){this._mask=document.createElement("div");this._mask.className=this.CSS_MASK;this._wrap.appendChild(this._mask);},_createResize:function(){this._resizeEl=document.createElement("div");this._resizeEl.className=YAHOO.util.Resize.prototype.CSS_RESIZE;this._resizeEl.style.position="absolute";this._resizeEl.innerHTML='<div class="'+this.CSS_RESIZE_MASK+'"></div>';this._resizeMaskEl=this._resizeEl.firstChild;this._wrap.appendChild(this._resizeEl);this._resizeEl.style.top=this.get("initialXY")[1]+"px";this._resizeEl.style.left=this.get("initialXY")[0]+"px";this._resizeMaskEl.style.height=Math.floor(this.get("initHeight"))+"px";this._resizeMaskEl.style.width=Math.floor(this.get("initWidth"))+"px";this._resize=new YAHOO.util.Resize(this._resizeEl,{knobHandles:true,handles:"all",draggable:true,status:this.get("status"),minWidth:this.get("minWidth"),minHeight:this.get("minHeight"),ratio:this.get("ratio"),autoRatio:this.get("autoRatio"),height:this.get("initHeight"),width:this.get("initWidth")});this._setBackgroundImage(this.get("element").getAttribute("src",2));this._setBackgroundPosition(-(this.get("initialXY")[0]),-(this.get("initialXY")[1]));this._resize.on("startResize",this._handleStartResizeEvent,this,true);this._resize.on("dragEvent",this._handleDragEvent,this,true);this._resize.on("beforeResize",this._handleBeforeResizeEvent,this,true);this._resize.on("resize",this._handleResizeEvent,this,true);this._resize.dd.on("b4StartDragEvent",this._handleB4DragEvent,this,true);},_handleMouseOver:function(F){var E="keydown";if(YAHOO.env.ua.gecko||YAHOO.env.ua.opera){E="keypress";}if(!this._active){this._active=true;if(this.get("useKeys")){A.on(document,E,this._handleKeyPress,this,true);}}},_handleMouseOut:function(F){var E="keydown";if(YAHOO.env.ua.gecko||YAHOO.env.ua.opera){E="keypress";}this._active=false;if(this.get("useKeys")){A.removeListener(document,E,this._handleKeyPress);}},_moveEl:function(G,J){var H=0,E=0,I=this._setConstraints(),F=true;switch(G){case"down":H=-(J);if((I.bottom-J)<0){F=false;this._resizeEl.style.top=(I.top+I.bottom)+"px";}break;case"up":H=(J);if((I.top-J)<0){F=false;this._resizeEl.style.top="0px";}break;case"right":E=-(J);if((I.right-J)<0){F=false;this._resizeEl.style.left=(I.left+I.right)+"px";}break;case"left":E=J;if((I.left-J)<0){F=false;this._resizeEl.style.left="0px";}break;}if(F){this._resizeEl.style.left=(parseInt(this._resizeEl.style.left,10)-E)+"px";this._resizeEl.style.top=(parseInt(this._resizeEl.style.top,10)-H)+"px";this.fireEvent("moveEvent",{target:"keypress"});}else{this._setConstraints();}this._syncBackgroundPosition();},_handleKeyPress:function(G){var E=A.getCharCode(G),F=false,H=((G.shiftKey)?this.get("shiftKeyTick"):this.get("keyTick"));switch(E){case 37:this._moveEl("left",H);F=true;break;case 38:this._moveEl("up",H);F=true;break;case 39:this._moveEl("right",H);F=true;break;case 40:this._moveEl("down",H);F=true;break;default:}if(F){A.preventDefault(G);}},_handleB4DragEvent:function(){this._setConstraints();},_handleDragEvent:function(){this._syncBackgroundPosition();this.fireEvent("dragEvent",arguments);this.fireEvent("moveEvent",{target:"dragevent"});},_handleBeforeResizeEvent:function(F){var I=C.getRegion(this.get("element")),J=this._resize._cache,H=this._resize._currentHandle,G=0,E=0;if(F.top&&(F.top<I.top)){G=(J.height+J.top)-I.top;C.setY(this._resize.getWrapEl(),I.top);this._resize.getWrapEl().style.height=G+"px";this._resize._cache.height=G;this._resize._cache.top=I.top;this._syncBackgroundPosition();return false;}if(F.left&&(F.left<I.left)){E=(J.width+J.left)-I.left;C.setX(this._resize.getWrapEl(),I.left);this._resize._cache.left=I.left;this._resize.getWrapEl().style.width=E+"px";this._resize._cache.width=E;this._syncBackgroundPosition();return false;}if(H!="tl"&&H!="l"&&H!="bl"){if(J.left&&F.width&&((J.left+F.width)>I.right)){E=(I.right-J.left);C.setX(this._resize.getWrapEl(),(I.right-E));this._resize.getWrapEl().style.width=E+"px";this._resize._cache.left=(I.right-E);this._resize._cache.width=E;this._syncBackgroundPosition();return false;}}if(H!="t"&&H!="tr"&&H!="tl"){if(J.top&&F.height&&((J.top+F.height)>I.bottom)){G=(I.bottom-J.top);C.setY(this._resize.getWrapEl(),(I.bottom-G));this._resize.getWrapEl().style.height=G+"px";this._resize._cache.height=G;this._resize._cache.top=(I.bottom-G);this._syncBackgroundPosition();return false;}}},_handleResizeMaskEl:function(){var E=this._resize._cache;this._resizeMaskEl.style.height=Math.floor(E.height)+"px";this._resizeMaskEl.style.width=Math.floor(E.width)+"px";},_handleResizeEvent:function(E){this._setConstraints(true);this._syncBackgroundPosition();this.fireEvent("resizeEvent",arguments);this.fireEvent("moveEvent",{target:"resizeevent"});},_syncBackgroundPosition:function(){this._handleResizeMaskEl();this._setBackgroundPosition(-(parseInt(this._resizeEl.style.left,10)),-(parseInt(this._resizeEl.style.top,10)));
},_setBackgroundPosition:function(F,H){var J=parseInt(C.getStyle(this._resize.get("element"),"borderLeftWidth"),10);var G=parseInt(C.getStyle(this._resize.get("element"),"borderTopWidth"),10);var E=this._resize.getWrapEl().firstChild;var I=(F-J)+"px "+(H-G)+"px";this._resizeMaskEl.style.backgroundPosition=I;},_setBackgroundImage:function(F){var E=this._resize.getWrapEl().firstChild;this._image=F;E.style.backgroundImage="url("+F+"#)";},_handleStartResizeEvent:function(){this._setConstraints(true);var I=this._resize._cache.height,F=this._resize._cache.width,H=parseInt(this._resize.getWrapEl().style.top,10),E=parseInt(this._resize.getWrapEl().style.left,10),G=0,J=0;switch(this._resize._currentHandle){case"b":G=(I+this._resize.dd.bottomConstraint);break;case"l":J=(F+this._resize.dd.leftConstraint);break;case"r":G=(I+H);J=(F+this._resize.dd.rightConstraint);break;case"br":G=(I+this._resize.dd.bottomConstraint);J=(F+this._resize.dd.rightConstraint);break;case"tr":G=(I+H);J=(F+this._resize.dd.rightConstraint);break;}if(G){}if(J){}this.fireEvent("startResizeEvent",arguments);},_setConstraints:function(J){var H=this._resize;H.dd.resetConstraints();var N=parseInt(H.get("height"),10),F=parseInt(H.get("width"),10);if(J){N=H._cache.height;F=H._cache.width;}var L=C.getRegion(this.get("element"));var G=H.getWrapEl();var O=C.getXY(G);var I=O[0]-L.left;var M=L.right-O[0]-F;var K=O[1]-L.top;var E=L.bottom-O[1]-N;if(K<0){K=0;}H.dd.setXConstraint(I,M);H.dd.setYConstraint(K,E);return{top:K,right:M,bottom:E,left:I};},getCropCoords:function(){var E={top:parseInt(this._resize.getWrapEl().style.top,10),left:parseInt(this._resize.getWrapEl().style.left,10),height:this._resize._cache.height,width:this._resize._cache.width,image:this._image};return E;},reset:function(){this._resize.resize(null,this.get("initHeight"),this.get("initWidth"),0,0,true);this._resizeEl.style.top=this.get("initialXY")[1]+"px";this._resizeEl.style.left=this.get("initialXY")[0]+"px";this._syncBackgroundPosition();return this;},getEl:function(){return this.get("element");},getResizeEl:function(){return this._resizeEl;},getWrapEl:function(){return this._wrap;},getMaskEl:function(){return this._mask;},getResizeMaskEl:function(){return this._resizeMaskEl;},getResizeObject:function(){return this._resize;},init:function(G,E){B.superclass.init.call(this,G,E);var H=G;if(!D.isString(H)){if(H.tagName&&(H.tagName.toLowerCase()=="img")){H=C.generateId(H);}else{return false;}}else{var F=C.get(H);if(F.tagName&&F.tagName.toLowerCase()=="img"){}else{return false;}}B._instances[H]=this;this._createWrap();this._createMask();this._createResize();this._setConstraints();},initAttributes:function(E){B.superclass.initAttributes.call(this,E);this.setAttributeConfig("initialXY",{writeOnce:true,validator:YAHOO.lang.isArray,value:E.initialXY||[10,10]});this.setAttributeConfig("keyTick",{validator:YAHOO.lang.isNumber,value:E.keyTick||1});this.setAttributeConfig("shiftKeyTick",{validator:YAHOO.lang.isNumber,value:E.shiftKeyTick||10});this.setAttributeConfig("useKeys",{validator:YAHOO.lang.isBoolean,value:((E.useKeys===false)?false:true)});this.setAttributeConfig("status",{validator:YAHOO.lang.isBoolean,value:((E.status===false)?false:true),method:function(F){if(this._resize){this._resize.set("status",F);}}});this.setAttributeConfig("minHeight",{validator:YAHOO.lang.isNumber,value:E.minHeight||50,method:function(F){if(this._resize){this._resize.set("minHeight",F);}}});this.setAttributeConfig("minWidth",{validator:YAHOO.lang.isNumber,value:E.minWidth||50,method:function(F){if(this._resize){this._resize.set("minWidth",F);}}});this.setAttributeConfig("ratio",{validator:YAHOO.lang.isBoolean,value:E.ratio||false,method:function(F){if(this._resize){this._resize.set("ratio",F);}}});this.setAttributeConfig("autoRatio",{validator:YAHOO.lang.isBoolean,value:((E.autoRatio===false)?false:true),method:function(F){if(this._resize){this._resize.set("autoRatio",F);}}});this.setAttributeConfig("initHeight",{writeOnce:true,validator:YAHOO.lang.isNumber,value:E.initHeight||(this.get("element").height/4)});this.setAttributeConfig("initWidth",{validator:YAHOO.lang.isNumber,writeOnce:true,value:E.initWidth||(this.get("element").width/4)});},destroy:function(){this._resize.destroy();this._resizeEl.parentNode.removeChild(this._resizeEl);this._mask.parentNode.removeChild(this._mask);A.purgeElement(this._wrap);this._wrap.parentNode.replaceChild(this.get("element"),this._wrap);for(var E in this){if(D.hasOwnProperty(this,E)){this[E]=null;}}},toString:function(){if(this.get){return"ImageCropper (#"+this.get("id")+")";}return"Image Cropper";}});YAHOO.widget.ImageCropper=B;})();YAHOO.register("imagecropper",YAHOO.widget.ImageCropper,{version:"2.5.1",build:"984"});