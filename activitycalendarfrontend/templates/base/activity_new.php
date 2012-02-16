<?php
	//include common logic for all templates
//	include("common.php");
	$act_so = activitycalendar_soactivity::get_instance();
	$contpers_so = activitycalendar_socontactperson::get_instance();
?>

<script type="text/javascript">

function checkNewGroup()
{
	var group_selected = document.getElementById('group_id').value;
	if(group_selected == 'new_group')
	{
		document.getElementById('new_group_fields').style.display = "block";
	}
	else
	{
		document.getElementById('new_group_fields').style.display = "none";
	}
}

function checkNewArena()
{
	var arena_selected = document.getElementById('arena_id').value;
	if(arena_selected == 'new_arena')
	{
		document.getElementById('new_arena_fields').style.display = "block";
	}
	else
	{
		document.getElementById('new_arena_fields').style.display = "none";
	}
}

function get_address_search()
{
	var address = document.getElementById('address_txt').value;
	var div_address = document.getElementById('address_container');

	url = "/aktivby/registreringsskjema/ny/index.php?menuaction=activitycalendarfrontend.uiactivity.get_address_search&amp;phpgw_return_as=json&amp;search=" + address;
	//url = "index.php?menuaction=activitycalendarfrontend.uiactivity.get_address_search&amp;phpgw_return_as=json&amp;search=" + address;

var divcontent_start = "<select name=\"address\" id=\"address\" size\"5\">";
var divcontent_end = "</select>";
	
	var callback = {
		success: function(response){
					div_address.innerHTML = divcontent_start + JSON.parse(response.responseText) + divcontent_end; 
				},
		failure: function(o) {
					 alert("AJAX doesn't work"); //FAILURE
				 }
	}
	var trans = YAHOO.util.Connect.asyncRequest('GET', url, callback, null);
	
}

function get_address_search_arena()
{
	var address = document.getElementById('arena_address_txt').value;
	var div_address = document.getElementById('arena_address_container');

	url = "/aktivby/registreringsskjema/ny/index.php?menuaction=activitycalendarfrontend.uiactivity.get_address_search&amp;phpgw_return_as=json&amp;search=" + address;
	//url = "index.php?menuaction=activitycalendarfrontend.uiactivity.get_address_search&amp;phpgw_return_as=json&amp;search=" + address;

var divcontent_start = "<select name=\"arena_address\" id=\"arena_address\" size\"5\">";
var divcontent_end = "</select>";
	
	var callback = {
		success: function(response){
					div_address.innerHTML = divcontent_start + JSON.parse(response.responseText) + divcontent_end; 
				},
		failure: function(o) {
					 alert("AJAX doesn't work"); //FAILURE
				 }
	}
	var trans = YAHOO.util.Connect.asyncRequest('GET', url, callback, null);
	
}

function get_address_search_cp2()
{
	var address = document.getElementById('contact2_address_txt').value;
	var div_address = document.getElementById('contact2_address_container');

	url = "/aktivby/registreringsskjema/ny/index.php?menuaction=activitycalendarfrontend.uiactivity.get_address_search&amp;phpgw_return_as=json&amp;search=" + address;
	//url = "index.php?menuaction=activitycalendarfrontend.uiactivity.get_address_search&amp;phpgw_return_as=json&amp;search=" + address;

var divcontent_start = "<select name=\"contact2_address\" id=\"address_cp2\" size\"5\">";
var divcontent_end = "</select>";
	
	var callback = {
		success: function(response){
					div_address.innerHTML = divcontent_start + JSON.parse(response.responseText) + divcontent_end; 
				},
		failure: function(o) {
					 alert("AJAX doesn't work"); //FAILURE
				 }
	}
	var trans = YAHOO.util.Connect.asyncRequest('GET', url, callback, null);
	
}

function run_checks()
{
	check_external();
	checkNewArena();
}

function check_internal()
{
	if(document.getElementById('internal_arena_id').value != null && document.getElementById('internal_arena_id').value > 0)
	{
		//disable external arena drop-down
		document.getElementById('arena_id').disabled="disabled";
		document.getElementById('new_arena_fields').style.display = "none";
	}
	else
	{
		//enable external arena drop-down
		document.getElementById('arena_id').disabled="";
	}
}

