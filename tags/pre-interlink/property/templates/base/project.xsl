<!-- $Id: project.xsl 18358 2007-11-27 04:43:37Z skwashd $ -->

	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="edit">
				<xsl:apply-templates select="edit"/>
			</xsl:when>
			<xsl:when test="view">
				<xsl:apply-templates select="view"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="list_project"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="list_project">

		<xsl:apply-templates select="menu"/>
		<table width="100%"  cellpadding="2" cellspacing="2" align="center">
			<tr>
				<td>
					<xsl:call-template name="cat_filter"/>
				</td>
				<td align="left">
					<xsl:call-template name="status_filter"/>
				</td>
				<td align="left">
					<xsl:call-template name="wo_hour_cat_filter"/>
				</td>
				<td align="center">
					<xsl:call-template name="user_id_filter"/>
				</td>
				<td align="right">
					<xsl:call-template name="search_field"/>
				</td>
				<td class="small_text" valign="top" align="left">
					<xsl:variable name="link_download"><xsl:value-of select="link_download"/></xsl:variable>
					<xsl:variable name="lang_download_help"><xsl:value-of select="lang_download_help"/></xsl:variable>
					<xsl:variable name="lang_download"><xsl:value-of select="lang_download"/></xsl:variable>
					<a href="javascript:var w=window.open('{$link_download}','','')"
						onMouseOver="overlib('{$lang_download_help}', CAPTION, '{$lang_download}')"
						onMouseOut="nd()">
						<xsl:value-of select="lang_download"/></a>
				</td>
			</tr>
			<tr>
				<td colspan="6" width="100%">
					<xsl:call-template name="nextmatchs"/>
				</td>
			</tr>
		</table>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
				<xsl:call-template name="table_header"/>
				<xsl:choose>
					<xsl:when test="values">
						<xsl:call-template name="values"/>
					</xsl:when>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="table_add !=''">
						<xsl:apply-templates select="table_add"/>
					</xsl:when>
				</xsl:choose>
		</table>
	</xsl:template>

	<xsl:template match="table_add">
			<tr>
				<td height="50">
					<xsl:variable name="add_action"><xsl:value-of select="add_action"/></xsl:variable>
					<xsl:variable name="lang_add"><xsl:value-of select="lang_add"/></xsl:variable>
					<form method="post" action="{$add_action}">
						<input type="submit" name="add" value="{$lang_add}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_add_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</form>
				</td>
			</tr>
	</xsl:template>

<!-- add / edit -->

	<xsl:template match="edit">
		<div align="left">
		<table cellpadding="2" cellspacing="2" align="center">
			<xsl:choose>
				<xsl:when test="msgbox_data != ''">
					<tr>
						<td align="left" colspan="3">
							<xsl:call-template name="msgbox"/>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="value_project_id!=''">
						<td valign="top">
							<xsl:variable name="add_workorder_action"><xsl:value-of select="add_workorder_action"/>&amp;project_id=<xsl:value-of select="value_project_id"/></xsl:variable>
							<xsl:variable name="lang_add_workorder"><xsl:value-of select="lang_add_workorder"/></xsl:variable>
							<form method="post" action="{$add_workorder_action}">
							<input type="submit" class="forms" name="add_workorder" value="{$lang_add_workorder}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
										<xsl:value-of select="lang_add_workorder_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
							</form>
						</td>
				</xsl:when>
			</xsl:choose>

			<form method="post" name="form" action="{form_action}">
			<tr>
				<td>
