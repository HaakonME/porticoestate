
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
		
		var valuesInputFilter = {};
		$('#query').on( 'keyup change', function () 
		{
			if ( $.trim($(this).val()) != $.trim(valuesInputFilter[i]) ) 
			{
				filterDataLocations('query', $(this).val());
				valuesInputFilter[i] = $(this).val();
			}
		});
		
		
		$('#contracts_search_option').change( function() 
		{
			filterDataContracts('search_option', $(this).val());
		});
		
		var valuesInputFilter = {};
		$('#contracts_query').on( 'keyup change', function () 
		{
			if ( $.trim($(this).val()) != $.trim(valuesInputFilter[i]) ) 
			{
				filterDataContracts('query', $(this).val());
				valuesInputFilter[i] = $(this).val();
			}
		});
		
		$('#contract_status').change( function() 
		{
			filterDataContracts('contract_status', $(this).val());
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