function check_external()
{
	if(document.getElementById('arena_id').value != null && (document.getElementById('arena_id').value > 0 || document.getElementById('arena_id').value == 'new_arena'))
	{
		//disable internal arena drop-down
		document.getElementById('internal_arena_id').disabled="disabled";
	}
	else
	{
		//enable internal arena drop-down
		document.getElementById('internal_arena_id').disabled="";
		document.getElementById('new_arena_fields').style.display = "none";
	}
}

function allOK()
{
	if(document.getElementById('title').value == null || document.getElementById('title').value == '')
	{
		alert("Tittel må fylles ut!");
		return false;
	} 
	if(document.getElementById('internal_arena_id').value == null || document.getElementById('internal_arena_id').value == 0)
	{
		if(document.getElementById('arena_id').value == null || document.getElementById('arena_id').value == 0)
		{
			alert("Arena må fylles ut!");
			return false;
		}
	}
	if(document.getElementById('time').value == null || document.getElementById('time').value == '')
	{
		alert("Tid må fylles ut!");
		return false;
	}
	if(document.getElementById('category').value == null || document.getElementById('category').value == 0)
	{
		alert("Kategori må fylles ut!");
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

</script>
<style>
dl.proplist,
dl.proplist-col {
    margin: 1em 0;
    padding-left: 2em;
}
dl.proplist dt,
dl.proplist-col dt { 
    font-style: italic; 
    font-weight: bolder; 
    font-size: 90%; 
    margin: .8em 0 .1em 0;
}

dl.proplist-col,
dl.form-col {
    width: 18em;
    float: left;
    text-align: left;
}

#frontend dl.proplist-col {
    width: 600px; !important
}

table#header {
	margin: 2em;
	
	}

div#unit_selector {
	
}

div#all_units_key_data {
	padding-left: 2em;
	}

div#unit_image {
	margin-left: 2em;
	}

div#unit_image img {
	height:170px;
}

div.yui-navset {
	padding-left: 2em;
	padding-right: 2em;
	}
	
div#contract_selector {
	padding-left: 1em;
	padding-top: 1em;
	}
	
img.list_image {
	margin-right: 5px;
	float:left;
	}

a.list_image {
	float:left;
	display:inline;
	}

ol.list_image {
	float: left;
	}
	
ol.list_image li {
	padding: 1px;
}
	
dl#key_data  {
	padding: 2px;
	}
	
	
dl#key_data dd {
	padding-bottom: 1em;
}

table#key_data td {
	padding-right: 1em;
	padding: 5px;
	}


.user_menu {
	list-style:none;
	height: 100%;
	padding: 2px;
	border-style: none none none solid;
	border-width: 1px;
	border-color: grey;
	padding-left: 5px;
}

.user_menu li {
	margin: 13px;
	}
	
#area_and_price {
	list-style:none;
	height: 100%;
	padding: 2px;
	padding-left: 5px;
	float:right;
	padding:0.5em 1em 0 0;
}

#area_and_price li {
	margin: 13px;
	}
	
#org_units {
	list-style: none;
	height: 100%;
	padding: 2px;
	padding-left: 5px;
	float:right;
	padding:0.5em 1em 0 0;
}

#org_units li {
	margin: 13px;
	}
	
#information {
	list-style:none;
	height: 100%;
	padding: 2px;
	padding-left: 5px;
	float:right;
	padding:0.5em 1em 0 0;
}

#information li {
	margin: 13px;
	}

a.header_link {
	text-decoration: none;
	float: none;
	}
	
#logo_holder {
	border: 0 none;
	font-family:Arial,sans-serif;
font-size:65%;
line-height:1.166;
position: absolute;
padding:2em;
}

em#bold {
	font-weight: bold;
	}

div#header a {
	float: none;
}

