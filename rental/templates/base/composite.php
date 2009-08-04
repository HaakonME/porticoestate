<?php 
	include("common.php");
	phpgwapi_yui::load_widget('tabview');        
  phpgwapi_yui::tabview_setup('composite_tabview');
?>

<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/actions/go-home.png" /> <?php echo lang('rental_common_showing_composite') ?> <em><?php echo $composite->get_name() ?></em></h1>

<div id="composite_tabview" class="yui-navset">
	<ul class="yui-nav">
		<li class="selected"><a href="#rental_rc_details"><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/actions/go-home.png" alt="icon" /> <?php echo lang('rental_rc_details') ?></em></a></li>
		<li><a href="#rental_rc_elements"><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/x-office-drawing-template.png" alt="icon" /> <?php echo lang('rental_rc_elements') ?></em></a></li>
		<li><a href="#rental_rc_contracts"><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/text-x-generic.png" alt="icon" />   <?php echo lang('rental_rc_contracts') ?></em></a></li>
	</ul>
	
	<div class="yui-content">
		<div id="details">
			<form action="#" method="post">
				<dl class="proplist-col">
					<dt>
						<label for="name"><?php echo lang('rental_rc_name') ?></label>
					</dt>
					<dd>
						<?php
							if ($editable) {
								echo '<input type="text" name="name" id="name" value="' . $composite->get_name() . '"/>';
							} else {
								echo $composite->get_name();
							}
						?>
					</dd>
					
					<dt>
						<label for="name"><?php echo lang('rental_rc_address') ?></label>
					</dt>
					<dd>
						<?php
							if (!$editable && $composite->has_custom_address()) {
								// In view mode the custom address should be displayed if it's filled in
								echo $composite->get_custom_address_1() . "<br />";
								
								if ($composite->get_custom_address_2()) {
									echo ', ' . $composite->get_custom_address_2();
								}
								
								if ($composite->get_custom_house_number()) {
									echo ' ' . $composite->get_custom_house_number();
								}
								
								if ($composite->get_custom_postcode()) {
									echo '<br />' . $composite->get_custom_postcode() . ' ' . $composite->get_custom_place();
								}
							} else {
								// Always show the original address if in edit mode, or if the
								// custom address isn't filled in
								echo $composite->get_address_1();
								
								if ($composite->get_address_2()) {
									echo ', ' . $composite->get_address_2();
								}
								
								if ($composite->get_house_number()) {
									echo ' ' . $composite->get_house_number();
								}
								
								if ($composite->get_postcode()) {
									echo '<br />' . $composite->get_postcode() . ' ' . $composite->get_place();
								}
							}
						?>
					</dd>
					
					<?php if ($editable) { // Only show custom address fields if we're in edit mode ?>
					<dt>
						<label for="address_1"><?php echo lang('rental_rc_overridden_address') ?></label>
						/ <label for="house_number"><?php echo lang('rental_rc_house_number') ?></label>
					</dt>
					<dd>
						<input type="text" name="address_1" id="address_1" value="<?php echo $composite->get_custom_address_1() ?>" />
						<input type="text" name="house_number" id="house_number" value="<?php echo $composite->get_custom_house_number() ?>" />
					</dd>
					<dt>
						<label for="postcode"><?php echo lang('rental_rc_post_code') ?></label>
						/ <label for="place"><?php echo lang('rental_rc_post_place') ?></label>
					</dt>
					<dd>
						<input type="text" name="postcode" id="postcode" class="postcode" value="<?php echo $composite->get_custom_postcode() ?>"/>
						<input type="text" name="place" id="place" value="<?php echo $composite->get_custom_place() ?>"/>
					</dd>
					<?php } // if ($editable) ?>
				</dl>
				
				<dl class="proplist-col">
					<dt><?php echo lang('rental_rc_serial') ?></dt>
					<dd><?php echo $composite->get_id() ?></dd>
					<dt><?php echo lang('rental_rc_area_gros') ?>></dt>
					<dd><?php echo $composite->get_area_gros() ?> m<sup>2</sup></dd>
					<dt><?php echo lang('rental_rc_area_net') ?></dt>
					<dd><?php echo $composite->get_area_net() ?> m<sup>2</sup></dd>
					<dt><?php echo lang('rental_rc_propertyident') ?></dt>
					<dd><?php echo $composite->get_gab_id() ?></dd>
					
					<dt>
						<label for="is_active"><?php echo lang('rental_rc_available?') ?></label>
					</dt>
					<dd>
						<input type="checkbox" name="is_active" id="is_active"<?php echo $composite->is_active() ? ' checked="checked"' : '' ?> <?php echo !$editable ? ' disabled="disabled"' : '' ?>/>
					</dd>
				</dl>
				
				<dl class="rental-description-edit">
					<dt>
						<label for="description"><?php echo lang('rental_rc_description') ?></label>
					</dt>
					<dd>
						<textarea name="description" id="description" rows="10" cols="50" <?php echo !$editable ? ' disabled="disabled"' : '' ?>>
							<?php echo $composite->get_description() ?>
						</textarea>
					</dd>
				</dl>
				
				<div class="form-buttons">
					<?php
						if ($editable) {
							echo '<input type="submit" name="save" value="' . lang('rental_rc_save') . '"/>';
							echo '<a class="cancel" href="' . $cancel_link . '">' . lang('rental_rc_cancel') . '</a>';
						} else {
							echo '<a class="cancel" href="' . $cancel_link . '">' . lang('rental_rc_back') . '</a>';
						}
					?>
				</div>
			</form>
		</div>
		<div id="elements">
			<h3><?php echo lang('rental_rc_added_areas') ?></h3>
			<div id="added-areas-datatable-container" class="datatable_container"></div>
			<?php if ($editable) { ?>
				<h3><?php echo lang('rental_rc_add_area') ?></h3>
				<form id="available_areas_form" method="GET">
					<fieldset>
						<!-- Filters -->
						<h3><?php echo lang('rental_rc_toolbar_filters') ?></h3>
						<label for="ctrl_toggle_level"><?php echo lang('rental_rc_level') ?></label>
						<select name="level" id="ctrl_toggle_level">
							<option value="1"><?php echo lang('rental_rc_property') ?></option>
							<option value="2" default=""><?php echo lang('rental_rc_building') ?></option>
							<option value="3"><?php echo lang('rental_rc_floor') ?></option>
							<option value="4"><?php echo lang('rental_rc_level') ?></option>
							<option value="5"><?php echo lang('rental_rc_section') ?></option>
						</select>
						
						<label class="toolbar_element_label" for="calendarPeriodFrom"><?php echo lang('rental_rc_available_at') ?></label>
						<input type="text" name="available_date" id="available_date" size="10"/>
						<input type="hidden" name="available_date_hidden" id="available_date_hidden"/>
						<div id="calendarPeriodFrom">
						</div>
						
						<input type="submit" id="ctrl_search_button" value="<?php echo lang('rental_rc_search') ?>" />
						<input type="button" id="ctrl_reset_button" value="<?php echo lang('rental_reset') ?>" />
					</fieldset>
					<div id="available-areas-datatable-container" class="datatable_container"></div>
				</form>
			<?php } ?>
		</div>
		<div id="contracts">
			<form id="contracts_form" method="GET">
				<fieldset>
					<!-- Filters -->
					<h3><?php echo lang('rental_rc_toolbar_filters') ?></h3>
					<label class="toolbar_element_label" for="ctrl_toggle_contract_status"><?php echo lang('rental_rc_contract_status') ?></label>
						<select name="contract_status" id="ctrl_toggle_contract_status">
							<option value="active" default=""><?php echo lang('rental_rc_active') ?></option>
							<option value="not_started"><?php echo lang('rental_rc_not_started') ?></option>
							<option value="both"><?php echo lang('rental_rc_ended') ?></option>
						</select>
					
					<input type="submit" id="ctrl_search_button" value="<?php echo lang('rental_rc_search') ?>" />
					<input type="button" id="ctrl_reset_button" value="<?php echo lang('rental_reset') ?>" />
				</fieldset>
				<div id="contracts-container" class="datatable_container"></div>
			</form>
		</div>
	</div>
