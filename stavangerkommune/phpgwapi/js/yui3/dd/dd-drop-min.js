/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.3.0
build: 3167
*/
YUI.add("dd-drop",function(A){var B="node",G=A.DD.DDM,F="offsetHeight",C="offsetWidth",I="drop:over",H="drop:enter",D="drop:exit",E=function(){this._lazyAddAttrs=false;E.superclass.constructor.apply(this,arguments);A.on("domready",A.bind(function(){A.later(100,this,this._createShim);},this));G._regTarget(this);};E.NAME="drop";E.ATTRS={node:{setter:function(J){var K=A.one(J);if(!K){A.error("DD.Drop: Invalid Node Given: "+J);}return K;}},groups:{value:["default"],setter:function(J){this._groups={};A.each(J,function(L,K){this._groups[L]=true;},this);return J;}},padding:{value:"0",setter:function(J){return G.cssSizestoObject(J);}},lock:{value:false,setter:function(J){if(J){this.get(B).addClass(G.CSS_PREFIX+"-drop-locked");}else{this.get(B).removeClass(G.CSS_PREFIX+"-drop-locked");}return J;}},bubbles:{setter:function(J){this.addTarget(J);return J;}},useShim:{value:true,setter:function(J){A.DD.DDM._noShim=!J;return J;}}};A.extend(E,A.Base,{_bubbleTargets:A.DD.DDM,addToGroup:function(J){this._groups[J]=true;return this;},removeFromGroup:function(J){delete this._groups[J];return this;},_createEvents:function(){var J=[I,H,D,"drop:hit"];A.each(J,function(L,K){this.publish(L,{type:L,emitFacade:true,preventable:false,bubbles:true,queuable:false,prefix:"drop"});},this);},_valid:null,_groups:null,shim:null,region:null,overTarget:null,inGroup:function(J){this._valid=false;var K=false;A.each(J,function(M,L){if(this._groups[M]){K=true;this._valid=true;}},this);return K;},initializer:function(J){A.later(100,this,this._createEvents);var K=this.get(B),L;if(!K.get("id")){L=A.stamp(K);K.set("id",L);}K.addClass(G.CSS_PREFIX+"-drop");this.set("groups",this.get("groups"));},destructor:function(){G._unregTarget(this);if(this.shim&&(this.shim!==this.get(B))){this.shim.detachAll();this.shim.remove();this.shim=null;}this.get(B).removeClass(G.CSS_PREFIX+"-drop");this.detachAll();},_deactivateShim:function(){if(!this.shim){return false;}this.get(B).removeClass(G.CSS_PREFIX+"-drop-active-valid");this.get(B).removeClass(G.CSS_PREFIX+"-drop-active-invalid");this.get(B).removeClass(G.CSS_PREFIX+"-drop-over");if(this.get("useShim")){this.shim.setStyles({top:"-999px",left:"-999px",zIndex:"1"});}this.overTarget=false;},_activateShim:function(){if(!G.activeDrag){return false;}if(this.get(B)===G.activeDrag.get(B)){return false;}if(this.get("lock")){return false;}var J=this.get(B);if(this.inGroup(G.activeDrag.get("groups"))){J.removeClass(G.CSS_PREFIX+"-drop-active-invalid");J.addClass(G.CSS_PREFIX+"-drop-active-valid");G._addValid(this);this.overTarget=false;if(!this.get("useShim")){this.shim=this.get(B);}this.sizeShim();}else{G._removeValid(this);J.removeClass(G.CSS_PREFIX+"-drop-active-valid");J.addClass(G.CSS_PREFIX+"-drop-active-invalid");}},sizeShim:function(){if(!G.activeDrag){return false;}if(this.get(B)===G.activeDrag.get(B)){return false;}if(this.get("lock")){return false;}if(!this.shim){A.later(100,this,this.sizeShim);return false;}var O=this.get(B),M=O.get(F),K=O.get(C),Q=O.getXY(),P=this.get("padding"),J,N,L;K=K+P.left+P.right;M=M+P.top+P.bottom;Q[0]=Q[0]-P.left;Q[1]=Q[1]-P.top;if(G.activeDrag.get("dragMode")===G.INTERSECT){J=G.activeDrag;N=J.get(B).get(F);L=J.get(B).get(C);M=(M+N);K=(K+L);Q[0]=Q[0]-(L-J.deltaXY[0]);Q[1]=Q[1]-(N-J.deltaXY[1]);}if(this.get("useShim")){this.shim.setStyles({height:M+"px",width:K+"px",top:Q[1]+"px",left:Q[0]+"px"});}this.region={"0":Q[0],"1":Q[1],area:0,top:Q[1],right:Q[0]+K,bottom:Q[1]+M,left:Q[0]};},_createShim:function(){if(!G._pg){A.later(10,this,this._createShim);return;}if(this.shim){return;}var J=this.get("node");if(this.get("useShim")){J=A.Node.create('<div id="'+this.get(B).get("id")+'_shim"></div>');J.setStyles({height:this.get(B).get(F)+"px",width:this.get(B).get(C)+"px",backgroundColor:"yellow",opacity:".5",zIndex:"1",overflow:"hidden",top:"-900px",left:"-900px",position:"absolute"});G._pg.appendChild(J);J.on("mouseover",A.bind(this._handleOverEvent,this));J.on("mouseout",A.bind(this._handleOutEvent,this));}this.shim=J;},_handleTargetOver:function(){if(G.isOverTarget(this)){this.get(B).addClass(G.CSS_PREFIX+"-drop-over");G.activeDrop=this;G.otherDrops[this]=this;if(this.overTarget){G.activeDrag.fire("drag:over",{drop:this,drag:G.activeDrag});this.fire(I,{drop:this,drag:G.activeDrag});}else{if(G.activeDrag.get("dragging")){this.overTarget=true;this.fire(H,{drop:this,drag:G.activeDrag});G.activeDrag.fire("drag:enter",{drop:this,drag:G.activeDrag});G.activeDrag.get(B).addClass(G.CSS_PREFIX+"-drag-over");}}}else{this._handleOut();}},_handleOverEvent:function(){this.shim.setStyle("zIndex","999");G._addActiveShim(this);},_handleOutEvent:function(){this.shim.setStyle("zIndex","1");G._removeActiveShim(this);},_handleOut:function(J){if(!G.isOverTarget(this)||J){if(this.overTarget){this.overTarget=false;if(!J){G._removeActiveShim(this);}if(G.activeDrag){this.get(B).removeClass(G.CSS_PREFIX+"-drop-over");G.activeDrag.get(B).removeClass(G.CSS_PREFIX+"-drag-over");this.fire(D);G.activeDrag.fire("drag:exit",{drop:this});delete G.otherDrops[this];}}}}});A.DD.Drop=E;},"3.3.0",{requires:["dd-ddm-drop","dd-drag"],skinnable:false});