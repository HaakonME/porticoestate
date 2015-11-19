/**
 *  JQUERY-FORM-VALIDATOR
 *
 *  @website by 
 *  @license MIT
 *  @version 2.2.83
 */
!function(a,b){"use strict";var c="undefined"!=typeof b.FileReader,d=function(b){var c=a.split((b.valAttr("allowing")||"").toLowerCase());return a.inArray("jpg",c)>-1&&-1===a.inArray("jpeg",c)?c.push("jpeg"):a.inArray("jpeg",c)>-1&&-1===a.inArray("jpg",c)&&c.push("jpg"),c},e=function(a,b,c,d){var e=d[b]||"";a.errorMessageKey="",a.errorMessage=e.replace("%s",c)},f=function(a){b.console&&b.console.log&&b.console.log(a)},g=function(c,d,e){var f=new FileReader,g=new Image;f.readAsDataURL(c),f.onload=function(c){g.onload=function(){a(b).trigger("imageValidation",[this]),d(this)},g.onerror=function(){e()},g.src=c.target.result}};a.formUtils.addValidator({name:"mime",validatorFunction:function(b,g,h,i){if(c){var j=!0,k=g.get(0).files||[],l="",m=d(g);return k.length&&(a.each(k,function(b,c){return j=!1,l=c.type||"",a.each(m,function(a,b){return j=l.indexOf(b)>-1,j?!1:void 0}),j}),j||(f("Trying to upload a file with mime type "+l+" which is not allowed"),e(this,"wrongFileType",m.join(", "),i))),j}return f("FileReader not supported by browser, will check file extension"),a.formUtils.validators.validate_extension.validatorFunction(b,g,h,i)},errorMessage:"",errorMessageKey:"wrongFileType"}),a.formUtils.addValidator({name:"extension",validatorFunction:function(b,c,f,g){var h=!0,i=this,j=d(c);return a.each(c.get(0).files||[b],function(b,c){var d="string"==typeof c?c:c.value||c.fileName||c.name,f=d.substr(d.lastIndexOf(".")+1);return-1===a.inArray(f.toLowerCase(),j)?(h=!1,e(i,"wrongFileType",j.join(", "),g),!1):void 0}),h},errorMessage:"",errorMessageKey:"wrongFileType"}),a.formUtils.addValidator({name:"size",validatorFunction:function(b,d,g,h){var i=d.valAttr("max-size");if(!i)return f('Input "'+d.attr("name")+'" is missing data-validation-max-size attribute'),!0;if(!c)return!0;var j=a.formUtils.convertSizeNameToBytes(i),k=!0;return a.each(d.get(0).files||[],function(a,b){return k=b.size<=j}),k||e(this,"wrongFileSize",i,h),k},errorMessage:"",errorMessageKey:"wrongFileSize"}),a.formUtils.convertSizeNameToBytes=function(a){return a=a.toUpperCase(),"M"===a.substr(a.length-1,1)?1024*parseInt(a.substr(0,a.length-1),10)*1024:"MB"===a.substr(a.length-2,2)?1024*parseInt(a.substr(0,a.length-2),10)*1024:"KB"===a.substr(a.length-2,2)?1024*parseInt(a.substr(0,a.length-2),10):"B"===a.substr(a.length-1,1)?parseInt(a.substr(0,a.length-1),10):parseInt(a,10)};var h=function(){return!1};a.formUtils.checkImageDimension=function(a,b,c){var d=!1,e={width:0,height:0},f=function(a){a=a.replace("min","").replace("max","");var b=a.split("x");e.width=b[0],e.height=b[1]?b[1]:b[0]},g=!1,h=!1,i=b.split("-");return 1===i.length?0===i[0].indexOf("min")?g=i[0]:h=i[0]:(g=i[0],h=i[1]),g&&(f(g),(a.width<e.width||a.height<e.height)&&(d=c.imageTooSmall+" ("+c.min+" "+e.width+"x"+e.height+"px)")),!d&&h&&(f(h),(a.width>e.width||a.height>e.height)&&(d=a.width>e.width?c.imageTooWide+" "+e.width+"px":c.imageTooTall+" "+e.height+"px",d+=" ("+c.max+" "+e.width+"x"+e.height+"px)")),d},a.formUtils.checkImageRatio=function(a,b,c){var d=a.width/a.height,e=function(a){var b=a.replace("max","").replace("min","").split(":");return b[0]/b[1]},f=b.split("-"),g=function(a,b,c){return a>=b&&c>=a};if(1===f.length){if(d!==e(f[0]))return c.imageRatioNotAccepted}else if(2===f.length&&!g(d,e(f[0]),e(f[1])))return c.imageRatioNotAccepted;return!1},a.formUtils.addValidator({name:"dimension",validatorFunction:function(b,d,e,f,i){var j=!1;if(c){var k=d.get(0).files||[];if(j=!0,-1===d.attr("data-validation").indexOf("mime"))return alert("You should validate file type being jpg, gif or png on input "+d[0].name),!1;if(k.length>1)return alert("Validating image dimensions does not support inputs allowing multiple files"),!1;if(0===k.length)return!0;if(d.valAttr("has-valid-dim"))return!0;if(d.valAttr("has-not-valid-dim"))return this.errorMessage=f.wrongFileDim+" "+d.valAttr("has-not-valid-dim"),!1;if("keyup"===a.formUtils.eventType)return null;var l=!1;return a.formUtils.isValidatingEntireForm&&(l=!0,a.formUtils.haltValidation=!0,i.bind("submit",h).addClass("on-blur")),g(k[0],function(b){var c=!1;d.valAttr("dimension")&&(c=a.formUtils.checkImageDimension(b,d.valAttr("dimension"),f)),!c&&d.valAttr("ratio")&&(c=a.formUtils.checkImageRatio(b,d.valAttr("ratio"),f)),c?d.valAttr("has-not-valid-dim",c):d.valAttr("has-valid-dim","true"),d.valAttr("has-keyup-event")||d.valAttr("has-keyup-event","1").bind("keyup change",function(b){9!==b.keyCode&&16!==b.keyCode&&a(this).valAttr("has-not-valid-dim",!1).valAttr("has-valid-dim",!1)}),l?(a.formUtils.haltValidation=!1,i.removeClass("on-blur").get(0).onsubmit=function(){},i.unbind("submit",h),i.trigger("submit")):d.trigger("blur")},function(a){throw a}),!0}return j},errorMessage:"",errorMessageKey:""}),a(b).one("validatorsLoaded formValidationSetup",function(b,c){var d;d=c?c.find('input[type="file"]'):a('input[type="file"]'),d.filter("*[data-validation]").bind("change",function(){a(this).removeClass("error").parent().find(".form-error").remove()})})}(jQuery,window);