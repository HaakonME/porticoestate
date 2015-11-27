$(document).ready(function(){
    var text = document.getElementById("displayText");
    //ele.hide();
    $("#toggleText").hide();
    text.innerHTML = "Ikke i listen? Registrer nytt lokale";
});

function toggle() {
    var ele = document.getElementById("toggleText");
    var text = document.getElementById("displayText");
    var arenahidden = document.getElementById("new_arena_hidden");
    if(ele.style.display == "block") {
        ele.style.display = "none";
        text.innerHTML = "Registrer nytt lokale";
    }
    else {
        ele.style.display = "block";
        text.innerHTML = "";
        arenahidden.value="new_arena";
    }
}

function showhide(id)
{
    if(id == "org")
    {
        document.getElementById('orgf').style.display = "block";
        document.getElementById('no_orgf').style.display = "none";
    }
    else
    {
        document.getElementById('orgf').style.display = "none";
        document.getElementById('no_orgf').style.display = "block";
    }
}

//function get_address_search()
//{
//    var address = document.getElementById('address').value;
//    var div_address = document.getElementById('address_container');
//    div_address.style.display="block";
//
//    //url = "/aktivby/registreringsskjema/ny/index.php?menuaction=activitycalendarfrontend.uiactivity.get_address_search&amp;phpgw_return_as=json&amp;search=" + address;
//    url = "<?php echo $ajaxURL ?>index.php?menuaction=activitycalendarfrontend.uiactivity.get_address_search&amp;phpgw_return_as=json&amp;search=" + address;
//
//    var divcontent_start = "<select name=\"address\" id=\"address\" size=\"5\" onChange='setAddressValue(this)'>";
//    var divcontent_end = "</select>";
//
//    var callback = {
//        success: function(response){
//            div_address.innerHTML = divcontent_start + JSON.parse(response.responseText) + divcontent_end; 
//        },
//        failure: function(o) {
//            alert("AJAX doesn't work"); //FAILURE
//        }
//    }
//    var trans = YAHOO.util.Connect.asyncRequest('GET', url, callback, null);
//}

var current_address = "";
function get_address_search()
{
    var address = $('#address').val();
    var div_address = $('#addess_container');
    
    var url = phpGWLink('activitycalendarfrontend/', {menuaction: 'activitycalendarfrontend.uiactivity.get_address_search', search: address}, true);
    var attr = [{name: 'name', value: 'address'}, {name: 'id', value: 'address'}, {name: 'size', value: '5'}, {name: 'onChange', value: 'setAddressValue(this)'}];
    
    div_address.hide();
    
    if (address && address != current_address) {
        div_address.show();
        populateSelect_activityCalendar(url, div_address, attr);
        current_address = address;
    }
}

//function get_address_search_arena()
//{
//    var address = document.getElementById('arena_address').value;
//    var div_address = document.getElementById('arena_address_container');
//    div_address.style.display="block";
//
//    //url = "/aktivby/registreringsskjema/ny/index.php?menuaction=activitycalendarfrontend.uiactivity.get_address_search&amp;phpgw_return_as=json&amp;search=" + address;
//    url = "<?php echo $ajaxURL ?>index.php?menuaction=activitycalendarfrontend.uiactivity.get_address_search&amp;phpgw_return_as=json&amp;search=" + address;
//
//    var divcontent_start = "<select name=\"arena_address_select\" id=\"arena_address\" size=\"5\" onChange='setAddressValue(this)'>";
//    var divcontent_end = "</select>";
//
//    var callback = {
//        success: function(response){
//            div_address.innerHTML = divcontent_start + JSON.parse(response.responseText) + divcontent_end; 
//        },
//        failure: function(o) {
//            alert("AJAX doesn't work"); //FAILURE
//        }
//    }
//    var trans = YAHOO.util.Connect.asyncRequest('GET', url, callback, null);
//}

var current_arena_address = "";
function get_address_search_arena()
{
    var address = $('#arena_address').val();
    var div_address = $('#arena_address_container');
    
    var url = phpGWLink('activitycalendarfrontend/', {menuaction: 'activitycalendarfrontend.uiactivity.get_address_search', search: address}, true);
    var attr = [{name: 'name', value: 'arena_address_select'}, {name: 'id', value: 'arena_address'}, {name: 'size', value: '5'}, {name: 'onChange', value: 'setAddressValue(this)'}];
    
    div_address.hide();
    
    if (address && address != current_arena_address) {
        div_address.show();
        populateSelect_activityCalendar(url, div_address, attr);
        current_arena_address = address;
    }
}

