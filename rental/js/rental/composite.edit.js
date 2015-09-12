
	$(document).ready(function () 
	{
		$('#type_id').change( function() 
		{
			filterDataLocations('type_id', $(this).val());
		});

		$('#search_option').change( function() 
		{
			filterDataLocations('search_option', $(this).val());
		});
		
		var previous_query = '';
		$('#query').on( 'keyup change', function () 
		{
			if ( $.trim($(this).val()) != $.trim(previous_query) ) 
			{
				filterDataLocations('search', {'value': $(this).val()});
				previous_query = $(this).val();
			}
		});
		
		
		$('#contracts_search_option').change( function() 
		{
			filterDataContracts('search_option', $(this).val());
		});
		
		var previous_contract_query = '';
		$('#contracts_query').on( 'keyup change', function () 
		{
			if ( $.trim($(this).val()) != $.trim(previous_contract_query) ) 
			{
				filterDataContracts('search', {'value': $(this).val()});
				previous_contract_query = $(this).val();
			}
		});
		
		$('#contract_status').change( function() 
		{
			filterDataContracts('contract_status', $(this).val());
		});
		
		var previous_status_date;
		$("#status_date").on('keyup change', function ()
		{
			if ( $.trim($(this).val()) != $.trim(previous_status_date) ) 
			{
				filterDataContracts('status_date', $(this).val());
				previous_status_date = $(this).val();
			}
		});
	
		$('#contract_type').change( function() 
		{
			filterDataContracts('contract_type', $(this).val());
		});
	});

	function filterDataLocations(param, value)
	{
		oTable1.dataTableSettings[1]['ajax']['data'][param] = value;
		oTable1.fnDraw();
	}
	
	function filterDataContracts(param, value)
	{
		oTable2.dataTableSettings[2]['ajax']['data'][param] = value;
		oTable2.fnDraw();
	}
	
	function formatterArea (key, oData) 
	{
		var amount = $.number( oData[key], decimalPlaces, decimalSeparator, thousandsSeparator ) + ' ' + area_suffix;
		return amount;
	}
	
	function formatterPrice (key, oData) 
	{
		var amount = $.number( oData[key], decimalPlaces, decimalSeparator, thousandsSeparator ) + ' ' + currency_suffix;
		return amount;
	}
	
	downloadContracts = function(oArgs){

		if(!confirm("This will take some time..."))
		{
			return false;
		}

		var requestUrl = phpGWLink('index.php', oArgs);

		requestUrl += '&search_option=' + $('#contracts_search_options').val();
		requestUrl += '&search=' + $('#contracts_query').val();
		requestUrl += '&contract_type=' + $('#contract_type').val();
		requestUrl += '&contract_status=' + $('#contract_status').val();

		window.open(requestUrl,'_self');
	};