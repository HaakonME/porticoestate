YUI.add("gallery-sm-menu-base",function(e,t){var n="disable",r="enable",i="hide",s="show",o=e.Base.create("menuBase",e.Tree,[e.Tree.Labelable,e.Tree.Openable],{nodeClass:e.Menu.Item,initializer:function(e){e&&(e.nodes=e.items)},closeSubMenus:function(){return e.Object.each(this._openMenus,function(e){e.close()},this),this},disableItem:function(e,t){return e.isDisabled()||this._fireTreeEvent(n,{item:e},{defaultFn:this._defDisableFn,silent:t&&t.silent}),this},enableItem:function(e,t){return e.isDisabled()&&this._fireTreeEvent(r,{item:e},{defaultFn:this._defEnableFn,silent:t&&t.silent}),this},hideItem:function(e,t){return e.isHidden()||this._fireTreeEvent(i,{item:e},{defaultFn:this._defHideFn,silent:t&&t.silent}),this},showItem:function(e,t){return e.isHidden()&&this._fireTreeEvent(s,{item:e},{defaultFn:this._defShowFn,silent:t&&t.silent}),this},_defDisableFn:function(e){e.item.state.disabled=!0},_defEnableFn:function(e){delete e.item.state.disabled},_defHideFn:function(e){e.item.state.hidden=!0},_defShowFn:function(e){delete e.item.state.hidden}});e.namespace("Menu").Base=o},"@VERSION@",{requires:["gallery-sm-menu-item","tree-labelable","tree-openable"]});
