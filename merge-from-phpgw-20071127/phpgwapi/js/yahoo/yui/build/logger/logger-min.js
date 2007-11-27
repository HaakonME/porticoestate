/*
Copyright (c) 2006, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 0.11.0
*/



YAHOO.widget.Logger={loggerEnabled:true,_firebugEnabled:true,categories:["info","warn","error","time","window"],sources:["global"],_stack:[],_startTime:new Date().getTime(),_lastTime:null};YAHOO.widget.Logger.categoryCreateEvent=new YAHOO.util.CustomEvent("categoryCreate",this,true);YAHOO.widget.Logger.sourceCreateEvent=new YAHOO.util.CustomEvent("sourceCreate",this,true);YAHOO.widget.Logger.newLogEvent=new YAHOO.util.CustomEvent("newLog",this,true);YAHOO.widget.Logger.logResetEvent=new YAHOO.util.CustomEvent("logReset",this,true);YAHOO.widget.Logger.log=function(sMsg,sCategory,sSource){if(this.loggerEnabled){if(!sCategory){sCategory="info";}

else if(this._isNewCategory(sCategory)){this._createNewCategory(sCategory);}

var sClass="global";var sDetail=null;if(sSource){var spaceIndex=sSource.indexOf(" ");if(spaceIndex>0){sClass=sSource.substring(0,spaceIndex);sDetail=sSource.substring(spaceIndex,sSource.length);}

else{sClass=sSource;}

if(this._isNewSource(sClass)){this._createNewSource(sClass);}}

var timestamp=new Date();var logEntry={time:timestamp,category:sCategory,source:sClass,sourceDetail:sDetail,msg:sMsg};this._stack.push(logEntry);this.newLogEvent.fire(logEntry);if(this._firebugEnabled){this._printToFirebug(logEntry);}

return true;}

else{return false;}};YAHOO.widget.Logger.reset=function(){this._stack=[];this._startTime=new Date().getTime();this.loggerEnabled=true;this.log(null,"Logger reset");this.logResetEvent.fire();};YAHOO.widget.Logger.getStack=function(){return this._stack;};YAHOO.widget.Logger.getStartTime=function(){return this._startTime;};YAHOO.widget.Logger.disableFirebug=function(){YAHOO.log("YAHOO.Logger output to Firebug has been disabled.");this._firebugEnabled=false;};YAHOO.widget.Logger.enableFirebug=function(){this._firebugEnabled=true;YAHOO.log("YAHOO.Logger output to Firebug has been enabled.");};YAHOO.widget.Logger._createNewCategory=function(category){this.categories.push(category);this.categoryCreateEvent.fire(category);};YAHOO.widget.Logger._isNewCategory=function(category){for(var i=0;i<this.categories.length;i++){if(category==this.categories[i]){return false;}}

return true;};YAHOO.widget.Logger._createNewSource=function(source){this.sources.push(source);this.sourceCreateEvent.fire(source);};YAHOO.widget.Logger._isNewSource=function(source){if(source){for(var i=0;i<this.sources.length;i++){if(source==this.sources[i]){return false;}}

return true;}};YAHOO.widget.Logger._printToFirebug=function(entry){if(window.console&&console.log){var category=entry.category;var label=entry.category.substring(0,4).toUpperCase();var time=entry.time;if(time.toLocaleTimeString){var localTime=time.toLocaleTimeString();}

else{localTime=time.toString();}

var msecs=time.getTime();var elapsedTime=(YAHOO.widget.Logger._lastTime)?(msecs-YAHOO.widget.Logger._lastTime):0;YAHOO.widget.Logger._lastTime=msecs;var output=localTime+" ("+

elapsedTime+"ms): "+

entry.source+": "+

entry.msg;console.log(output);}};YAHOO.widget.Logger._onWindowError=function(msg,url,line){try{YAHOO.widget.Logger.log(msg+' ('+url+', line '+line+')',"window");if(YAHOO.widget.Logger._origOnWindowError){YAHOO.widget.Logger._origOnWindowError();}}

catch(e){return false;}};if(window.onerror){YAHOO.widget.Logger._origOnWindowError=window.onerror;}

