YAHOO.booking.setupToolbar = function() {
    var items = YAHOO.util.Dom. getElementsBy(function(){return true;}, 'input', 'toolbar');
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
}

YAHOO.booking.setupPaginator = function() {
	var paginatorConfig = {
        rowsPerPage: 10,
        alwaysVisible: false,
        template: "{PreviousPageLink} <strong>{CurrentPageReport}</strong> {NextPageLink}",
        pageReportTemplate: "Showing items {startRecord} - {endRecord} of {totalRecords}",
        containers: ['paginator']
    };
	
	YAHOO.booking.lang('setupPaginator', paginatorConfig);
	var pag = new YAHOO.widget.Paginator(paginatorConfig);
    pag.render();
	return pag;
};

YAHOO.booking.initializeDataTable = function()
{
	YAHOO.booking.setupToolbar();
	YAHOO.booking.setupDatasource();

    var fields = [];
    for(var i=0; i < YAHOO.booking.columnDefs.length; i++) {
        fields.push(YAHOO.booking.columnDefs[i].key);
    }
    var baseUrl = YAHOO.booking.dataSourceUrl;
    if(baseUrl[baseUrl.length - 1] != '&') {
        baseUrl += '&';
    }
	baseUrl += 'sort=' + fields[0] + '&';
    var myDataSource = new YAHOO.util.DataSource(baseUrl);

    myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
    myDataSource.connXhrMode = "queueRequests";
    myDataSource.responseSchema = {
        resultsList: "ResultSet.Result",
        fields: fields,
        metaFields : {
            totalResultsAvailable: "ResultSet.totalResultsAvailable"
        }
    };

	var pag = YAHOO.booking.setupPaginator();

    var myDataTable = new YAHOO.widget.DataTable("datatable-container", 
        YAHOO.booking.columnDefs, myDataSource, {
            paginator: pag,
            dynamicData: true,
            sortedBy: {key: fields[0], dir: YAHOO.widget.DataTable.CLASS_ASC}
    });
    myDataTable.handleDataReturnPayload = function(oRequest, oResponse, oPayload) {
        oPayload.totalRecords = oResponse.meta.totalResultsAvailable;
        return oPayload;
    }
    YAHOO.util.Event.addListener('queryForm', "submit", function(e){
        YAHOO.util.Event.stopEvent(e);
        var qs = YAHOO.booking.serializeForm('queryForm');
        myDataSource.liveData = baseUrl + qs + '&';
        myDataSource.sendRequest('', {success: function(sRequest, oResponse, oPayload) {
            myDataTable.onDataReturnInitializeTable(sRequest, oResponse, pag);
        }});
    });
};

YAHOO.util.Event.addListener(window, "load", YAHOO.booking.initializeDataTable);
