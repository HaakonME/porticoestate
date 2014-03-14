/*
YUI 3.7.3 (build 5687)
Copyright 2012 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/
YUI.add("datatable-mutable",function(e,t){var n=e.Array,r=e.Lang,i=r.isString,s=r.isArray,o=r.isObject,u=r.isNumber,a=e.Array.indexOf,f;e.namespace("DataTable").Mutable=f=function(){},f.ATTRS={autoSync:{value:!1,validator:r.isBoolean}},e.mix(f.prototype,{addColumn:function(e,t){i(e)&&(e={key:e});if(e){if(arguments.length<2||!u(t)&&!s(t))t=this.get("columns").length;this.fire("addColumn",{column:e,index:t})}return this},modifyColumn:function(e,t){return i(t)&&(t={key:t}),o(t)&&this.fire("modifyColumn",{column:e,newColumnDef:t}),this},moveColumn:function(e,t){return e!==undefined&&(u(t)||s(t))&&this.fire("moveColumn",{column:e,index:t}),this},removeColumn:function(e){return e!==undefined&&this.fire("removeColumn",{column:e}),this},addRow:function(e,t){var r=t&&"sync"in t?t.sync:this.get("autoSync"),i,s,o,u,a;if(e&&this.data){i=this.data.add.apply(this.data,arguments);if(r){i=n(i),a=n(arguments,1,!0);for(o=0,u=i.length;o<u;++o)s=i[o],s.isNew()&&i[o].save.apply(i[o],a)}}return this},removeRow:function(e,t){var r=this.data,i=t&&"sync"in t?t.sync:this.get("autoSync"),s,u,a,f,l;o(e)&&e instanceof this.get("recordType")?u=e:r&&e!==undefined&&(u=r.getById(e)||r.getByClientId(e)||r.item(e));if(u){l=n(arguments,1,!0),s=r.remove.apply(r,[u].concat(l));if(i){o(l[0])||l.unshift({}),l[0]["delete"]=!0,s=n(s);for(a=0,f=s.length;a<f;++a)u=s[a],u.destroy.apply(u,l)}}return this},modifyRow:function(e,t,r){var i=this.data,s=r&&"sync"in r?r.sync:this.get("autoSync"),u,a;return o(e)&&e instanceof this.get("recordType")?u=e:i&&e!==undefined&&(u=i.getById(e)||i.getByClientId(e)||i.item(e)),u&&o(t)&&(a=n(arguments,1,!0),u.setAttrs.apply(u,a),s&&!u.isNew()&&u.save.apply(u,a)),this},_defAddColumnFn:function(e){var t=n(e.index),r=this.get("columns"),i=r,s,o;for(s=0,o=t.length-1;i&&s<o;++s)i=i[t[s]]&&i[t[s]].children;i&&(i.splice(t[s],0,e.column),this.set("columns",r,{originEvent:e}))},_defModifyColumnFn:function(t){var n=this.get("columns"),r=this.getColumn(t.column);r&&(e.mix(r,t.newColumnDef,!0),this.set("columns",n,{originEvent:t}))},_defMoveColumnFn:function(e){var t=this.get("columns"),r=this.getColumn(e.column),i=n(e.index),s,o,u,f,l;if(r){s=r._parent?r._parent.children:t,o=a(s,r);if(o>-1){u=t;for(f=0,l=i.length-1;u&&f<l;++f)u=u[i[f]]&&u[i[f]].children;u&&(l=u.length,s.splice(o,1),i=i[f],l>u.lenth&&o<i&&i--,u.splice(i,0,r),this.set("columns",t,{originEvent:e}))}}},_defRemoveColumnFn:function(t){var n=this.get("columns"),r=this.getColumn(t.column),i,s;r&&(i=r._parent?r._parent.children:n,s=e.Array.indexOf(i,r),s>-1&&(i.splice(s,1),this.set("columns",n,{originEvent:t})))},initializer:function(){this.publish({addColumn:{defaultFn:e.bind("_defAddColumnFn",this)},removeColumn:{defaultFn:e.bind("_defRemoveColumnFn",this)},moveColumn:{defaultFn:e.bind("_defMoveColumnFn",this)},modifyColumn:{defaultFn:e.bind("_defModifyColumnFn",this)}})}}),f.prototype.addRows=f.prototype.addRow,r.isFunction(e.DataTable)&&e.Base.mix(e.DataTable,[f])},"3.7.3",{requires:["datatable-base"]});
