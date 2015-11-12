var building_id_selection = "";
$(document).ready(function() {
    JqueryPortico.autocompleteHelper(phpGWLink('bookingfrontend/', {menuaction: 'bookingfrontend.uibuilding.index'}, true), 'field_building_name', 'field_building_id', 'building_container' );
    JqueryPortico.autocompleteHelper(phpGWLink('bookingfrontend/', {menuaction: 'bookingfrontend.uiorganization.index'}, true), 'field_org_name', 'field_org_id', 'org_container');

    $("#field_activity").change(function(){
        var oArgs = {menuaction:'bookingfrontend.uiapplication.get_activity_data', activity_id:$(this).val()};
        var requestUrl = phpGWLink('bookingfrontend/', oArgs, true);

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: requestUrl,
            success: function(data) {
                var html_agegroups = '';
                var html_audience = '';

                if( data != null)
                {
                    var agegroups = data.agegroups;
                    for ( var i = 0; i < agegroups.length; ++i )
                    {
                        html_agegroups += "<tr>";
                        html_agegroups += "<th>" + agegroups[i]['name'] + "</th>";
                        html_agegroups += "<td>";
                        html_agegroups += "<input class=\"input50\" type=\"text\" name='male[" +agegroups[i]['id'] + "]' value='0'></input>";
                        html_agegroups += "</td>";
                        html_agegroups += "<td>";
                        html_agegroups += "<input class=\"input50\" type=\"text\" name='female[" +agegroups[i]['id'] + "]' value='0'></input>";
                        html_agegroups += "</td>";
                        html_agegroups += "</tr>";
                    }
                    $("#agegroup_tbody").html( html_agegroups );

                    var audience = data.audience;
                    for ( var i = 0; i < audience.length; ++i )
                    {
                            html_audience += "<li>";
                            html_audience += "<label>";
                            html_audience += "<input type=\"radio\" name=\"audience[]\" value='" +audience[i]['id'] + "'></input>";
                            html_audience += audience[i]['name'];
                            html_audience += "</label>";
                            html_audience += "</li>";
                    }
                    $("#audience").html( html_audience );
                }
            }
        });
    });
});
    
$(window).load(function() {
    var building_id = $('#field_building_id').val();
    if(building_id) {
        populateTableChkResources(building_id, initialSelection);
        building_id_selection = building_id;
    }
    $("#field_building_name").on("autocompleteselect", function(event, ui){
        var building_id = ui.item.value;        
        if (building_id != building_id_selection){
            populateTableChkResources(building_id, []);
            building_id_selection = building_id;
        }
    });
});

$.formUtils.addValidator({
    name: 'target_audience',
    validatorFunction: function(value, $el, config, languaje, $form) {
        var n = 0;
        $('#audience input[name="audience[]"]').each(function(){
           if ($(this).is(':checked')) {
               n++;
           }
        });
        var v = (n > 0) ? true : false;
        return v;
    },
    errorMessage: 'Please choose at least 1 target audience',
    errorMessageKey: ''
})

$.formUtils.addValidator({
    name: 'number_participants',
    validatorFunction: function(value, $el, config, languaje, $form) {
        var n = 0;
        $('#agegroup_tbody input').each(function() {
            if ($(this).val() != "" && $(this).val() > 0) {
                n++;
            } 
        });
        var v = (n > 0) ? true : false;
        return v;
    },
    errorMessage: 'Number of participants is required',
    errorMessageKey: ''
});

$.formUtils.addValidator({
    name: 'customer_identifier',
    validatorFunction: function(value, $el, config, languaje, $form) {
        var v = true;
        var customer_ssn = $('#field_customer_ssn').val();
        var customer_organization_number = $('#field_customer_organization_number').val();
        var cost = $('#field_cost').val();
        if ( (customer_ssn == "" && customer_organization_number == "") && (cost > 0) ) {
           v = false;
        }
        return v;
    },
    errorMessage: 'There is set a cost, but no invoice data is filled inn',
    errorMessageKey: ''
});

