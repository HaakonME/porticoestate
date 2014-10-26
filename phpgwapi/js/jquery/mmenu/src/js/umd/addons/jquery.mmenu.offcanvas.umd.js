(function ( factory ) {
    if ( typeof define === 'function' && define.amd )
    {
        // AMD. Register as an anonymous module.
        define( [ 'jquery' ], factory );
    }
    else if ( typeof exports === 'object' )
    {
        // Node/CommonJS
        factory( require( 'jquery' ) );
    }
    else
    {
        // Browser globals
        factory( jQuery );
    }
}( function ( jQuery ) {


/*	
 * jQuery mmenu offCanvas addon
 * mmenu.frebsite.nl
 *
 * Copyright (c) Fred Heusschen
 */
!function(e){function t(e){return e}function o(e){return"string"!=typeof e.pageSelector&&(e.pageSelector="> "+e.pageNodetype),e}function n(){c=!0,a=e[s]._c,p=e[s]._d,r=e[s]._e,a.add("offcanvas modal background opening blocker page"),p.add("style"),r.add("opening opened closing closed setPage"),l=e[s].glbl,l.$allMenus=(l.$allMenus||e()).add(this.$menu),l.$wndw.on(r.keydown,function(e){return l.$html.hasClass(a.opened)&&9==e.keyCode?(e.preventDefault(),!1):void 0});var t=0;l.$wndw.on(r.resize,function(e,o){if(o||l.$html.hasClass(a.opened)){var n=l.$wndw.height();(o||n!=t)&&(t=n,l.$page.css("minHeight",n))}})}var s="mmenu",i="offCanvas";e[s].prototype["_init_"+i]=function(){if(this.opts[i]&&!this.vars[i+"_added"]){this.vars[i+"_added"]=!0,c||n(),this.opts[i]=t(this.opts[i]),this.conf[i]=o(this.conf[i]);var e=this.opts[i],s=this.conf[i],p=[a.offcanvas];"boolean"!=typeof this.vars.opened&&(this.vars.opened=!1),"left"!=e.position&&p.push(a.mm(e.position)),"back"!=e.zposition&&p.push(a.mm(e.zposition)),this.$menu.addClass(p.join(" ")).parent().removeClass(a.wrapper),this[i+"_initPage"](l.$page),this[i+"_initBlocker"](),this[i+"_initOpenClose"](),this[i+"_bindCustomEvents"](),this.$menu[s.menuInjectMethod+"To"](s.menuWrapperSelector)}},e[s].addons.push(i),e[s].defaults[i]={position:"left",zposition:"back",modal:!1,moveBackground:!0},e[s].configuration[i]={pageNodetype:"div",pageSelector:null,menuWrapperSelector:"body",menuInjectMethod:"prepend"},e[s].prototype.open=function(){if(this.vars.opened)return!1;var e=this;return this._openSetup(),setTimeout(function(){e._openFinish()},this.conf.openingInterval),"open"},e[s].prototype._openSetup=function(){l.$allMenus.not(this.$menu).trigger(r.close),l.$page.data(p.style,l.$page.attr("style")||""),l.$wndw.trigger(r.resize,[!0]);var e=[a.opened];this.opts[i].modal&&e.push(a.modal),this.opts[i].moveBackground&&e.push(a.background),"left"!=this.opts[i].position&&e.push(a.mm(this.opts[i].position)),"back"!=this.opts[i].zposition&&e.push(a.mm(this.opts[i].zposition)),this.opts.classes&&e.push(this.opts.classes),l.$html.addClass(e.join(" ")),this.vars.opened=!0,this.$menu.addClass(a.current+" "+a.opened)},e[s].prototype._openFinish=function(){var e=this;this.__transitionend(l.$page,function(){e.$menu.trigger(r.opened)},this.conf.transitionDuration),l.$html.addClass(a.opening),this.$menu.trigger(r.opening)},e[s].prototype.close=function(){if(!this.vars.opened)return!1;var e=this;return this.__transitionend(l.$page,function(){e.$menu.removeClass(a.current).removeClass(a.opened),l.$html.removeClass(a.opened).removeClass(a.modal).removeClass(a.background).removeClass(a.mm(e.opts[i].position)).removeClass(a.mm(e.opts[i].zposition)),e.opts.classes&&l.$html.removeClass(e.opts.classes),l.$page.attr("style",l.$page.data(p.style)),e.vars.opened=!1,e.$menu.trigger(r.closed)},this.conf.transitionDuration),l.$html.removeClass(a.opening),this.$menu.trigger(r.closing),"close"},e[s].prototype[i+"_initBlocker"]=function(){var t=this;l.$blck||(l.$blck=e('<div id="'+a.blocker+'" />').appendTo(l.$body)),l.$blck.off(r.touchstart).on(r.touchstart,function(e){e.preventDefault(),e.stopPropagation(),l.$blck.trigger(r.mousedown)}).on(r.mousedown,function(e){e.preventDefault(),l.$html.hasClass(a.modal)||t.close()})},e[s].prototype[i+"_initPage"]=function(t){t||(t=e(this.conf[i].pageSelector,l.$body),t.length>1&&(t=t.wrapAll("<"+this.conf[i].pageNodetype+" />").parent())),t.addClass(a.page),l.$page=t},e[s].prototype[i+"_initOpenClose"]=function(){var t=this,o=this.$menu.attr("id");o&&o.length&&(this.conf.clone&&(o=a.umm(o)),e('a[href="#'+o+'"]').off(r.click).on(r.click,function(e){e.preventDefault(),t.open()}));var o=l.$page.attr("id");o&&o.length&&e('a[href="#'+o+'"]').on(r.click,function(e){e.preventDefault(),t.close()})},e[s].prototype[i+"_bindCustomEvents"]=function(){var e=this,t=r.open+" "+r.opening+" "+r.opened+" "+r.close+" "+r.closing+" "+r.closed+" "+r.setPage;this.$menu.off(t).on(t,function(e){e.stopPropagation()}),this.$menu.on(r.open,function(){e.open()}).on(r.close,function(){e.close()}).on(r.setPage,function(t,o){e[i+"_initPage"](o),e[i+"_initOpenClose"]()})};var a,p,r,l,c=!1}(jQuery);
}));