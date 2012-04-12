<!-- BEGIN header -->
<form method="POST" action="{action_url}">
 <center><h3>{message}</h3></center>
 <table border="0" align="center" width="50%">
<!-- END header -->

<!-- BEGIN list -->
<table border="0" align="center" width="75%">
  <tr class="{row_on}">
   <td colspan="7">{lang_current_fields}</td>
  </tr>
  <tr class="{row_off}">
   <td width=5%>{lang_name_and_shortdesc}</td>
   <td>{lang_text}</td>
   <td>{lang_type}</td>
   <td>{lang_values_and_shortdesc}</td>
   <td>{lang_required}</td>
   <td>{lang_remove}</td>
   <td>{lang_order}</td>
  </tr>

<!-- BEGIN info -->
  <tr class="{row_off}">
   <td><input type="text" name="{field_short_name}_name" size="10" value="{field_name}"></td>
   <td><input type="text" name="{field_short_name}_text" value="{field_text}"></td>
   <td><select name="{field_short_name}_type">
	<option {field_type_selected_text} value="text">{lang_text}</option>
	<option {field_type_selected_textarea} value="textarea">{lang_textarea}</option>
	<option {field_type_selected_dropdown} value="dropdown">{lang_dropdown}</option>
	<option {field_type_selected_checkbox} value="checkbox">{lang_checkbox}</option>
	<option {field_type_selected_email} value="email">{lang_email}</option>
	<option {field_type_selected_first_name} value="first_name">{lang_first_name}</option>
	<option {field_type_selected_last_name} value="last_name">{lang_last_name}</option>
	<option {field_type_selected_address} value="address">{lang_address}</option>
	<option {field_type_selected_city} value="city">{lang_city}</option>
	<option {field_type_selected_state} value="state">{lang_state}</option>
	<option {field_type_selected_zip} value="zip">{lang_zip}</option>
	<option {field_type_selected_country} value="country">{lang_country}</option>
	<option {field_type_selected_gender} value="gender">{lang_gender}</option>
	<option {field_type_selected_phone} value="phone">{lang_phone}</option>
	<option {field_type_selected_birthday} value="birthday">{lang_birthday}</option>
	<option {field_type_selected_location} value="location">{lang_location}</option>
	</select></td>
   <td><input type="text" name="{field_short_name}_values" value="{field_values}" size="15"></td>
   <td><input type="checkbox" name="{field_short_name}_required" {field_required}></td>
   <td><input type="checkbox" name="{field_short_name}_remove" {field_remove}></td>
   <td><input type="text" name="{field_short_name}_order" size="2" value="{field_order}"></td>
  </tr>
<!-- END info -->

</table>
<!-- END list -->

<!-- BEGIN footer -->
<p>
<table border="0" align="center" width="50%">
  <tr>
<!--
   <td align="left"><input type="submit" name="cancel" value="{lang_cancel}"></td>
-->
   <td align="center">
   <input type="submit" name="submit" value="{lang_update_add}"></td>
  </tr>
 </table>
</form>
<!-- END footer -->
