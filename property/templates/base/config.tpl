<!-- BEGIN header -->
<form method="POST" action="{action_url}">
<table border="0" align="center" width="85%">
   <tr class="th">
    <td colspan="2"><font color="{th_text}">&nbsp;<b>{title}</b></font></td>
   </tr>
<!-- END header -->
<!-- BEGIN body -->
   <tr class="row_on">
    <td colspan="2">&nbsp;</td>
   </tr>
   <tr class="row_off">
    <td colspan="2">&nbsp;<b>{lang_Workorder}/{lang_FM_settings}</b></td>
   </tr>
   <tr class="row_on">
    <td>{lang_organisation}:</td>
    <td><input name="newsettings[org_name]" value="{value_org_name}"></td>
   </tr>
   <tr class="row_off">
    <td>{lang_needed_for_invoice_approval}:</td>
    <td>
     <select name="newsettings[invoice_approval]">
      <option value="" {selected_invoice_approval_}>2</option>
      <option value="1" {selected_invoice_approval_1}>1</option>
     </select>
    </td>
   </tr>
 
   <tr class="row_on">
    <td>{lang_Path_to_Invoice_import_files}:<br>
    {lang_On_windows_use}: "//computername/share" {lang_or} "\\\\computername\share"</td>
    <td><input name="newsettings[import_path]" value="{value_import_path}"></td>
   </tr>
   <tr class="row_on">
    <td>{lang_Path_to_Invoice_Export_files}: ({lang_mandatory})<br>
    {lang_On_windows_use}: "//computername/share" {lang_or} "\\\\computername\share"</td>
    <td><input name="newsettings[export_path]" value="{value_export_path}"></td>
   </tr>
   <tr class="row_off">
    <td>{lang_Path_to_Invoice_Export_preregistering}: ({lang_mandatory})<br>
    {lang_On_windows_use}: "//computername/share" {lang_or} "\\\\computername\share"</td>
    <td><input name="newsettings[export_pre_path]" value="{value_export_pre_path}"></td>
   </tr>
   <tr class="row_off">
    <td>{lang_baseurl_to_invoice_image}:</td>
    <td><input name="newsettings[baseurl_invoice]" value="{value_baseurl_invoice}"></td>
   </tr>
   <tr class="row_on">
    <td>{lang_invoice_export_method}:</td>
    <td>
     <select name="newsettings[invoice_export_method]">
      <option value="local" {selected_invoice_export_method_local}>Local</option>
      <option value="ftp" {selected_invoice_export_method_ftp}>Local and ftp</option>
     </select>
    </td>
   </tr>
   <tr class="row_off">
    <td>{lang_invoice_ftp_host}:</td>
    <td><input name="newsettings[invoice_ftp_host]" value="{value_invoice_ftp_host}"></td>
   </tr>
   <tr class="row_on">
    <td>{lang_invoice_ftp_host_user}:</td>
    <td><input name="newsettings[invoice_ftp_user]" value="{value_invoice_ftp_user}"></td>
   </tr>
   <tr class="row_off">
    <td>{lang_invoice_ftp_host_pw}:</td>
    <td><input type ="password" name="newsettings[invoice_ftp_pw]" value="{value_invoice_ftp_pw}"></td>
   </tr>
   <tr class="row_on">
    <td>{lang_invoice_ftp_host_basedir_with_NO_trailing_slash}:</td>
    <td><input name="newsettings[invoice_ftp_basedir]" value="{value_invoice_ftp_basedir}"></td>
   </tr>
   <tr class="row_off">
    <td>{lang_Default_municipal_number}:</td>
    <td><input name="newsettings[default_municipal]" value="{value_default_municipal}"></td>
   </tr>
   <tr class="row_on">
    <td>{lang_Tax_[%]}:</td>
    <td><input name="newsettings[fm_tax]" value="{value_fm_tax}"></td>
   </tr>
   <tr class="row_off">
    <td>{lang_Enter_the_location_of_files_URL.} <br>
	{lang_Example:_http://www.domain.com/files}:</td>
    <td><input name="newsettings[files_url]" value="{value_files_url}"></td>
   </tr>
   <tr class="row_on">
    <td>{lang_Enter_MAP_URL.} <br>
	{lang_Example:_http://www.domain.com/map}:</td>
    <td><input name="newsettings[map_url]" value="{value_map_url}"></td>
   </tr>
   <tr class="row_off">
    <td>{lang_Enter_GAB_Location_Level.} <br>
	{lang_Default_value_is}: 3</td>
    <td><input name="newsettings[gab_insert_level]" value="{value_gab_insert_level}"></td>
   </tr>
   <tr class="row_on">
    <td>{lang_Enter_GAB_URL.} <br>
	{lang_Example:_http://www.domain.com/gab}:</td>
    <td><input name="newsettings[gab_url]" value="{value_gab_url}"></td>
   </tr>
   <tr class="row_off">
    <td>{lang_suppress_old_tenant}:</td>
    <td>
     <select name="newsettings[suppress_tenant]">
      <option value="" {selected_suppress_tenant_}>NO</option>
      <option value="1" {selected_suppress_tenant_1}>YES</option>
     </select>
    </td>
   </tr>
   <tr class="row_off">
    <td colspan="2">&nbsp;<b>{lang_TTS}::{lang_settings}</b></td>
   </tr>
   <tr class="row_on">
    <td>{lang_Open_translates_to}: <br>
	{lang_default}: {lang_Open}</td>
    <td><input name="newsettings[tts_lang_open]" value="{value_tts_lang_open}"></td>
   </tr>
   <tr class="row_on">
    <td valign = 'top'>{lang_TTS_simplified_group}:</td>
    <td>
    	<!--to be able to blank the setting - need an empty value-->
    	<input type = 'hidden' name="newsettings[fmttssimple_group][]" value="">
     <table>
{hook_fmttssimple_group}
	 </table>
    </td>
   </tr>
   <tr class="row_on">
    <td>{lang_Mail_Notification}:</td>
    <td>
     <select name="newsettings[mailnotification]">
      <option value="" {selected_mailnotification_}>NO</option>
      <option value="1" {selected_mailnotification_1}>YES</option>
     </select>
    </td>
   </tr>
   <tr class="row_off">
    <td>{lang_Owner_Notification_Project}.</td>
    <td>
     <select name="newsettings[notify_project_owner]">
      <option value="" {selected_notify_project_owner_}>NO</option>
      <option value="1" {selected_notify_project_owner_1}>YES</option>
     </select>
    </td>
   </tr>
   <tr class="row_off">
    <td>{lang_Owner_Notification_TTS}.</td>
    <td>
     <select name="newsettings[ownernotification]">
      <option value="" {selected_ownernotification_}>NO</option>
      <option value="1" {selected_ownernotification_1}>YES</option>
     </select>
    </td>
   </tr>
   <tr class="row_on">
    <td>{lang_Assigned_Notification_TTS}.</td>
    <td>
     <select name="newsettings[assignednotification]">
      <option value="" {selected_assignednotification_}>NO</option>
      <option value="1" {selected_assignednotification_1}>YES</option>
     </select>
    </td>
   </tr>
   <tr class="row_off">
    <td>{lang_Group_Notification_TTS}.</td>
    <td>
     <select name="newsettings[groupnotification]">
      <option value="" {selected_groupnotification_}>NO</option>
      <option value="1" {selected_groupnotification_1}>YES</option>
     </select>
    </td>
   </tr>
   <tr class="row_off">
    <td>{lang_TTS_file_upload}:</td>
    <td>
     <select name="newsettings[fmttsfileupload]">
      <option value="" {selected_fmttsfileupload_}>NO</option>
      <option value="1" {selected_fmttsfileupload_1}>YES</option>
     </select>
    </td>
   </tr>
   <tr class="row_on">
    <td>{lang_priority_levels_TTS}.</td>
    <td>
     <select name="newsettings[prioritylevels]">
      <option value="" {selected_prioritylevels_}>3</option>
      <option value="4" {selected_prioritylevels_4}>4</option>
      <option value="5" {selected_prioritylevels_5}>5</option>
     </select>
    </td>
   </tr>
   <tr class="row_off">
    <td>{lang_mandatory_title_TTS}.</td>
    <td>
     <select name="newsettings[tts_mandatory_title]">
      <option value="" {selected_tts_mandatory_title_}>NO</option>
      <option value="1" {selected_tts_mandatory_title_1}>YES</option>
     </select>
    </td>
   </tr>

   <tr class="row_on">
    <td valign = 'top'>{lang_TTS_finnish_date}:</td>
    <td>
    	<!--to be able to blank the setting - need an empty value-->
    	<input type = 'hidden' name="newsettings[fmtts_group_finnish_date][]" value="">
     <table>
{hook_fmtts_group_finnish_date}
	 </table>
    </td>
   </tr>

   <tr class="row_off">
    <td>{lang_Ask_for_workorder_approval_by_e-mail}.</td>
    <td>
     <select name="newsettings[workorder_approval]">
       <option value="" {selected_workorder_approval_}>NO</option>
     <option value="1" {selected_workorder_approval_1}>YES</option>
     </select>
    </td>
   </tr>

   <tr class="row_off">
    <td>{lang_Ask_for_project_approval_by_e-mail}.</td>
    <td>
     <select name="newsettings[project_approval]">
       <option value="" {selected_project_approval_}>NO</option>
     <option value="1" {selected_project_approval_1}>YES</option>
     </select>
    </td>
   </tr>

   <tr class="row_on">
    <td>{lang_project_suppress_meter}.</td>
    <td>
     <select name="newsettings[project_suppressmeter]">
      <option value="" {selected_project_suppressmeter_}>NO</option>
      <option value="1" {selected_project_suppressmeter_1}>YES</option>
     </select>
    </td>
   </tr>
   <tr class="row_off">
    <td>{lang_project_suppress_coordination}.</td>
    <td>
     <select name="newsettings[project_suppresscoordination]">
      <option value="" {selected_project_suppresscoordination_}>NO</option>
      <option value="1" {selected_project_suppresscoordination_1}>YES</option>
     </select>
    </td>
   </tr>

   <tr class="row_on">
    <td>{lang_meter_table}:</td>
    <td><input name="newsettings[meter_table]" value="{value_meter_table}"></td>
   </tr>
   <tr class="row_off">
    <td>{lang_email_addresses_(comma-separated)_to_be_notified_about_tenant_claim_(empty_for_no_notify)}:</td>
    <td>
     <input name="newsettings[tenant_claim_notify_mails]" value="{value_tenant_claim_notify_mails}" size="40">
    </td>
   </tr>
   <tr class="row_on">
    <td>{lang_Receive_workorder_status_by_SMS}.</td>
    <td>
     <select name="newsettings[wo_status_sms]">
      <option value="" {selected_wo_status_sms_}>NO</option>
      <option value="1" {selected_wo_status_sms_1}>YES</option>
     </select>
    </td>
   </tr>
   <tr class="row_off">
    <td>{lang_Use_ACL_for_accessing_location_based_information}.</td>
    <td>
     <select name="newsettings[acl_at_location]">
      <option value="" {selected_acl_at_location_}>NO</option>
      <option value="1" {selected_acl_at_location_1}>YES</option>
     </select>
    </td>
   </tr>
   <tr class="row_on">
    <td>{lang_Use_location_at_workorder}.</td>
    <td>
     <select name="newsettings[location_at_workorder]">
      <option value="" {selected_location_at_workorder_}>NO</option>
      <option value="1" {selected_location_at_workorder_1}>YES</option>
     </select>
    </td>
   </tr>
   <tr class="row_off">
    <td>{lang_budget_at_project_level}.</td>
    <td>
     <select name="newsettings[budget_at_project]">
      <option value="" {selected_budget_at_project_}>NO</option>
      <option value="1" {selected_budget_at_project_1}>YES</option>
     </select>
    </td>
   </tr>
<!--
groupnotification
-->

<!-- END body -->
<!-- BEGIN footer -->
  <tr class="th">
    <td colspan="2">
&nbsp;
    </td>
  </tr>
  <tr>
    <td colspan="2" align="center">
      <input type="submit" name="submit" value="{lang_submit}">
      <input type="submit" name="cancel" value="{lang_cancel}">
    </td>
  </tr>
</table>
</form>
<!-- END footer -->
