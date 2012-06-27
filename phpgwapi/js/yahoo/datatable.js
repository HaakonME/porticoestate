YAHOO.portico.setupToolbar = function() {
	YAHOO.portico.renderUiFormItems('toolbar');
};

YAHOO.portico.setupListActions = function() {
	YAHOO.portico.renderUiFormItems('list_actions');
};

YAHOO.portico.renderUiFormItems = function(container) {
	var items = YAHOO.util.Dom.getElementsBy(function(){return true;}, 'input', container);
   for(var i=0; i < items.length; i++) {
       var type = items[i].getAttribute('type');
       if(type == 'link') {
           new YAHOO.widget.Button(items[i], 
                                   {type: 'link', 
                                    href: items[i].getAttribute('href')});
       }
       else if(type == 'submit') {
           new YAHOO.widget.Button(items[i], {type: 'submit'});
       }
   }
};

YAHOO.portico.setupPaginator = function() {
	var paginatorConfig = {
        rowsPerPage: 10,
        alwaysVisible: false,
        template: "{PreviousPageLink} <strong>{CurrentPageReport}</strong> {NextPageLink}",
        pageReportTemplate: "Showing items {startRecord} - {endRecord} of {totalRecords}",
        containers: ['paginator']
    };
	
	YAHOO.portico.lang('setupPaginator', paginatorConfig);
	var pag = new YAHOO.widget.Paginator(paginatorConfig);
    pag.render();
	return pag;
};

YAHOO.portico.preSerializeQueryFormListeners = new Array();

	YAHOO.portico.addPreSerializeQueryFormListener = function(func) {
	YAHOO.portico.preSerializeQueryFormListeners.push(func);
}

YAHOO.portico.preSerializeQueryForm = function(form) {
	for (var key in YAHOO.portico.preSerializeQueryFormListeners) {
		YAHOO.portico.preSerializeQueryFormListeners[key](form);
	}
}

