/*
YUI 3.16.0 (build 76f0e08)
Copyright 2014 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/

YUI.add("editor-br",function(e,t){var n=function(){n.superclass.constructor.apply(this,arguments)},r="host",i="li";e.extend(n,e.Base,{_onKeyDown:function(t){if(t.stopped){t.halt();return}if(t.keyCode===13){var n=this.get(r),s=n.getInstance(),o=new s.EditorSelection;o&&(e.UA.ie&&(!o.anchorNode||!o.anchorNode.test(i)&&!o.anchorNode.ancestor(i))&&(n.execCommand("inserthtml",s.EditorSelection.CURSOR),t.halt()),e.UA.webkit&&(!o.anchorNode||!o.anchorNode.test(i)&&!o.anchorNode.ancestor(i))&&(n.frame._execCommand("insertlinebreak",null),t.halt()))}},_afterEditorReady:function(){var t=this.get(r).getInstance(),n;try{t.config.doc.execCommand("insertbronreturn",null,!0)}catch(i){}if(e.UA.ie||e.UA.webkit)n=t.EditorSelection.ROOT,n.test("body")&&(n=t.config.doc),t.on("keydown",e.bind(this._onKeyDown,this),n)},_onNodeChange:function(e){switch(e.changedType){case"backspace-up":case"backspace-down":case"delete-up":var t=this.get(r).getInstance(),n=e.changedNode,i=t.config.doc.createTextNode(" ");n.appendChild(i),n.removeChild(i)}},initializer:function(){var t=this.get(r);if(t.editorPara){e.error("Can not plug EditorBR and EditorPara at the same time.");return}t.after("ready",e.bind(this._afterEditorReady,this)),e.UA.gecko&&t.on("nodeChange",e.bind(this._onNodeChange,this))}},{NAME:"editorBR",NS:"editorBR",ATTRS:{host:{value:!1}}}),e.namespace("Plugin"),e.Plugin.EditorBR=n},"3.16.0",{requires:["editor-base"]});
