(function($){"use strict";$.setupValidation=function(conf){var $forms=$(conf.form||"form");$.each(conf.validate||conf.validation||{},function(elemRef,attr){var $elem;if(elemRef[0]=="#"){$elem=$(elemRef)}else if(elemRef[0]=="."){$elem=$forms.find(elemRef)}else{$elem=$forms.find('*[name="'+elemRef+'"]')}$elem.attr("data-validation",attr.validation);$.each(attr,function(name,val){if(name!="validation"){$elem.attr("data-validation-"+name,typeof val=="string"?val:"true")}})});$.validate(conf)}})(jQuery);
