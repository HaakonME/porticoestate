<?php
	include("common.php");
?>



<script type="text/javascript">

	YAHOO.util.Event.addListener(
		'ctrl_add_rental_party',
		'click',
		function(e)
		{
            YAHOO.util.Event.stopEvent(e);
            window.location = 'index.php?menuaction=rental.uiparty.add';
        }
   );
</script>

<?php echo rental_uicommon::get_page_error($error) ?>
<?php echo rental_uicommon::get_page_message($message) ?>

<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/x-office-address-book.png" /> <?php echo lang('sync_parties_identifier') ?></h1>

<fieldset>
	<input type="submit" name="ctrl_sync_rental_party" id="ctrl_sync_rental_party" value="<?php echo lang('f_sync_party') ?>" />
</fieldset>

<p>
	Synkroniser kontraktsparter basert på identifikatoren til kontraktspartene. 
</p>

<?php
	$list_form = true;
	$list_id = 'sync_parties_identifier';
	$url_add_on = '&amp;type=sync_parties_identifier';
	$extra_cols = array(
		array("key" => "service_id", "label" => lang('service_id'), "index" => 3),
		array("key" => "responsibility_id", "label" => lang('responsibility_id'), "index" => 4),
		array("key" => "identifier", "label" => lang('identifier'), "index" => 5),
		array("key" => "sync_message", "label" => lang('sync_message'), "index" => 6),
		array("key" => "service_exist", "label" => lang('service_exist'), "index" => 7)
	);
	include('party_list_partial.php');
?>