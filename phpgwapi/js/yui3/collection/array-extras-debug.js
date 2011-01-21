/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.3.0
build: 3167
*/
YUI.add('array-extras', function(Y) {

/**
 * Collection utilities beyond what is provided in the YUI core
 * @module collection
 * @submodule array-extras
 */

var L = Y.Lang, Native = Array.prototype, A = Y.Array;

/**
 * Adds the following array utilities to the YUI instance
 * (Y.Array).  This is in addition to the methods provided
 * in the core.
 * @class YUI~array~extras
 */

/**
 * Returns the index of the last item in the array that contains the specified
 * value, or -1 if the value isn't found.
 * @method Array.lastIndexOf
 * @static
 * @param {Array} a Array to search in.
 * @param {any} val Value to search for.
 * @param {Number} fromIndex (optional) Index at which to start searching
 *   backwards. Defaults to the array's length - 1. If negative, it will be
 *   taken as an offset from the end of the array. If the calculated index is
 *   less than 0, the array will not be searched and -1 will be returned.
 * @return {Number} Index of the item that contains the value, or -1 if not
 *   found.
 */
A.lastIndexOf = Native.lastIndexOf ?
    function(a, val, fromIndex) {
        // An undefined fromIndex is still considered a value by some (all?)
        // native implementations, so we can't pass it unless it's actually
        // specified.
        return fromIndex || fromIndex === 0 ? a.lastIndexOf(val, fromIndex) :
                a.lastIndexOf(val);
    } :
    function(a, val, fromIndex) {
        var len = a.length,
            i   = len - 1;

        if (fromIndex || fromIndex === 0) {
            i = Math.min(fromIndex < 0 ? len + fromIndex : fromIndex, len);
        }

        if (i > -1 && len > 0) {
            for (; i > -1; --i) {
                if (a[i] === val) {
                    return i;
                }
            }
        }

        return -1;
    };

/**
 * Returns a copy of the specified array with duplicate items removed.
 * @method Array.unique
 * @param {Array} a Array to dedupe.
 * @return {Array} Copy of the array with duplicate items removed.
 * @static
 */
A.unique = function(a, sort) {
    // Note: the sort param is deprecated and intentionally undocumented since
    // YUI 3.3.0. It never did what the API docs said it did (see the older
    // comment below as well).
    var i       = 0,
        len     = a.length,
        results = [],
        item, j;

    for (; i < len; ++i) {
        item = a[i];

        // This loop iterates over the results array in reverse order and stops
        // if it finds an item that matches the current input array item (a
        // dupe). If it makes it all the way through without finding a dupe, the
        // current item is pushed onto the results array.
        for (j = results.length; j > -1; --j) {
            if (item === results[j]) {
                break;
            }
        }

        if (j === -1) {
            results.push(item);
        }
    }

    // Note: the sort option doesn't really belong here... I think it was added
    // because there was a way to fast path the two operations together.  That
    // implementation was not working, so I replaced it with the following.
    // Leaving it in so that the API doesn't get broken.
    if (sort) {
        if (L.isNumber(results[0])) {
            results.sort(A.numericSort);
        } else {
            results.sort();
        }
    }

    return results;
};

/**
* Executes the supplied function on each item in the array. Returns a new array
* containing the items for which the supplied function returned a truthy value.
* @method Array.filter
* @param {Array} a Array to filter.
* @param {Function} f Function to execute on each item.
* @param {Object} o Optional context object.
* @static
* @return {Array} Array of items for which the supplied function returned a
*   truthy value (empty if it never returned a truthy value).
*/
A.filter = Native.filter ?
    function(a, f, o) {
        return a.filter(f, o);
    } :
    function(a, f, o) {
        var i       = 0,
            len     = a.length,
            results = [],
            item;

        for (; i < len; ++i) {
            item = a[i];

            if (f.call(o, item, i, a)) {
                results.push(item);
            }
        }

        return results;
    };

/**
* The inverse of filter. Executes the supplied function on each item.
* Returns a new array containing the items that the supplied
* function returned *false* for.
* @method Array.reject
* @param {Array} a the array to iterate.
* @param {Function} f the function to execute on each item.
* @param {object} o Optional context object.
* @static
* @return {Array} The items on which the supplied function
* returned false.
*/
A.reject = function(a, f, o) {
    return A.filter(a, function(item, i, a) {
        return !f.call(o, item, i, a);
    });
};

/**
* Executes the supplied function on each item in the array.
* Iteration stops if the supplied function does not return
* a truthy value.
* @method Array.every
* @param {Array} a the array to iterate.
* @param {Function} f the function to execute on each item.
* @param {object} o Optional context object.
* @static
* @return {boolean} true if every item in the array returns true
* from the supplied function.
*/
A.every = Native.every ?
    function(a, f, o) {
        return a.every(f, o);
    } :
    function(a, f, o) {
        for (var i = 0, l = a.length; i < l; ++i) {
            if (!f.call(o, a[i], i, a)) {
                return false;
            }
        }

        return true;
    };

/**
* Executes the supplied function on each item in the array.
* @method Array.map
* @param {Array} a the array to iterate.
* @param {Function} f the function to execute on each item.
* @param {object} o Optional context object.
* @static
* @return {Array} A new array containing the return value
* of the supplied function for each item in the original
* array.
*/
A.map = Native.map ?
    function(a, f, o) {
        return a.map(f, o);
    } :
    function(a, f, o) {
        var i       = 0,
            len     = a.length,
            results = a.concat();

        for (; i < len; ++i) {
            results[i] = f.call(o, a[i], i, a);
        }

        return results;
    };


/**
* Executes the supplied function on each item in the array.
* Reduce "folds" the array into a single value.  The callback
* function receives four arguments:
* the value from the previous callback call (or the initial value),
* the value of the current element, the current index, and
* the array over which iteration is occurring.
* @method Array.reduce
* @param {Array} a the array to iterate.
* @param {any} init The initial value to start from.
* @param {Function} f the function to execute on each item. It
* is responsible for returning the updated value of the
* computation.
* @param {object} o Optional context object.
* @static
* @return {any} A value that results from iteratively applying the
* supplied function to each element in the array.
*/
A.reduce = Native.reduce ?
    function(a, init, f, o) {
        // ES5 Array.reduce doesn't support a thisObject, so we need to
        // implement it manually
        return a.reduce(function(init, item, i, a) {
            return f.call(o, init, item, i, a);
        }, init);
    } :
    function(a, init, f, o) {
        var i      = 0,
            len    = a.length,
            result = init;

        for (; i < len; ++i) {
            result = f.call(o, result, a[i], i, a);
        }

        return result;
    };


/**
* Executes the supplied function on each item in the array,
* searching for the first item that matches the supplied
* function.
* @method Array.find
* @param {Array} a the array to search.
* @param {Function} f the function to execute on each item.
* Iteration is stopped as soon as this function returns true
* on an item.
* @param {object} o Optional context object.
* @static
* @return {object} the first item that the supplied function
* returns true for, or null if it never returns true.
*/
A.find = function(a, f, o) {
    for (var i = 0, l = a.length; i < l; i++) {
        if (f.call(o, a[i], i, a)) {
            return a[i];
        }
    }
    return null;
};

/**
* Iterates over an array, returning a new array of all the elements
* that match the supplied regular expression
* @method Array.grep
* @param {Array} a a collection to iterate over.
* @param {RegExp} pattern The regular expression to test against
* each item.
* @static
* @return {Array} All the items in the collection that
* produce a match against the supplied regular expression.
* If no items match, an empty array is returned.
*/
A.grep = function(a, pattern) {
    return A.filter(a, function(item, index) {
        return pattern.test(item);
    });
};


/**
* Partitions an array into two new arrays, one with the items
* that match the supplied function, and one with the items that
* do not.
* @method Array.partition
* @param {Array} a a collection to iterate over.
* @param {Function} f a function that will receive each item
* in the collection and its index.
* @param {object} o Optional execution context of f.
* @static
* @return {object} An object with two members, 'matches' and 'rejects',
* that are arrays containing the items that were selected or
* rejected by the test function (or an empty array).
*/
A.partition = function(a, f, o) {
    var results = {
        matches: [],
        rejects: []
    };

    A.each(a, function(item, index) {
        var set = f.call(o, item, index, a) ? results.matches : results.rejects;
        set.push(item);
    });

    return results;
};

/**
* Creates an array of arrays by pairing the corresponding
* elements of two arrays together into a new array.
* @method Array.zip
* @param {Array} a a collection to iterate over.
* @param {Array} a2 another collection whose members will be
* paired with members of the first parameter.
* @static
* @return {array} An array of arrays formed by pairing each element
* of the first collection with an item in the second collection
* having the corresponding index.
*/
A.zip = function(a, a2) {
    var results = [];
    A.each(a, function(item, index) {
        results.push([item, a2[index]]);
    });
    return results;
};

/**
 * forEach is an alias of Array.each.  This is part of the
 * collection module.
 * @method Array.forEach
 */
A.forEach = A.each;


}, '3.3.0' );
