/*
YUI 3.16.0 (build 76f0e08)
Copyright 2014 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/

YUI.add("dd-ddm",function(e,t){e.mix(e.DD.DDM,{_pg:null,_debugShim:!1,_activateTargets:function(){},_deactivateTargets:function(){},_startDrag:function(){this.activeDrag&&this.activeDrag.get("useShim")&&(this._shimming=!0,this._pg_activate(),this._activateTargets())},_endDrag:function(){this._pg_deactivate(),this._deactivateTargets()},_pg_deactivate:function(){this._pg.setStyle("display","none")},_pg_activate:function(){this._pg||this._createPG();var e=this.activeDrag.get("activeHandle"),t="auto";e&&(t=e.getStyle("cursor")),t==="auto"&&(t=this.get("dragCursor")),this._pg_size(),this._pg.setStyles({top:0,left:0,display:"block",opacity:this._debugShim?".5":"0",cursor:t})},_pg_size:function(){if(this.activeDrag){var t=e.one("body"),n=t.get("docHeight"),r=t.get("docWidth");this._pg.setStyles({height:n+"px",width:r+"px"})}},_createPG:function(){var t=e.Node.create("<div></div>"),n=e.one("body"),r;t.setStyles({top:"0",left:"0",position:"absolute",zIndex:"9999",overflow:"hidden",backgroundColor:"red",display:"none",height:"5px",width:"5px"}),t.set("id",e.stamp(t)),t.addClass(e.DD.DDM.CSS_PREFIX+"-shim"),n.prepend(t),this._pg=t,this._pg.on("mousemove",e.throttle(e.bind(this._move,this),this.get("throttleTime"))),this._pg.on("mouseup",e.bind(this._end,this)),r=e.one("win"),e.on("window:resize",e.bind(this._pg_size,this)),r.on("scroll",e.bind(this._pg_size,this))}},!0)},"3.16.0",{requires:["dd-ddm-base","event-resize"]});
