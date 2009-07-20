<?php
	include("common.php");
?>

<h1><img src="<?= RENTAL_TEMPLATE_PATH ?>images/32x32/mimetypes/x-office-spreadsheet.png" /> <?= lang('rental_menu_price_list') ?></h1>

<form id="list_form" method="GET">

	<?php 
	if($this->hasWritePermission())
	{
	?>
	<fieldset>
		<!-- Create new rental composite -->
		<h3><?= lang('rental_price_item_toolbar_new') ?></h3>
		<label for="ctrl_add_price_item_name"><?= lang('rental_rc_name') ?></label>
		<input type="text" id="ctrl_add_price_item_name" name="ctrl_add_price_item_name"/>
		<input type="submit" name="ctrl_price_item" id="ctrl_price_item" value="<?= lang('rental_price_item_toolbar_functions_new_price_item') ?>" />
	</fieldset>
	<?php 
	}
	?>
</form>

<div id="paginator" class="paginator"></div>
<div id="datatable-container" class="datatable_container"></div>

<script type="text/javascript">
	YAHOO.util.Event.addListener(
		'ctrl_add_rental_composite', 
		'click', 
		function(e)
		{    	
	  	YAHOO.util.Event.stopEvent(e);
	  	newName = document.getElementById('ctrl_add_rental_composite_name').value;
			window.location = 'index.php?menuaction=rental.uicomposite.add&amp;rental_composite_name=' + newName;
		}
	);
	
	// Defining columns for datatable
	var columnDefs = [
		{
			key: "title",
			label: "<?= lang('rental_rc_name') ?>",
		  sortable: true
		},
		{
			key: "agresso_id",
			label: "<?= lang('rental_rc_agresso_id') ?>",
		  sortable: false
		},
		{
			key: "is_area",
			label: "<?= lang('rental_rc_type') ?>",
		  sortable: true
		},
		{
			key: "price",
			label: "<?= lang('rental_rc_price') ?>",
			sortable: true
		},
		{
			key: "labels",
			hidden: true
		}];
		
	// Initiating the data source
	setDataSource(
		'index.php?menuaction=rental.uiadmin.query&amp;phpgw_return_as=json&amp;type=price_item_list',
		columnDefs,
		'list_form',
		['ctrl_toggle_active_rental_composites','ctrl_toggle_occupancy_of_rental_composites','ctrl_search_query'],
		'datatable-container',
		1,
		['<?= lang('rental_cm_show') ?>','<?= lang('rental_cm_edit') ?>'],
		['view','edit']	
	);
</script>