.yui-skin-sam .yui-navset .yui-nav, .yui-skin-sam .yui-navset .yui-navset-top .yui-nav {
	border-color: #BF0005;
	border-width:0 0 2px;
	}
	
.yui-skin-sam .yui-navset .yui-content {
	background: none repeat scroll 0 0 #F4F2ED;
}

.yui-skin-sam .yui-navset .yui-nav .selected a, .yui-skin-sam .yui-navset .yui-nav .selected a:focus, .yui-skin-sam .yui-navset .yui-nav .selected a:hover {
	background:url("../../../../assets/skins/sam/sprite.png") repeat-x scroll left -1400px #2647A0;
	}
	
div.tickets {
	margin-top: 1em;
	}

em.select_header {
	font-size: larger;
	padding-top: 10px;
	}

#contract_price_and_area {
	float: left;
	margin: 1em 2em 0 0;
}

#contract_price_and_area li {
		margin-bottom: 1em;
	}

#contract_essentials {
	float: left;
	margin: 1em 2em 0 2em;
	}
	
#composites {
	float: left;
	margin: 1em 2em 0 2em;
	}
	
	
#comment {
	float: left;
	margin: 1em 2em 0 2em;
	}
	
	#contract_essentials li {
		margin-bottom: 1em;
	}
	
#contract_parts {
	float: left;
	margin: 1em 2em 0 2em;
	}
	
div.toolbar {
background-color:#EEEEEE;
border:1px solid #BBBBBB;
float:left;
width:100%;
}

div.toolbar_manual {
background-color:#EEEEEE;
border:1px solid #BBBBBB;
float:left;
width:100%;
}

.yui-pg-container {
	white-space: normal;
	}
	
li.ticket_detail {
	padding: 5px;
	margin-left: 5px;
	}

div.success {
	font-weight: normal;
	margin:10px;
	padding:5px;
	font-size:1.1em;
	text-align: left;
	background-color: green;
	border:1px solid #9F6000;
	color: white;
}

