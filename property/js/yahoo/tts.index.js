//--------------------------------------------------------
// Declaration of location.index vars
//--------------------------------------------------------
	//define SelectButton
 	var oMenuButton_0, oMenuButton_1, oMenuButton_2;
 	var selectsButtons = [
	{order:0, var_URL:'cat_id',name:'btn_cat_id',style:'categorybutton',dependiente:''},
	{order:1, var_URL:'district_id',name:'btn_district_id',style:'districtbutton',dependiente:''},
	{order:2, var_URL:'status_id',name:'btn_status_id',style:'partOFTownbutton',dependiente:''}
//	{order:3, var_URL:'user_id', name:'btn_user_id',style:'ownerIdbutton',dependiente:''}
	];

	// define buttons
	var oNormalButton_0, oNormalButton_1, oNormalButton_2;
	var normalButtons = [
	{order:0, name:'btn_search', funct:"onSearchClick"},
	{order:1, name:'btn_new', funct:"onNewClick"},
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
		{name:'btn_data_search', title:'Data search', description:'Narrow the search dates'}
	]

	var config_values =
	{
		date_search : 1 //if search has link "Data search"
	}

	var tableYUI;

	var FormatterRight = function(elCell, oRecord, oColumn, oData)
	{
		elCell.innerHTML = "<P align=\"right\">"+oData+"</p>";
	}

	this.onChangeSelect = function()
	{
		var myselect=document.getElementById("sel_user_id");
		for (var i=0; i<myselect.options.length; i++)
		{
			if (myselect.options[i].selected==true)
			{
				break;
			}
		}
		eval("path_values.user_id='"+myselect.options[i].value+"'");
		execute_ds();
	}


	this.particular_setting = function()
	{
		if(flag_particular_setting=='init')
		{
			tableYUI = YAHOO.util.Dom.getElementsByClassName("yui-dt-data","tbody")[0].parentNode;
			tableYUI.setAttribute("id","tableYUI");

			//category
			index = locate_in_array_options(0,"value",path_values.cat_id);
			if(index)
			{
				oMenuButton_0.set("label", ("<em>" + array_options[0][index][1] + "</em>"));
			}
			//district
			index = locate_in_array_options(1,"value",path_values.district_id);
			if(index)
			{
				oMenuButton_1.set("label", ("<em>" + array_options[1][index][1] + "</em>"));
			}
			//status
			index = locate_in_array_options(2,"value",path_values.status_id);
			if(index)
			{
				oMenuButton_2.set("label", ("<em>" + array_options[2][index][1] + "</em>"));
			}
/*
			//user
			index = locate_in_array_options(3,"value",path_values.user_id);
			if(index)
			{
				oMenuButton_3.set("label", ("<em>" + array_options[3][index][1] + "</em>"));
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
		if(values_ds.show_sum)
		{
			tableYUI.deleteTFoot();
			addFooterDatatable();
		}
  	}

  	this.addFooterDatatable = function()
  	{
		tmp_sum_budget = YAHOO.util.Number.format(values_ds.sum_budget, {decimalPlaces:0, decimalSeparator:",", thousandsSeparator:" "});
		tmp_sum_actual_cost = YAHOO.util.Number.format(values_ds.sum_actual_cost, {decimalPlaces:2, decimalSeparator:",", thousandsSeparator:" "});

		count_empty = 0;
		for(i=0;i<myColumnDefs.length;i++)
		{
			if (myColumnDefs[i].key == 'estimate')
			{
				count_empty = i;
				break;
			}
		}

		count_empty_end = myColumnDefs.length - count_empty - 2;

		//Create ROW
		newTR = document.createElement('tr');
		td_empty(count_empty);
		td_sum(tmp_sum_budget);
		td_sum(tmp_sum_actual_cost);
		td_empty(count_empty_end);
		//Add to Table
		myfoot = tableYUI.createTFoot();
		myfoot.setAttribute("id","myfoot");
		myfoot.appendChild(newTR.cloneNode(true));

		//clean value for values_ds.message
		//values_ds.message = null;
  	}


/****************************************************************************************/

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




