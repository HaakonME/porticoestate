/*
YUI 3.7.3 (build 5687)
Copyright 2012 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/
YUI.add("datatable-column-widths",function(e,t){function i(){}var n=e.Lang.isNumber,r=e.Array.indexOf;e.Features.add("table","badColWidth",{test:function(){var t=e.one("body"),n,r;return t&&(n=t.insertBefore('<table style="position:absolute;visibility:hidden;border:0 none"><colgroup><col style="width:9px"></colgroup><tbody><tr><td style="padding:0 4px;font:normal 2px/2px arial;border:0 none">.</td></tr></tbody></table>',t.get("firstChild")),r=n.one("td").getComputedStyle("width")!=="1px",n.remove(!0)),r}}),e.mix(i.prototype,{COL_TEMPLATE:"<col/>",COLGROUP_TEMPLATE:"<colgroup/>",setColumnWidth:function(e,t){var i=this.getColumn(e),s=i&&r(this._displayColumns,i);return s>-1&&(n(t)&&(t+="px"),i.width=t,this._setColumnWidth(s,t)),this},_createColumnGroup:function(){return e.Node.create(this.COLGROUP_TEMPLATE)},initializer:function(e){this.after(["renderView","columnsChange"],this._uiSetColumnWidths)},_setColumnWidth:function(t,r){var i=this._colgroupNode,s=i&&i.all("col").item(t),o,u;s&&(r&&n(r)&&(r+="px"),s.setStyle("width",r),r&&e.Features.test("table","badColWidth")&&(o=this.getCell([0,t]),o&&(u=function(e){return parseInt(o.getComputedStyle(e),10)|0},s.setStyle("width",parseInt(r,10)-u("paddingLeft")-u("paddingRight")-u("borderLeftWidth")-u("borderRightWidth")+"px"))))},_uiSetColumnWidths:function(){if(!this.view)return;var e=this.COL_TEMPLATE,t=this._colgroupNode,n=this._displayColumns,r,i;t?t.empty():(t=this._colgroupNode=this._createColumnGroup(),this._tableNode.insertBefore(t,this._tableNode.one("> thead, > tfoot, > tbody")));for(r=0,i=n.length;r<i;++r)t.append(e),this._setColumnWidth(r,n[r].width)}},!0),e.DataTable.ColumnWidths=i,e.Base.mix(e.DataTable,[i])},"3.7.3",{requires:["datatable-base"]});
