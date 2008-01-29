<!-- BEGIN header -->
<div class="{error_class} msg">
	{error}
</div>
<form method="post" action="{action_url}">
<table>
	<thead>
		<tr>
			<th colspan="2">{title}</th>
		</tr>
	</thead>
	<tbody>
<!-- END header -->
<!-- BEGIN body -->
   <tr class="row_on">
    <td>{lang_Would_you_like_phpGroupWare_to_check_for_a_new_version<br />when_admins_login_?}:</td>
    <td>
     <select name="newsettings[checkfornewversion]">
      <option value="">{lang_No}</option>
      <option value="True"{selected_checkfornewversion_True}>{lang_Yes}</option>
     </select>
    </td>
   </tr>

   <tr class="row_off">
    <td>{lang_Timeout_for_sessions_in_seconds_(default_14400_=_4_hours)}:</td>
    <td><input size="8" name="newsettings[sessions_timeout]" value="{value_sessions_timeout}"></td>
   </tr>

   <tr class="row_on">
    <td>{lang_Timeout_for_application_session_data_in_seconds_(default_86400_=_1_day)}:</td>
    <td><input size="8" name="newsettings[sessions_app_timeout]" value="{value_sessions_app_timeout}"></td>
   </tr>

   <tr class="row_off">
    <td>{lang_Would_you_like_to_show_each_application's_upgrade_status_?}:</td><td>
     <select name="newsettings[checkappversions]">
      <option value="">{lang_No}</option>
      <option value="Admin"{selected_checkappversions_Admin}>{lang_Admins}</option>
      <option value="All"{selected_checkappversions_All}>{lang_All_Users}</option>
     </select>
    </td>
   </tr>

   <tr class="row_on">
    <td>{lang_Would_you_like_phpGroupWare_to_cache_the_phpgw_info_array_?}:</td>
    <td>
     <select name="newsettings[cache_phpgw_info]">
      <option value="">{lang_No}</option>
      <option value="True"{selected_cache_phpgw_info_True}>{lang_Yes}</option>
     </select>
    </td>
   </tr>

   <tr class="row_off">
    <td>{lang_Maximum_entries_in_click_path_history}:</td>
    <td><input size="8" name="newsettings[max_history]" value="{value_max_history}"></td>
   </tr>

   <tr class="row_on">
    <td>{lang_Would_you_like_to_automaticaly_load_new_langfiles_(at_login-time)_?}:</td>
    <td>
     <select name="newsettings[disable_autoload_langfiles]">
      <option value="">{lang_Yes}</option>
      <option value="True"{selected_disable_autoload_langfiles_True}>{lang_No}</option>
     </select>
    </td>
   </tr>

    <tr class="row_on">
    <td>{lang_Would_you_like_phpGroupWare_to_cache_the_langfiles_in_shared_memory_?}:</td>
    <td>
     <select name="newsettings[shm_lang]">
      <option value="">{lang_No}</option>
      <option value="True"{selected_shm_lang_True}>{lang_Yes}</option>
     </select>
    </td>
   </tr>

    <tr class="th">
    <td colspan="2">&nbsp;<b>{lang_SMTP_settings}</b></td>
   </tr>

   <tr class="row_on">
   	<td>{lang_SMTP_server_hostname_(or_IP_address)}:</td>
	<td><input name="newsettings[smtp_server]" value="{value_smtp_server}" /></td>
   </tr>

   <tr class="row_off">
   	<td>{lang_SMTP_server_port_number}:</td>
	<td><input name="newsettings[smtp_port]" value="{value_smtp_port}" /></td>
   </tr>

   <tr class="row_on">
    <td>{lang_Use_SMTP_auth}:</td>
    <td>
     <select name="newsettings[smtpAuth]">
      <option value="">{lang_no}</option>
      <option value="yes" {selected_smtpAuth_yes}>{lang_yes}</option>
     </select>
    </td>
   </tr>
   <tr class="row_off">
    <td>{lang_Enter_your_SMTP_server_user}:</td>
    <td><input name="newsettings[smtpUser]" value="{value_smtpUser}"></td>
   </tr>
   <tr class="row_on">
    <td>{lang_Enter_your_SMTP_server_password}:</td>
    <td><input type= "password" name="newsettings[smtpPassword]" value="{value_smtpPassword}"></td>
   </tr>

   <tr class="th">
    <td colspan="2">&nbsp;<b>{lang_appearance}</b></td>
   </tr>

   <tr class="row_on">
    <td>{lang_Enter_the_title_for_your_site}:</td>
    <td><input name="newsettings[site_title]" value="{value_site_title}"></td>
   </tr>

   <tr class="row_off">
    <td>{lang_Enter_the_background_color_for_the_site_title}:</td>
    <td>#<input name="newsettings[login_bg_color_title]" value="{value_login_bg_color_title}"></td>
   </tr>

   <tr class="row_on">
    <td>{lang_Enter_the_background_color_for_the_login_page}:</td>
    <td>#<input name="newsettings[login_bg_color]" value="{value_login_bg_color}"></td>
   </tr>

   <tr class="row_off">
    <td>{lang_Enter_the_file_name_of_your_logo_at_login}:</td>
    <td><input name="newsettings[login_logo_file]" value="{value_login_logo_file}"></td>
   </tr>

   <tr class="row_on">
    <td>{lang_Enter_the_file_name_of_your_logo}:</td>
    <td><input name="newsettings[logo_file]" value="{value_logo_file}"></td>
   </tr>

   <tr class="row_off">
    <td>{lang_Enter_the_url_where_your_logo_should_link_to}:</td>
    <td>http://<input name="newsettings[login_logo_url]" value="{value_login_logo_url}"></td>
   </tr>

   <tr class="row_on">
    <td>{lang_Enter_the_title_of_your_logo}:</td>
    <td><input name="newsettings[login_logo_title]" value="{value_login_logo_title}"></td>
   </tr>

   <tr class="row_off">
    <td>{lang_Show_'powered_by'_logo_on}:</td>
    <td>
     <select name="newsettings[showpoweredbyon]">
      <option value="bottom" {selected_showpoweredbyon_bottom}>{lang_bottom}</option>
      <option value="top" {selected_showpoweredbyon_top}>{lang_top}</option>
     </select>
    </td>
   </tr>

   <tr class="row_on">
    <td>{lang_enable_fckeditor_wysiwyg_html_editor}:</td><td>
     <select name="newsettings[enable_fckeditor]">
      <option value="">{lang_No}</option>
      <option value="True"{selected_enable_fckeditor_True}>{lang_Yes}</option>
     </select>
    </td>
   </tr>

   <tr class="row_off">
    <td>{lang_enable_tinymce_wysiwyg_html_editor}:</td><td>
     <select name="newsettings[enable_tinymce]">
      <option value="">{lang_No}</option>
      <option value="True"{selected_enable_tinymce_True}>{lang_Yes}</option>
     </select>
    </td>
   </tr>

   <tr class="th">
    <td colspan="2">&nbsp;<b>{lang_security}</b></td>
   </tr>

   <tr class="row_on">
    <td>{lang_Use_cookies_to_pass_sessionid}:</td>
    <td>
     <select name="newsettings[usecookies]">
      <option value="">{lang_No}</option>
      <option value="True"{selected_usecookies_True}>{lang_Yes}</option>
     </select>
    </td>
   </tr>

   <tr class="row_off">
    <td>{lang_check_ip_address_of_all_sessions}:</td>
    <td>
     <select name="newsettings[sessions_checkip]">
      <option value="">{lang_No}</option>
      <option value="True"{selected_sessions_checkip_True}>{lang_Yes}</option>
     </select>
    </td>
   </tr>

   <tr class="row_on">
    <td>{lang_Deny_all_users_access_to_grant_other_users_access_to_their_entries_?}:</td>
    <td>
     <select name="newsettings[deny_user_grants_access]">
      <option value="">{lang_No}</option>
      <option value="True"{selected_deny_user_grants_access_True}>{lang_Yes}</option>
     </select>
    </td>
   </tr>

   <tr class="row_off">
    <td>{lang_How_many_days_should_entries_stay_in_the_access_log,_before_they_get_deleted_(default_90)_?}:</td>
    <td>
     <input name="newsettings[max_access_log_age]" value="{value_max_access_log_age}" size="5">
    </td>
   </tr>

   <tr class="row_on">
    <td>{lang_After_how_many_unsuccessful_attempts_to_login,_an_account_should_be_blocked_(default_3)_?}:</td>
    <td>
     <input name="newsettings[num_unsuccessful_id]" value="{value_num_unsuccessful_id}" size="5">
    </td>
   </tr>
   
   <tr class="row_off">
    <td>{lang_After_how_many_unsuccessful_attempts_to_login,_an_IP_should_be_blocked_(default_3)_?}:</td>
    <td>
     <input name="newsettings[num_unsuccessful_ip]" value="{value_num_unsuccessful_ip}" size="5">
    </td>
   </tr>
   
   <tr class="row_on">
    <td>{lang_How_many_minutes_should_an_account_or_IP_be_blocked_(default_30)_?}:</td>
    <td>
     <input name="newsettings[block_time]" value="{value_block_time}" size="5">
    </td>
   </tr>
   
   <tr class="row_off">
    <td>{lang_Admin_email_addresses_(comma-separated)_to_be_notified_about_the_blocking_(empty_for_no_notify)}:</td>
    <td>
     <input name="newsettings[admin_mails]" value="{value_admin_mails}" size="40">
    </td>
   </tr>

   <tr class="row_on">
    <td>{lang_Disable_"auto_completion"_of_the_login_form_}:</td>
    <td>
      <select name="newsettings[autocomplete_login]">
         <option value="">{lang_No}</option>
	 <option value="True"{selected_autocomplete_login_True}>{lang_Yes}</option>
       </select>
    </td>
   </tr>
<!-- END body -->

<!-- BEGIN footer -->
	</tbody>
</table>
<div class="button_group">
      <input type="submit" name="submit" value="{lang_submit}">
      <input type="submit" name="cancel" value="{lang_cancel}">
</div>
</form>
<!-- END footer -->
