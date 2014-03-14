/*
YUI 3.7.3 (build 5687)
Copyright 2012 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/
YUI.add("charts-legend",function(e,t){function E(t){return t.type!="pie"?new e.CartesianChart(t):new e.PieChart(t)}var n=e.config.doc,r="top",i="right",s="bottom",o="left",u="external",a="horizontal",f="vertical",l="width",c="height",h="position",p="x",d="y",v="px",m={setter:function(t){var n=this.get("legend");return n&&n.destroy(!0),t instanceof e.ChartLegend?(n=t,n.set("chart",this)):(t.chart=this,t.hasOwnProperty("render")||(t.render=this.get("contentBox"),t.includeInChartLayout=!0),n=new e.ChartLegend(t)),n}},g={_positionLegendItems:function(e,t,n,r,i,s,o,u,a,f){var l=0,c=0,h,p,d,m,y,b=this.get("width"),w,E,S,x,T,N=s.top-u,C=b-(s.left+s.right),k,L,A,O;g._setRowArrays(e,C,o),w=g.rowArray,x=g.totalWidthArray,E=w.length;for(;c<E;++c){N+=u,S=w[c],y=S.length,T=g.getStartPoint(b,x[c],a,s);for(l=0;l<y;++l)h=S[l],p=h.node,d=h.width,m=h.height,h.x=T,h.y=0,k=isNaN(k)?T:Math.min(k,T),L=isNaN(L)?N:Math.min(L,N),A=isNaN(A)?T+d:Math.max(T+d,A),O=isNaN(O)?N+m:Math.max(N+m,O),p.setStyle("left",T+v),p.setStyle("top",N+v),T+=d+o;N+=h.height}this._contentRect={left:k,top:L,right:A,bottom:O},this.get("includeInChartLayout")&&this.set("height",N+s.bottom)},_setRowArrays:function(e,t,n){var r=e[0],i=[[r]],s=1,o=0,u=e.length,a=r.width,f,l=[[a]];for(;s<u;++s)r=e[s],f=r.width,a+n+f<=t?(a+=n+f,i[o].push(r)):(a=n+f,i[o]&&(o+=1),i[o]=[r]),l[o]=a;g.rowArray=i,g.totalWidthArray=l},getStartPoint:function(e,t,n,r){var s;switch(n){case o:s=r.left;break;case"center":s=(e-t)*.5;break;case i:s=e-t-r.right}return s}},y={_positionLegendItems:function(e,t,n,r,i,s,o,u,a,f){var l=0,c=0,h,p,d,m,g,b=this.get("height"),w,E,S,x,T,N=s.left-o,C,k=b-(s.top+s.bottom),L,A,O,M;y._setColumnArrays(e,k,u),w=y.columnArray,x=y.totalHeightArray,E=w.length;for(;c<E;++c){N+=o,S=w[c],g=S.length,T=y.getStartPoint(b,x[c],f,s),C=0;for(l=0;l<g;++l)h=S[l],p=h.node,d=h.height,m=h.width,h.y=T,h.x=N,L=isNaN(L)?N:Math.min(L,N),A=isNaN(A)?T:Math.min(A,T),O=isNaN(O)?N+m:Math.max(N+m,O),M=isNaN(M)?T+d:Math.max(T+d,M),p.setStyle("left",N+v),p.setStyle("top",T+v),T+=d+u,C=Math.max(C,h.width);N+=C}this._contentRect={left:L,top:A,right:O,bottom:M},this.get("includeInChartLayout")&&this.set("width",N+s.right)},_setColumnArrays:function(e,t,n){var r=e[0],i=[[r]],s=1,o=0,u=e.length,a=r.height,f,l=[[a]];for(;s<u;++s)r=e[s],f=r.height,a+n+f<=t?(a+=n+f,i[o].push(r)):(a=n+f,i[o]&&(o+=1),i[o]=[r]),l[o]=a;y.columnArray=i,y.totalHeightArray=l},getStartPoint:function(e,t,n,i){var o;switch(n){case r:o=i.top;break;case"middle":o=(e-t)*.5;break;case s:o=e-t-i.bottom}return o}},b=e.Base.create("cartesianChartLegend",e.CartesianChart,[],{_redraw:function(){if(this._drawing){this._callLater=!0;return}this._drawing=!0,this._callLater=!1;var e=this.get("width"),t=this.get("height"),n=this._getLayoutBoxDimensions(),r=n.left,i=n.right,s=n.top,o=n.bottom,u=this.get("leftAxesCollection"),a=this.get("rightAxesCollection"),f=this.get("topAxesCollection"),l=this.get("bottomAxesCollection"),c=0,h,p,d="visible",m=this.get("graph"),g,y,b,w,E,S,x,T,N=this.get("allowContentOverflow"),C,k,L,A,O,M=this.get("legend"),_={};if(u){L=[],h=u.length;for(c=h-1;c>-1;--c)L.unshift(r),r+=u[c].get("width")}if(a){k=[],h=a.length,c=0;for(c=h-1;c>-1;--c)i+=a[c].get("width"),k.unshift(e-i)}if(f){A=[],h=f.length;for(c=h-1;c>-1;--c)A.unshift(s),s+=f[c].get("height")}if(l){O=[],h=l.length;for(c=h-1;c>-1;--c)o+=l[c].get("height"),O.unshift(t-o)}E=e-(r+i),S=t-(o+s),_.left=r,_.top=s,_.bottom=t-o,_.right=e-i;if(!N){g=this._getTopOverflow(u,a),y=this._getBottomOverflow(u,a),b=this._getLeftOverflow(l,f),w=this._getRightOverflow(l,f),C=g-s;if(C>0){_.top=g;if(A){c=0,h=A.length;for(;c<h;++c)A[c]+=C}}C=y-o;if(C>0){_.bottom=t-y;if(O){c=0,h=O.length;for(;c<h;++c)O[c]-=C}}C=b-r;if(C>0){_.left=b;if(L){c=0,h=L.length;for(;c<h;++c)L[c]+=C}}C=w-i;if(C>0){_.right=e-w;if(k){c=0,h=k.length;for(;c<h;++c)k[c]-=C}}}E=_.right-_.left,S=_.bottom-_.top,x=_.left,T=_.top;if(M&&M.get("includeInChartLayout"))switch(M.get("position")){case"left":M.set("y",T),M.set("height",S);break;case"top":M.set("x",x),M.set("width",E);break;case"bottom":M.set("x",x),M.set("width",E);break;case"right":M.set("y",T),M.set("height",S)}if(f){h=f.length,c=0;for(;c<h;c++)p=f[c],p.get("width")!==E&&p.set("width",E),p.get("boundingBox").setStyle("left",x+v),p.get("boundingBox").setStyle("top",A[c]+v);p._hasDataOverflow()&&(d="hidden")}if(l){h=l.length,c=0;for(;c<h;c++)p=l[c],p.get("width")!==E&&p.set("width",E),p.get("boundingBox").setStyle("left",x+v),p.get("boundingBox").setStyle("top",O[c]+v);p._hasDataOverflow()&&(d="hidden")}if(u){h=u.length,c=0;for(;c<h;++c)p=u[c],p.get("boundingBox").setStyle("top",T+v),p.get("boundingBox").setStyle("left",L[c]+v),p.get("height")!==S&&p.set("height",S);p._hasDataOverflow()&&(d="hidden")}if(a){h=a.length,c=0;for(;c<h;++c)p=a[c],p.get("boundingBox").setStyle("top",T+v),p.get("boundingBox").setStyle("left",k[c]+v),p.get("height")!==S&&p.set("height",S);p._hasDataOverflow()&&(d="hidden")}this._drawing=!1;if(this._callLater){this._redraw();return}m&&(m.get("boundingBox").setStyle("left",x+v),m.get("boundingBox").setStyle("top",T+v),m.set("width",E),m.set("height",S),m.get("boundingBox").setStyle("overflow",d)),this._overlay&&(this._overlay.setStyle("left",x+v),this._overlay.setStyle("top",T+v),this._overlay.setStyle("width",E+v),this._overlay.setStyle("height",S+v))},_getLayoutBoxDimensions:function(){var e={top:0,right:0,bottom:0,left:0},t=this.get("legend"),n,f,v,m,g=this.get(l),y=this.get(c),b;if(t&&t.get("includeInChartLayout")){b=t.get("styles").gap,n=t.get(h);if(n!=u){f=t.get("direction"),v=f==a?c:l,m=t.get(v),e[n]=m+b;switch(n){case r:t.set(d,0);break;case s:t.set(d,y-m);break;case i:t.set(p,g-m);break;case o:t.set(p,0)}}}return e},destructor:function(){var e=this.get("legend");e&&e.destroy(!0)}},{ATTRS:{legend:m}});e.CartesianChart=b;var w=e.Base.create("pieChartLegend",e.PieChart,[],{_redraw:function(){if(this._drawing){this._callLater=!0;return}this._drawing=!0,this._callLater=!1;var e=this.get("graph"),t=this.get("width"),n=this.get("height"),u,a,f=this.get("legend"),h=0,v=0,m=0,g=0,y,b,w,E,S,x;if(e)if(f){S=f.get("position"),x=f.get("direction"),u=e.get("width"),a=e.get("height"),y=f.get("width"),b=f.get("height"),E=f.get("styles").gap;if(x=="vertical"&&u+y+E!==t||x=="horizontal"&&a+b+E!==n){switch(f.get("position")){case o:w=Math.min(t-(y+E),n),b=n,h=y+E,f.set(c,b);break;case r:w=Math.min(n-(b+E),t),y=t,v=b+E,f.set(l,y);break;case i:w=Math.min(t-(y+E),n),b=n,m=w+E,f.set(c,b);break;case s:w=Math.min(n-(b+E),t),y=t,g=w+E,f.set(l,y)}e.set(l,w),e.set(c,w)}else switch(f.get("position")){case o:h=y+E;break;case r:v=b+E;break;case i:m=u+E;break;case s:g=a+E}}else e.set(p,0),e.set(d,0),e.set(l,t),e.set(c,n);this._drawing=!1;if(this._callLater){this._redraw();return}e&&(e.set(p,h),e.set(d,v)),f&&(f.set(p,m),f.set(d,g))}},{ATTRS:{legend:m}});e.PieChart=w,e.ChartLegend=e.Base.create("chartlegend",e.Widget,[e.Renderer],{initializer:function(){this._items=[]},renderUI:function(){var t=this.get("boundingBox"),n=this.get("contentBox"),r=this.get("styles").background,i=new e.Rect({graphic:n,fill:r.fill,stroke:r.border});t.setStyle("display","block"),t.setStyle("position","absolute"),this.set("background",i)},bindUI:function(){this.get("chart").after("seriesCollectionChange",e.bind(this._updateHandler,this)),this.after("stylesChange",this._updateHandler),this.after("positionChange",this._positionChangeHandler),this.after("widthChange",this._handleSizeChange),this.after("heightChange",this._handleSizeChange)},syncUI:function(){var e=this.get("width"),t=this.get("height");isFinite(e)&&isFinite(t)&&e>0&&t>0&&this._drawLegend()},_updateHandler:function(e){this.get("rendered")&&this._drawLegend()},_positionChangeHandler:function(e){var t=this.get("chart"),n=this._parentNode;n&&t&&this.get("includeInChartLayout")?this.fire("legendRendered"):this.get("rendered")&&this._drawLegend()},_handleSizeChange:function(e){var t=e.attrName,n=this.get(h),u=n==o||n==i,a=n==s||n==r;(a&&t==l||u&&t==c)&&this._drawLegend()},_drawLegend:function(){if(this._drawing){this._callLater=!0;return}this._drawing=!0,this._callLater=!1,this.get("includeInChartLayout")&&this.get("chart")._itemRenderQueue.unshift(this);var t=this.get("chart"),n=this.get("contentBox"),r=t.get("seriesCollection"),i,s=this.get("styles"),o=s.padding,u=s.item,a,f=u.hSpacing,l=u.vSpacing,c=s.hAlign,h=s.vAlign,p=s.marker,d=u.label,v,m=this._layout[this.get("direction")],g,y,b,w,E,S,x,T,N,C,k,L=[],A=p.width,O=p.height,M=0-f,_=0-l,D=0,P=0,H,B;p&&p.shape&&(w=p.shape),this._destroyLegendItems();if(t instanceof e.PieChart){i=r[0],v=i.get("categoryAxis").getDataByKey(i.get("categoryKey")),a=i.get("styles").marker,N=a.fill.colors,C=a.border.colors,k=a.border.weight,g=0,y=v.length,w=w||e.Circle,b=e.Lang.isArray(w);for(;g<y;++g)w=b?w[g]:w,x={color:N[g]},T={colors:C[g],weight:k},v=t.getSeriesItems(i,g).category.value,S=this._getLegendItem(n,this._getShapeClass(w),x,T,d,A,O,v),H=S.width,B=S.height,D=Math.max(D,H),P=Math.max(P,B),M+=H+f,_+=B+l,L.push(S)}else{g=0,y=r.length;for(;g<y;++g)i=r[g],a=this._getStylesBySeriesType(i,w),w||(w=a.shape,w||(w=e.Circle)),E=e.Lang.isArray(w)?w[g]:w,S=this._getLegendItem(n,this._getShapeClass(w),a.fill,a.border,d,A,O,i.get("valueDisplayName")),H=S.width,B=S.height,D=Math.max(D,H),P=Math.max(P,B),M+=H+f,_+=B+l,L.push(S)}this._drawing=!1,this._callLater?this._drawLegend():(m._positionLegendItems.apply(this,[L,D,P,M,_,o,f,l,c,h]),this._updateBackground(s),this.fire("legendRendered"))},_updateBackground:function(e){var t=e.background,n=this._contentRect,r=e.padding,i=n.left-r.left,s=n.top-r.top,o=n.right-i+r.right,u=n.bottom-s+r.bottom;this.get("background").set({fill:t.fill,stroke:t.border,width:o,height:u,x:i,y:s})},_getStylesBySeriesType:function(t){var n=t.get("styles"),r;return t instanceof e.LineSeries||t instanceof e.StackedLineSeries?(n=t.get("styles").line,r=n.color||t._getDefaultColor(t.get("graphOrder"),"line"),{border:{weight:1,color:r},fill:{color:r}}):t instanceof e.AreaSeries||t instanceof e.StackedAreaSeries?(n=t.get("styles").area,r=n.color||t._getDefaultColor(t.get("graphOrder"),"slice"),{border:{weight:1,color:r},fill:{color:r}}):(n=t.get("styles").marker,{fill:n.fill,border:{weight:n.border.weight,color:n.border.color,shape:n.shape},shape:n.shape})},_getLegendItem:function(t,r,i,s,o,u,a,f){var l=e.one(n.createElement("div")),c=e.one(n.createElement("span")),p,d,m,g,y;return l.setStyle(h,"absolute"),c.setStyle(h,"absolute"),c.setStyles(o),c.appendChild(n.createTextNode(f)),l.appendChild(c),t.appendChild(l),d=c.get("offsetHeight"),m=d-a,g=u+m+2,c.setStyle("left",g+v),l.setStyle("height",d+v),l.setStyle("width",g+c.get("offsetWidth")+v),p=new r({fill:i,stroke:s,width:u,height:a,x:m*.5,y:m*.5,w:u,h:a,graphic:l}),c.setStyle("left",d+v),y={node:l,width:l.get("offsetWidth"),height:l.get("offsetHeight"),shape:p,textNode:c,text:f},this._items.push(y),y},_getShapeClass:function(){var e=this.get("background").get("graphic");return e._getShapeClass.apply(e,arguments)},_getDefaultStyles:function(){var e={padding:{top:8,right:8,bottom:8,left:9},gap:10,hAlign:"center",vAlign:"top",marker:this._getPlotDefaults(),item:{hSpacing:10,vSpacing:5,label:{color:"#808080",fontSize:"85%",whiteSpace:"nowrap"}},background:{shape:"rect",fill:{color:"#faf9f2"},border:{color:"#dad8c9",weight:1}}};return e},_getPlotDefaults:function(){var e={width:10,height:10};return e},_destroyLegendItems:function(){var e;if(this._items)while(this._items.length>0)e=this._items.shift(),e.shape.get("graphic").destroy(),e.node.empty(),e.node.destroy(!0),e.node=null,e=null;this._items=[]},_layout:{vertical:y,horizontal:g},destructor:function(){var e=this.get("background"),t;this._destroyLegendItems(),e&&(t=e.get("graphic"),t?t.destroy():e.destroy())}},{ATTRS:{includeInChartLayout:{value:!1},chart:{setter:function(t){return this.after("legendRendered",e.bind(t._itemRendered,t)),t}},direction:{value:"vertical"},position:{lazyAdd:!1,value:"right",setter:function(e){return e==r||e==s?this.set("direction",a):(e==o||e==i)&&this.set("direction",f),e}},width:{getter:function(){var e=this.get("chart"),t=this._parentNode;return t?e&&this.get("includeInChartLayout")||this._width?(this._width||(this._width=0),this._width):t.get("offsetWidth"):""},setter:function(e){return this._width=e,e}},height:{valueFn:"_heightGetter",getter:function(){var e=this.get("chart"),t=this._parentNode;return t?e&&this.get("includeInChartLayout")||this._height?(this._height||(this._height=0),this._height):t.get("offsetHeight"):""},setter:function(e){return this._height=e,e}},x:{lazyAdd:!1,value:0,setter:function(e){var t=this.get("boundingBox");return t&&t.setStyle(o,e+v),e}},y:{lazyAdd:!1,value:0,setter:function(e){var t=this.get("boundingBox");return t&&t.setStyle(r,e+v),e}},items:{getter:function(){return this._items}},background:{}}}),e.Chart=E},"3.7.3",{requires:["charts-base"]});