<div align="center">
<table class="tabletab">
  <tr>
    <th id="tab1" class="activetab" valign="top" onclick="javascript:tab.display(1);">
      <table class="basic">
        <tr>
          <td id="starttab"></td>
          <td>
            <a href="#" tabindex="0" accesskey="1" onfocus="tab.display(1);" onclick="tab.display(1); return(false);"><xsl:value-of select="lang_general"/></a>
          </td>
          <td id="tweentab_r"></td>
        </tr>
      </table>
    </th>
    <th id="tab2" class="activetab" onclick="javascript:tab.display(2);">
      <table>
        <tr>
          <td id="tweentab_l"></td>
          <td>
            <a href="#" tabindex="0" accesskey="2" onfocus="tab.display(2);" onclick="tab.display(2); return(false);"><xsl:value-of select="lang_location"/></a>
          </td>
          <td id="tweentab_r"></td>
        </tr>
      </table>
    </th>
    <th id="tab3" class="activetab" onclick="javascript:tab.display(3);">
      <table>
        <tr>
          <td id="tweentab_l"></td>
          <td>
            <a href="#" tabindex="0" accesskey="3" onfocus="tab.display(3);" onclick="tab.display(3); return(false);"><xsl:value-of select="lang_time_and_budget"/></a>
          </td>
          <td id="tweentab_r"></td>
        </tr>
      </table>
    </th>
    <th id="tab4" class="activetab" onclick="javascript:tab.display(4);">
      <table>
        <tr>
          <td id="tweentab_l"></td>
          <td>
            <a href="#" tabindex="0" accesskey="4" onfocus="tab.display(4);" onclick="tab.display(4); return(false);"><xsl:value-of select="lang_coordination"/></a>
          </td>
          <td id="tweentab_r"></td>
        </tr>
      </table>
    </th>
    <th id="tab5" class="activetab" onclick="javascript:tab.display(5);">
      <table>
        <tr>
          <td id="tweentab_l"></td>
          <td>
            <a href="#" tabindex="0" accesskey="5" onfocus="tab.display(5);" onclick="tab.display(5); return(false);"><xsl:value-of select="lang_extra"/></a>
          </td>
          <td id="tweentab_r"></td>
        </tr>
      </table>
    </th>
    <th id="tab6" class="activetab" onclick="javascript:tab.display(6);">
      <table>
        <tr>
          <td id="tweentab_l"></td>
          <td>
            <a href="#" tabindex="0" accesskey="6" onfocus="tab.display(6);" onclick="tab.display(6); return(false);"><xsl:value-of select="lang_history"/></a>
          </td>
          <td id="tweentab_r"></td>
        </tr>
      </table>
    </th>
  </tr>
</table>
</div>

<div id="tabcontent1" class="activetab">
<table class="contenttab" align="left">

			<xsl:choose>
				<xsl:when test="value_project_id!=''">
					<tr>
						<td title="{lang_copy_project_statustext}" style="cursor:help">
							<xsl:value-of select="lang_copy_project"/>
						</td>
						<td>
							<input type="checkbox" name="values[copy_project]" value="True"  onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
										<xsl:value-of select="lang_copy_project_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr>
						<td>
							<xsl:value-of select="lang_project_id"/>
						</td>
						<td>
							<xsl:value-of select="value_project_id"/>
						</td>
					</tr>


					<xsl:choose>
						<xsl:when test="selected_request =''">
							<tr>
								<td valign="top">
									<a href="{link_select_request}" title="{lang_select_request_statustext}" onMouseover="window.status='{lang_select_request_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="lang_select_request"/></a>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>

					<xsl:for-each select="value_origin" >
						<xsl:variable name="link_origin_type"><xsl:value-of select="link"/></xsl:variable>
						<tr>
							<td valign ="top">
								<xsl:choose>
									<xsl:when test="type ='request'">
										<a href="{//link_select_request}" onMouseover="window.status='{//lang_select_request_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="//lang_select_request"/></a>
									</xsl:when>
									<xsl:otherwise>
											<xsl:value-of select="descr"/>
									</xsl:otherwise>
								</xsl:choose>
							</td>
							<td>
							<table>

							<xsl:for-each select="data">
							<tr>

							<td class="th_text"  align="left" >
								<xsl:variable name="link_request"><xsl:value-of select="//link_request"/>&amp;id=<xsl:value-of select="id"/></xsl:variable>
								<a href="{$link_origin_type}&amp;id={id}"  onMouseover="window.status='{//lang_origin_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="id"/></a>
								<xsl:text> </xsl:text>

								<xsl:choose>
									<xsl:when test="type ='request'">
									<input type="checkbox" name="values[delete_request][]" value="{id}"  onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
												<xsl:value-of select="//lang_delete_request_statustext"/>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
									</input>
									</xsl:when>
								</xsl:choose>
							</td>
							</tr>
							</xsl:for-each>
							</table>
							</td>
						</tr>
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise>
					<xsl:for-each select="value_origin" >
						<xsl:variable name="link_origin_type"><xsl:value-of select="link"/></xsl:variable>
						<tr>
							<td valign ="top">
								<xsl:value-of select="descr"/>
							</td>
							<td>
								<table>
									<xsl:for-each select="data">
										<tr>
											<td class="th_text"  align="left" >
												<xsl:variable name="link_request"><xsl:value-of select="//link_request"/>&amp;id=<xsl:value-of select="id"/></xsl:variable>
												<a href="{$link_origin_type}&amp;id={id}"  onMouseover="window.status='{//lang_origin_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="id"/></a>
												<xsl:text> </xsl:text>
											</td>
										</tr>
									</xsl:for-each>
								</table>
							</td>
						</tr>
					</xsl:for-each>
				</xsl:otherwise>
			</xsl:choose>

			<tr>
				<td valign="top">
					<xsl:value-of select="lang_name"/>
				</td>
				<td>
					<input type="hidden" name="values[origin]" value="{value_origin_type}"></input>
					<input type="hidden" name="values[origin_id]" value="{value_origin_id}"></input>
					<input type="text" name="values[name]" value="{value_name}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_name_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_descr"/>
				</td>
				<td>
					<textarea cols="60" rows="6" name="values[descr]" wrap="virtual" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_descr_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
						<xsl:value-of select="value_descr"/>
					</textarea>
				</td>
			</tr>
			<tr>
				<td>
					<xsl:value-of select="lang_category"/>
				</td>
				<td>
					<xsl:call-template name="cat_select"/>
				</td>
			</tr>
			<tr>
				<td>
					<xsl:value-of select="lang_status"/>
				</td>
				<td>
					<xsl:call-template name="status_select"/>
				</td>
			</tr>
			<xsl:choose>
				<xsl:when test="value_project_id!=''">
				<tr>
					<td>
						<xsl:value-of select="lang_confirm_status"/>
					</td>
					<td>
						<input type="checkbox" name="values[confirm_status]" value="True"  onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_confirm_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</td>
				</tr>
				</xsl:when>
			</xsl:choose>

