YAHOO.util.Event.addListener(window, "load", function() {
	YAHOO.booking.autocompleteHelper('index.php?menuaction=booking.uipermission_root.index_accounts&phpgw_return_as=json&', 
                                     'field_subject_name', 'field_subject_id', 'subject_container');
});