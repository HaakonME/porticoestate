//--------------------------------------------------------
// Declaration of location.index vars
//--------------------------------------------------------
	//define SelectButton
 	var oMenuButton_0;
 	var selectsButtons = [
	{order:0, var_URL:'status_id',name:'btn_status_id',style:'partOFTownbutton',dependiente:''}
	];

	// define buttons
	var oNormalButton_0, oNormalButton_1, oNormalButton_2;
	var normalButtons = [
	{order:0, name:'btn_search', funct:"onSearchClick"},
	{order:1, name:'btn_new', funct:"onAddClick"},
	{order:2, name:'btn_export', funct:"onDownloadClick"}
	];

	// define Text buttons
	var textImput = [
	{order:0, name:'query',	id:'txt_query'}
	]

	var toolTips =
	[
		{name:'status', title:'Status', description:'',ColumnDescription:'status'},
		{name:'btn_export', title:'download', description:'Download table to your browser',ColumnDescription:''}
	]

	var linktoolTips =
	[
		{name:'btn_data_search', title:'Date search', description:'Narrow the search dates'}
	]

	var config_values =
	{
		date_search : 1 //if search has link "Data search"
	}

	this.particular_setting = function()
	{
		if(flag_particular_setting=='init')
		{
			//status
/*
			index = locate_in_array_options(2,"value",path_values.status_id);
			if(index)
			{
				oMenuButton_2.set("label", ("<em>" + array_options[2][index][1] + "</em>"));
			}
*/
//			oMenuButton_0.focus();
			YAHOO.util.Dom.get(textImput[0].id).focus();
		}
		else if(flag_particular_setting=='update')
		{

		}
	}



/****************************************************************************************/

  	this.myParticularRenderEvent = function()
  	{
  		//nothing
  	}

/****************************************************************************************/


	this.onAddClick = function()
	{
		var oArgs = {menuaction:'frontend.uihelpdesk.add_ticket', noframework:'1'};
		var sUrl = phpGWLink('index.php', oArgs);
/*
		TINY.box.show({
		iframe:sUrl,
		boxid:'frameless',
		width:750,
		height:450,
		fixed:false,
		maskid:'darkmask',
		maskopacity:40,
		mask:true,
		animate:true,
		close: true
		});
*/
		var onDialogShow = function(e, args, o)
		{
			var frame = document.createElement('iframe');
			frame.src = sUrl;
			frame.width = "100%";
			frame.height = "400";
			o.setBody(frame);
		};
		lightbox.showEvent.subscribe(onDialogShow, lightbox);
		lightbox.show();
	}


//----------------------------------------------------------
	//YAHOO.util.Event.addListener(window, "load", function()
	YAHOO.util.Event.onDOMReady(function()
	{
		YAHOO.util.Dom.getElementsByClassName('toolbar','div')[0].style.display = 'none';
		var loader = new YAHOO.util.YUILoader();
		loader.addModule({
			name: "anyone", //module name; must be unique
			type: "js", //can be "js" or "css"
		    fullpath: property_js //'property_js' have the path for property.js, is render in HTML
		    });

		loader.require("anyone");

		//Insert JSON utility on the page

	    loader.insert();

	});