</table>
</div>

<div id="tabcontent2" class="activetab">
<table class="contenttab" align="left">
			<xsl:choose>
				<xsl:when test="location_type='form'">
					<xsl:call-template name="location_form"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:call-template name="location_view"/>
				</xsl:otherwise>
			</xsl:choose>

			<tr>
				<td valign="top">
					<xsl:value-of select="lang_power_meter"/>
				</td>
				<td>
					<input type="text" name="values[power_meter]" value="{value_power_meter}" size="12" onMouseout="window.status='';return true;" >
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_power_meter_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>

</table>
</div>

<div id="tabcontent3" class="activetab">
<table class="contenttab" align="left">
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_start_date"/>
				</td>
				<td>
					<input type="text" id="values_start_date" name="values[start_date]" size="10" value="{value_start_date}" readonly="readonly" onMouseout="window.status='';return true;" >
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_start_date_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<img id="values_start_date-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;" />
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_end_date"/>
				</td>
				<td>
					<input type="text" id="values_end_date" name="values[end_date]" size="10" value="{value_end_date}" readonly="readonly" onMouseout="window.status='';return true;" >
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_end_date_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<img id="values_end_date-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;" />
				</td>
			</tr>

			<tr>
				<td valign="top">
					<xsl:value-of select="lang_budget"/>
				</td>
				<td>
					<input type="text" name="values[budget]" value="{value_budget}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_budget_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_reserve"/>
				</td>
				<td>
					<input type="text" name="values[reserve]" value="{value_reserve}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_reserve_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_sum"/>
				</td>
				<td>
					<xsl:value-of select="value_sum"/>
					<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_reserve_remainder"/>
				</td>
				<td>
					<xsl:value-of select="value_reserve_remainder"/>
					<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
					<xsl:text> </xsl:text> ( <xsl:value-of select="value_reserve_remainder_percent"/>
					<xsl:text> % )</xsl:text>
				</td>
			</tr>
			<tr>
				<td class="th_text" valign="top">
					<xsl:value-of select="lang_workorder_id"/>
				</td>
				<xsl:choose>
					<xsl:when test="sum_workorder_budget=''">
						<td class="th_text">
							<xsl:value-of select="lang_no_workorders"/>
						</td>
					</xsl:when>
					<xsl:otherwise>
					<td>
					<table width="100%" cellpadding="2" cellspacing="2" align="center">
						<xsl:apply-templates select="table_header_workorder_budget"/>
						<xsl:apply-templates select="workorder_budget"/>
						<tr class="th">
							<td class="th_text" width="5%" align="right">
								<xsl:value-of select="lang_sum"/>
							</td>
							<td class="th_text" width="5%" align="right">
								<xsl:value-of select="sum_workorder_budget"/>
							</td>
							<td class="th_text" width="5%" align="right">
								<xsl:value-of select="sum_workorder_calculation"/>
							</td>
							<td>
							</td>
							<td>
							</td>
						</tr>
					</table>
					</td>
					</xsl:otherwise>
				</xsl:choose>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_actual_cost"/>
				</td>
				<td>
					<xsl:value-of select="sum_workorder_actual_cost"/>
					<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
				</td>
			</tr>
