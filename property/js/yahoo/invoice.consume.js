//--------------------------------------------------------
// Declaration of location.index vars
//--------------------------------------------------------

		//define SelectButton
	 	var oMenuButton_0, oMenuButton_1, oMenuButton_2;
	 	var selectsButtons = [
		{order:0, var_URL:'cat_id',			name:'btn_cat_id',			style:'',dependiente:''},
		{order:1, var_URL:'district_id',	name:'btn_district_id',		style:'',dependiente:''},
		{order:2, var_URL:'b_account_class',name:'btn_b_account_class',	style:'',dependiente:''}
		]

		// define buttons
		var oNormalButton_0;
		var normalButtons = [
			{order:0, name:'btn_search', funct:"onSearchClick"}
		]

		// define Link Buttons
		var linktoolTips = [
		  {name:'lnk_workorder', title:'Workorder ID', description:'enter the Workorder ID to search by workorder - at any Date'},
		  {name:'lnk_vendor', title:'Vendor', description:'Select the vendor by clicking this link'},
		  {name:'lnk_property', title:'Facilities Managements', description:'Select the property by clicking this link'}
		 ]


		var textImput = [
			{order:0, name:'workorder_id',	id:'txt_workorder'},
			{order:1, name:'vendor_id',		id:'txt_vendor'},
			{order:1, name:'loc1',			id:'txt_loc1'}
		]

		var toolTips = [
		]

		// define the hidden column in datatable
		var config_values = {
			date_search : 1, //if search has link "Data search"
			PanelLoading : 1
		}

		var tableYUI;

	/********************************************************************************/
		this.particular_setting = function()
		{
			if(flag_particular_setting=='init')
			{
				//locate (asign ID) to datatable
				tableYUI = YAHOO.util.Dom.getElementsByClassName("yui-dt-data","tbody")[0].parentNode;
				tableYUI.setAttribute("id","tableYUI");

				YAHOO.util.Dom.get("start_date-trigger").focus();
			}
			else if(flag_particular_setting=='update')
			{
				//nothing
			}
		}
	/********************************************************************************/
		this.myParticularRenderEvent = function()
		{
			//not SHOW paginator
			YAHOO.util.Dom.get("paging").style.display = "none";

			delete_message();
			create_message();
			tableYUI.deleteTFoot();
			addFooterDatatable();
		}
	/********************************************************************************
	* Delete all message un DIV 'message'
	*/
	this.delete_message = function()
	{
		div_message= YAHOO.util.Dom.get("message");
		if ( div_message.hasChildNodes() )
		{
			while ( div_message.childNodes.length >= 1 )
		    {
		        div_message.removeChild( div_message.firstChild );
		    }
		}
	}
	/********************************************************************************
	* Delete all message un DIV 'message'
	*/
	this.create_message = function()
	{

		div_message= YAHOO.util.Dom.get("message");
		newTable = document.createElement('table');
		//fix IE error
		newTbody = document.createElement("TBODY");

		//SHOW message if exist 'values_ds.message'
		 if(window.values_ds.current_consult)
		 {
		 	for(i=0; i<values_ds.current_consult.length; i++)
		 	{
		 		newTR = document.createElement('tr');
		 		for(j=0; j<2; j++)
		 		{
		 			newTD = document.createElement('td');
		 			newTD.appendChild(document.createTextNode(values_ds.current_consult[i][j]));
		 			newTR.appendChild(newTD);
		 			//add : after title
		 			if(j==0)
		 			{
			 			newTD = document.createElement('td');
			 			newTD.appendChild(document.createTextNode("\u00A0:\u00A0"));
			 			newTR.appendChild(newTD);
		 			}
		 		}
		 		newTbody.appendChild(newTR);
			 }
		 }
		 newTable.appendChild(newTbody);
		 div_message.appendChild(newTable);
	}
	/********************************************************************************/
	  	this.addFooterDatatable = function()
	  	{
			//Create ROW
			newTR = document.createElement('tr');
			//columns with colspan 3
			newTD = document.createElement('td');
			newTD.colSpan = 3;
			newTD.style.borderTop="1px solid #000000";
			newTD.appendChild(document.createTextNode(''));
			newTR.appendChild(newTD.cloneNode(true));
			//Sum
			newTD = document.createElement('td');
			newTD.colSpan = 1;
			newTD.style.borderTop="1px solid #000000";
			newTD.style.fontWeight = 'bolder';
			newTD.style.textAlign = 'right';
			newTD.style.paddingRight = '0.8em';
			newTD.appendChild(document.createTextNode(values_ds.sum));
			newTR.appendChild(newTD.cloneNode(true));
			//Add to Table
			myfoot = tableYUI.createTFoot();
			myfoot.setAttribute("id","myfoot");
			myfoot.appendChild(newTR.cloneNode(true));
	  	}
	/********************************************************************************/
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