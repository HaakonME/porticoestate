
var arURLParts = strBaseURL.split('?');
var comboBase = arURLParts[0] + 'phpgwapi/inc/yui-combo-master/combo.php?';

YUI_config = {
    //Don't combine the files
    combine: true,
    //Ignore things that are already loaded (in this process)
    ignoreRegistered: false,
    //Set the base path
	comboBase: comboBase,
    base: '',
    //And the root
    root: '',
    //Require your deps
    require: [ ]
};


YUI({
}).use(
	'gallery-formvalidator', 
		function(Y) {	
                Y.on("domready", function () {
                   var form = new Y.Validator(
                        {
                            form:'form',
                            defaultIncorrectIndicatorCss:'validator',
                            defaultCorrectIndicatorCss:'indicator',
                            createCorrectIndicator:true,
                            createIncorrectIndicator:true
                        }
                    );

                });

});

	YAHOO.util.Event.addListener(window, "load", function()
	{
		var oArgs = {menuaction:'property.uicondition_survey.get_vendors'};
		var strURL = phpGWLink('index.php', oArgs, true);
	    YAHOO.portico.autocompleteHelper(strURL, 
		'vendor_name', 'vendor_id', 'vendor_container');
	});

	YAHOO.util.Event.addListener(window, "load", function()
	{
		var oArgs = {menuaction:'property.uicondition_survey.get_users'};
		var strURL = phpGWLink('index.php', oArgs, true);
	    YAHOO.portico.autocompleteHelper(strURL, 
		'coordinator_name', 'coordinator_id', 'coordinator_container');
	});

	this.fileuploader = function()
	{
		var requestUrl = phpGWLink('index.php', fileuploader_action);
		TINY.box.show({iframe:requestUrl, boxid:'frameless',width:750,height:450,fixed:false,maskid:'darkmask',maskopacity:40, mask:true, animate:true, close: true,closejs:function(){closeJS_local()}});
	}


	function closeJS_local()
	{
	//	var reqUrl = '<xsl:value-of select="//datatable/source"/>';
	//	YAHOO.portico.inlineTableHelper('requirement-container', reqUrl, YAHOO.portico.columnDefs);
	}

	function refresh_files()
	{
		alert('refresh_files');
	}