</table>
</div>

<div id="tabcontent4" class="activetab">
<table class="contenttab" align="left">
			<tr>
				<td>
					<xsl:value-of select="lang_coordinator"/>
				</td>
				<td>
					<xsl:call-template name="user_id_select"/>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_branch"/>
				</td>
				<td>
					<xsl:variable name="lang_branch_statustext"><xsl:value-of select="lang_branch_statustext"/></xsl:variable>
						<select name="values[branch][]" class="forms" multiple="multiple" onMouseover="window.status='{$lang_branch_statustext}'; return true;" onMouseout="window.status='';return true;">
							<xsl:apply-templates select="branch_list"/>
						</select>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_other_branch"/>
				</td>
				<td>
					<input type="text" name="values[other_branch]" value="{value_other_branch}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_other_branch_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<tr>
				<td>
					<xsl:value-of select="lang_key_fetch"/>
				</td>
				<td>
					<xsl:variable name="lang_key_fetch_statustext"><xsl:value-of select="lang_key_fetch_statustext"/></xsl:variable>
						<select name="values[key_fetch]" class="forms" onMouseover="window.status='{$lang_key_fetch_statustext}'; return true;" onMouseout="window.status='';return true;">
							<option value=""><xsl:value-of select="lang_no_key_fetch"/></option>
							<xsl:apply-templates select="key_fetch_list"/>
						</select>
				</td>
			</tr>
			<tr>
				<td>
					<xsl:value-of select="lang_key_deliver"/>
				</td>
				<td>
					<xsl:variable name="lang_key_deliver_statustext"><xsl:value-of select="lang_key_deliver_statustext"/></xsl:variable>
						<select name="values[key_deliver]" class="forms" onMouseover="window.status='{$lang_key_deliver_statustext}'; return true;" onMouseout="window.status='';return true;">
							<option value=""><xsl:value-of select="lang_no_key_deliver"/></option>
							<xsl:apply-templates select="key_deliver_list"/>
						</select>
				</td>
			</tr>
			<tr>
				<td>
					<xsl:value-of select="lang_key_responsible"/>
				</td>
				<td>
					<xsl:variable name="lang_key_responsible_statustext"><xsl:value-of select="lang_key_responsible_statustext"/></xsl:variable>
						<select name="values[key_responsible]" class="forms" onMouseover="window.status='{$lang_key_responsible_statustext}'; return true;" onMouseout="window.status='';return true;">
							<option value=""><xsl:value-of select="lang_no_key_responsible"/></option>
							<xsl:apply-templates select="key_responsible_list"/>
						</select>
				</td>
			</tr>
</table>
</div>

<div id="tabcontent5" class="activetab">
<table class="contenttab" align="left">
			<tr>
			<xsl:choose>
				<xsl:when test="need_approval='yes'">
				<td valign="top">
					<xsl:value-of select="lang_ask_approval"/>
				</td>
				<td>
				<table>
				<tr>
				<td>
					<input type="checkbox" name="values[approval]" value="True"  onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_ask_approval_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
				<td>
					<input type="text" name="values[mail_address]" value="{value_approval_mail_address}" size="40" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_ask_approval_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
				</tr>
				</table>
				</td>
				</xsl:when>
			</xsl:choose>
			</tr>

			<tr>
				<td valign="top">
					<xsl:value-of select="lang_remark"/>
				</td>
				<td>
					<textarea cols="60" rows="6" name="values[remark]" wrap="virtual" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_remark_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
						<xsl:value-of select="value_remark"/>
					</textarea>
				</td>
			</tr>
