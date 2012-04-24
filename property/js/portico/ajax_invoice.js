$(document).ready(function(){
	
	// When janitor is selected, vouchers are fetched from db and voucer select list is populated
	$("#janitor_lid").change(function () {
//		var janitor_lid = $(this).val();
		var janitor_lid = $("#janitor_lid").val();
		var supervisor_lid = $("#supervisor_lid").val();
		var budget_responsible_lid = $("#budget_responsible_lid").val();
		var query = $("#query").val();

		var oArgs = {menuaction:'property.uiinvoice2.get_vouchers'};
		var requestUrl = phpGWLink('index.php', oArgs, true);
      
		var htmlString = "";

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl + "&janitor_lid=" + janitor_lid + "&supervisor_lid=" + supervisor_lid + "&budget_responsible_lid=" + budget_responsible_lid + "&query=" + query,
			success: function(data) {
				if( data != null)
				{
					if(data.sessionExpired)
					{
						alert('Sesjonen er utløpt - du må logge inn på nytt');
						return;
					}

					htmlString  = "<option>" + data.length + " bilag funnet</option>"
					var obj = data;

					$.each(obj, function(i) {
						htmlString  += "<option value='" + obj[i].id + "'>" + obj[i].name + "</option>";
		    			});

					$("#voucher_id_filter").html( htmlString );
				}
				else
				{
					htmlString  += "<option>Ingen bilag</option>"
					$("#voucher_id_filter").html( htmlString );
				}
			} 
			});
		
    });

	$("#supervisor_lid").change(function () {
//		var janitor_lid = $(this).val();
		var janitor_lid = $("#janitor_lid").val();
		var supervisor_lid = $("#supervisor_lid").val();
		var budget_responsible_lid = $("#budget_responsible_lid").val();
		var query = $("#query").val();

		var oArgs = {menuaction:'property.uiinvoice2.get_vouchers'};
		var requestUrl = phpGWLink('index.php', oArgs, true);
      
		var htmlString = "";

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl + "&janitor_lid=" + janitor_lid + "&supervisor_lid=" + supervisor_lid + "&budget_responsible_lid=" + budget_responsible_lid + "&query=" + query,
			success: function(data) {
				if( data != null)
				{
					if(data.sessionExpired)
					{
						alert('Sesjonen er utløpt - du må logge inn på nytt');
						return;
					}

					htmlString  = "<option>" + data.length + " bilag funnet</option>"
					var obj = data;

					$.each(obj, function(i) {
						htmlString  += "<option value='" + obj[i].id + "'>" + obj[i].name + "</option>";
		    			});

					$("#voucher_id_filter").html( htmlString );
				}
				else
				{
					htmlString  += "<option>Ingen bilag</option>"
					$("#voucher_id_filter").html( htmlString );
				}
			} 
			});
		
    });

	$("#budget_responsible_lid").change(function () {
		var janitor_lid = $("#janitor_lid").val();
		var supervisor_lid = $("#supervisor_lid").val();
		var budget_responsible_lid = $("#budget_responsible_lid").val();
		var query = $("#query").val();

		var oArgs = {menuaction:'property.uiinvoice2.get_vouchers'};
		var requestUrl = phpGWLink('index.php', oArgs, true);
      
		var htmlString = "";

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl + "&janitor_lid=" + janitor_lid + "&supervisor_lid=" + supervisor_lid + "&budget_responsible_lid=" + budget_responsible_lid + "&query=" + query,
			success: function(data) {
				if( data != null)
				{
					if(data.sessionExpired)
					{
						alert('Sesjonen er utløpt - du må logge inn på nytt');
						return;
					}

					htmlString  = "<option>" + data.length + " bilag funnet</option>"
					var obj = data;

					$.each(obj, function(i) {
						htmlString  += "<option value='" + obj[i].id + "'>" + obj[i].name + "</option>";
		    			});

					$("#voucher_id_filter").html( htmlString );
				}
				else
				{
					htmlString  += "<option>Ingen bilag</option>"
					$("#voucher_id_filter").html( htmlString );
				}
			} 
			});
		
    });


	$("#search").click(function(e){
		var janitor_lid = $("#janitor_lid").val();
		var supervisor_lid = $("#supervisor_lid").val();
		var budget_responsible_lid = $("#budget_responsible_lid").val();
		var query = $("#query").val();
		var oArgs = {menuaction:'property.uiinvoice2.get_vouchers'};
		var requestUrl = phpGWLink('index.php', oArgs, true);

		var htmlString = "";

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl + "&janitor_lid=" + janitor_lid + "&supervisor_lid=" + supervisor_lid + "&budget_responsible_lid=" + budget_responsible_lid + "&query=" + query,
			success: function(data) {
				if( data != null)
				{
					if(data.sessionExpired)
					{
						alert('Sesjonen er utløpt - du må logge inn på nytt');
						return;
					}

					htmlString  = "<option>" + data.length + " bilag funnet</option>"
					var obj = data;

					$.each(obj, function(i) {
						htmlString  += "<option value='" + obj[i].id + "'>" + obj[i].name + "</option>";
		    			});

					$("#voucher_id_filter").html( htmlString );
				}
				else
				{
					htmlString  += "<option>Ingen bilag</option>"
					$("#voucher_id_filter").html( htmlString );
				}
			}
			});
	});

	$("#voucher_id_filter").change(function () {

		$("#voucher_id").val( '' );
		$("#voucher_id_text").html( '' );
		$("#line_id").val( '' );
		$("#line_text").val( '' );
		$("#order_id").val( '' );
		$("#project_group").val( '' );
		$("#invoice_id").html( '' );
		$("#kid_nr").html( '' );
		$("#vendor").html('' );
//		$("#janitor").html( '' );
//		$("#supervisor").html( '' );
//		$("#budget_responsible").html( '' );
		$("#close_order_orig").val( '' );
		$("#my_initials").val( '' );
		$("#sign_orig").val( '' );
		$("#invoice_date").html( '' );
		$("#payment_date").html( '' );
		$("#b_account_id").val( '' );
//		$("#amount").html( '' );
//		$("#approved_amount").html( '' );
		$("#currency").html( '' );
		$("#oppsynsmannid").html( '' );
		$("#saksbehandlerid").html( '' );
		$("#budsjettansvarligid").html( '' );
		$("#remark").html( '' );
		$("#process_log").html( '' );
		$("#dim_a").val('' );
		$("#dim_b").html( "<option>Velg</option>" );
		$("#dim_e").html( "<option>Velg</option>" );
		$("#period").html( "<option>Velg</option>" );
		$("#periodization").html( "<option>Velg</option>" );
		$("#periodization_start").html( "<option>Velg</option>" );
		$("#process_code").html( "<option>Velg</option>" );
		$("#tax_code").html( "<option>0</option>" );
		$("#approve_as").html( "<option>Velg</option>" );
		$("#order_text").html( 'Bestilling' );
		$("#invoice_id_text").html('FakturaNr');
		$("#close_order").html( '' );
		$("#close_order_orig").val( '' );
		$("#park_order").html( '' );
		$("#receipt").html('');

		var voucher_id = $(this).val();
		var oArgs = {menuaction:'property.uiinvoice2.get_first_line'};
		var requestUrl = phpGWLink('index.php', oArgs, true);

		var line_id = 0;

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl + "&voucher_id=" + voucher_id,
			success: function(data) {
				if( data != null)
				{
					line_id = data['line_id'];
					base_java_url['line_id'] = line_id;
					base_java_url['voucher_id_filter'] = voucher_id;
					execute_async(myDataTable_0);
					update_form_values(line_id, 0);
				}
			}
			});


	//	document.getElementById('image_content').src = '';
	});

	$("#approve_line").live("click", function(e){
		$("#receipt").html('');
		var line_id = $(this).val();
		var voucher_id_orig = $("#voucher_id").val();
		update_form_values(line_id, voucher_id_orig);
    });


	$("#voucher_form").live("submit", function(e){
		e.preventDefault();
		var line_id = $("#line_id").val();
		var voucher_id_orig = $("#voucher_id").val();
		if(!line_id)
		{
			alert('Du må velge linje i bilag');
			return;
		}

		var thisForm = $(this);
		var submitBnt = $(thisForm).find("input[type='submit']");
		var requestUrl = $(thisForm).attr("action");
		$.ajax({
			type: 'POST',
			url: requestUrl + "&phpgw_return_as=json&" + $(thisForm).serialize(),
			success: function(data) {
				if(data)
				{
					if(data.sessionExpired)
					{
						alert('Sesjonen er utløpt - du må logge inn på nytt');
						return;
					}

	    			var obj = data;
		    	
	    			var submitBnt = $(thisForm).find("input[type='submit']");
	    			if(obj.status == "updated")
	    			{
		    			$(submitBnt).val("Lagret");
				/*
						var oArgs = {menuaction:'property.uiinvoice2.get_vouchers'};
						var requestUrl = phpGWLink('index.php', oArgs, true);
						requestUrl = requestUrl + "&voucher_id_filter=" + $("#voucher_id").val();
						execute_async(myDataTable_0,requestUrl);
				*/

						base_java_url['voucher_id_filter'] = $("#voucher_id").val();
						base_java_url['line_id'] = line_id;
						execute_async(myDataTable_0);
					}
					else
					{
		    			$(submitBnt).val("Feil ved lagring");					
					}
		    				 
		    		// Changes text on save button back to original
		    		window.setTimeout(function() {
						$(submitBnt).val('Lagre Linje');
						$(submitBnt).addClass("not_active");
		    		}, 1000);

					var htmlString = "";
	   				if(typeof(data['receipt']['error']) != 'undefined')
	   				{
						for ( var i = 0; i < data['receipt']['error'].length; ++i )
						{
							htmlString += "<div class=\"error\">";
							htmlString += data['receipt']['error'][i]['msg'];
							htmlString += '</div>';
						}
	   				
	   				}
	   				if(typeof(data['receipt']['message']) != 'undefined')
	   				{
						for ( var i = 0; i < data['receipt']['message'].length; ++i )
						{
							htmlString += "<div class=\"msg_good\">";
							htmlString += data['receipt']['message'][i]['msg'];
							htmlString += '</div>';
						}
	   				
	   				}
	   				$("#receipt").html(htmlString);
	   				
	   				update_form_values(line_id, voucher_id_orig);
				}
			}
		});
	});
});