div.error {
	font-weight: normal;
	margin:10px;
	padding:5px;
	font-size:1.1em;
	text-align: left;
	background-color: red;
	border:1px solid #9F6000;
	color: white;
}
</style>
<div class="yui-content" style="width: 100%;">
	<div id="details">
	
	<?php if($message){?>
	<div class="success">
		<?php echo $message;?>
	</div>
	<?php }else if($error){?>
	<div class="error">
		<?php echo $error;?>
	</div>
	<?php }?>
	</div>
		<h1><?php echo lang('new_activity') ?></h1>
		<form action="#" method="post">
			<input type="hidden" name="id" value="<?php if($activity->get_id()){ echo $activity->get_id(); } else { echo '0'; }  ?>"/>
			<dl class="proplist-col" style="width: 200%">
				<h2><?php echo lang('what')?></h2>
				<dt>
					<label for="title"><?php echo lang('title') ?></label>
				</dt>
				<dd>
					<?php echo lang('title_helptext')?><br/>
					<input type="text" name="title" id="title" value="<?php echo $activity->get_title() ?>" size="60"/>
				</dd>
				<dt>
					<label for="category"><?php echo lang('category') ?></label>
				</dt>
				<dd>
					<?php
					$current_category_id = $activity->get_category();
					?>
					<select name="category" id="category">
						<option value="0">Ingen kategori valgt</option>
						<?php
						foreach($categories as $category)
						{
							echo "<option ".($current_category_id == $category->get_id() ? 'selected="selected"' : "")." value=\"{$category->get_id()}\">".$category->get_name()."</option>";
						}
						?>
					</select>
				</dd>
				<dt>
					<label for="target"><?php echo lang('target') ?></label>
				</dt>
				<dd>
					<?php
					$current_target_ids = $activity->get_target();
					$current_target_id_array=explode(",", $current_target_ids);
					foreach($targets as $t)
					{
					?>
						<input name="target[]" type="checkbox" value="<?php echo $t->get_id()?>" <?php echo (in_array($t->get_id(), $current_target_id_array) ? 'checked' : "")?>/><?php echo $t->get_name()?><br/>
					<?php
					}
					?>
				</dd>
				<dt>
					<label for="district"><?php echo lang('district') ?></label>
				</dt>
				<dd>
					<?php
					$current_district_ids = $activity->get_district();
					$current_district_id_array=explode(",", $current_district_ids);
					foreach($districts as $d)
					{
					?>
						<input name="district[]" type="checkbox" value="<?php echo $d['part_of_town_id']?>" <?php echo (in_array($d['part_of_town_id'], $current_district_id_array) ? 'checked' : "")?>/><?php echo $d['name']?><br/>
					<?php
					}
					?>
				</dd>
				<dt>
					<label for="special_adaptation"><?php echo lang('special_adaptation') ?></label>
				</dt>
				<dd>
					<input type="checkbox" name="special_adaptation" id="special_adaptation" />
				</dd>
				<hr />
				<h2><?php echo lang('where_when')?></h2>
				<dt>
					<label for="arena"><?php echo lang('arena') ?></label>
					<br/><?php echo lang('arena_helptext')?>
				</dt>
				<dt>
					<label for="internal_arena_id"><?php echo lang('building') ?></label>
				</dt>
				<dd>
					<?php
					$current_internal_arena_id = $activity->get_internal_arena();
					?>
					<select name="internal_arena_id" id="internal_arena_id" onchange="javascript: check_internal();">
						<option value="0">Ingen kommunale bygg valgt</option>
						<?php
						foreach($buildings as $building_id => $building_name)
						{
							echo "<option ".($current_internal_arena_id == $building_id? 'selected="selected"' : "")." value=\"{$building_id}\">".$building_name."</option>";
						}
						?>
					</select>
				</dd>
				<dt>
					<label for="arena_id"><?php echo lang('external_arena') ?></label>
				</dt>
				<dd>
					<?php
					$current_arena_id = $activity->get_arena();
					?>
					<select name="arena_id" id="arena_id" style="width: 400px;" onchange="javascript: run_checks();">
						<option value="0">Ingen arena valgt</option>
						<option value="new_arena">Ny arena</option>
						<?php
						foreach($arenas as $arena)
						{
							echo "<option ".($current_arena_id == $arena->get_id() ? 'selected="selected"' : "")." value=\"{$arena->get_id()}\" title=\"{$arena->get_arena_name()}\">".$arena->get_arena_name()."</option>";
						}
						?>
					</select>
				</dd>
				<span id="new_arena_fields" style="display: none;">
					<dt>
						<label for="new_arena"><?php echo lang('new_arena') ?></label>
					</dt>
					<dt><label for="arena_name"><?php echo lang('name') ?></label></dt>
					<dd><input type="text" name="arena_name" id="arena_name" /></dd>
					<dt><label for="arena_address"><?php echo lang('address') ?></label></dt>
					<dd><input type="text" name="arena_address_txt" id="arena_address_txt" onkeyup="javascript:get_address_search_arena()"/>
					<div id="arena_address_container"></div>
					<label for="arena_number">Nummer</label>
					<input type="text" name="arena_number"/><br/>
					<label for="arena_postaddress">Postnummer og Sted</label>
					<input type="text" name="arena_postaddress"/></dd>
				</span>
				<dt>
					<label for="time"><?php echo lang('time') ?></label>
				</dt>
				<dd>
					<input type="text" name="time" id="time" value="<?php echo $activity->get_time() ?>" />
				</dd>
				<dt>
					<label for="office"><?php echo lang('office') ?></label>
				</dt>
				<dd>
					<?php
					$selected_office = $activity->get_office();
					?>
					<select name="office" id="office">
						<option value="0">Ingen kontor valgt</option>
						<?php
						foreach($offices as $office)
						{
							echo "<option ".($selected_office == $office['id'] ? 'selected="selected"' : "")." value=\"{$office['id']}\">".$office['name']."</option>";
						}
						?>
					</select>
				</dd>
				<hr />
				<h2><?php echo lang('who')?></h2>
				<dt>
					<label for="organization_id"><?php echo lang('organization') ?></label>
				</dt>
					<?php if($new_org){?>
						<input type="hidden" name="new_org" id="new_org" value="yes" />
					<?php }?>
					<?php if($new_organization){?>
					<input type="hidden" name="organization_id" id="organization_id" value="new_org" />
						<dt><label for="orgname">Organisasjonsnavn</label></dt>
						<dd><input type="text" name="orgname" size="100"/></dd>
						<dt><label for="orgno">Organisasjonsnummer</label></dt>
						<dd><input type="text" name="orgno"/></dd>
						<dt><label for="district">Bydel</label></dt>
						<dd><select name="org_district">
								<option value="0">Ingen bydel valgt</option>
						<?php 
						foreach($districts as $d)
						{
						?>
							<option value="<?php echo $d['part_of_town_id']?>"><?php echo $d['name']?></option>
						<?php
						}?>
						</select></dd>
						<dt><label for="homepage">Hjemmeside</label></dt>
						<dd><input type="text" name="homepage" size="100"/></dd>
						<dt><label for="email">E-post</label></dt>
						<dd><input type="text" name="email"/></dd>
						<dt><label for="phone">Telefon</label></dt>
						<dd><input type="text" name="phone"/></dd>
						<dt><label for="street">Gate</label></dt>
						<dd><input type="text" name="address_txt" id="address_txt" onkeyup="javascript:get_address_search()"/>
						<div id="address_container"></div>
						<label for="number">Nummer</label>
						<input type="text" name="number"/><br/>
						<label for="postaddress">Postnummer og Sted</label>
						<input type="text" name="postaddress" size="100"/></dd>
						<dt><label for="org_description">Beskrivelse</label></dt>
						<dd><textarea rows="10" cols="100" name="org_description"></textarea></dd>
					<hr/>
					<b>Kontaktperson 1</b><br/>
					<dt><label for="contact1_name">Navn</label>
					<input type="text" name="org_contact1_name" size="100"/><br/>
					<dt><label for="contact1_phone">Telefon</label>
					<input type="text" name="org_contact1_phone"/><br/>
					<dt><label for="contact1_mail">E-post</label>
					<input type="text" name="org_contact1_mail"/><br/>
					<b>Kontaktperson 2</b><br/>
					<dt><label for="contact2_name">Navn</label>
					<input type="text" name="org_contact2_name" size="100"/><br/>
					<dt><label for="contact2_phone">Telefon</label>
					<input type="text" name="org_contact2_phone"/><br/>
					<dt><label for="contact2_mail">E-post</label>
					<input type="text" name="org_contact2_mail"/><br/>
					<dt><label for="contact2_address">Adresse</label>
					<input type="text" name="contact2_address_txt" id="contact2_address_txt" onkeyup="javascript:get_address_search_cp2()"/>
					<div id="contact2_address_container"></div><br/>
					<label for="contact2_number">Nummer</label>
					<input type="text" name="org_contact2_number"/><br/>
					<dt><label for="contact2_postaddress">Postnummer og Sted</label>
					<input type="text" name="org_contact2_postaddress" size="100"/>
					<hr/>
					<?php }else{?>
						<input type="hidden" name="organization_id" id="organization_id" value="<?php echo $organization->get_id()?>" />
						<dt><label for="orgname">Organisasjonsnavn</label></dt>
						<dd><?php echo $organization->get_name()?></dd>
						<dt><label for="orgno">Organisasjonsnummer</label></dt>
						<dd><?php echo $organization->get_organization_number()?></dd>
						<dt><label for="homepage">Hjemmeside</label></dt>
						<dd><?php echo $organization->get_homepage()?></dd>
						<dt><label for="email">E-post</label></dt>
						<dd><?php echo $organization->get_email()?></dd>
						<dt><label for="phone">Telefon</label></dt>
						<dd><?php echo $organization->get_phone()?></dd>
						<dt><label for="street">Adresse</label></dt>
						<dd><?php echo $organization->get_address()?></dd>
						<dt><label for="org_description">Beskrivelse av aktiviteten</label></dt>
						<dd><?php echo $organization->get_description()?></dd>
					<hr/>
					<dt>Kontaktperson 1</dt>
					<dt><label for="contact1_name">Navn</label></dt>
					<dd><?php echo isset($contact1)?$contact1->get_name():''?></dd>
					<dt><label for="contact1_phone">Telefon</label></dt>
					<dd><?php echo isset($contact1)?$contact1->get_phone():''?></dd>
					<dt><label for="contact1_mail">E-post</label></dt>
					<dd><?php echo isset($contact1)?$contact1->get_email():''?></dd>
					<dt>Kontaktperson 2</dt>
					<dt><label for="contact2_name">Navn</label></dt>
					<dd><?php echo isset($contact2)?$contact2->get_name():''?></dd>
					<dt><label for="contact2_phone">Telefon</label></dt>
					<dd><?php echo isset($contact2)?$contact2->get_phone():''?></dd>
					<dt><label for="contact2_mail">E-post</label></dt>
					<dd><?php echo isset($contact2)?$contact2->get_email():''?></dd>
					<hr/>
					<?php }?>
				<?php if(!$new_organization){?>
				<dt>
					<label for="group_id" id="group_label"><?php echo lang('group') ?></label>
				</dt>
				<dd>
					<?php echo lang('group_helptext')?><br/>
					<select name="group_id" id="group_id" onchange="javascript:checkNewGroup()">
						<option value="0">Ingen gruppe valgt</option>
						<option value='new_group'>Ny gruppe</option>
					<?php foreach($groups as $group){?>
						<option value="<?php echo $group->get_id()?>"><?php echo $group->get_name()?></option>
					<?php }?>
					</select>
				</dd>
				<span id="new_group_fields" style="display: none;">
					<dt><label for="groupname">Navn</label></dt>
					<dd><input type="text" name="groupname" size="100"/><br/></dd>
					<dt><label for="group_description">Beskrivelse av aktiviteten</label></dt>
					<dd><textarea rows="10" cols="100" name="group_description"></textarea></dd>
					<hr/>
					<dt>Kontaktperson 1</dt>
					<dt><label for="contact1_name">Navn</label></dt>
					<dd><input type="text" name="group_contact1_name" value="<?php echo isset($contact1)?$contact1->get_name():''?>"/></dd>
					<dt><label for="contact1_phone">Telefon</label></dt>
					<dd><input type="text" name="group_contact1_phone" value="<?php echo isset($contact1)?$contact1->get_phone():''?>"/></dd>
					<dt><label for="contact1_mail">E-post</label></dt>
					<dd><input type="text" name="group_contact1_mail" value="<?php echo isset($contact1)?$contact1->get_email():''?>"/></dd>
					<dt>Kontaktperson 2</dt>
					<dt><label for="contact2_name">Navn</label></dt>
					<dd><input type="text" name="group_contact2_name" value="<?php echo isset($contact2)?$contact2->get_name():''?>"/></dd>
					<dt><label for="contact2_phone">Telefon</label></dt>
					<dd><input type="text" name="group_contact2_phone" value="<?php echo isset($contact2)?$contact2->get_phone():''?>"/></dd>
					<dt><label for="contact2_mail">E-post</label></dt>
					<dd><input type="text" name="group_contact2_mail" value="<?php echo isset($contact2)?$contact2->get_email():''?>"/></dd>
					<dt><label for="contact2_address">Adresse</label></dt>
					<dd><input type="text" name="contact2_address_txt" id="contact2_address_txt" onkeyup="javascript:get_address_search_cp2()" />
					<div id="contact2_address_container"></div><br/>
					<label for="contact2_number">Nummer</label>
					<input type="text" name="group_contact2_number"/><br/>
					<label for="contact2_postaddress">Postnummer og Sted</label>
					<input type="text" name="group_contact2_postaddress"/></dd>
					<hr/>
				</span>
				<?php }?>
				<div class="form-buttons">
					<input type="submit" name="save_activity" value="<?php echo lang('save_activity') ?>" onclick="return allOK();"/>
				</div>
			</dl>
			
		</form>
		
	</div>
</div>