</table>
</div>
		</td>
	</tr>
			<tr height="50">
				<td>
					<xsl:variable name="lang_save"><xsl:value-of select="lang_save"/></xsl:variable>
					<input type="submit" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_save_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>

			</form>
			<tr>
				<td>
					<xsl:variable name="done_action"><xsl:value-of select="done_action"/></xsl:variable>
					<xsl:variable name="lang_done"><xsl:value-of select="lang_done"/></xsl:variable>
					<form method="post" action="{$done_action}">
						<input type="submit" name="done" value="{$lang_done}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_done_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</form>
				</td>
			</tr>
		</table>
<div id="tabcontent6" class="activetab">

		<hr noshade="noshade" width="100%" align="center" size="1"/>
		<table class="contenttab" width="80%" cellpadding="2" cellspacing="2" align="center">
			<xsl:choose>
				<xsl:when test="record_history=''">
					<tr>
						<td class="th_text" align="center">
							<xsl:value-of select="lang_no_history"/>
						</td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
					<tr>
						<td class="th_text" align="left">
							<xsl:value-of select="lang_history"/>
						</td>
					</tr>
					<xsl:apply-templates select="table_header_history"/>
					<xsl:apply-templates select="record_history"/>
				</xsl:otherwise>
			</xsl:choose>
		</table>


</div>

		</div>
		<hr noshade="noshade" width="100%" align="center" size="1"/>

<script language="JavaScript1.1" type="text/javascript">
  var tab = new Tabs(6,'activetab','inactivetab','tab','tabcontent','','','tabpage');
  tab.init();
</script>

	</xsl:template>

	<xsl:template match="workorder_budget">
		<xsl:variable name="workorder_link"><xsl:value-of select="//workorder_link"/>&amp;id=<xsl:value-of select="workorder_id"/></xsl:variable>
			<tr>
				<xsl:attribute name="class">
					<xsl:choose>
						<xsl:when test="@class">
							<xsl:value-of select="@class"/>
						</xsl:when>
						<xsl:when test="position() mod 2 = 0">
							<xsl:text>row_off</xsl:text>
						</xsl:when>
						<xsl:otherwise>
							<xsl:text>row_on</xsl:text>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:attribute>

				<td align="right">
					<a href="{$workorder_link}"><xsl:value-of select="workorder_id"/></a>
				</td>
				<td align="right">
					<xsl:value-of select="budget"/>
				</td>
				<td align="right">
					<xsl:value-of select="calculation"/>
				</td>
				<td align="left">
					<xsl:value-of select="vendor_name"/>
				</td>
				<td align="left">
					<xsl:value-of select="status"/>
				</td>
			</tr>
	</xsl:template>

	<xsl:template match="branch_list">
	<xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="key_responsible_list">
	<xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="key_fetch_list">
	<xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>


	<xsl:template match="key_deliver_list">
	<xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>


	<xsl:template match="table_header_history">
			<tr class="th">
				<td class="th_text" width="20%" align="left">
					<xsl:value-of select="lang_date"/>
				</td>
				<td class="th_text" width="10%" align="left">
					<xsl:value-of select="lang_user"/>
				</td>
				<td class="th_text" width="30%" align="left">
					<xsl:value-of select="lang_action"/>
				</td>
				<td class="th_text" width="10%" align="left">
					<xsl:value-of select="lang_new_value"/>
				</td>
			</tr>
	</xsl:template>

	<xsl:template match="record_history">
			<tr>
				<xsl:attribute name="class">
					<xsl:choose>
						<xsl:when test="@class">
							<xsl:value-of select="@class"/>
						</xsl:when>
						<xsl:when test="position() mod 2 = 0">
							<xsl:text>row_off</xsl:text>
						</xsl:when>
						<xsl:otherwise>
							<xsl:text>row_on</xsl:text>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:attribute>
				<td align="left">
					<xsl:value-of select="value_date"/>
				</td>
				<td align="left">
					<xsl:value-of select="value_user"/>
				</td>
				<td align="left">
					<xsl:value-of select="value_action"/>
				</td>
				<td align="left">
					<xsl:value-of select="value_new_value"/>
				</td>
			</tr>
	</xsl:template>

	<xsl:template match="table_header_workorder_budget">
			<tr class="th">
				<td class="th_text" width="4%" align="right">
					<xsl:value-of select="lang_workorder_id"/>
				</td>
				<td class="th_text" width="10%" align="right">
					<xsl:value-of select="lang_budget"/>
				</td>
				<td class="th_text" width="10%" align="right">
					<xsl:value-of select="lang_calculation"/>
				</td>
				<td class="th_text" width="10%" align="right">
					<xsl:value-of select="lang_vendor"/>
				</td>
				<td class="th_text" width="10%" align="right">
					<xsl:value-of select="lang_status"/>
				</td>
			</tr>
	</xsl:template>