</div>


<script type="text/javascript">
	YAHOO.util.Event.onDOMReady(function() {

		//initCalendar('available_date', 'calendarPeriodFrom', 'cal1', 'Velg dato');
		
		
		//Columns for added areas datatable
		var addedAreasColumnDefs = [{
			key: "location_code",
			label: "<?php echo lang('rental_rc_id') ?>",
		  sortable: true
		},
		{
			key: "loc1_name",
			label: "<?php echo lang('rental_rc_property') ?>",
		  sortable: true
		},
		{
			key: "loc2_name",
			label: "<?php echo lang('rental_rc_building') ?>",
		  sortable: false
		},
		{
			key: "loc3_name",
			label: "<?php echo lang('rental_rc_section') ?>",
		  sortable: false
		},
		{
			key: "address",
			label: "<?php echo lang('rental_rc_address') ?>",
		  sortable: false
		},
		{
			key: "area_gros",
			label: "<?php echo lang('rental_rc_area_gros') ?>",
		  sortable: false
		},
		{
			key: "area_net",
			label: "<?php echo lang('rental_rc_area_net') ?>",
		  sortable: false
		},
		{
			key: "actions",
			hidden: true
		}
		];

		// Initiating the data source
		setDataSource(
				'index.php?menuaction=rental.uicomposite.query&amp;phpgw_return_as=json&amp;type=included_areas&amp;id=<?php echo $composite->get_id() ?>',
				addedAreasColumnDefs,
				'added_areas_form',
				[],
				'added-areas-datatable-container',
				1,
				['<?php echo lang('rental_cm_remove') ?>'],
				['remove_unit']	
		);

		//Columns for available areas datatable
		var availableAreasColumnDefs = [{
			key: "location_code",
			label: "<?php echo lang('rental_rc_id') ?>",
		  sortable: true
		},
		{
			key: "loc1_name",
			label: "<?php echo lang('rental_rc_property') ?>",
		  sortable: true
		},
		{
			key: "loc2_name",
			label: "<?php echo lang('rental_rc_building') ?>",
		  sortable: false
		},
		{
			key: "loc3_name",
			label: "<?php echo lang('rental_rc_section') ?>",
		  sortable: false
		},
		{
			key: "address",
			label: "<?php echo lang('rental_rc_address') ?>",
		  sortable: false
		},
		{
			key: "area_gros",
			label: "<?php echo lang('rental_rc_area_gros') ?>",
		  sortable: false
		},
		{
			key: "area_net",
			label: "<?php echo lang('rental_rc_area_net') ?>",
		  sortable: false
		},
		{
			key: "occupied",
			label: "<?php echo lang('rental_rc_availibility') ?>",
		  sortable: false
		},
		{
			key: "actions",
			hidden: true
		}
		];
		
		// Initiating the data source
		setDataSource(
				'index.php?menuaction=rental.uicomposite.query&amp;phpgw_return_as=json&amp;type=available_areas&amp;id=<?php echo $composite->get_id() ?>',
				availableAreasColumnDefs,
				'available_areas_form',
				['crtl_toggle_level'],
				'available-areas-datatable-container',
				2,
				['<?php echo lang('rental_cm_add') ?>'],
				['add_unit']	
		);

		
		// Columns for contracts table 
		var contractsColumnDefs = [{
			key: "id",
			label: "<?php echo lang('rental_rc_id') ?>",
		  sortable: true
		},
		{
			key: "date_start",
			label: "<?php echo lang('rental_rc_date_start') ?>",
		  sortable: true
		},
		{
			key: "date_end",
			label: "<?php echo lang('rental_rc_date_end') ?>",
		  sortable: false
		},
		{
			key: "tenant",
			label: "<?php echo lang('rental_common_party') ?>",
		  sortable: false
		},
		{
			key: "actions",
			hidden: true
		}
		];
		
		// Initiating the data source
		setDataSource(
				'index.php?menuaction=rental.uicomposite.query&amp;phpgw_return_as=json&amp;type=contracts&amp;id=<?php echo $composite->get_id() ?>',
				availableAreasColumnDefs,
				'contracts_form',
				['ctrl_toggle_contract_status'],
				'contracts-container',
				3,
				['<?php echo lang('rental_cm_show') ?>', '<?php echo lang('rental_cm_edit') ?>'],
				['view_contract', 'edit_contract']	
		);
	});	
</script>