//function get_address_search_cp2()
//{
//    var address = document.getElementById('contact2_address').value;
//    var div_address = document.getElementById('contact2_address_container');
//    div_address.style.display="block";
//
//    //url = "/aktivby/registreringsskjema/ny/index.php?menuaction=activitycalendarfrontend.uiactivity.get_address_search&amp;phpgw_return_as=json&amp;search=" + address;
//    url = "<?php echo $ajaxURL ?>index.php?menuaction=activitycalendarfrontend.uiactivity.get_address_search&amp;phpgw_return_as=json&amp;search=" + address;
//
//    var divcontent_start = "<select name=\"contact2_address_select\" id=\"address_cp2\" size=\"5\" onChange='setAddressValue(this)'>";
//    var divcontent_end = "</select>";
//
//    var callback = {
//        success: function(response){
//            div_address.innerHTML = divcontent_start + JSON.parse(response.responseText) + divcontent_end; 
//        },
//        failure: function(o) {
//            alert("AJAX doesn't work"); //FAILURE
//        }
//    }
//    var trans = YAHOO.util.Connect.asyncRequest('GET', url, callback, null);
//}

var current_address_search_cp2 = "";
function get_address_search_cp2()
{
    var address = $('contact2_address');
    var div_address = $('#address_container');
    
    var url = phpGWLink('activitycalendarfrontend/', {menuaction: 'activitycalendarfrontend.uiactivity.get_address_search', search: address}, true);
    var attr = [{name: 'name', value: 'contact2_address_select'}, {name: 'id', value: 'address_cp2'}, {name: 'size', value: '5'}, {name: 'onChange', value: 'setAddressValue(this)'}];
    
    div_address.hide();
    
    if (address && address != current_address_search_cp2) {
        div_address.show();
        populateSelect_activityCalendar(url, div_address, attr);
        current_address_search_cp2 = address;
    }
}

function setAddressValue(field)
{
    if(field.name == 'contact2_address_select')
    {
        var address = document.getElementById('contact2_address');
        var div_address = document.getElementById('contact2_address_container');

        address.value=field.value;
        div_address.style.display="none";
    }
    else if(field.name == 'arena_address_select')
    {
        var address = document.getElementById('arena_address');
        var div_address = document.getElementById('arena_address_container');

        address.value=field.value;
        div_address.style.display="none";
    }
    else
    {
        var address = document.getElementById('address');
        var div_address = document.getElementById('address_container');

        address.value=field.value;
        div_address.style.display="none";
    }
}

function allOK()
{
    if(document.getElementById('title').value == null || document.getElementById('title').value == '')
    {
        alert("Navn på aktivitet må fylles ut!");
        return false;
    }
    if(document.getElementById('description').value == null || document.getElementById('description').value == '')
    {
        alert("Beskrivelse må fylles ut!");
        return false;
    }
    if(document.getElementById('description').value.length > 254)
    {
        alert("Beskrivelse kan maksimalt være 255 tegn!");
        return false;
    }
    if(document.getElementById('category').value == null || document.getElementById('category').value == 0)
    {
        alert("Kategori må fylles ut!");
        return false;
    }
    var malgrupper = document.getElementsByName('target[]');
    var malgruppe_ok = false;
    for(i=0;i<malgrupper.length;i++)
    {
        if(!malgruppe_ok)
        {
            if(malgrupper[i].checked)
            {malgruppe_ok = true;}
        }
    }
    if(!malgruppe_ok)
    {
        alert("Målgruppe må fylles ut!");
        return false;
    }
    if((document.getElementById('internal_arena_id').value == null || document.getElementById('internal_arena_id').value == 0) && (document.getElementById('new_arena_hidden').value==null || document.getElementById('new_arena_hidden').value==''))
    {
        alert("Lokale må fylles ut!");
        return false;
    }
    var distrikter = document.getElementsByName('district');
    var distrikt_ok = false;
    for(i=0;i<distrikter.length;i++)
    {
        if(!distrikt_ok)
        {
            if(distrikter[i].checked)
            {distrikt_ok = true;}
        }
    }
    if(!distrikt_ok)
    {
        alert("Bydel må fylles ut!");
        return false;
    }
    if(document.getElementById('time').value == null || document.getElementById('time').value == '')
    {
        alert("Dag og tid må fylles ut!");
        return false;
    }
    if(document.getElementById('contact_name').value == null || document.getElementById('contact_name').value == '')
    {
        alert("Navn på kontaktperson må fylles ut!");
        return false;
    }
    if(document.getElementById('contact_phone').value == null || document.getElementById('contact_phone').value == '')
    {
        alert("Telefonnummer til kontaktperson må fylles ut!");
        return false;
    }
    if(document.getElementById('contact_phone').value != null && document.getElementById('contact_phone').value.length < 8)
    {
        alert("Telefonnummer må inneholde minst 8 siffer!");
        return false;
    }
    if(document.getElementById('contact_mail').value == null || document.getElementById('contact_mail').value == '')
    {
        alert("E-postadresse til kontaktperson må fylles ut!");
        return false;
    }
    if(document.getElementById('contact_mail2').value == null || document.getElementById('contact_mail2').value == '')
    {
        alert("Begge felter for E-post må fylles ut!");
        return false;
    }
    if(document.getElementById('contact_mail').value != document.getElementById('contact_mail2').value)
    {
        alert("E-post må være den samme i begge felt!");
        return false;
    }
    if(document.getElementById('office').value == null || document.getElementById('office').value == 0)
    {
        alert("Hovedansvarlig kulturkontor må fylles ut!");
        return false;
    }
    else
        return true;
}