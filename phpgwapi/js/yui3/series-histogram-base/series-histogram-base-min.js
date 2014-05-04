/*
YUI 3.16.0 (build 76f0e08)
Copyright 2014 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/

YUI.add("series-histogram-base",function(e,t){function r(){}var n=e.Lang;r.prototype={drawSeries:function(){if(this.get("xcoords").length<1)return;var e=this._copyObject(this.get("styles").marker),t=this.get("graphic"),r,i,s=this.get("xcoords"),o=this.get("ycoords"),u=0,a=s.length,f=o[0],l=this.get("seriesTypeCollection"),c=l?l.length:0,h=0,p=0,d=0,v,m,g=this.get("order"),y=this.get("graphOrder"),b,w,E,S,x,T=null,N=null,C=[],k=[],L,A,O,M,_={width:[],height:[]},D=[],P=[],H=this.get("groupMarkers");n.isArray(e.fill.color)&&(T=e.fill.color.concat()),n.isArray(e.border.color)&&(N=e.border.color.concat()),this.get("direction")==="vertical"?(E="height",S="width"):(E="width",S="height"),r=e[E],i=e[S],this._createMarkerCache(),this._maxSize=t.get(E);if(l&&c>1){for(;u<c;++u)m=l[u],h+=m.get("styles").marker[E],g>u&&(d=h);p=a*h,p>this._maxSize&&(v=t.get(E)/p,h*=v,d*=v,r*=v,r=Math.max(r,1),this._maxSize=r)}else h=e[E],p=a*h,p>this._maxSize&&(h=this._maxSize/a,this._maxSize=h);d-=h/2;for(u=0;u<a;++u){L=s[u]-h/2,A=L+h,O=o[u]-h/2,M=O+h,C.push({start:L,end:A}),k.push({start:O,end:M});if(!H&&(isNaN(s[u])||isNaN(o[u]))){this._markers.push(null);continue}x=this._getMarkerDimensions(s[u],o[u],i,d),!isNaN(x.calculatedSize)&&x.calculatedSize>0?(f=x.top,b=x.left,H?(_[E][u]=r,_[S][u]=x.calculatedSize,D.push(b),P.push(f)):(e[E]=r,e[S]=x.calculatedSize,e.x=b,e.y=f,T&&(e.fill.color=T[u%T.length]),N&&(e.border.color=N[u%N.length]),w=this.getMarker(e,y,u))):H||this._markers.push(null)}this.set("xMarkerPlane",C),this.set("yMarkerPlane",k),H?this._createGroupMarker({fill:e.fill,border:e.border,dimensions:_,xvalues:D,yvalues:P,shape:e.shape}):this._clearMarkerCache()},_defaultFillColors:["#66007f","#a86f41","#295454","#996ab2","#e8cdb7","#90bdbd","#000000","#c3b8ca","#968373","#678585"],_getPlotDefaults:function(){var e={fill:{type:"solid",alpha:1,colors:null,alphas:null,ratios:null},border:{weight:0,alpha:1},width:12,height:12,shape:"rect",padding:{top:0,left:0,right:0,bottom:0}};return e.fill.color=this._getDefaultColor(this.get("graphOrder"),"fill"),e.border.color=this._getDefaultColor(this.get("graphOrder"),"border"),e}},e.Histogram=r},"3.16.0",{requires:["series-cartesian","series-plot-util"]});