$.formUtils.addValidator({
    name: 'application_dates',
    validatorFunction: function(value, $el, config, languaje, $form) {
        var n = 0;
        if ($('input[name="from_[]"]').length == 0 || $('input[name="from_[]"]').length == 0) {
            return false;
        }
        $('input[name="from_[]"]').each(function(){
            if ($(this).val() == "") {
                $($(this).addClass("error").css("border-color","red"));
                n++;
            } else {
                $($(this).removeClass("error").css("border-color",""));
            }
        });
        $('input[name="to_[]"]').each(function(){
            if ($(this).val() == "") {
                $($(this).addClass("error").css("border-color","red"));
                n++;
            } else {
                $($(this).removeClass("error").css("border-color",""));
            }
        });
        var v = (n == 0) ? true : false;
        return v;
    },
    errorMessage: 'Invalida date',
    errorMessageKey: ''
});

function populateTableChkResources (building_id, selection) {
    var url = phpGWLink('bookingfrontend/', {menuaction: 'booking.uiresource.index', sort: 'name', filter_building_id: building_id}, true);
    var container = 'resources_container';
    var colDefsResources = [{label: '', object: [{type: 'input', attrs: [{name: 'type', value: 'checkbox'},{name: 'name', value: 'resources[]'},{name: 'data-validation', value: 'checkbox_group'},{name: 'data-validation-qty', value: 'min1'},{name: 'data-validation-error-msg', value: 'Please choose at least 1 resource'}]}], value: 'id', checked: selection},{key: 'name', label: lang['Name']}, {key: 'type', label: lang['Resource Type']}];
    populateTableChk(url, container, colDefsResources);
}

function populateTableChk (url, container, colDefs) {    
    createTable(container,url,colDefs);
}


/*

populateResourceTable = function(building_id, selection) {
    YAHOO.booking.checkboxTableHelper('resources_container', 'index.php?menuaction=booking.uiresource.index&sort=name&filter_building_id=' +  building_id + '&phpgw_return_as=json&',
    'resources[]', selection, {additional_fields: [{key: 'type', label: lang['Resource Type']}]});
}

var createFromToDatePickerSection = function(containerEl) {
	if (!this.counter) { this.counter = 0; }
	containerEl.className = 'date-container';
	containerEl.innerHTML = '							' +
'			<a href="#" class="close-btn">-</a>		' +
'			<div><label>'+lang['From']+'</label></div>				' +
'			<div class="datetime-picker">			' +
'				<input id="js_date_'+this.counter+'_from" type="text" name="from_[]">	' +
'			</div>									' +
'			<div><label>'+lang['To']+'</label></div>				' +
'			<div class="datetime-picker">			' +
'				<input id="js_date_'+this.counter+'_to" type="text" name="to_[]">	' +
'			</div>';
	this.counter++;
}

removeDateRow = function(e) {
	this.parentNode.parentNode.removeChild(this.parentNode);
	YAHOO.util.Event.stopEvent(e);
}

YAHOO.util.Event.addListener(window, "load", function() {
	var Dom = YAHOO.util.Dom;
    var building_id = YAHOO.util.Dom.get('field_building_id').value;
    if(building_id) {
        populateResourceTable(building_id, YAHOO.booking.initialSelection);
    }
    var ac = YAHOO.booking.autocompleteHelper('index.php?menuaction=booking.uibuilding.index&phpgw_return_as=json&', 
                                              'field_building_name', 'field_building_id', 'building_container');
    // Update the resource table as soon a new building is selected
    ac.itemSelectEvent.subscribe(function(sType, aArgs) {
        populateResourceTable(aArgs[2].id, []);
    });
	Dom.getElementsByClassName('close-btn', 'a', null, function(a) {
		a.onclick = removeDateRow;
	});
	// Add more From-To datepicker pairs when the user clicks on the add link/button
	YAHOO.util.Event.addListener("add-date-link", "click", function(e) {
		var container = Dom.get('dates-container');
		var div = document.createElement('div');

		createFromToDatePickerSection(div);	
	
		container.appendChild(div);
		var a = div.getElementsByTagName('a')[0];
		a.onclick = removeDateRow;
		YAHOO.booking.setupDatePickers();
		YAHOO.util.Event.stopEvent(e);
	});
    YAHOO.booking.autocompleteHelper('index.php?menuaction=booking.uiorganization.index&phpgw_return_as=json&', 
                                     'field_org_name', 'field_org_id', 'org_container');
});


*/