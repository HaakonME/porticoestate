/*
YUI 3.7.3 (build 5687)
Copyright 2012 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/
YUI.add("uploader-deprecated",function(e){var b=e.Event,c=e.Node;var a=e.Env.cdn+"uploader-deprecated/assets/uploader.swf";function d(f){d.superclass.constructor.apply(this,arguments);if(f.hasOwnProperty("boundingBox")){this.set("boundingBox",f.boundingBox);}if(f.hasOwnProperty("buttonSkin")){this.set("buttonSkin",f.buttonSkin);}if(f.hasOwnProperty("transparent")){this.set("transparent",f.transparent);}if(f.hasOwnProperty("swfURL")){this.set("swfURL",f.swfURL);}}e.extend(d,e.Base,{uploaderswf:null,_id:"",initializer:function(){this._id=e.guid("uploader");var f=c.one(this.get("boundingBox"));var i={version:"10.0.45",fixedAttributes:{allowScriptAccess:"always",allowNetworking:"all",scale:"noscale"},flashVars:{}};if(this.get("buttonSkin")!=""){i.flashVars["buttonSkin"]=this.get("buttonSkin");}if(this.get("transparent")){i.fixedAttributes["wmode"]="transparent";}this.uploaderswf=new e.SWF(f,this.get("swfURL"),i);var h=this.uploaderswf;var g=e.bind(this._relayEvent,this);h.on("swfReady",e.bind(this._initializeUploader,this));h.on("click",g);h.on("fileselect",g);h.on("mousedown",g);h.on("mouseup",g);h.on("mouseleave",g);h.on("mouseenter",g);h.on("uploadcancel",g);h.on("uploadcomplete",g);h.on("uploadcompletedata",g);h.on("uploaderror",g);h.on("uploadprogress",g);h.on("uploadstart",g);},removeFile:function(f){return this.uploaderswf.callSWF("removeFile",[f]);},clearFileList:function(){return this.uploaderswf.callSWF("clearFileList",[]);},upload:function(f,h,j,g,i){if(e.Lang.isArray(f)){return this.uploaderswf.callSWF("uploadThese",[f,h,j,g,i]);}else{if(e.Lang.isString(f)){return this.uploaderswf.callSWF("upload",[f,h,j,g,i]);}}},uploadThese:function(h,g,j,f,i){return this.uploaderswf.callSWF("uploadThese",[h,g,j,f,i]);},uploadAll:function(g,i,f,h){return this.uploaderswf.callSWF("uploadAll",[g,i,f,h]);},cancel:function(f){return this.uploaderswf.callSWF("cancel",[f]);},setAllowLogging:function(f){this.uploaderswf.callSWF("setAllowLogging",[f]);},setAllowMultipleFiles:function(f){this.uploaderswf.callSWF("setAllowMultipleFiles",[f]);},setSimUploadLimit:function(f){this.uploaderswf.callSWF("setSimUploadLimit",[f]);},setFileFilters:function(f){this.uploaderswf.callSWF("setFileFilters",[f]);},enable:function(){this.uploaderswf.callSWF("enable");},disable:function(){this.uploaderswf.callSWF("disable");},_initializeUploader:function(f){this.publish("uploaderReady",{fireOnce:true});this.fire("uploaderReady",{});},_relayEvent:function(f){this.fire(f.type,f);},toString:function(){return"Uploader "+this._id;}},{ATTRS:{log:{value:false,setter:"setAllowLogging"},multiFiles:{value:false,setter:"setAllowMultipleFiles"},simLimit:{value:2,setter:"setSimUploadLimit"},fileFilters:{value:[],setter:"setFileFilters"},boundingBox:{value:null,writeOnce:"initOnly"},buttonSkin:{value:null,writeOnce:"initOnly"},transparent:{value:true,writeOnce:"initOnly"},swfURL:{value:a,writeOnce:"initOnly"}}});e.Uploader=d;},"3.7.3",{requires:["swf","base","node","event-custom"]});