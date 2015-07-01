/**
 * jQuery Form Validator Module: JSconf
 * ------------------------------------------
 * Created by Victor Jonsson <http://www.victorjonsson.se>
 *
 * This module makes it possible to configure form validation using javascript
 *
 * @website http://formvalidator.net/#location-validators
 * @license Dual licensed under the MIT or GPL Version 2 licenses
 * @version 2.2.beta.58
 */
(function($) {

  "use strict";

  $.setupValidation = function(conf) {
    var $forms = $(conf.form || 'form');
    $.each(conf.validate || conf.validation || {}, function(elemRef, attr) {
        var $elem;
        if( elemRef[0] == '#' ) {
          $elem = $(elemRef);
        } else if( elemRef[0] == '.' ) {
          $elem = $forms.find(elemRef);
        } else {
          $elem = $forms.find('*[name="' +elemRef+ '"]');
        }

        $elem.attr('data-validation', attr.validation);

        $.each(attr, function(name, val) {
          if( name != 'validation' ) {
            $elem.attr('data-validation-'+name, typeof val == 'string' ? val : 'true');
          }
        });
    });

    $.validate(conf);
  };

})(jQuery);
