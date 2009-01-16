/*
Copyright (c) 2008, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 2.6.0
*/
if(typeof (YAHOO.util.ImageLoader)=="undefined"){YAHOO.util.ImageLoader={};}YAHOO.util.ImageLoader.group=function(A,B,C){this.name="unnamed";this._imgObjs={};this.timeoutLen=C;this._timeout=null;this._triggers=[];this._customTriggers=[];this.foldConditional=false;this.className=null;this._classImageEls=null;YAHOO.util.Event.addListener(window,"load",this._onloadTasks,this,true);this.addTrigger(A,B);};YAHOO.util.ImageLoader.group.prototype.addTrigger=function(B,C){if(!B||!C){return ;}var A=function(){this.fetch();};this._triggers.push([B,C,A]);YAHOO.util.Event.addListener(B,C,A,this,true);};YAHOO.util.ImageLoader.group.prototype.addCustomTrigger=function(B){if(!B||!B instanceof YAHOO.util.CustomEvent){return ;}var A=function(){this.fetch();};this._customTriggers.push([B,A]);B.subscribe(A,this,true);};YAHOO.util.ImageLoader.group.prototype._onloadTasks=function(){if(this.timeoutLen&&typeof (this.timeoutLen)=="number"&&this.timeoutLen>0){this._timeout=setTimeout(this._getFetchTimeout(),this.timeoutLen*1000);}if(this.foldConditional){this._foldCheck();}};YAHOO.util.ImageLoader.group.prototype._getFetchTimeout=function(){var A=this;return function(){A.fetch();};};YAHOO.util.ImageLoader.group.prototype.registerBgImage=function(B,A){this._imgObjs[B]=new YAHOO.util.ImageLoader.bgImgObj(B,A);return this._imgObjs[B];};YAHOO.util.ImageLoader.group.prototype.registerSrcImage=function(D,B,C,A){this._imgObjs[D]=new YAHOO.util.ImageLoader.srcImgObj(D,B,C,A);return this._imgObjs[D];};YAHOO.util.ImageLoader.group.prototype.registerPngBgImage=function(C,B,A){this._imgObjs[C]=new YAHOO.util.ImageLoader.pngBgImgObj(C,B,A);return this._imgObjs[C];};YAHOO.util.ImageLoader.group.prototype.fetch=function(){clearTimeout(this._timeout);for(var B=0,A=this._triggers.length;B<A;B++){YAHOO.util.Event.removeListener(this._triggers[B][0],this._triggers[B][1],this._triggers[B][2]);}for(var B=0,A=this._customTriggers.length;B<A;B++){this._customTriggers[B][0].unsubscribe(this._customTriggers[B][1],this);}this._fetchByClass();for(var C in this._imgObjs){if(YAHOO.lang.hasOwnProperty(this._imgObjs,C)){this._imgObjs[C].fetch();}}};YAHOO.util.ImageLoader.group.prototype._foldCheck=function(){var C=(document.compatMode!="CSS1Compat")?document.body.scrollTop:document.documentElement.scrollTop;var D=YAHOO.util.Dom.getViewportHeight();var A=C+D;var E=(document.compatMode!="CSS1Compat")?document.body.scrollLeft:document.documentElement.scrollLeft;var G=YAHOO.util.Dom.getViewportWidth();var I=E+G;for(var B in this._imgObjs){if(YAHOO.lang.hasOwnProperty(this._imgObjs,B)){var J=YAHOO.util.Dom.getXY(this._imgObjs[B].domId);if(J[1]<A&&J[0]<I){this._imgObjs[B].fetch();}}}if(this.className){this._classImageEls=YAHOO.util.Dom.getElementsByClassName(this.className);for(var F=0,H=this._classImageEls.length;F<H;F++){var J=YAHOO.util.Dom.getXY(this._classImageEls[F]);if(J[1]<A&&J[0]<I){YAHOO.util.Dom.removeClass(this._classImageEls[F],this.className);}}}};YAHOO.util.ImageLoader.group.prototype._fetchByClass=function(){if(!this.className){return ;}if(this._classImageEls===null){this._classImageEls=YAHOO.util.Dom.getElementsByClassName(this.className);}YAHOO.util.Dom.removeClass(this._classImageEls,this.className);};YAHOO.util.ImageLoader.imgObj=function(B,A){this.domId=B;this.url=A;this.width=null;this.height=null;this.setVisible=false;this._fetched=false;};YAHOO.util.ImageLoader.imgObj.prototype.fetch=function(){if(this._fetched){return ;}var A=document.getElementById(this.domId);if(!A){return ;}this._applyUrl(A);if(this.setVisible){A.style.visibility="visible";}if(this.width){A.width=this.width;}if(this.height){A.height=this.height;}this._fetched=true;};YAHOO.util.ImageLoader.imgObj.prototype._applyUrl=function(A){};YAHOO.util.ImageLoader.bgImgObj=function(B,A){YAHOO.util.ImageLoader.bgImgObj.superclass.constructor.call(this,B,A);};YAHOO.lang.extend(YAHOO.util.ImageLoader.bgImgObj,YAHOO.util.ImageLoader.imgObj);YAHOO.util.ImageLoader.bgImgObj.prototype._applyUrl=function(A){A.style.backgroundImage="url('"+this.url+"')";};YAHOO.util.ImageLoader.srcImgObj=function(D,B,C,A){YAHOO.util.ImageLoader.srcImgObj.superclass.constructor.call(this,D,B);this.width=C;this.height=A;};YAHOO.lang.extend(YAHOO.util.ImageLoader.srcImgObj,YAHOO.util.ImageLoader.imgObj);YAHOO.util.ImageLoader.srcImgObj.prototype._applyUrl=function(A){A.src=this.url;};YAHOO.util.ImageLoader.pngBgImgObj=function(C,B,A){YAHOO.util.ImageLoader.pngBgImgObj.superclass.constructor.call(this,C,B);this.props=A||{};};YAHOO.lang.extend(YAHOO.util.ImageLoader.pngBgImgObj,YAHOO.util.ImageLoader.imgObj);YAHOO.util.ImageLoader.pngBgImgObj.prototype._applyUrl=function(B){if(YAHOO.env.ua.ie&&YAHOO.env.ua.ie<=6){var C=(YAHOO.lang.isUndefined(this.props.sizingMethod))?"scale":this.props.sizingMethod;var A=(YAHOO.lang.isUndefined(this.props.enabled))?"true":this.props.enabled;B.style.filter='progid:DXImageTransform.Microsoft.AlphaImageLoader(src="'+this.url+'", sizingMethod="'+C+'", enabled="'+A+'")';}else{B.style.backgroundImage="url('"+this.url+"')";}};YAHOO.register("imageloader",YAHOO.util.ImageLoader,{version:"2.6.0",build:"1321"});