window.onerror=YAHOO.widget.Logger._onWindowError;YAHOO.widget.Logger.log("Logger initialized");YAHOO.widget.LogWriter=function(sSource){if(!sSource){YAHOO.log("Could not instantiate LogWriter due to invalid source.","error","LogWriter");return;}

this._source=sSource;};YAHOO.widget.LogWriter.prototype.toString=function(){return"LogWriter "+this._sSource;};YAHOO.widget.LogWriter.prototype.log=function(sMsg,sCategory){YAHOO.widget.Logger.log(sMsg,sCategory,this._source);};YAHOO.widget.LogWriter.prototype.getSource=function(){return this._sSource;};YAHOO.widget.LogWriter.prototype.setSource=function(sSource){if(!sSource){YAHOO.log("Could not set source due to invalid source.","error",this.toString());return;}

else{this._sSource=sSource;}};YAHOO.widget.LogWriter.prototype._source=null;YAHOO.widget.LogReader=function(containerEl,oConfig){var oSelf=this;if(typeof oConfig=="object"){for(var param in oConfig){this[param]=oConfig[param];}}

if(containerEl){if(typeof containerEl=="string"){this._containerEl=document.getElementById(containerEl);}

else if(containerEl.tagName){this._containerEl=containerEl;}

this._containerEl.className="yui-log";}

if(!this._containerEl){if(YAHOO.widget.LogReader._defaultContainerEl){this._containerEl=YAHOO.widget.LogReader._defaultContainerEl;}

else{this._containerEl=document.body.appendChild(document.createElement("div"));this._containerEl.id="yui-log";this._containerEl.className="yui-log";YAHOO.widget.LogReader._defaultContainerEl=this._containerEl;}

var containerStyle=this._containerEl.style;if(this.width){containerStyle.width=this.width;}

if(this.left){containerStyle.left=this.left;}

if(this.right){containerStyle.right=this.right;}

if(this.bottom){containerStyle.bottom=this.bottom;}

if(this.top){containerStyle.top=this.top;}

if(this.fontSize){containerStyle.fontSize=this.fontSize;}}

if(this._containerEl){if(!this._hdEl){this._hdEl=this._containerEl.appendChild(document.createElement("div"));this._hdEl.id="yui-log-hd"+YAHOO.widget.LogReader._index;this._hdEl.className="yui-log-hd";this._collapseEl=this._hdEl.appendChild(document.createElement("div"));this._collapseEl.className="yui-log-btns";this._collapseBtn=document.createElement("input");this._collapseBtn.type="button";this._collapseBtn.style.fontSize=YAHOO.util.Dom.getStyle(this._containerEl,"fontSize");this._collapseBtn.className="yui-log-button";this._collapseBtn.value="Collapse";this._collapseBtn=this._collapseEl.appendChild(this._collapseBtn);YAHOO.util.Event.addListener(oSelf._collapseBtn,'click',oSelf._onClickCollapseBtn,oSelf);this._title=this._hdEl.appendChild(document.createElement("h4"));this._title.innerHTML="Logger Console";if(YAHOO.util.DD&&(YAHOO.widget.LogReader._defaultContainerEl==this._containerEl)){var ylog_dd=new YAHOO.util.DD(this._containerEl.id);ylog_dd.setHandleElId(this._hdEl.id);this._hdEl.style.cursor="move";}}

if(!this._consoleEl){this._consoleEl=this._containerEl.appendChild(document.createElement("div"));this._consoleEl.className="yui-log-bd";if(this.height){this._consoleEl.style.height=this.height;}}

if(!this._ftEl&&this.footerEnabled){this._ftEl=this._containerEl.appendChild(document.createElement("div"));this._ftEl.className="yui-log-ft";this._btnsEl=this._ftEl.appendChild(document.createElement("div"));this._btnsEl.className="yui-log-btns";this._pauseBtn=document.createElement("input");this._pauseBtn.type="button";this._pauseBtn.style.fontSize=YAHOO.util.Dom.getStyle(this._containerEl,"fontSize");this._pauseBtn.className="yui-log-button";this._pauseBtn.value="Pause";this._pauseBtn=this._btnsEl.appendChild(this._pauseBtn);YAHOO.util.Event.addListener(oSelf._pauseBtn,'click',oSelf._onClickPauseBtn,oSelf);this._clearBtn=document.createElement("input");this._clearBtn.type="button";this._clearBtn.style.fontSize=YAHOO.util.Dom.getStyle(this._containerEl,"fontSize");this._clearBtn.className="yui-log-button";this._clearBtn.value="Clear";this._clearBtn=this._btnsEl.appendChild(this._clearBtn);YAHOO.util.Event.addListener(oSelf._clearBtn,'click',oSelf._onClickClearBtn,oSelf);this._categoryFiltersEl=this._ftEl.appendChild(document.createElement("div"));this._categoryFiltersEl.className="yui-log-categoryfilters";this._sourceFiltersEl=this._ftEl.appendChild(document.createElement("div"));this._sourceFiltersEl.className="yui-log-sourcefilters";}}

if(!this._buffer){this._buffer=[];}

YAHOO.widget.Logger.newLogEvent.subscribe(this._onNewLog,this);this._lastTime=YAHOO.widget.Logger.getStartTime();this._categoryFilters=[];var catsLen=YAHOO.widget.Logger.categories.length;if(this._categoryFiltersEl){for(var i=0;i<catsLen;i++){this._createCategoryCheckbox(YAHOO.widget.Logger.categories[i]);}}

this._sourceFilters=[];var sourcesLen=YAHOO.widget.Logger.sources.length;if(this._sourceFiltersEl){for(var j=0;j<sourcesLen;j++){this._createSourceCheckbox(YAHOO.widget.Logger.sources[j]);}}

YAHOO.widget.Logger.categoryCreateEvent.subscribe(this._onCategoryCreate,this);YAHOO.widget.Logger.sourceCreateEvent.subscribe(this._onSourceCreate,this);YAHOO.widget.LogReader._index++;this._filterLogs();};YAHOO.widget.LogReader.prototype.logReaderEnabled=true;YAHOO.widget.LogReader.prototype.width=null;YAHOO.widget.LogReader.prototype.height=null;YAHOO.widget.LogReader.prototype.top=null;YAHOO.widget.LogReader.prototype.left=null;YAHOO.widget.LogReader.prototype.right=null;YAHOO.widget.LogReader.prototype.bottom=null;YAHOO.widget.LogReader.prototype.fontSize=null;YAHOO.widget.LogReader.prototype.footerEnabled=true;YAHOO.widget.LogReader.prototype.verboseOutput=true;YAHOO.widget.LogReader.prototype.newestOnTop=true;YAHOO.widget.LogReader.prototype.pause=function(){this._timeout=null;this.logReaderEnabled=false;};YAHOO.widget.LogReader.prototype.resume=function(){this.logReaderEnabled=true;this._printBuffer();};YAHOO.widget.LogReader.prototype.hide=function(){this._containerEl.style.display="none";};YAHOO.widget.LogReader.prototype.show=function(){this._containerEl.style.display="block";};YAHOO.widget.LogReader.prototype.setTitle=function(sTitle){var regEx=/>/g;sTitle=sTitle.replace(regEx,"&gt;");regEx=/</g;sTitle=sTitle.replace(regEx,"&lt;");this._title.innerHTML=(sTitle);};YAHOO.widget.LogReader._index=0;YAHOO.widget.LogReader._defaultContainerEl=null;YAHOO.widget.LogReader.prototype._buffer=null;YAHOO.widget.LogReader.prototype._lastTime=null;YAHOO.widget.LogReader.prototype._timeout=null;YAHOO.widget.LogReader.prototype._categoryFilters=null;YAHOO.widget.LogReader.prototype._sourceFilters=null;YAHOO.widget.LogReader.prototype._containerEl=null;YAHOO.widget.LogReader.prototype._hdEl=null;YAHOO.widget.LogReader.prototype._collapseEl=null;YAHOO.widget.LogReader.prototype._collapseBtn=null;YAHOO.widget.LogReader.prototype._title=null;YAHOO.widget.LogReader.prototype._consoleEl=null;YAHOO.widget.LogReader.prototype._ftEl=null;YAHOO.widget.LogReader.prototype._btnsEl=null;YAHOO.widget.LogReader.prototype._categoryFiltersEl=null;YAHOO.widget.LogReader.prototype._sourceFiltersEl=null;YAHOO.widget.LogReader.prototype._pauseBtn=null;YAHOO.widget.LogReader.prototype._clearBtn=null;YAHOO.widget.LogReader.prototype._createCategoryCheckbox=function(category){var oSelf=this;if(this._ftEl){var parentEl=this._categoryFiltersEl;var filters=this._categoryFilters;var filterEl=parentEl.appendChild(document.createElement("span"));filterEl.className="yui-log-filtergrp";var categoryChk=document.createElement("input");categoryChk.id="yui-log-filter-"+category+YAHOO.widget.LogReader._index;categoryChk.className="yui-log-filter-"+category;categoryChk.type="checkbox";categoryChk.category=category;categoryChk=filterEl.appendChild(categoryChk);categoryChk.checked=true;filters.push(category);YAHOO.util.Event.addListener(categoryChk,'click',oSelf._onCheckCategory,oSelf);var categoryChkLbl=filterEl.appendChild(document.createElement("label"));categoryChkLbl.htmlFor=categoryChk.id;categoryChkLbl.className=category;categoryChkLbl.innerHTML=category;}};YAHOO.widget.LogReader.prototype._createSourceCheckbox=function(source){var oSelf=this;if(this._ftEl){var parentEl=this._sourceFiltersEl;var filters=this._sourceFilters;var filterEl=parentEl.appendChild(document.createElement("span"));filterEl.className="yui-log-filtergrp";var sourceChk=document.createElement("input");sourceChk.id="yui-log-filter"+source+YAHOO.widget.LogReader._index;sourceChk.className="yui-log-filter"+source;sourceChk.type="checkbox";sourceChk.source=source;sourceChk=filterEl.appendChild(sourceChk);sourceChk.checked=true;filters.push(source);YAHOO.util.Event.addListener(sourceChk,'click',oSelf._onCheckSource,oSelf);var sourceChkLbl=filterEl.appendChild(document.createElement("label"));sourceChkLbl.htmlFor=sourceChk.id;sourceChkLbl.className=source;sourceChkLbl.innerHTML=source;}};YAHOO.widget.LogReader.prototype._filterLogs=function(){if(this._consoleEl!==null){this._clearConsole();this._printToConsole(YAHOO.widget.Logger.getStack());}};YAHOO.widget.LogReader.prototype._clearConsole=function(){this._timeout=null;this._buffer=[];this._lastTime=YAHOO.widget.Logger.getStartTime();var consoleEl=this._consoleEl;while(consoleEl.hasChildNodes()){consoleEl.removeChild(consoleEl.firstChild);}};YAHOO.widget.LogReader.prototype._printBuffer=function(){this._timeout=null;if(this._consoleEl!==null){var entries=[];for(var i=0;i<this._buffer.length;i++){entries[i]=this._buffer[i];}

this._buffer=[];this._printToConsole(entries);if(!this.newestOnTop){this._consoleEl.scrollTop=this._consoleEl.scrollHeight;}}};YAHOO.widget.LogReader.prototype._printToConsole=function(aEntries){var entriesLen=aEntries.length;var sourceFiltersLen=this._sourceFilters.length;var categoryFiltersLen=this._categoryFilters.length;for(var i=0;i<entriesLen;i++){var entry=aEntries[i];var category=entry.category;var source=entry.source;var sourceDetail=entry.sourceDetail;var okToPrint=false;var okToFilterCats=false;for(var j=0;j<sourceFiltersLen;j++){if(source==this._sourceFilters[j]){okToFilterCats=true;break;}}

if(okToFilterCats){for(var k=0;k<categoryFiltersLen;k++){if(category==this._categoryFilters[k]){okToPrint=true;break;}}}

if(okToPrint){var label=entry.category.substring(0,4).toUpperCase();var time=entry.time;if(time.toLocaleTimeString){var localTime=time.toLocaleTimeString();}

else{localTime=time.toString();}

var msecs=time.getTime();var startTime=YAHOO.widget.Logger.getStartTime();var totalTime=msecs-startTime;var elapsedTime=msecs-this._lastTime;this._lastTime=msecs;var verboseOutput=(this.verboseOutput)?"<br>":"";var sourceAndDetail=(sourceDetail)?source+" "+sourceDetail:source;var output="<span class='"+category+"'>"+label+"</span> "+

totalTime+"ms (+"+

elapsedTime+") "+localTime+": "+

sourceAndDetail+": "+

verboseOutput+

entry.msg;var oNewElement=(this.newestOnTop)?this._consoleEl.insertBefore(document.createElement("p"),this._consoleEl.firstChild):this._consoleEl.appendChild(document.createElement("p"));oNewElement.innerHTML=output;}}};YAHOO.widget.LogReader.prototype._onCategoryCreate=function(type,args,oSelf){var category=args[0];if(oSelf._ftEl){oSelf._createCategoryCheckbox(category);}};YAHOO.widget.LogReader.prototype._onSourceCreate=function(type,args,oSelf){var source=args[0];if(oSelf._ftEl){oSelf._createSourceCheckbox(source);}};YAHOO.widget.LogReader.prototype._onCheckCategory=function(v,oSelf){var newFilter=this.category;var filtersArray=oSelf._categoryFilters;if(!this.checked){for(var i=0;i<filtersArray.length;i++){if(newFilter==filtersArray[i]){filtersArray.splice(i,1);break;}}}

else{filtersArray.push(newFilter);}

oSelf._filterLogs();};YAHOO.widget.LogReader.prototype._onCheckSource=function(v,oSelf){var newFilter=this.source;var filtersArray=oSelf._sourceFilters;if(!this.checked){for(var i=0;i<filtersArray.length;i++){if(newFilter==filtersArray[i]){filtersArray.splice(i,1);break;}}}

else{filtersArray.push(newFilter);}

oSelf._filterLogs();};YAHOO.widget.LogReader.prototype._onClickCollapseBtn=function(v,oSelf){var btn=oSelf._collapseBtn;if(btn.value=="Expand"){oSelf._consoleEl.style.display="block";if(oSelf._ftEl){oSelf._ftEl.style.display="block";}

btn.value="Collapse";}

else{oSelf._consoleEl.style.display="none";if(oSelf._ftEl){oSelf._ftEl.style.display="none";}

btn.value="Expand";}};YAHOO.widget.LogReader.prototype._onClickPauseBtn=function(v,oSelf){var btn=oSelf._pauseBtn;if(btn.value=="Resume"){oSelf.resume();btn.value="Pause";}

else{oSelf.pause();btn.value="Resume";}};YAHOO.widget.LogReader.prototype._onClickClearBtn=function(v,oSelf){oSelf._clearConsole();};YAHOO.widget.LogReader.prototype._onNewLog=function(type,args,oSelf){var logEntry=args[0];oSelf._buffer.push(logEntry);if(oSelf.logReaderEnabled===true&&oSelf._timeout===null){oSelf._timeout=setTimeout(function(){oSelf._printBuffer();},100);}};