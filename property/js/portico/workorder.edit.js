
	var vendor_id;

	this.validate_form = function()
	{
		conf = {
				modules : 'location, date, security, file',
				validateOnBlur : false,
				scrollToTopOnError : true,
				errorMessagePosition : 'top',
				language : validateLanguage
			};
		return $('form').validateForm(validateLanguage, conf);
	}

	function submit_workorder()
	{
		if(!validate_form())
		{
			return;
		}
		document.form.submit();
	}

	function calculate_workorder()
	{
		if(!validate_form())
		{
			return;
		}
		document.getElementsByName("calculate_workorder")[0].value = 1;
		document.form.submit();
	}
	function send_workorder()
	{
		if(!validate_form())
		{
			return;
		}
		document.getElementsByName("send_workorder")[0].value = 1;
		document.form.submit();
	}
	function set_tab(tab)
	{
		document.form.tab.value = tab;
	}

	this.showlightbox_manual_invoide = function(workorder_id)
	{
		var oArgs = {menuaction:'property.uiworkorder.add_invoice', order_id:workorder_id};
		var sUrl = phpGWLink('index.php', oArgs);

		TINY.box.show({iframe:sUrl, boxid:'frameless',width:750,height:450,fixed:false,maskid:'darkmask',maskopacity:40, mask:true, animate:true,
		close: true
	//	closejs:function(){closeJS_local()}
		});
	}

	this.fetch_vendor_email=function()
	{
		if(document.getElementById('vendor_id').value)
		{
			base_java_url['vendor_id'] = document.getElementById('vendor_id').value;
		}

		if(document.getElementById('vendor_id').value != vendor_id)
		{
			var oArgs = base_java_url;
			var strURL = phpGWLink('index.php', oArgs, true);
			JqueryPortico.updateinlineTableHelper(oTable4, strURL);
			vendor_id = document.getElementById('vendor_id').value;
		}
	};

	this.onDOMAttrModified = function(e)
	{
		var attr = e.attrName || e.propertyName;
		var target = e.target || e.srcElement;
		if (attr.toLowerCase() == 'vendor_id')
		{
			fetch_vendor_email();
		}
	}

	window.addEventListener("load", function()
	{
		d = document.getElementById('vendor_id');
		if(d)
		{
			if (d.attachEvent)
			{
				d.attachEvent('onpropertychange', onDOMAttrModified, false);
			}
			else
			{
				d.addEventListener('DOMAttrModified', onDOMAttrModified, false);
			}
		}
	});