//--------------------------------------------------------
// Declaration of workorder.index vars
//--------------------------------------------------------
	//define SelectButton
 	var oMenuButton_0, oMenuButton_1, oMenuButton_2, oMenuButton_3;
 	var selectsButtons = [
	{order:0, var_URL:'cat_id',name:'btn_cat_id',style:'categorybutton',dependiente:''},
	{order:1, var_URL:'status_id',name:'btn_status_id',style:'districtbutton',dependiente:''},
	{order:2, var_URL:'wo_hour_cat_id',name:'btn_wo_hour_cat_id',style:'partOFTownbutton',dependiente:''},
	{order:3, var_URL:'filter', name:'btn_user_id',style:'ownerIdbutton',dependiente:''}
	]

	// define buttons
	var oNormalButton_0, oNormalButton_1, oNormalButton_2;
	var normalButtons = [
	{order:0, name:'btn_search', funct:"onSearchClick"},
	{order:1, name:'btn_new', funct:"onNewClick"},
	{order:2, name:'btn_export', funct:"onDownloadClick"}
	]

	// define Text buttons
	var textImput = [
	{order:0, name:'query',	id:'txt_query'},
	{order:1, name:'search_vendor',	id:'txt_search_vendor'}
	]

	var toolTips =
	[
	 	{name:'btn_export', title:'Download', description:'Download table to your browser',ColumnDescription:''}
	]

	var linktoolTips =
	[
	  {name:'btn_data_search', title:'Date search', description:'Narrow the search by dates'}
	]

	// define the hidden column in datatable
	var config_values = {
	 date_search : 1 //if search has link "Data search"
	};

	/********************************************************************************/
	this.myFormatNum2 = function(Data)
	{
		return  YAHOO.util.Number.format(Data, {decimalPlaces:0, decimalSeparator:"", thousandsSeparator:" "});
	}				
	/********************************************************************************/
	var myFormatCount2 = function(elCell, oRecord, oColumn, oData)
	{
		elCell.innerHTML = myFormatNum2(oData);
	}	



	this.particular_setting = function()
	{
		if(flag_particular_setting=='init')
		{
			oMenuButton_0.focus();
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






