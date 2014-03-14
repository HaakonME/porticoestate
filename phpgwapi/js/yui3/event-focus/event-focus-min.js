/*
YUI 3.7.3 (build 5687)
Copyright 2012 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/
YUI.add("event-focus",function(e,t){function u(t,r,u){var a="_"+t+"Notifiers";e.Event.define(t,{_useActivate:o,_attach:function(i,s,o){return e.DOM.isWindow(i)?n._attach([t,function(e){s.fire(e)},i]):n._attach([r,this._proxy,i,this,s,o],{capture:!0})},_proxy:function(t,r,i){var s=t.target,f=t.currentTarget,l=s.getData(a),c=e.stamp(f._node),h=o||s!==f,p;r.currentTarget=i?s:f,r.container=i?f:null,l?h=!0:(l={},s.setData(a,l),h&&(p=n._attach([u,this._notify,s._node]).sub,p.once=!0)),l[c]||(l[c]=[]),l[c].push(r),h||this._notify(t)},_notify:function(t,n){var r=t.currentTarget,i=r.getData(a),o=r.ancestors(),u=r.get("ownerDocument"),f=[],l=i?e.Object.keys(i).length:0,c,h,p,d,v,m,g,y,b,w;r.clearData(a),o.push(r),u&&o.unshift(u),o._nodes.reverse(),l&&(m=l,o.some(function(t){var n=e.stamp(t),r=i[n],s,o;if(r){l--;for(s=0,o=r.length;s<o;++s)r[s].handle.sub.filter&&f.push(r[s])}return!l}),l=m);while(l&&(c=o.shift())){d=e.stamp(c),h=i[d];if(h){for(g=0,y=h.length;g<y;++g){p=h[g],b=p.handle.sub,v=!0,t.currentTarget=c,b.filter&&(v=b.filter.apply(c,[c,t].concat(b.args||[])),f.splice(s(f,p),1)),v&&(t.container=p.container,w=p.fire(t));if(w===!1||t.stopped===2)break}delete h[d],l--}if(t.stopped!==2)for(g=0,y=f.length;g<y;++g){p=f[g],b=p.handle.sub,b.filter.apply(c,[c,t].concat(b.args||[]))&&(t.container=p.container,t.currentTarget=c,w=p.fire(t));if(w===!1||t.stopped===2)break}if(t.stopped)break}},on:function(e,t,n){t.handle=this._attach(e._node,n)},detach:function(e,t){t.handle.detach()},delegate:function(t,n,r,s){i(s)&&(n.filter=function(n){return e.Selector.test(n._node,s,t===n?null:t._node)}),n.handle=this._attach(t._node,r,!0)},detachDelegate:function(e,t){t.handle.detach()}},!0)}var n=e.Event,r=e.Lang,i=r.isString,s=e.Array.indexOf,o=function(){var t=e.config.doc.createElement("p"),n;return t.setAttribute("onbeforeactivate",";"),n=t.onbeforeactivate,n!==undefined}();o?(u("focus","beforeactivate","focusin"),u("blur","beforedeactivate","focusout")):(u("focus","focus","focus"),u("blur","blur","blur"))},"3.7.3",{requires:["event-synthetic"]});
