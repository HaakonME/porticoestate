/*
YUI 3.16.0 (build 76f0e08)
Copyright 2014 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/

YUI.add("imageloader",function(e,t){e.ImgLoadGroup=function(){this._init(),e.ImgLoadGroup.superclass.constructor.apply(this,arguments)},e.ImgLoadGroup.NAME="imgLoadGroup",e.ImgLoadGroup.ATTRS={name:{value:""},timeLimit:{value:null},foldDistance:{validator:e.Lang.isNumber,setter:function(e){return this._setFoldTriggers(),e},lazyAdd:!1},className:{value:null,setter:function(e){return this._className=e,e},lazyAdd:!1},classNameAction:{value:"default"}};var n={_init:function(){this._triggers=[],this._imgObjs={},this._timeout=null,this._classImageEls=null,this._className=null,this._areFoldTriggersSet=!1,this._maxKnownHLimit=0,e.on("domready",this._onloadTasks,this)},addTrigger:function(t,n){if(!t||!n)return this;var r=function(){this.fetch()};return this._triggers.push(e.on(n,r,t,this)),this},addCustomTrigger:function(t,n){if(!t)return this;var r=function(){this.fetch()};return e.Lang.isUndefined(n)?this._triggers.push(e.on(t,r,this)):this._triggers.push(n.on(t,r,this)),this},_setFoldTriggers:function(){if(this._areFoldTriggersSet)return;var t=function(){this._foldCheck()};this._triggers.push(e.on("scroll",t,window,this)),this._triggers.push(e.on("resize",t,window,this)),this._areFoldTriggersSet=!0},_onloadTasks:function(){var t=this.get("timeLimit");t&&t>0&&(this._timeout=setTimeout(this._getFetchTimeout(),t*1e3)),e.Lang.isUndefined(this.get("foldDistance"))||this._foldCheck()},_getFetchTimeout:function(){var e=this;return function(){e.fetch()}},registerImage:function(){var t=arguments[0].domId;return t?(this._imgObjs[t]=new e.ImgLoadImgObj(arguments[0]),this._imgObjs[t]):null},fetch:function(){this._clearTriggers(),this._fetchByClass();for(var e in this._imgObjs)this._imgObjs.hasOwnProperty(e)&&this._imgObjs[e].fetch()},_clearTriggers:function(){clearTimeout(this._timeout);for(var e=0,t=this._triggers.length;e<t;e++)this._triggers[e].detach()},_foldCheck:function(){var t=!0,n=e.DOM.viewportRegion(),r=n.bottom+this.get("foldDistance"),i,s,o,u,a;if(r<=this._maxKnownHLimit)return;this._maxKnownHLimit=r;for(i in this._imgObjs)this._imgObjs.hasOwnProperty(i)&&(s=this._imgObjs[i].fetch(r),t=t&&s);if(this._className){this._classImageEls===null&&(this._classImageEls=[],o=e.all("."+this._className),o.each(function(e){this._classImageEls.push({el:e,y:e.getY(),fetched:!1})},this)),o=this._classImageEls;for(u=0,a=o.length;u<a;u++){if(o[u].fetched)continue;o[u].y&&o[u].y<=r?(this._updateNodeClassName(o[u].el),o[u].fetched=!0):t=!1}}t&&this._clearTriggers()},_updateNodeClassName:function(e){var t;this.get("classNameAction")=="enhanced"&&e.get("tagName").toLowerCase()=="img"&&(t=e.getStyle("backgroundImage"),/url\(["']?(.*?)["']?\)/.test(t),t=RegExp.$1,e.set("src",t),e.setStyle("backgroundImage","")),e.removeClass(this._className)},_fetchByClass:function(){if(!this._className)return;e.all("."+this._className).each(e.bind(this._updateNodeClassName,this))}};e.extend(e.ImgLoadGroup,e.Base,n),e.ImgLoadImgObj=function(){e.ImgLoadImgObj.superclass.constructor.apply(this,arguments),this._init()},e.ImgLoadImgObj.NAME="imgLoadImgObj",e.ImgLoadImgObj.ATTRS={domId:{value:null,writeOnce:!0},bgUrl:{value:null},srcUrl:{value:null},width:{value:null},height:{value:null},setVisible:{value:!1},isPng:{value:!1},sizingMethod:{value:"scale"},enabled:{value:"true"}};var r={_init:function(){this._fetched=!1,this._imgEl=null,this._yPos=null},fetch:function(t){if(this._fetched)return!0;var n=this._getImgEl(),r;if(!n)return!1;if(t){r=this._getYPos();if(!r||r>t)return!1}return this.get("bgUrl")!==null?this.get("isPng")&&e.UA.ie&&e.UA.ie<=6?n.setStyle("filter",'progid:DXImageTransform.Microsoft.AlphaImageLoader(src="'+this.get("bgUrl")+'", sizingMethod="'+this.get("sizingMethod")+'", enabled="'+this.get("enabled")+'")'):n.setStyle("backgroundImage","url('"+this.get("bgUrl")+"')"):this.get("srcUrl")!==null&&n.setAttribute("src",this.get("srcUrl")),this.get("setVisible")&&n.setStyle("visibility","visible"),this.get("width")&&n.setAttribute("width",this.get("width")),this.get("height")&&n.setAttribute("height",this.get("height")),this._fetched=!0,!0},_getImgEl:function(){return this._imgEl===null&&(this._imgEl=e.one("#"+this.get("domId"))),this._imgEl},_getYPos:function(){return this._yPos===null&&(this._yPos=this._getImgEl().getY()),this._yPos}};e.extend(e.ImgLoadImgObj,e.Base,r)},"3.16.0",{requires:["base-base","node-style","node-screen"]});
