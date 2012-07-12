/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.3.0
build: 3167
*/
YUI.add('autocomplete-list-keys', function(Y) {

/**
 * Mixes keyboard support into AutoCompleteList. By default, this module is not
 * loaded for iOS and Android devices.
 *
 * @module autocomplete
 * @submodule autocomplete-list-keys
 */

 // keyCode constants.
var KEY_DOWN  = 40,
    KEY_ENTER = 13,
    KEY_ESC   = 27,
    KEY_TAB   = 9,
    KEY_UP    = 38;

function ListKeys() {
    Y.before(this._unbindKeys, this, 'destructor');
    Y.before(this._bindKeys, this, 'bindUI');

    this._initKeys();
}

ListKeys.prototype = {
    // -- Lifecycle Methods ----------------------------------------------------

    /**
     * Initializes keyboard command mappings.
     *
     * @method _initKeys
     * @protected
     * @for AutoCompleteList
     */
    _initKeys: function () {
        var keys        = {},
            keysVisible = {};

        this._keyEvents = [];

        // Register keyboard command handlers. _keys contains handlers that will
        // always be called; _keysVisible contains handlers that will only be
        // called when the list is visible.
        keys[KEY_DOWN] = this._keyDown;

        keysVisible[KEY_ENTER] = this._keyEnter;
        keysVisible[KEY_ESC]   = this._keyEsc;
        keysVisible[KEY_TAB]   = this._keyTab;
        keysVisible[KEY_UP]    = this._keyUp;

        this._keys        = keys;
        this._keysVisible = keysVisible;
    },

    /**
     * Binds keyboard events.
     *
     * @method _bindKeys
     * @protected
     */
    _bindKeys: function () {
        this._keyEvents.push(this._inputNode.on(
                Y.UA.gecko ? 'keypress' : 'keydown', this._onInputKey, this));
    },

    /**
     * Unbinds keyboard events.
     *
     * @method _unbindKeys
     * @protected
     */
    _unbindKeys: function () {
        while (this._keyEvents.length) {
            this._keyEvents.pop().detach();
        }
    },

    // -- Protected Methods ----------------------------------------------------

    /**
     * Called when the down arrow key is pressed.
     *
     * @method _keyDown
     * @protected
     */
    _keyDown: function () {
        if (this.get('visible')) {
            this._activateNextItem();
        } else {
            this.show();
        }
    },

    /**
     * Called when the enter key is pressed.
     *
     * @method _keyEnter
     * @protected
     */
    _keyEnter: function () {
        var item = this.get('activeItem');

        if (item) {
            this.selectItem(item);
        } else {
            // Don't prevent form submission when there's no active item.
            return false;
        }
    },

    /**
     * Called when the escape key is pressed.
     *
     * @method _keyEsc
     * @protected
     */
    _keyEsc: function () {
        this.hide();
    },

    /**
     * Called when the tab key is pressed.
     *
     * @method _keyTab
     * @protected
     */
    _keyTab: function () {
        var item;

        if (this.get('tabSelect')) {
            item = this.get('activeItem');

            if (item) {
                this.selectItem(item);
                return true;
            }
        }

        return false;
    },

    /**
     * Called when the up arrow key is pressed.
     *
     * @method _keyUp
     * @protected
     */
    _keyUp: function () {
        this._activatePrevItem();
    },

    // -- Protected Event Handlers ---------------------------------------------

    /**
     * Handles <code>inputNode</code> key events.
     *
     * @method _onInputKey
     * @param {EventTarget} e
     * @protected
     */
    _onInputKey: function (e) {
        var handler,
            keyCode = e.keyCode;

        this._lastInputKey = keyCode;

        if (this.get('results').length) {
            handler = this._keys[keyCode];

            if (!handler && this.get('visible')) {
                handler = this._keysVisible[keyCode];
            }

            if (handler) {
                // A handler may return false to indicate that it doesn't wish
                // to prevent the default key behavior.
                if (handler.call(this, e) !== false) {
                    e.preventDefault();
                }
            }
        }
    }
};

Y.Base.mix(Y.AutoCompleteList, [ListKeys]);


}, '3.3.0' ,{requires:['autocomplete-list', 'base-build']});
