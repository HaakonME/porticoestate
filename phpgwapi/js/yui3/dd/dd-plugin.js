/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.3.0
build: 3167
*/
YUI.add('dd-plugin', function(Y) {


       /**
        * Simple Drag plugin that can be attached to a Node via the plug method.
        * @module dd
        * @submodule dd-plugin
        */
       /**
        * Simple Drag plugin that can be attached to a Node via the plug method.
        * @class Drag
        * @extends DD.Drag
        * @constructor
        * @namespace Plugin
        */


        var Drag = function(config) {
            config.node = ((Y.Widget && config.host instanceof Y.Widget) ? config.host.get('boundingBox') : config.host);
            Drag.superclass.constructor.call(this, config);
        };
        
        /**
        * @property NAME
        * @description dd-plugin
        * @type {String}
        */
        Drag.NAME = "dd-plugin";

        /**
        * @property NS
        * @description The Drag instance will be placed on the Node instance under the dd namespace. It can be accessed via Node.dd;
        * @type {String}
        */
        Drag.NS = "dd";


        Y.extend(Drag, Y.DD.Drag);
        Y.namespace('Plugin');
        Y.Plugin.Drag = Drag;





}, '3.3.0' ,{requires:['dd-drag'], skinnable:false, optional:['dd-constrain', 'dd-proxy']});
