/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.3.0
build: 3167
*/
YUI.add("dd-ddm",function(A){A.mix(A.DD.DDM,{_pg:null,_debugShim:false,_activateTargets:function(){},_deactivateTargets:function(){},_startDrag:function(){if(this.activeDrag&&this.activeDrag.get("useShim")){this._pg_activate();this._activateTargets();}},_endDrag:function(){this._pg_deactivate();this._deactivateTargets();},_pg_deactivate:function(){this._pg.setStyle("display","none");},_pg_activate:function(){var B=this.activeDrag.get("activeHandle"),C="auto";if(B){C=B.getStyle("cursor");}if(C=="auto"){C=this.get("dragCursor");}this._pg_size();this._pg.setStyles({top:0,left:0,display:"block",opacity:((this._debugShim)?".5":"0"),cursor:C});},_pg_size:function(){if(this.activeDrag){var B=A.one("body"),D=B.get("docHeight"),C=B.get("docWidth");this._pg.setStyles({height:D+"px",width:C+"px"});}},_createPG:function(){var D=A.Node.create("<div></div>"),B=A.one("body"),C;D.setStyles({top:"0",left:"0",position:"absolute",zIndex:"9999",overflow:"hidden",backgroundColor:"red",display:"none",height:"5px",width:"5px"});D.set("id",A.stamp(D));D.addClass(A.DD.DDM.CSS_PREFIX+"-shim");B.prepend(D);this._pg=D;this._pg.on("mousemove",A.throttle(A.bind(this._move,this),this.get("throttleTime")));this._pg.on("mouseup",A.bind(this._end,this));C=A.one("win");A.on("window:resize",A.bind(this._pg_size,this));C.on("scroll",A.bind(this._pg_size,this));}},true);},"3.3.0",{requires:["dd-ddm-base","event-resize"],skinnable:false});