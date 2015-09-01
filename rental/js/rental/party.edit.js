
$(document).ready(function()
{
	$("#date_start").change(function(){

		var date_start = $("#date_start").val();
		var billing_start = $("#billing_start_date").val();
		if(!billing_start)
		{
			$("#billing_start_date").val(date_start);
		}
	});

	$("#date_end").change(function(){

		var date_end = $("#date_end").val();
		var billing_end_date = $("#billing_end_date").val();
		if(!billing_end_date)
		{
			$("#billing_end_date").val(date_end);
		}
	});

	/******************************************************************************/
	
	$('#contract_search_options').change( function() 
	{
		filterDataContract('search_option', $(this).val());
	});

	var previous_party_query = '';
	$('#contract_query').on( 'keyup change', function () 
	{
		if ( $.trim($(this).val()) != $.trim(previous_party_query) ) 
		{
			filterDataContract('search', {'value': $(this).val()});
			previous_party_query = $(this).val();
		}
	});

	$('#contract_status').change( function() 
	{
		filterDataContract('contract_status', $(this).val());
	});

	$('#contract_type').change( function() 
	{
		filterDataContract('contract_type', $(this).val());
	});	
	
	/******************************************************************************/
	
	$('#document_search_option').change( function() 
	{
		filterDataDocument('search_option', $(this).val());
	});

	var previous_document_query = '';
	$('#document_query').on( 'keyup change', function () 
	{
		if ( $.trim($(this).val()) != $.trim(previous_document_query) ) 
		{
			filterDataDocument('search', {'value': $(this).val()});
			previous_document_query = $(this).val();
		}
	});

	$('#document_type_search').change( function() 
	{
		filterDataDocument('document_type', $(this).val());
	});
	
	/******************************************************************************/
	
	$('#upload_button').on('click', function() 
	{
		
		if ($('#ctrl_upoad_path').val() === '') {
			alert('no file selected');
			return false;
		}
		if ($.trim($('#document_title').val()) === '') {
			alert('enter document title');
			return false;
		}
		
		var form = document.forms.namedItem("form_upload");
		var file_data = $('#ctrl_upoad_path').prop('files')[0];            
		var form_data = new FormData(form);
		form_data.append('file_path', file_data);
		form_data.append('document_type', $('#document_type').val());
		form_data.append('document_title', $('#document_title').val());
		
		var nTable = 1;
		$.ajax({
			url: link_upload_document,
			cache: false,
			contentType: false,
			processData: false,
			data: form_data,                         
			type: 'post',
			success: function(result){
				JqueryPortico.show_message(nTable, result);
				$('#document_type')[0].selectedIndex = 0;
				$('#document_title').val('');
				$('#ctrl_upoad_path').val('');
				oTable1.fnDraw();
			}
		});
	});
});

function filterDataContract(param, value)
{
	oTable0.dataTableSettings[0]['ajax']['data'][param] = value;
	oTable0.fnDraw();
}

function filterDataDocument(param, value)
{
	oTable1.dataTableSettings[1]['ajax']['data'][param] = value;
	oTable1.fnDraw();
}

function onGetSync_data(requestUrl)
{
	var org_enhet_id = document.getElementById('org_enhet_id').value;
	
	if( org_enhet_id > 0)
	{
		var data = {"org_enhet_id": org_enhet_id};
		JqueryPortico.execute_ajax(requestUrl, function(result){
			setSyncInfo(result);
		}, data, "POST", "JSON");		
	}
	else {
		alert(msg_get_syncData);
	}
}

function setSyncInfo(syncInfo)
{
	document.getElementById('email').value = syncInfo.email;
	document.getElementById('company_name').value = syncInfo.org_name;
	document.getElementById('department').value = syncInfo.department;
	document.getElementById('unit_leader').value = syncInfo.unit_leader_fullname;
}

function formatterPrice (key, oData) 
{
	var amount = $.number( oData[key], decimalPlaces, decimalSeparator, thousandsSeparator ) + ' ' + currency_suffix;
	return amount;
}
	
function formatterArea (key, oData) 
{
	var amount = $.number( oData[key], decimalPlaces, decimalSeparator, thousandsSeparator ) + ' ' + area_suffix;
	return amount;
}