function update_form_values( line_id, voucher_id_orig ){
	var oArgs = {menuaction:'property.uiinvoice2.get_single_line'};
	var requestUrl = phpGWLink('index.php', oArgs, true);

	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: requestUrl + "&line_id=" + line_id,
		success: function(data) {
			if(data.sessionExpired)
			{
				alert('Sesjonen er utløpt - du må logge inn på nytt');
				return;
			}
			var voucher = data['voucher'];
			if( voucher != null && voucher.length > 0)
			{
				$("#line_id").val( line_id );
		
				var update_image = false;

				if(voucher_id_orig != voucher[0].voucher_id)
				{
					update_image = true;
				}
				$("#voucher_id").val( voucher[0].voucher_id );
				if( voucher[0].voucher_out_id )
				{
					$("#voucher_id_text").html( voucher[0].voucher_out_id );
				}
				else
				{
					$("#voucher_id_text").html( voucher[0].voucher_id );
				}

				$("#order_id").val( voucher[0].order_id );

				if(voucher[0].order_id)
				{
					var oArgs_order = {menuaction:'property.uiinvoice.view_order', order_id: voucher[0].order_id};
					var requestUrl_order = phpGWLink('index.php', oArgs_order);
					var htmlString_order  =  " <a target= \"_blank\" href=\"" + requestUrl_order + "\" title=\"" + voucher[0].status + "\" > Bestilling</a>";
					$("#order_text").html( htmlString_order );
				}
				else
				{
					$("#order_text").html( 'Bestilling' );
				}

				$("#project_group").val( voucher[0].project_group );

				if(update_image)
				{
					if(voucher[0].external_ref)
					{
						$("#invoice_id_text").html(voucher[0].external_ref );
						document.getElementById('image_content').src = voucher[0].image_url;
					}
					else
					{
						$("#invoice_id_text").html('FakturaNr');
						document.getElementById('image_content').src = '';
					}
				}

				$("#invoice_id").html( voucher[0].invoice_id );
				$("#kid_nr").html( voucher[0].kid_nr );
				$("#vendor").html( voucher[0].vendor );
				$("#invoice_date").html( voucher[0].invoice_date );
				$("#payment_date").html( voucher[0].payment_date );
				$("#b_account_id").val( voucher[0].b_account_id );
				$("#dim_a").val( voucher[0].dim_a );
				$("#currency").html( voucher[0].currency );
				$("#process_log").html( data['generic'].process_log );
				$("#my_initials").val( data['generic'].my_initials );
				$("#sign_orig").val( data['generic'].sign_orig );
				$("#line_text").val( voucher[0].line_text );

				if(voucher[0].merknad)
				{
					var oArgs_remark = {menuaction:'property.uiinvoice.remark', id: voucher[0].id};
					var requestUrl_remark = phpGWLink('index.php', oArgs_remark);
					var htmlString  =  " <a href=\"javascript:openwindow('" +requestUrl_remark + "','550','400')\" > Remark</a>";

					$("#remark").html( htmlString );
				}
//---------
				var checked_park_invoice = "";
				var park_invoice_status = "";
				if(voucher[0].parked)
				{
					checked_park_invoice = "checked = \"checked\"";
					var park_invoice_status = " X";
				}
				var htmlString_park_invoice = "<input type=\"checkbox\" name=\"values[park_invoice]\" value=\"1\" title=\"park invoice\"" + checked_park_invoice + "></input>" + park_invoice_status;
				$("#park_order").html( htmlString_park_invoice );
//---------
				var checked_close_order = "";
				var close_order_status = " " + voucher[0].status;
				if(voucher[0].closed)
				{
					checked_close_order = "checked = \"checked\"";
				}
				var htmlString_close_order = "<input type=\"checkbox\" name=\"values[close_order]\" value=\"1\" title=\"close order\"" + checked_close_order + "></input>" + close_order_status;
				$("#close_order").html( htmlString_close_order );
				$("#close_order_orig").val( voucher[0].closed );
//---------

				if(typeof(data['generic']['dimb_list']['options']) != 'undefined')
				{
					var htmlString = "";
					var obj = data['generic']['dimb_list']['options'];

					$.each(obj, function(i) {
						var selected = '';
						if(obj[i].id == voucher[0].dim_b)
						{
							selected = ' selected';
						}
						htmlString  += "<option value='" + obj[i].id + "'" + selected + ">" + obj[i].name + "</option>";
	    			});

					$("#dim_b").html( htmlString );
				}
				if(typeof(data['generic']['dime_list']['options']) != 'undefined')
				{
					var htmlString = "";
					var obj = data['generic']['dime_list']['options'];

					$.each(obj, function(i) {
						var selected = '';
						if(obj[i].id == voucher[0].dim_e)
						{
							selected = ' selected';
						}
						htmlString  += "<option value='" + obj[i].id + "'" + selected + ">" + obj[i].name + "</option>";
	    			});

					$("#dim_e").html( htmlString );
				}
				if(typeof(data['generic']['tax_code_list']['options']) != 'undefined')
				{
					var htmlString = "";

					htmlString  = "<option>Velg</option>"

					var obj = data['generic']['tax_code_list']['options'];

					$.each(obj, function(i) {
						var selected = '';
						if(obj[i].id == voucher[0].tax_code)
						{
							selected = ' selected';
						}
						htmlString  += "<option value='" + obj[i].id + "'" + selected + ">" + obj[i].name + "</option>";
	    			});

					$("#tax_code").html( htmlString );
				}

				if(typeof(data['generic']['period_list']['options']) != 'undefined')
				{
					var htmlString = "";
					var obj = data['generic']['period_list']['options'];

					$.each(obj, function(i) {
						var selected = '';
						if(obj[i].id == voucher[0].period)
						{
							selected = ' selected';
						}
						htmlString  += "<option value='" + obj[i].id + "'" + selected + ">" + obj[i].name + "</option>";
	    			});
					$("#period").html( htmlString );
				}
				if(typeof(data['generic']['periodization_list']['options']) != 'undefined')
				{
					var htmlString = "";

					var obj = data['generic']['periodization_list']['options'];

					$.each(obj, function(i) {
						var selected = '';
						if(obj[i].id == voucher[0].periodization)
						{
							selected = ' selected';
						}
						htmlString  += "<option value='" + obj[i].id + "'" + selected + ">" + obj[i].name + "</option>";
	    			});

					$("#periodization").html( htmlString );
				}
				if(typeof(data['generic']['periodization_start_list']['options']) != 'undefined')
				{
					var htmlString = "";

					var obj = data['generic']['periodization_start_list']['options'];

					$.each(obj, function(i) {
						var selected = '';
						if(obj[i].id == voucher[0].periodization_start)
						{
							selected = ' selected';
						}
						htmlString  += "<option value='" + obj[i].id + "'" + selected + ">" + obj[i].name + "</option>";
	    			});

					$("#periodization_start").html( htmlString );
				}

				if(typeof(data['generic']['process_code_list']['options']) != 'undefined')
				{
					var htmlString = "";

					var obj = data['generic']['process_code_list']['options'];
					$.each(obj, function(i) {
						var selected = '';
						if(obj[i].id == voucher[0].process_code)
						{
							selected = ' selected';
						}
						htmlString  += "<option value='" + obj[i].id + "'" + selected + ">" + obj[i].name + "</option>";
		    			});

					$("#process_code").html( htmlString );
				}

				if(typeof(data['generic']['approved_list']) != 'undefined')
				{
					for ( var i = 0; i < data['generic']['approved_list'].length; ++i )
					{
						var role_sign = data['generic']['approved_list'][i].role_sign;

						var role_initials = data['generic']['approved_list'][i].initials;

						if( data['generic']['approved_list'][i].date )
						{
							var htmlString = role_initials + ": " + data['generic']['approved_list'][i].date;
						}
						else
						{
							var htmlString = "<select id=\"_" + role_sign + "\" name=\"values[forward][" + role_sign + "]\">";
							var obj = data['generic']['approved_list'][i]['user_list'].options;
							$.each(obj, function(i) {
								var selected = '';
								if(obj[i].id == role_initials)
								{
									selected = ' selected';
								}
								htmlString  += "<option value='" + obj[i].id + "'" + selected + ">" + obj[i].name + "</option>";
				    			});
							htmlString  += "</select>";
						}

						$("#" + role_sign).html( htmlString );
					}
				}

				if(typeof(data['generic']['approve_list']['options']) != 'undefined')
				{
					var htmlString = "";

					var obj = data['generic']['approve_list']['options'];

					$.each(obj, function(i) {
						var selected = '';
						if(typeof(obj[i].selected) != 'undefined' && obj[i].selected == 1)
						{
							selected = ' selected';
						}
						htmlString  += "<option value='" + obj[i].id + "'" + selected + ">" + obj[i].name + "</option>";
		    			});

					$("#approve_as").html( htmlString );
				}
			}
			else
			{
				$("#line_text").val( '' );
				$("#voucher_id").val( '' );
				$("#voucher_id_text").html( '' );
				$("#order_id").val( '' );
				$("#project_group").val( '' );
				$("#invoice_id").html( '' );
				$("#kid_nr").html( '' );
				$("#vendor").html('' );
				$("#close_order_orig").val( '' );
				$("#my_initials").val( '' );
				$("#sign_orig").val( '' );
				$("#invoice_date").html( '' );
				$("#payment_date").html( '' );
				$("#b_account_id").val( '' );
				$("#currency").html( '' );
				$("#oppsynsmannid").html( '' );
				$("#saksbehandlerid").html( '' );
				$("#budsjettansvarligid").html( '' );
				$("#remark").html( '' );
				$("#process_log").html( '' );
				$("#dim_a").val('' );
				$("#dim_b").html( "<option>Velg</option>" );
				$("#dim_e").html( "<option>Velg</option>" );
				$("#period").html( "<option>Velg</option>" );
				$("#periodization").html( "<option>Velg</option>" );
				$("#periodization_start").html( "<option>Velg</option>" );
				$("#process_code").html( "<option>Velg</option>" );
				$("#tax_code").html( "<option>0</option>" );
				$("#approve_as").html( "<option>Velg</option>" );
				$("#order_text").html( 'Bestilling' );
				$("#invoice_id_text").html('FakturaNr');
				$("#receipt").html('');
				document.getElementById('image_content').src = '';
			}
		}
	});
}
