<script type="text/javascript">
	//Add listener resetting form: redirects browser to call index  again
	YAHOO.util.Event.addListener(
		'ctrl_reset_button',
		'click',
		function(e)
		{
			YAHOO.util.Event.stopEvent(e);
	 		window.location = 'index.php?menuaction=rental.uidelegate.index';
		}
		);

	// Defining columns for datatable
	var columnDefs = [{
			key: "account_lastname",
			label: "<?php echo lang('account_lastname') ?>",
			sortable: true
		},
		{
			key: "account_firstname",
			label: "<?php echo lang('account_firstname') ?>",
			sortable: true
		},
		{
			key: "actions",
			hidden: true
		},
		{
			key: "labels",
			hidden: true
		},
		{
			key: "ajax",
			hidden: true
		}];

	// Initiating the data source
	setDataSource(
		'index.php?menuaction=rental.uidelegate.query&amp;phpgw_return_as=json<?php echo $url_add_on; ?>&amp;editable=<?php echo isset($editable) && $editable ? "true" : "false"; ?>',
		columnDefs,
		'<?php echo $list_id ?>_form',
		'<?php echo $list_id ?>_ctrl_search_query',
		'<?php echo $list_id ?>_container',
		'<?php echo $list_id ?>_paginator',
		'<?php echo $list_id ?>',
		'<?php echo isset($editor_action) ? $editor_action : '' ?>'
	);

</script>

<?php
	if($list_form)
	{
?>
<form id="<?php echo $list_id ?>_form" method="GET">
	<fieldset>
	
		<input type="hidden" name="account_id" value="{search/account_id}"/>
		<label><?php echo lang('username') ?> </label>
		<input type="text" name="username" value="{search/username}"/><input type="submit" name="search" value="<?php echo lang('btn_search') ?>"/>
		<br/>
		<label><?php echo lang('firstname') ?> </label>
		<input type="text" name="firstname" readonly="" value="{search/firstname}" style="background-color: #CCCCCC;"/>
		<br/>
		<label><?php echo lang('lastname') ?> </label>
		<input type="text" name="lastname" readonly="" value="{search/lastname}" style="background-color: #CCCCCC;"/>
		<br/>
		<label><?php echo lang('email') ?> </label>
		<input type="text" name="email" readonly="" value="{search/email}" style="background-color: #CCCCCC;"/>
		<br/>
		<input type="submit" name="add" value="<?php echo lang('btn_add') ?>"/>
	</fieldset>

</form>
<?php
	} // end if($list_form)
?>

<div id="<?php echo $list_id ?>_paginator" class="paginator"></div>
<div id="<?php echo $list_id ?>_container" class="datatable_container"></div>