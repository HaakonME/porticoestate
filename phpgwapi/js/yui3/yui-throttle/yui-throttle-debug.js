/*
YUI 3.16.0 (build 76f0e08)
Copyright 2014 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/

YUI.add('yui-throttle', function (Y, NAME) {

/**
Throttles a call to a method based on the time between calls. This method is attached
to the `Y` object and is <a href="../classes/YUI.html#method_throttle">documented there</a>.

    var fn = Y.throttle(function() {
        counter++;
    });

    for (i; i< 35000; i++) {
        out++;
        fn();
    }


@module yui
@submodule yui-throttle
*/

/*! Based on work by Simon Willison: http://gist.github.com/292562 */
/**
 * Throttles a call to a method based on the time between calls.
 * @method throttle
 * @for YUI
 * @param fn {function} The function call to throttle.
 * @param ms {Number} The number of milliseconds to throttle the method call.
 * Can set globally with Y.config.throttleTime or by call. Passing a -1 will
 * disable the throttle. Defaults to 150.
 * @return {function} Returns a wrapped function that calls fn throttled.
 * @since 3.1.0
 */
Y.throttle = function(fn, ms) {
    ms = (ms) ? ms : (Y.config.throttleTime || 150);

    if (ms === -1) {
        return function() {
            fn.apply(this, arguments);
        };
    }

    var last = Y.Lang.now();

    return function() {
        var now = Y.Lang.now();
        if (now - last > ms) {
            last = now;
            fn.apply(this, arguments);
        }
    };
};


}, '3.16.0', {"requires": ["yui-base"]});
