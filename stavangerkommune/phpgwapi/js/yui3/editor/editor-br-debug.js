/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.3.0
build: 3167
*/
YUI.add('editor-br', function(Y) {



    /**
     * Plugin for Editor to normalize BR's.
     * @module editor
     * @submodule editor-br
     */     
    /**
     * Plugin for Editor to normalize BR's.
     * @class Plugin.EditorBR
     * @extends Base
     * @constructor
     */


    var EditorBR = function() {
        EditorBR.superclass.constructor.apply(this, arguments);
    }, HOST = 'host', LI = 'li';


    Y.extend(EditorBR, Y.Base, {
        /**
        * Frame keyDown handler that normalizes BR's when pressing ENTER.
        * @private
        * @method _onKeyDown
        */
        _onKeyDown: function(e) {
            if (e.stopped) {
                e.halt();
                return;
            }
            if (e.keyCode == 13) {
                var host = this.get(HOST), inst = host.getInstance(),
                    sel = new inst.Selection(),
                    last = '';

                if (sel) {
                    if (Y.UA.ie) {
                        if (!sel.anchorNode || (!sel.anchorNode.test(LI) && !sel.anchorNode.ancestor(LI))) {
                            sel._selection.pasteHTML('<br>');
                            sel._selection.collapse(false);
                            sel._selection.select();
                            e.halt();
                        }
                    }
                    if (Y.UA.webkit) {
                        if (!sel.anchorNode.test(LI) && !sel.anchorNode.ancestor(LI)) {
                            host.frame._execCommand('insertlinebreak', null);
                            e.halt();
                        }
                    }
                }
            }
        },
        /**
        * Adds listeners for keydown in IE and Webkit. Also fires insertbeonreturn for supporting browsers.
        * @private
        * @method _afterEditorReady
        */
        _afterEditorReady: function() {
            var inst = this.get(HOST).getInstance();
            try {
                inst.config.doc.execCommand('insertbronreturn', null, true);
            } catch (bre) {};

            if (Y.UA.ie || Y.UA.webkit) {
                inst.on('keydown', Y.bind(this._onKeyDown, this), inst.config.doc);
            }
        },
        /**
        * Adds a nodeChange listener only for FF, in the event of a backspace or delete, it creates an empy textNode
        * inserts it into the DOM after the e.changedNode, then removes it. Causing FF to redraw the content.
        * @private
        * @method _onNodeChange
        * @param {Event} e The nodeChange event.
        */
        _onNodeChange: function(e) {
            switch (e.changedType) {
                case 'backspace-up':
                case 'backspace-down':
                case 'delete-up':
                    /**
                    * This forced FF to redraw the content on backspace.
                    * On some occasions FF will leave a cursor residue after content has been deleted.
                    * Dropping in the empty textnode and then removing it causes FF to redraw and
                    * remove the "ghost cursors"
                    */
                    var inst = this.get(HOST).getInstance();
                    var d = e.changedNode;
                    var t = inst.config.doc.createTextNode(' ');
                    d.appendChild(t);
                    d.removeChild(t);
                    break;
            }
        },
        initializer: function() {
            var host = this.get(HOST);
            if (host.editorPara) {
                Y.error('Can not plug EditorBR and EditorPara at the same time.');
                return;
            }
            host.after('ready', Y.bind(this._afterEditorReady, this));
            if (Y.UA.gecko) {
                host.on('nodeChange', Y.bind(this._onNodeChange, this));
            }
        }
    }, {
        /**
        * editorBR
        * @static
        * @property NAME
        */
        NAME: 'editorBR',
        /**
        * editorBR
        * @static
        * @property NS
        */
        NS: 'editorBR',
        ATTRS: {
            host: {
                value: false
            }
        }
    });
    
    Y.namespace('Plugin');
    
    Y.Plugin.EditorBR = EditorBR;



}, '3.3.0' ,{requires:['node'], skinnable:false});
