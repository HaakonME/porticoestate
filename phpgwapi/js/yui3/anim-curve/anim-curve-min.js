/*
YUI 3.16.0 (build 76f0e08)
Copyright 2014 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/

YUI.add("anim-curve",function(e,t){e.Anim.behaviors.curve={set:function(t,n,r,i,s,o,u){r=r.slice.call(r),i=i.slice.call(i);var a=u(s,0,100,o)/100;i.unshift(r),t._node.setXY(e.Anim.getBezier(i,a))},get:function(e){return e._node.getXY()}},e.Anim.getBezier=function(e,t){var n=e.length,r=[],i,s;for(i=0;i<n;++i)r[i]=[e[i][0],e[i][1]];for(s=1;s<n;++s)for(i=0;i<n-s;++i)r[i][0]=(1-t)*r[i][0]+t*r[parseInt(i+1,10)][0],r[i][1]=(1-t)*r[i][1]+t*r[parseInt(i+1,10)][1];return[r[0][0],r[0][1]]}},"3.16.0",{requires:["anim-xy"]});