YAHOO.portico.initializeDataTable = function()
{
	YAHOO.portico.setupToolbar();
	YAHOO.portico.setupListActions();
	YAHOO.portico.setupDatasource();
	var pag = YAHOO.portico.setupPaginator();

    var fields = [];
    for(var i=0; i < YAHOO.portico.columnDefs.length; i++) {
        fields.push(YAHOO.portico.columnDefs[i].key);
    }
    var baseUrl = YAHOO.portico.dataSourceUrl;
    if(baseUrl[baseUrl.length - 1] != '&') {
        baseUrl += '&';
    }

    if (YAHOO.portico.initialSortedBy) {
//      baseUrl += 'sort=' + YAHOO.portico.initialSortedBy.key + '&dir=' + YAHOO.portico.initialSortedBy.dir;
    } else {
//      baseUrl += 'sort=' + fields[0];
    }
	
	  baseUrl += '&results=' + pag.getRowsPerPage() + '&';
    var myDataSource = new YAHOO.util.DataSource(baseUrl);

    myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
    myDataSource.connXhrMode = "queueRequests";
    myDataSource.responseSchema = {
        resultsList: "ResultSet.Result",
        fields: fields,
        metaFields : {
            totalResultsAvailable: "ResultSet.totalResultsAvailable",
			startIndex: 'ResultSet.startIndex',
			sortKey: 'ResultSet.sortKey',
			sortDir: 'ResultSet.sortDir'
        }
    };
    var myDataTable = new YAHOO.widget.DataTable("datatable-container", 
        YAHOO.portico.columnDefs, myDataSource, {
            paginator: pag,
            dynamicData: true,
            sortedBy: YAHOO.portico.initialSortedBy || {key: fields[0], dir: YAHOO.widget.DataTable.CLASS_ASC}
    });
    var handleSorting = function (oColumn) {
        var sDir = this.getColumnSortDir(oColumn);
        var newState = getState(oColumn.key, sDir);
        History.navigate("state", newState);
    };
    myDataTable.sortColumn = handleSorting;

	/* Start from Property*/

  /********************************************************************************
 *
 */
	var onContextMenuBeforeShow = function(p_sType, p_aArgs)
	{
		var prefixSelected = '';
		var oTarget = this.contextEventTarget;
		if (this.getRoot() == this)
		{
			if(oTarget.tagName != "TD")
			{
				oTarget = YAHOO.util.Dom.getAncestorByTagName(oTarget, "td");
			}
			oSelectedTR = YAHOO.util.Dom.getAncestorByTagName(oTarget, "tr");
			oSelectedTR.style.backgroundColor  = '#AAC1D8' ;
			oSelectedTR.style.color = "black";
			YAHOO.util.Dom.addClass(oSelectedTR, prefixSelected);
		}
	}


 /********************************************************************************
 *
 */
	var onContextMenuHide = function(p_sType, p_aArgs)
	{
		var prefixSelected = '';
		if (this.getRoot() == this && oSelectedTR)
		{
			oSelectedTR.style.backgroundColor  = "" ;
			oSelectedTR.style.color = "";
			YAHOO.util.Dom.removeClass(oSelectedTR, prefixSelected);
		}
	}
 /********************************************************************************
 *
 */
	var onContextMenuClick = function(p_sType, p_aArgs, p_myDataTable)
	{
		var task = p_aArgs[1];
			if(task)
			{
				// Extract which TR element triggered the context menu
				var elRow = p_myDataTable.getTrEl(this.contextEventTarget);
				if(elRow)
				{
					var oRecord = p_myDataTable.getRecord(elRow);
					var url = YAHOO.portico.actions[task.groupIndex].action;
					var sUrl = "";
					var vars2 = "";

					if(YAHOO.portico.actions[task.groupIndex].parameters!=null)
					{
						for(f=0; f<YAHOO.portico.actions[task.groupIndex].parameters.parameter.length; f++)
						{
							param_name = YAHOO.portico.actions[task.groupIndex].parameters.parameter[f].name;
							param_source = YAHOO.portico.actions[task.groupIndex].parameters.parameter[f].source;
							vars2 = vars2 + "&"+param_name+"=" + oRecord.getData(param_source);
						}
						sUrl = url + vars2;
					}
					if(YAHOO.portico.actions[task.groupIndex].parameters.parameter.length > 0)
					{
						//nothing
					}
					else //for New
					{
						sUrl = url;
					}
					//Convert all HTML entities to their applicable characters

					sUrl=YAHOO.portico.html_entity_decode(sUrl);

					// look for the word "DELETE" in URL
					if(YAHOO.portico.substr_count(sUrl,'delete')>0)
					{
						confirm_msg = YAHOO.portico.actions[task.groupIndex].confirm_msg;
						if(confirm(confirm_msg))
						{
							sUrl = sUrl + "&confirm=yes&phpgw_return_as=json";
							delete_record(sUrl);
						}
					}
					else
					{
						if(YAHOO.portico.substr_count(sUrl,'target=_blank')>0)
						{
							window.open(sUrl,'_blank');
						}
						else if(YAHOO.portico.substr_count(sUrl,'target=_lightbox')>0)
						{
							//have to be defined as a local function. Example in invoice.list_sub.js
							//console.log(sUrl); // firebug
							showlightbox(sUrl);
						}
						else if(YAHOO.portico.substr_count(sUrl,'target=_tinybox')>0)
						{
							//have to be defined as a local function. Example in invoice.list_sub.js
							//console.log(sUrl); // firebug
							showtinybox(sUrl);
						}
						else
						{
							window.open(sUrl,'_self');
						}
					}
				}
			}
	};
 /********************************************************************************
 *
 */
	var GetMenuContext = function()
	{
		var opts = new Array();
		var p=0;
		for(var k =0; k < YAHOO.portico.actions.length; k ++)
		{
			if(YAHOO.portico.actions[k].my_name != 'add')
			{	opts[p]=[{text: YAHOO.portico.actions[k].text}];
				p++;
			}
		}
		return opts;
   }


	myDataTable.subscribe("rowMouseoverEvent", myDataTable.onEventHighlightRow);

	myDataTable.subscribe("rowMouseoutEvent", myDataTable.onEventUnhighlightRow);

	myContextMenu = new YAHOO.widget.ContextMenu("mycontextmenu", {trigger:myDataTable.getTbodyEl()});
	myContextMenu.addItems(GetMenuContext());

	myContextMenu.subscribe("beforeShow", onContextMenuBeforeShow);
	myContextMenu.subscribe("hide", onContextMenuHide);
	//Render the ContextMenu instance to the parent container of the DataTable
	myContextMenu.subscribe("click", onContextMenuClick, myDataTable);
	myContextMenu.render("datatable-container");


	for(var i=0; i < YAHOO.portico.columnDefs.length;i++)
	{
		if( YAHOO.portico.columnDefs[i].sortable )
		{
			YAHOO.util.Dom.getElementsByClassName( 'yui-dt-resizerliner' , 'div' )[i].style.background  = '#D8D8DA url(phpgwapi/js/yahoo/assets/skins/sam/sprite.png) repeat-x scroll 0 -100px';
		}
		//title columns alwyas center
//		YAHOO.util.Dom.getElementsByClassName( 'yui-dt-resizerliner', 'div' )[0].style.textAlign = 'center';
	}

	/* End from Property*/

    var handlePagination = function(state) {
        var sortedBy  = this.get("sortedBy");
        var newState = getState(sortedBy.key, sortedBy.dir, state.recordOffset);
        History.navigate("state", newState);
     };
    pag.unsubscribe("changeRequest", myDataTable.onPaginatorChangeRequest);
    pag.subscribe("changeRequest", handlePagination, myDataTable, true);

    myDataTable.doBeforeLoadData = function(oRequest, oResponse, oPayload) {
        oPayload.totalRecords = oResponse.meta.totalResultsAvailable;
		oPayload.pagination = { 
			rowsPerPage: oResponse.meta.paginationRowsPerPage || 10, 
			recordOffset: oResponse.meta.startIndex || 0 
	    }
		oPayload.sortedBy = { 
			key: oResponse.meta.sortKey || "id", 
			dir: (oResponse.meta.sortDir) ? "yui-dt-" + oResponse.meta.sortDir : "yui-dt-asc" 
		};
		return true;
    }

	YAHOO.util.Event.on(
	    YAHOO.util.Selector.query('select'), 'change', function (e) {
	        //var val = this.value;
			var state = getState();
			YAHOO.util.Dom.setStyle('list_flash', 'display', 'none');
			History.navigate('state', state);
	});

    YAHOO.util.Event.addListener('queryForm', "submit", function(e){
        YAHOO.util.Event.stopEvent(e);
		var state = getState();
		YAHOO.util.Dom.setStyle('list_flash', 'display', 'none');
		History.navigate('state', state);
    });

	YAHOO.util.Event.addListener('list_actions_form', "submit", function(e){
		YAHOO.util.Event.stopEvent(e);
		window.setTimeout(function() {
			var state = getState();
			var action = myDataSource.liveData + '&' + state;
			action = action.replace('&phpgw_return_as=json', '');
			YAHOO.util.Dom.setAttribute(document.getElementById('list_actions_form'), 'action', action);
		   document.getElementById('list_actions_form').submit();
		}, 0);
	});

	var History = YAHOO.util.History; 
	var getState = function(skey, sdir, start) {
		var state = YAHOO.portico.serializeForm('queryForm');
		var sortedBy  = myDataTable.get("sortedBy");
		skey = skey ? skey : sortedBy.key;
		sdir = sdir ? sdir : sortedBy.dir; 
		sdir = sdir == 'yui-dt-asc' ? 'asc' : 'desc';
		start = start ? start : 0;
		state += '&sort=' + skey;
		state += '&dir=' + sdir;
		state += '&startIndex=' + start;
		return state;
	}

	var handleHistoryNavigation = function (state) {
		var params = YAHOO.portico.parseQS(state);
		YAHOO.portico.fillForm('queryForm', params);
		myDataSource.sendRequest(state, {success: function(sRequest, oResponse, oPayload) {
			myDataTable.onDataReturnInitializeTable(sRequest, oResponse, pag);
		}});
	};
	
	var initialRequest = History.getBookmarkedState("state") || getState();
	History.register("state", initialRequest, handleHistoryNavigation);
/*
	History.onReady(function() {
		var state = YAHOO.util.History.getCurrentState('state');
		handleHistoryNavigation(state);
	});

*/
	History.initialize("yui-history-field", "yui-history-iframe");


};


	onDownloadClick = function()
	{
		var state = YAHOO.util.History.getCurrentState('state');
alert(state);
		//store actual values
		actuall_funct = path_values.menuaction;

		if(config_values.particular_download)
		{
			path_values.menuaction = config_values.particular_download;
		}
		else
		{
			donwload_func = path_values.menuaction;
			// modify actual function for "download" in path_values
			// for example: property.uilocation.index --> property.uilocation.download
			tmp_array= donwload_func.split(".")
			tmp_array[2]="download"; //set function DOWNLOAD
			donwload_func = tmp_array.join('.');
			path_values.menuaction=donwload_func;
		}

		ds_download = phpGWLink('index.php',path_values);
		//show all records since the first
		ds_download+="&allrows=1&start=0";
		//return to "function index"
		path_values.menuaction=actuall_funct;
		window.open(ds_download,'window');
   }




YAHOO.util.Event.addListener(window, "load", YAHOO.portico.initializeDataTable);


