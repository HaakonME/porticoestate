//--------------------------------------------------------
// Declaration of location.index vars
//--------------------------------------------------------
	//SelectButton deined i ui-class
	// define buttons
	var oNormalButton_0, oNormalButton_1, oNormalButton_2;
	var normalButtons = [
	{order:0, name:'btn_search',funct:"onSearchClick"},
	{order:1, name:'btn_new',	funct:"onNewClick"},
	{order:2, name:'btn_export',funct:"onDownloadClick"}
	]

    var toolTips = [
    {name:'btn_export', title:'download', description:'Download table to your browser',ColumnDescription:''}
    ]

	// define Text buttons
	var textImput = [
	{order:0, name:'query',	id:'txt_query'}
	]

	// define the hidden column in datatable
	var config_values = {
	date_search : 1 //if search has link "Data search"
	}

   	var linktoolTips =[
	{name:'btn_columns', title:'columns', description:'Choose columns'},
	{name:'btn_data_search', title:'Data search', description:'Narrow the search by dates'}
	]


/****************************************************************************************/
	this.particular_setting = function()
	{
		if(flag_particular_setting=='init')
		{

			//eliminate "no category" option because is necesary have a category in the  PHP query
	//		delete oMenuButton_0.getMenu().itemData[0];
			//correcting it. now look for value 
	//		index = locate_in_array_options(0,"value",path_values.cat_id);
			//only change LABEL, because value (cat_id) is include un URL (PHP use redirect)
	//		oMenuButton_0.set("label", ("<em>" + array_options[0][index][1] + "</em>"));
	//		oMenuButton_0.focus();
			YAHOO.util.Dom.get(textImput[0].id).focus();
		}
		else if(flag_particular_setting=='update')
		{
			//nothing
		}

	}


	this.showlightbox = function(sUrl)
	{
		var onDialogShow = function(e, args, o)
		{
			var frame = document.createElement('iframe');
			frame.src = sUrl;
			frame.width = "100%";
			frame.height = "460";
			o.setBody(frame);
		};
		lightbox.showEvent.subscribe(onDialogShow, lightbox);
		lightbox.show();
	}


	var show_picture = function(elCell, oRecord, oColumn, oData)
	{
		if(oRecord.getData('img_id'))
		{
			var oArgs = {menuaction:'property.uigallery.view_file', file:oRecord.getData('directory') + '/' + oRecord.getData('file_name')};
			var sUrl = phpGWLink('index.php', oArgs);
			elCell.innerHTML =  "<a href=\""+sUrl+"\" title=\""+oRecord.getData('file_name')+"\" id=\""+oRecord.getData('img_id')+"\" rel=\"colorbox\" target=\"_blank\"><img src=\""+sUrl+"&thumb=1\" alt=\""+oRecord.getData('file_name')+"\" /></a>";
		}
	}


/****************************************************************************************/

  	this.myParticularRenderEvent = function()
  	{
  	//don't delete it
  	}

	var oArgs_entity = {menuaction:'property.uientity.edit'};
	var sUrl_entity = phpGWLink('index.php', oArgs_entity);

	var linktToEntity = function(elCell, oRecord, oColumn, oData)
	{
//	  	elCell.innerHTML = "<a href="+sUrl_entity+"&entity_id="+oRecord.getData('entity_id')+"&cat_id="+oRecord.getData('cat_id')+"&id="+oRecord.getData('id')+"&type="+oRecord.getData('_type')+">" + oData + "</a>";
	};


/****************************************************************************************/
	YAHOO.util.Event.addListener(window, "load", function()
	{
		//avoid render buttons html
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
