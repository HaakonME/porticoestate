/*
YUI 3.16.0 (build 76f0e08)
Copyright 2014 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/

if (typeof __coverage__ === 'undefined') { __coverage__ = {}; }
if (!__coverage__['build/template-micro/template-micro.js']) {
   __coverage__['build/template-micro/template-micro.js'] = {"path":"build/template-micro/template-micro.js","s":{"1":0,"2":0,"3":0,"4":0,"5":0,"6":0,"7":0,"8":0,"9":0,"10":0,"11":0,"12":0,"13":0,"14":0,"15":0,"16":0,"17":0,"18":0,"19":0,"20":0,"21":0,"22":0,"23":0,"24":0,"25":0},"b":{"1":[0,0],"2":[0,0],"3":[0,0],"4":[0,0]},"f":{"1":0,"2":0,"3":0,"4":0,"5":0,"6":0,"7":0,"8":0,"9":0,"10":0,"11":0},"fnMap":{"1":{"name":"(anonymous_1)","line":1,"loc":{"start":{"line":1,"column":26},"end":{"line":1,"column":45}}},"2":{"name":"(anonymous_2)","line":117,"loc":{"start":{"line":117,"column":16},"end":{"line":117,"column":41}}},"3":{"name":"(anonymous_3)","line":148,"loc":{"start":{"line":148,"column":36},"end":{"line":148,"column":59}}},"4":{"name":"(anonymous_4)","line":152,"loc":{"start":{"line":152,"column":40},"end":{"line":152,"column":63}}},"5":{"name":"(anonymous_5)","line":156,"loc":{"start":{"line":156,"column":31},"end":{"line":156,"column":54}}},"6":{"name":"(anonymous_6)","line":160,"loc":{"start":{"line":160,"column":39},"end":{"line":160,"column":56}}},"7":{"name":"(anonymous_7)","line":165,"loc":{"start":{"line":165,"column":39},"end":{"line":165,"column":63}}},"8":{"name":"(anonymous_8)","line":201,"loc":{"start":{"line":201,"column":19},"end":{"line":201,"column":44}}},"9":{"name":"(anonymous_9)","line":225,"loc":{"start":{"line":225,"column":15},"end":{"line":225,"column":46}}},"10":{"name":"(anonymous_10)","line":241,"loc":{"start":{"line":241,"column":15},"end":{"line":241,"column":38}}},"11":{"name":"(anonymous_11)","line":242,"loc":{"start":{"line":242,"column":11},"end":{"line":242,"column":27}}}},"statementMap":{"1":{"start":{"line":1,"column":0},"end":{"line":249,"column":39}},"2":{"start":{"line":27,"column":0},"end":{"line":27,"column":42}},"3":{"start":{"line":50,"column":0},"end":{"line":65,"column":2}},"4":{"start":{"line":117,"column":0},"end":{"line":181,"column":2}},"5":{"start":{"line":120,"column":4},"end":{"line":123,"column":15}},"6":{"start":{"line":125,"column":4},"end":{"line":125,"column":46}},"7":{"start":{"line":138,"column":4},"end":{"line":172,"column":25}},"8":{"start":{"line":149,"column":12},"end":{"line":149,"column":90}},"9":{"start":{"line":153,"column":12},"end":{"line":153,"column":94}},"10":{"start":{"line":157,"column":12},"end":{"line":157,"column":89}},"11":{"start":{"line":161,"column":12},"end":{"line":161,"column":54}},"12":{"start":{"line":166,"column":12},"end":{"line":166,"column":47}},"13":{"start":{"line":175,"column":4},"end":{"line":177,"column":5}},"14":{"start":{"line":176,"column":8},"end":{"line":176,"column":61}},"15":{"start":{"line":180,"column":4},"end":{"line":180,"column":64}},"16":{"start":{"line":201,"column":0},"end":{"line":206,"column":2}},"17":{"start":{"line":202,"column":4},"end":{"line":202,"column":30}},"18":{"start":{"line":203,"column":4},"end":{"line":203,"column":30}},"19":{"start":{"line":205,"column":4},"end":{"line":205,"column":39}},"20":{"start":{"line":225,"column":0},"end":{"line":227,"column":2}},"21":{"start":{"line":226,"column":4},"end":{"line":226,"column":45}},"22":{"start":{"line":241,"column":0},"end":{"line":246,"column":2}},"23":{"start":{"line":242,"column":4},"end":{"line":245,"column":6}},"24":{"start":{"line":243,"column":8},"end":{"line":243,"column":28}},"25":{"start":{"line":244,"column":8},"end":{"line":244,"column":62}}},"branchMap":{"1":{"line":161,"type":"binary-expr","locations":[{"start":{"line":161,"column":19},"end":{"line":161,"column":47}},{"start":{"line":161,"column":51},"end":{"line":161,"column":53}}]},"2":{"line":175,"type":"if","locations":[{"start":{"line":175,"column":4},"end":{"line":175,"column":4}},{"start":{"line":175,"column":4},"end":{"line":175,"column":4}}]},"3":{"line":202,"type":"binary-expr","locations":[{"start":{"line":202,"column":4},"end":{"line":202,"column":11}},{"start":{"line":202,"column":16},"end":{"line":202,"column":28}}]},"4":{"line":243,"type":"binary-expr","locations":[{"start":{"line":243,"column":8},"end":{"line":243,"column":12}},{"start":{"line":243,"column":17},"end":{"line":243,"column":26}}]}},"code":["(function () { YUI.add('template-micro', function (Y, NAME) {","","/*jshint expr:true */","","/**","Adds the `Y.Template.Micro` template engine, which provides fast, simple","string-based micro-templating similar to ERB or Underscore templates.","","@module template","@submodule template-micro","@since 3.8.0","**/","","/**","Fast, simple string-based micro-templating engine similar to ERB or Underscore","templates.","","@class Template.Micro","@static","@since 3.8.0","**/","","// This code was heavily inspired by Underscore.js's _.template() method","// (written by Jeremy Ashkenas), which was in turn inspired by John Resig's","// micro-templating implementation.","","var Micro = Y.namespace('Template.Micro');","","/**","Default options for `Y.Template.Micro`.","","@property {Object} options","","    @param {RegExp} [options.code] Regex that matches code blocks like","        `<% ... %>`.","    @param {RegExp} [options.escapedOutput] Regex that matches escaped output","        tags like `<%= ... %>`.","    @param {RegExp} [options.rawOutput] Regex that matches raw output tags like","        `<%== ... %>`.","    @param {RegExp} [options.stringEscape] Regex that matches characters that","        need to be escaped inside single-quoted JavaScript string literals.","    @param {Object} [options.stringReplace] Hash that maps characters matched by","        `stringEscape` to the strings they should be replaced with. If you add","        a character to the `stringEscape` regex, you need to add it here too or","        it will be replaced with an empty string.","","@static","@since 3.8.0","**/","Micro.options = {","    code         : /<%([\\s\\S]+?)%>/g,","    escapedOutput: /<%=([\\s\\S]+?)%>/g,","    rawOutput    : /<%==([\\s\\S]+?)%>/g,","    stringEscape : /\\\\|'|\\r|\\n|\\t|\\u2028|\\u2029/g,","","    stringReplace: {","        '\\\\'    : '\\\\\\\\',","        \"'\"     : \"\\\\'\",","        '\\r'    : '\\\\r',","        '\\n'    : '\\\\n',","        '\\t'    : '\\\\t',","        '\\u2028': '\\\\u2028',","        '\\u2029': '\\\\u2029'","    }","};","","/**","Compiles a template string into a JavaScript function. Pass a data object to the","function to render the template using the given data and get back a rendered","string.","","Within a template, use `<%= ... %>` to output the value of an expression (where","`...` is the JavaScript expression or data variable to evaluate). The output","will be HTML-escaped by default. To output a raw value without escaping, use","`<%== ... %>`, but be careful not to do this with untrusted user input.","","To execute arbitrary JavaScript code within the template without rendering its","output, use `<% ... %>`, where `...` is the code to be executed. This allows the","use of if/else blocks, loops, function calls, etc., although it's recommended","that you avoid embedding anything beyond basic flow control logic in your","templates.","","Properties of the data object passed to a template function are made available","on a `data` variable within the scope of the template. So, if you pass in","the object `{message: 'hello!'}`, you can print the value of the `message`","property using `<%= data.message %>`.","","@example","","    YUI().use('template-micro', function (Y) {","        var template = '<ul class=\"<%= data.classNames.list %>\">' +","                           '<% Y.Array.each(data.items, function (item) { %>' +","                               '<li><%= item %></li>' +","                           '<% }); %>' +","                       '</ul>';","","        // Compile the template into a function.","        var compiled = Y.Template.Micro.compile(template);","","        // Render the template to HTML, passing in the data to use.","        var html = compiled({","            classNames: {list: 'demo'},","            items     : ['one', 'two', 'three', 'four']","        });","    });","","@method compile","@param {String} text Template text to compile.","@param {Object} [options] Options. If specified, these options will override the","    default options defined in `Y.Template.Micro.options`. See the documentation","    for that property for details on which options are available.","@return {Function} Compiled template function. Execute this function and pass in","    a data object to render the template with the given data.","@static","@since 3.8.0","**/","Micro.compile = function (text, options) {","    /*jshint evil:true */","","    var blocks     = [],","        tokenClose = \"\\uffff\",","        tokenOpen  = \"\\ufffe\",","        source;","","    options = Y.merge(Micro.options, options);","","    // Parse the input text into a string of JavaScript code, with placeholders","    // for code blocks. Text outside of code blocks will be escaped for safe","    // usage within a double-quoted string literal.","    //","    // $b is a blank string, used to avoid creating lots of string objects.","    //","    // $v is a function that returns the supplied value if the value is truthy","    // or the number 0, or returns an empty string if the value is falsy and not","    // 0.","    //","    // $t is the template string.","    source = \"var $b='', $v=function (v){return v || v === 0 ? v : $b;}, $t='\" +","","        // U+FFFE and U+FFFF are guaranteed to represent non-characters, so no","        // valid UTF-8 string should ever contain them. That means we can freely","        // strip them out of the input text (just to be safe) and then use them","        // for our own nefarious purposes as token placeholders!","        //","        // See http://en.wikipedia.org/wiki/Mapping_of_Unicode_characters#Noncharacters","        text.replace(/\\ufffe|\\uffff/g, '')","","        .replace(options.rawOutput, function (match, code) {","            return tokenOpen + (blocks.push(\"'+\\n$v(\" + code + \")+\\n'\") - 1) + tokenClose;","        })","","        .replace(options.escapedOutput, function (match, code) {","            return tokenOpen + (blocks.push(\"'+\\n$e($v(\" + code + \"))+\\n'\") - 1) + tokenClose;","        })","","        .replace(options.code, function (match, code) {","            return tokenOpen + (blocks.push(\"';\\n\" + code + \"\\n$t+='\") - 1) + tokenClose;","        })","","        .replace(options.stringEscape, function (match) {","            return options.stringReplace[match] || '';","        })","","        // Replace the token placeholders with code.","        .replace(/\\ufffe(\\d+)\\uffff/g, function (match, index) {","            return blocks[parseInt(index, 10)];","        })","","        // Remove noop string concatenations that have been left behind.","        .replace(/\\n\\$t\\+='';\\n/g, '\\n') +","","        \"';\\nreturn $t;\";","","    // If compile() was called from precompile(), return precompiled source.","    if (options.precompile) {","        return \"function (Y, $e, data) {\\n\" + source + \"\\n}\";","    }","","    // Otherwise, return an executable function.","    return this.revive(new Function('Y', '$e', 'data', source));","};","","/**","Precompiles the given template text into a string of JavaScript source code that","can be evaluated later in another context (or on another machine) to render the","template.","","A common use case is to precompile templates at build time or on the server,","then evaluate the code on the client to render a template. The client only needs","to revive and render the template, avoiding the work of the compilation step.","","@method precompile","@param {String} text Template text to precompile.","@param {Object} [options] Options. If specified, these options will override the","    default options defined in `Y.Template.Micro.options`. See the documentation","    for that property for details on which options are available.","@return {String} Source code for the precompiled template.","@static","@since 3.8.0","**/","Micro.precompile = function (text, options) {","    options || (options = {});","    options.precompile = true;","","    return this.compile(text, options);","};","","/**","Compiles and renders the given template text in a single step.","","This can be useful for single-use templates, but if you plan to render the same","template multiple times, it's much better to use `compile()` to compile it once,","then simply call the compiled function multiple times to avoid recompiling.","","@method render","@param {String} text Template text to render.","@param {Object} data Data to pass to the template.","@param {Object} [options] Options. If specified, these options will override the","    default options defined in `Y.Template.Micro.options`. See the documentation","    for that property for details on which options are available.","@return {String} Rendered result.","@static","@since 3.8.0","**/","Micro.render = function (text, data, options) {","    return this.compile(text, options)(data);","};","","/**","Revives a precompiled template function into a normal compiled template function","that can be called to render the template. The precompiled function must already","have been evaluated to a function -- you can't pass raw JavaScript code to","`revive()`.","","@method revive","@param {Function} precompiled Precompiled template function.","@return {Function} Revived template function, ready to be rendered.","@static","@since 3.8.0","**/","Micro.revive = function (precompiled) {","    return function (data) {","        data || (data = {});","        return precompiled.call(data, Y, Y.Escape.html, data);","    };","};","","","}, '3.16.0', {\"requires\": [\"escape\"]});","","}());"]};
}
var __cov_KTkvPcKaiXgfHYkTLSzbTA = __coverage__['build/template-micro/template-micro.js'];
__cov_KTkvPcKaiXgfHYkTLSzbTA.s['1']++;YUI.add('template-micro',function(Y,NAME){__cov_KTkvPcKaiXgfHYkTLSzbTA.f['1']++;__cov_KTkvPcKaiXgfHYkTLSzbTA.s['2']++;var Micro=Y.namespace('Template.Micro');__cov_KTkvPcKaiXgfHYkTLSzbTA.s['3']++;Micro.options={code:/<%([\s\S]+?)%>/g,escapedOutput:/<%=([\s\S]+?)%>/g,rawOutput:/<%==([\s\S]+?)%>/g,stringEscape:/\\|'|\r|\n|\t|\u2028|\u2029/g,stringReplace:{'\\':'\\\\','\'':'\\\'','\r':'\\r','\n':'\\n','\t':'\\t','\u2028':'\\u2028','\u2029':'\\u2029'}};__cov_KTkvPcKaiXgfHYkTLSzbTA.s['4']++;Micro.compile=function(text,options){__cov_KTkvPcKaiXgfHYkTLSzbTA.f['2']++;__cov_KTkvPcKaiXgfHYkTLSzbTA.s['5']++;var blocks=[],tokenClose='\uffff',tokenOpen='\ufffe',source;__cov_KTkvPcKaiXgfHYkTLSzbTA.s['6']++;options=Y.merge(Micro.options,options);__cov_KTkvPcKaiXgfHYkTLSzbTA.s['7']++;source='var $b=\'\', $v=function (v){return v || v === 0 ? v : $b;}, $t=\''+text.replace(/\ufffe|\uffff/g,'').replace(options.rawOutput,function(match,code){__cov_KTkvPcKaiXgfHYkTLSzbTA.f['3']++;__cov_KTkvPcKaiXgfHYkTLSzbTA.s['8']++;return tokenOpen+(blocks.push('\'+\n$v('+code+')+\n\'')-1)+tokenClose;}).replace(options.escapedOutput,function(match,code){__cov_KTkvPcKaiXgfHYkTLSzbTA.f['4']++;__cov_KTkvPcKaiXgfHYkTLSzbTA.s['9']++;return tokenOpen+(blocks.push('\'+\n$e($v('+code+'))+\n\'')-1)+tokenClose;}).replace(options.code,function(match,code){__cov_KTkvPcKaiXgfHYkTLSzbTA.f['5']++;__cov_KTkvPcKaiXgfHYkTLSzbTA.s['10']++;return tokenOpen+(blocks.push('\';\n'+code+'\n$t+=\'')-1)+tokenClose;}).replace(options.stringEscape,function(match){__cov_KTkvPcKaiXgfHYkTLSzbTA.f['6']++;__cov_KTkvPcKaiXgfHYkTLSzbTA.s['11']++;return(__cov_KTkvPcKaiXgfHYkTLSzbTA.b['1'][0]++,options.stringReplace[match])||(__cov_KTkvPcKaiXgfHYkTLSzbTA.b['1'][1]++,'');}).replace(/\ufffe(\d+)\uffff/g,function(match,index){__cov_KTkvPcKaiXgfHYkTLSzbTA.f['7']++;__cov_KTkvPcKaiXgfHYkTLSzbTA.s['12']++;return blocks[parseInt(index,10)];}).replace(/\n\$t\+='';\n/g,'\n')+'\';\nreturn $t;';__cov_KTkvPcKaiXgfHYkTLSzbTA.s['13']++;if(options.precompile){__cov_KTkvPcKaiXgfHYkTLSzbTA.b['2'][0]++;__cov_KTkvPcKaiXgfHYkTLSzbTA.s['14']++;return'function (Y, $e, data) {\n'+source+'\n}';}else{__cov_KTkvPcKaiXgfHYkTLSzbTA.b['2'][1]++;}__cov_KTkvPcKaiXgfHYkTLSzbTA.s['15']++;return this.revive(new Function('Y','$e','data',source));};__cov_KTkvPcKaiXgfHYkTLSzbTA.s['16']++;Micro.precompile=function(text,options){__cov_KTkvPcKaiXgfHYkTLSzbTA.f['8']++;__cov_KTkvPcKaiXgfHYkTLSzbTA.s['17']++;(__cov_KTkvPcKaiXgfHYkTLSzbTA.b['3'][0]++,options)||(__cov_KTkvPcKaiXgfHYkTLSzbTA.b['3'][1]++,options={});__cov_KTkvPcKaiXgfHYkTLSzbTA.s['18']++;options.precompile=true;__cov_KTkvPcKaiXgfHYkTLSzbTA.s['19']++;return this.compile(text,options);};__cov_KTkvPcKaiXgfHYkTLSzbTA.s['20']++;Micro.render=function(text,data,options){__cov_KTkvPcKaiXgfHYkTLSzbTA.f['9']++;__cov_KTkvPcKaiXgfHYkTLSzbTA.s['21']++;return this.compile(text,options)(data);};__cov_KTkvPcKaiXgfHYkTLSzbTA.s['22']++;Micro.revive=function(precompiled){__cov_KTkvPcKaiXgfHYkTLSzbTA.f['10']++;__cov_KTkvPcKaiXgfHYkTLSzbTA.s['23']++;return function(data){__cov_KTkvPcKaiXgfHYkTLSzbTA.f['11']++;__cov_KTkvPcKaiXgfHYkTLSzbTA.s['24']++;(__cov_KTkvPcKaiXgfHYkTLSzbTA.b['4'][0]++,data)||(__cov_KTkvPcKaiXgfHYkTLSzbTA.b['4'][1]++,data={});__cov_KTkvPcKaiXgfHYkTLSzbTA.s['25']++;return precompiled.call(data,Y,Y.Escape.html,data);};};},'3.16.0',{'requires':['escape']});