<!-- view -->

	<xsl:template match="view">
		<div align="left">
		<table cellpadding="2" cellspacing="2" width="80%" align="center">
			<xsl:choose>
				<xsl:when test="msgbox_data != ''">
					<tr>
						<td align="left" colspan="3">
							<xsl:call-template name="msgbox"/>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<tr>
				<td>
					<xsl:value-of select="lang_project_id"/>
				</td>
				<td>
					<xsl:value-of select="value_project_id"/>
				</td>
			</tr>

			<xsl:for-each select="value_origin" >
				<xsl:variable name="link_origin_type"><xsl:value-of select="link"/></xsl:variable>
				<tr>
					<td valign ="top">
						<xsl:value-of select="descr"/>
					</td>
						<td class="th_text"  align="left" >
						<xsl:for-each select="data">
							<a href="{$link_origin_type}&amp;id={id}"  onMouseover="window.status='{//lang_origin_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="id"/></a>
							<xsl:text> </xsl:text>
						</xsl:for-each>
					</td>
				</tr>
			</xsl:for-each>

			<tr>
				<td valign="top">
					<xsl:value-of select="lang_name"/>
				</td>
				<td>
					<xsl:value-of select="value_name"/>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_descr"/>
				</td>
				<td>
					<xsl:value-of select="value_descr"/>
				</td>
			</tr>
			<tr>
				<td>
					<xsl:value-of select="lang_category"/>
				</td>
				<xsl:for-each select="cat_list" >
					<xsl:choose>
						<xsl:when test="selected='selected'">
							<td>
								<xsl:value-of select="name"/>
							</td>
						</xsl:when>
					</xsl:choose>
				</xsl:for-each>
			</tr>
			<xsl:call-template name="location_view"/>

			<xsl:choose>
				<xsl:when test="contact_phone !=''">
					<tr>
						<td class="th_text"  align="left">
							<xsl:value-of select="lang_contact_phone"/>
						</td>
						<td  align="left">
							<xsl:value-of select="contact_phone"/>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>

			<tr>
				<td valign="top">
					<xsl:value-of select="lang_power_meter"/>
				</td>
				<td>
					<xsl:value-of select="value_power_meter"/>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_budget"/>
				</td>
				<td>
					<xsl:value-of select="value_budget"/>
					<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_reserve"/>
				</td>
				<td>
					<xsl:value-of select="value_reserve"/>
					<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_sum"/>
				</td>
				<td>
					<xsl:value-of select="value_sum"/>
					<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_reserve_remainder"/>
				</td>
				<td>
					<xsl:value-of select="value_reserve_remainder"/>
					<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
					<xsl:text> </xsl:text> ( <xsl:value-of select="value_reserve_remainder_percent"/>
					<xsl:text> % )</xsl:text>
				</td>
			</tr>

			<tr>
				<td class="th_text" valign="top">
					<xsl:value-of select="lang_workorder_id"/>
				</td>
				<xsl:choose>
					<xsl:when test="sum_workorder_budget=''">
						<td class="th_text">
							<xsl:value-of select="lang_no_workorders"/>
						</td>
					</xsl:when>
					<xsl:otherwise>
					<td>
					<table width="100%" cellpadding="2" cellspacing="2" align="center">
						<xsl:apply-templates select="table_header_workorder_budget"/>
						<xsl:apply-templates select="workorder_budget"/>
						<tr class="th">
							<td class="th_text" width="5%" align="right">
								<xsl:value-of select="lang_sum"/>
							</td>
							<td class="th_text" width="5%" align="right">
								<xsl:value-of select="sum_workorder_budget"/>
							</td>
							<td class="th_text" width="5%" align="right">
								<xsl:value-of select="sum_workorder_calculation"/>
							</td>
							<td>
							</td>
							<td>
							</td>
						</tr>
					</table>
					</td>
					</xsl:otherwise>
				</xsl:choose>
			</tr>

			<tr>
				<td valign="top">
					<xsl:value-of select="lang_actual_cost"/>
				</td>
				<td>
					<xsl:value-of select="sum_workorder_actual_cost"/>
					<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
				</td>
			</tr>

			<tr>
				<td>
					<xsl:value-of select="lang_coordinator"/>
				</td>
				<xsl:for-each select="user_list" >
					<xsl:choose>
						<xsl:when test="selected">
							<td>
								<xsl:value-of select="name"/>
							</td>
						</xsl:when>
					</xsl:choose>
				</xsl:for-each>
			</tr>
			<tr>
				<td>
					<xsl:value-of select="lang_status"/>
				</td>
					<xsl:for-each select="status_list" >
						<xsl:choose>
							<xsl:when test="selected">
								<td>
									<xsl:value-of select="name"/>
								</td>
							</xsl:when>
						</xsl:choose>
					</xsl:for-each>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_start_date"/>
				</td>
				<td>
					<xsl:value-of select="value_start_date"/>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_end_date"/>
				</td>
				<td>
					<xsl:value-of select="value_end_date"/>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_branch"/>
				</td>
				<td>
					<xsl:for-each select="branch_list[selected='selected']" >
						<xsl:value-of select="name"/>
						<xsl:if test="position() != last()">, </xsl:if>
					</xsl:for-each>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_other_branch"/>
				</td>
				<td>
					<xsl:value-of select="value_other_branch"/>
				</td>
			</tr>
			<tr>
				<td>
					<xsl:value-of select="lang_key_fetch"/>
				</td>
				<td>
					<xsl:for-each select="key_fetch_list" >
						<xsl:choose>
							<xsl:when test="selected">
								<xsl:value-of select="name"/>
							</xsl:when>
						</xsl:choose>
					</xsl:for-each>
				</td>
			</tr>
			<tr>
				<td>
					<xsl:value-of select="lang_key_deliver"/>
				</td>
				<td>
					<xsl:for-each select="key_deliver_list" >
						<xsl:choose>
							<xsl:when test="selected">
								<xsl:value-of select="name"/>
							</xsl:when>
						</xsl:choose>
					</xsl:for-each>
				</td>
			</tr>
			<tr>
				<td>
					<xsl:value-of select="lang_key_responsible"/>
				</td>

				<td>
					<xsl:for-each select="key_responsible_list" >
						<xsl:choose>
							<xsl:when test="selected">
								<xsl:value-of select="name"/>
							</xsl:when>
						</xsl:choose>
					</xsl:for-each>
				</td>
			</tr>
			<tr height="50">
				<td>
					<xsl:variable name="done_action"><xsl:value-of select="done_action"/></xsl:variable>
					<xsl:variable name="lang_done"><xsl:value-of select="lang_done"/></xsl:variable>
					<form method="post" action="{$done_action}">
					<input type="submit" class="forms" name="done" value="{$lang_done}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_done_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>

					</form>
					<xsl:variable name="edit_action"><xsl:value-of select="edit_action"/></xsl:variable>
					<xsl:variable name="lang_edit"><xsl:value-of select="lang_edit"/></xsl:variable>
					<form method="post" action="{$edit_action}">
					<input type="submit" class="forms" name="edit" value="{$lang_edit}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_edit_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					</form>
				</td>
			</tr>
		</table>
		<hr noshade="noshade" width="100%" align="center" size="1"/>
		<table width="80%" cellpadding="2" cellspacing="2" align="center">
			<xsl:choose>
				<xsl:when test="record_history=''">
					<tr>
						<td class="th_text" align="left">
							<xsl:value-of select="lang_no_history"/>
						</td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
					<tr>
						<td class="th_text" align="left">
							<xsl:value-of select="lang_history"/>
						</td>
					</tr>
					<xsl:apply-templates select="table_header_history"/>
					<xsl:apply-templates select="record_history"/>
				</xsl:otherwise>
			</xsl:choose>
		</table>
		</div>
		<hr noshade="noshade" width="100%" align="center" size="1"/>
	</xsl:template>
