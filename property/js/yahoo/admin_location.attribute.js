//--------------------------------------------------------
// Declaration of location.index vars
//--------------------------------------------------------
	//define SelectButton
 	var oMenuButton_0, oMenuButton_1, oMenuButton_2, oMenuButton_3;
 	var selectsButtons = [

	]

	// define buttons
	var oNormalButton_0, oNormalButton_1;
	var normalButtons = [
	{order:0, name:'btn_search',funct:"onSearchClick"},
	{order:1, name:'btn_new',	funct:"onNewClick"},
	{order:2, name:'btn_done', funct:"onDoneClick"}
	]

    var toolTips = [
    ]

	// define Text buttons
	var textImput = [
	{order:0, name:'query',	id:'txt_query'}
	]

	// define the hidden column in datatable
	var config_values =
	{
		date_search : 0, //if search has link "Data search"
		particular_done : "property.uiadmin_entity.category"
	}

   	var linktoolTips =[
	]


/****************************************************************************************/
	this.particular_setting = function()
	{
		if(flag_particular_setting=='init')
		{
			//eliminate "no category" option because is necesary have a category in the  PHP query
			//delete oMenuButton_0.getMenu().itemData[0];

			//for this particular module, the Category's combo box has sets his own category.
			//oMenuButton_0.set("label", ("<em>" + array_options[0][path_values.cat_id][1] + "</em>"));
			//oMenuButton_0.focus();
		}
		else if(flag_particular_setting=='update')
		{
			//nothing
		}

	}
/****************************************************************************************/

  	this.myParticularRenderEvent = function()
  	{
  	//don't delete it

  	}

	/********************************************************************************
 *
 */
	this.move_record = function(sUrl,allrows)
	{
		var callback =	{	success: function(o){execute_ds(allrows)},
							failure: function(o){window.alert('Server or your connection is death.')},
							timeout: 10000
						};
		sUrl = sUrl + "&confirm=yes&phpgw_return_as=json";
		var request = YAHOO.util.Connect.asyncRequest('POST', sUrl, callback);
	}



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






