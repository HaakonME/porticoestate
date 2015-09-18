var building_id_selection = "";
$(document).ready(function() {
    JqueryPortico.autocompleteHelper('index.php?menuaction=booking.uibuilding.index&phpgw_return_as=json&', 
                                                  'field_building_name', 'field_building_id', 'building_container');

    JqueryPortico.autocompleteHelper('index.php?menuaction=booking.uiorganization.index&phpgw_return_as=json&', 
                                         'field_org_name', 'field_org_id', 'org_container');
    
    
});


$(window).load(function() {
    var building_id = $('#field_building_id').val();
    if(building_id) {
        populateSelectSeason(building_id, season_id);
        populateTableChkResources(building_id, initialSelection);
        building_id_selection = building_id;
    }
    $("#field_building_name").on("autocompleteselect", function(event, ui){
        var building_id = ui.item.value;        
        if (building_id != building_id_selection){
            populateSelectSeason(building_id, season_id);
            populateTableChkResources(building_id, initialSelection);
            building_id_selection = building_id;
        }
    });
});

function populateSelectSeason (building_id, selection) {
    var url = 'index.php?menuaction=booking.uiseason.index&sort=name&filter_building_id=' +  building_id + '&phpgw_return_as=json&';
    var container = $('#season_container');
    populateSelect(url, selection, container);    
}
function populateTableChkResources (building_id, selection) {
    var url = 'index.php?menuaction=booking.uiresource.index&sort=name&filter_building_id=' +  building_id + '&phpgw_return_as=json&';
    var container = 'resources_container';
    var colDefsResources = [{label: '', object: [{type: 'input', attrs: [{name: 'type', value: 'checkbox'},{name: 'name', value: 'resources[]'}]}], value: 'id', checked: selection},{key: 'name', label: lang['Name']}, {key: 'type', label: lang['Resource Type']}];
    populateTableChk(url, container, colDefsResources);
}

function populateTableChk (url, container, colDefs) {    
    createTable(container,url,colDefs);
}
function populateSelect (url, selection, container) {
    container.html("");
    var select = document.createElement('select');
    var option = document.createElement('option');
    container.append(select);
    option.setAttribute('value', '');
    option.text = '-----';
    select.appendChild(option);
    $.get(url, function(r){
        $.each(r.data, function(index, value){
            var option = document.createElement('option');
            option.text = value.name;
            option.setAttribute('value', value.id);
            if(value.id == selection) {
                    option.selected = true;
            }
            select.appendChild(option);
        });
    });
}

/*
populateSeasonTable = function(building_id, selection) {
    YAHOO.booking.radioTableHelper('season_container', 'index.php?menuaction=booking.uiseason.index&sort=name&filter_building_id=' +  building_id + '&phpgw_return_as=json&',
    'season_id', selection);
}

populateResourceTable = function(building_id, selection) {
    YAHOO.booking.checkboxTableHelper('resources_container', 'index.php?menuaction=booking.uiresource.index&sort=name&filter_building_id=' +  building_id + '&phpgw_return_as=json&',
    'resources[]', selection, {additional_fields: [{key: 'type', label: lang['Resource Type']}]});
}

YAHOO.util.Event.addListener(window, "load", function() {
    var building_id = YAHOO.util.Dom.get('field_building_id').value;
    if(building_id) {
		populateSeasonSelect(building_id, [YAHOO.booking.season_id * 1]); 
        populateResourceTable(building_id, YAHOO.booking.initialSelection);
    }
    var ac = YAHOO.booking.autocompleteHelper('index.php?menuaction=booking.uibuilding.index&phpgw_return_as=json&', 
                                              'field_building_name', 'field_building_id', 'building_container');
    // Update the resource table as soon a new building is selected
    ac.itemSelectEvent.subscribe(function(sType, aArgs) {
		populateSeasonSelect(aArgs[2].id, YAHOO.booking.season_id); 
        populateResourceTable(aArgs[2].id, []);
    });
    YAHOO.booking.autocompleteHelper('index.php?menuaction=booking.uiorganization.index&phpgw_return_as=json&', 
                                     'field_org_name', 'field_org_id', 'org_container');
});

populateSeasonSelect = function(building_id, selection) {
	var url = 'index.php?menuaction=booking.uiseason.index&sort=name&filter_building_id=' +  building_id + '&phpgw_return_as=json&';
	YAHOO.util.Connect.asyncRequest('GET', url, 
	{
		success: function(o) {
			var result = eval('x='+o.responseText)['ResultSet']['Result'];
			var container = YAHOO.util.Dom.get('season_container');
			container.innerHTML = '';
			var select = document.createElement('select');
			container.appendChild(select);
			select.setAttribute('name', 'season_id');
			var option = document.createElement('option');
			option.setAttribute('value', '');
			option.appendChild(document.createTextNode('-----'));
			select.appendChild(option);
			for(var i in result) {
				var option = document.createElement('option');
				select.appendChild(option);
				option.appendChild(document.createTextNode(result[i]['name']));
				option.setAttribute('value', result[i]['id']);
				if(result[i]['id'] == selection) {
					option.selected = true;
				}
			}
		},
		failure: function(o) {alert('nay' + o)},
		argument: this
	});
}
*/