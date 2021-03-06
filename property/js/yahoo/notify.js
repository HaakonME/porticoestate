var d;
var notify_contact = 0;

/********************************************************************************/
	this.cleanValuesHiddenActionsButtons=function()
	{
		YAHOO.util.Dom.get('hd_notify[email]').value = '';
		YAHOO.util.Dom.get('hd_notify[sms]').value = '';
		YAHOO.util.Dom.get('hd_notify[enable]').value = '';
		YAHOO.util.Dom.get('hd_notify[disable]').value = '';
		YAHOO.util.Dom.get('hd_notify[delete]').value = '';
	}


/* This one is added dynamically from php-class property_notify::get_yui_table_def()
	YAHOO.widget.DataTable.formatLink_notify = function(elCell, oRecord, oColumn, oData)
	{

	};
*/

//	var notify_table_count defined in property_notify::get_yui_table_def()
//	var notify_lang_view defined in property_notify::get_yui_table_def()

	YAHOO.widget.DataTable.formatLink_notify = function(elCell, oRecord, oColumn, oData)
	{
	  	elCell.innerHTML = "<a href="+datatable[notify_table_count][0]["edit_action"]+"&ab_id="+oData+" title='"+oData+"'>"+notify_lang_view+"</a>";
	};

	var FormatterRight_notify = function(elCell, oRecord, oColumn, oData)
	{
		elCell.innerHTML = "<div align=\"right\">"+oData+"</div>";
	}	
	

	var myFormatterCheck_notify = function(elCell, oRecord, oColumn, oData)
	{
		var id = oRecord.getData('id');
		elCell.innerHTML = "<center><input type=\"checkbox\" class=\"mychecks\"  value=\""+id+"\" name=\"notify[ids][]\"/></center>";
	}

  	check_all_notify = function()
  	{
		var myclass = 'mychecks';
		controls = YAHOO.util.Dom.getElementsByClassName(myclass);
		for(i=0;i<controls.length;i++)
		{
			if(!controls[i].disabled)
			{
				//for class=transfer_idClass, they have to be interchanged
				if(myclass=="mychecks")
				{
					if(controls[i].checked)
					{
						controls[i].checked = false;
					}
					else
					{
						controls[i].checked = true;
					}
				}
				//for the rest, always id checked
				else
				{
					controls[i].checked = true;
				}
			}
		}
	}


 /********************************************************************************/

	this.notify_contact_lookup = function()
	{
		if(!base_java_notify_url['location_item_id'])
		{
			alert(notify_lang_alert); // notify_lang_alert is defined in php-class property_notify::get_yui_table_def()
			return;
		}	
		var oArgs = {menuaction:'property.uilookup.addressbook',column:'notify_contact'};
		var strURL = phpGWLink('index.php', oArgs);
		Window1=window.open(strURL,"Search","left=50,top=100,width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");
	}		

/* This one is added dynamically from php-class property_notify::get_yui_table_def()
	this.refresh_notify_contact=function()
	{
	}
*/
	this.onDOMAttrModified_notify = function(e)
	{
		var attr = e.attrName || e.propertyName
		var target = e.target || e.srcElement;
		if (attr.toLowerCase() == 'notify_contact')
		{
			refresh_notify_contact();
		}
	}

	YAHOO.util.Event.addListener(window, "load", function()
	{
		d = document.getElementById('notify_contact');
		if(d)
		{
			if (d.attachEvent)
			{
				d.attachEvent('onpropertychange', onDOMAttrModified_notify, false);
			}
			else
			{
				d.addEventListener('DOMAttrModified', onDOMAttrModified_notify, false);
			}
		}
	});
