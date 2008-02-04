<!-- $Id: workorder.xsl,v 1.13 2007/03/25 10:28:21 sigurdne Exp $ -->

	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="edit">
				<xsl:apply-templates select="edit"/>
			</xsl:when>
			<xsl:when test="add">
				<xsl:apply-templates select="add"/>
			</xsl:when>
			<xsl:when test="view">
				<xsl:apply-templates select="view"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="list_workorder"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	<xsl:template match="add">
		
		<xsl:apply-templates select="menu"/> 
		<table width="50%"  cellpadding="2" cellspacing="2" align="center">

			<tr height="50">
				<td>
					<xsl:variable name="add_action"><xsl:value-of select="add_action"/></xsl:variable>
					<xsl:variable name="lang_add"><xsl:value-of select="lang_add"/></xsl:variable>
					<form method="post" action="{$add_action}">
					<input type="submit" class="forms" name="add" value="{$lang_add}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_add_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					</form>

					<xsl:variable name="search_action"><xsl:value-of select="search_action"/></xsl:variable>
					<xsl:variable name="lang_search"><xsl:value-of select="lang_search"/></xsl:variable>
					<form method="post" action="{$search_action}">
					<input type="submit" class="forms" name="search" value="{$lang_search}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_search_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					</form>
					
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
				</td>
			</tr>
		</table>
	</xsl:template>

	<xsl:template match="list_workorder">		
		<xsl:apply-templates select="menu"/> 
		<table width="100%"  cellpadding="2" cellspacing="2" align="center">
			<tr>
			<xsl:choose>
				<xsl:when test="group_filters != ''">
					<xsl:variable name="select_action"><xsl:value-of select="select_action"/></xsl:variable>
					<form method="post" name="search" action="{$select_action}">
						<td>
							<xsl:call-template name="cat_select"/>
						</td>
						<td align="left">
							<xsl:call-template name="status_select"/>
						</td>
						<td align="left">
							<xsl:call-template name="wo_hour_cat_select"/>
						</td>
						<td align="center">
							<xsl:call-template name="user_id_select"/>
						</td>
						<td align="right">
							<xsl:call-template name="search_field_workorder_grouped"/>
						</td>

					</form>
				</xsl:when>
				<xsl:otherwise>
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
						<xsl:call-template name="search_field_workorder"/>
					</td>
				</xsl:otherwise>
			</xsl:choose>				
				<td class="small_text" valign="top" align="left">
					<xsl:variable name="link_excel"><xsl:value-of select="link_excel"/></xsl:variable>
					<xsl:variable name="lang_excel_help"><xsl:value-of select="lang_excel_help"/></xsl:variable>
					<xsl:variable name="lang_excel"><xsl:value-of select="lang_excel"/></xsl:variable>
					<a href="javascript:var w=window.open('{$link_excel}','','')"
						onMouseOver="overlib('{$lang_excel_help}', CAPTION, '{$lang_excel}')"
						onMouseOut="nd()">
						<xsl:value-of select="lang_excel"/></a>
				</td>
			</tr>
			<tr>
				<td colspan="16" width="100%">
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


	<xsl:template name="search_field_workorder">
		<xsl:variable name="select_url"><xsl:value-of select="select_action"/></xsl:variable>
		<xsl:variable name="query"><xsl:value-of select="query"/></xsl:variable>
		<xsl:variable name="search_vendor"><xsl:value-of select="search_vendor"/></xsl:variable>
		<xsl:variable name="lang_search"><xsl:value-of select="lang_search"/></xsl:variable>
		<table>
		<tr>
			<td class="small_text" valign="top" align="left">
				<xsl:variable name="link_date_search"><xsl:value-of select="link_date_search"/></xsl:variable>
				<xsl:variable name="lang_date_search_help"><xsl:value-of select="lang_date_search_help"/></xsl:variable>
				<xsl:variable name="lang_date_search"><xsl:value-of select="lang_date_search"/></xsl:variable>
				<a href="javascript:var w=window.open('{$link_date_search}','','width=300,height=300')"
					onMouseOver="overlib('{$lang_date_search_help}', CAPTION, '{$lang_date_search}')"
					onMouseOut="nd()">
					<xsl:value-of select="lang_date_search"/></a>					

				<table>
				<xsl:choose>
					<xsl:when test="start_date!=''">
						<tr>
							<td class="small_text" align="left">
								<xsl:value-of select="start_date"/>
							</td>
						</tr>
						<tr>
							<td class="small_text" align="left">
								<xsl:value-of select="end_date"/>
							</td>
						</tr>
					</xsl:when>
					<xsl:otherwise>
						<tr>
							<td class="small_text" align="left">
								<xsl:value-of select="lang_none"/>
							</td>
						</tr>
					</xsl:otherwise>
				</xsl:choose>
				</table>
			</td>

			<td valign="top" align="right">
			<form method="post" name="search" action="{$select_url}">
				<input type="hidden" name="start_date" value="{start_date}"></input>
				<input type="hidden" name="end_date" value="{end_date}"></input>
				<input type="text" name="search_vendor" value="{$search_vendor}" onMouseout="window.status='';return true;">
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_searchvendor_statustext"/>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>
				<input type="text" name="query" value="{$query}" onMouseout="window.status='';return true;">
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_searchfield_statustext"/>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>
				<xsl:text> </xsl:text>
				<input type="submit" name="submit" value="{$lang_search}" onMouseout="window.status='';return true;"> 
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_searchbutton_statustext"/>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>
			</form>
			</td>
		</tr>
		</table>
	</xsl:template>

	<xsl:template name="search_field_workorder_grouped">
		<xsl:variable name="query"><xsl:value-of select="query"/></xsl:variable>
		<xsl:variable name="search_vendor"><xsl:value-of select="search_vendor"/></xsl:variable>
		<xsl:variable name="lang_search"><xsl:value-of select="lang_search"/></xsl:variable>
		<table>
		<tr>
			<td class="small_text" valign="top" align="left">
				<xsl:variable name="link_date_search"><xsl:value-of select="link_date_search"/></xsl:variable>
				<xsl:variable name="lang_date_search_help"><xsl:value-of select="lang_date_search_help"/></xsl:variable>
				<xsl:variable name="lang_date_search"><xsl:value-of select="lang_date_search"/></xsl:variable>
				<a href="javascript:var w=window.open('{$link_date_search}','','width=300,height=300')"
					onMouseOver="overlib('{$lang_date_search_help}', CAPTION, '{$lang_date_search}')"
					onMouseOut="nd()">
					<xsl:value-of select="lang_date_search"/></a>					

				<table>
				<xsl:choose>
					<xsl:when test="start_date!=''">
						<tr>
							<td class="small_text" align="left">
								<xsl:value-of select="start_date"/>
							</td>
						</tr>
						<tr>
							<td class="small_text" align="left">
								<xsl:value-of select="end_date"/>
							</td>
						</tr>
					</xsl:when>
					<xsl:otherwise>
						<tr>
							<td class="small_text" align="left">
								<xsl:value-of select="lang_none"/>
							</td>
						</tr>
					</xsl:otherwise>
				</xsl:choose>
				</table>
			</td>

			<td valign="top" align="right">
				<input type="hidden" name="start_date" value="{start_date}"></input>
				<input type="hidden" name="end_date" value="{end_date}"></input>
				<input type="text" name="search_vendor" value="{$search_vendor}" onMouseout="window.status='';return true;">
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_searchvendor_statustext"/>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>
				<input type="text" name="query" value="{$query}" onMouseout="window.status='';return true;">
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_searchfield_statustext"/>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>
				<xsl:text> </xsl:text>
				<input type="submit" name="submit" value="{$lang_search}" onMouseout="window.status='';return true;"> 
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_searchbutton_statustext"/>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>
			</td>
		</tr>
		</table>
	</xsl:template>


<!-- add / edit -->

	<xsl:template match="edit">
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
			<xsl:choose>
				<xsl:when test="value_workorder_id!=''">
						<td>
						<table>
						<tr>
						<td valign="top" >
							<xsl:variable name="calculate_action"><xsl:value-of select="calculate_action"/>&amp;workorder_id=<xsl:value-of select="value_workorder_id"/></xsl:variable>
							<xsl:variable name="lang_calculate"><xsl:value-of select="lang_calculate"/></xsl:variable>
							<form method="post" action="{$calculate_action}">
							<input type="submit" class="forms" name="calculate" value="{$lang_calculate}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
										<xsl:value-of select="lang_calculate_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
							</form>
						</td>
						<td valign="top">
							<xsl:variable name="send_action"><xsl:value-of select="send_action"/>&amp;workorder_id=<xsl:value-of select="value_workorder_id"/></xsl:variable>
							<xsl:variable name="lang_send"><xsl:value-of select="lang_send"/></xsl:variable>
							<form method="post" action="{$send_action}">
							<input type="submit" class="forms" name="send" value="{$lang_send}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
										<xsl:value-of select="lang_send_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
							</form>
						</td>
						</tr>
						</table>
						</td>
				</xsl:when>
			</xsl:choose>
			<xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
			<form method="post" name="form" action="{$form_action}">

<div align="center">
<table class="tabletab">
  <tr>
    <th id="tab1" class="activetab" valign="top" onclick="javascript:tab.display(1);">
      <table class="basic">
        <tr>
          <td id="starttab"></td>
          <td>
            <a href="#" tabindex="0" accesskey="1" onfocus="tab.display(1);" onclick="tab.display(1); return(false);"><xsl:value-of select="lang_project_info"/></a>
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
            <a href="#" tabindex="0" accesskey="2" onfocus="tab.display(2);" onclick="tab.display(2); return(false);"><xsl:value-of select="lang_general"/></a>
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
						<td>
							<xsl:value-of select="lang_project_id"/>
						</td>
						<td>
							<xsl:variable name="project_link"><xsl:value-of select="project_link"/>&amp;id=<xsl:value-of select="value_project_id"/></xsl:variable>
							<a href="{$project_link}"><xsl:value-of select="value_project_id"/></a>
							<input type="hidden" name="values[project_id]" value="{value_project_id}"></input>
						</td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_project_id"/>
					</td>
					<td>
						<input type="text" name="values[project_id]" value="" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_title_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</td>
			</tr>
				</xsl:otherwise>
			</xsl:choose>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_project_name"/>
				</td>
				<td>
					<xsl:value-of select="value_project_name"/>
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
			<xsl:choose>
				<xsl:when test="location_type='form'">
					<xsl:call-template name="location_form"/>
				</xsl:when>
				<xsl:otherwise>
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

				</xsl:otherwise>
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
</table>
</div>

<div id="tabcontent2" class="activetab">
<table class="contenttab" align="left">
			<xsl:choose>
				<xsl:when test="value_workorder_id!=''">
					<tr>
						<td>
							<xsl:value-of select="lang_workorder_id"/>
						</td>
						<td>
							<xsl:value-of select="value_workorder_id"/>
						</td>
					</tr>	
					<tr>
						<td>
							<xsl:value-of select="lang_copy_workorder"/>
						</td>
						<td>
							<input type="checkbox" name="values[copy_workorder]" value="True"  onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
										<xsl:value-of select="lang_copy_workorder_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_title"/>
				</td>
				<td>
					<input type="text" name="values[title]" value="{value_title}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_title_statustext"/>
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
					<xsl:value-of select="lang_status"/>
				</td>
				<td>
					<xsl:call-template name="status_select"/>
				</td>
			</tr>
			<xsl:choose>
				<xsl:when test="value_workorder_id!=''">
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


				<xsl:call-template name="vendor_form"/>
				<xsl:call-template name="b_account_form"/>

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
					<xsl:value-of select="lang_addition_rs"/>
				</td>
				<td>
					<input type="text" name="values[addition_rs]" value="{value_addition_rs}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_addition_rs_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_addition_percentage"/>
				</td>
				<td>
					<input type="text" name="values[addition_percentage]" value="{value_addition_percentage}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_addition_percentage_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<xsl:text> </xsl:text> [ % ]
				</td>
			</tr>
			<tr>
				<td>
					<xsl:value-of select="lang_charge_tenant"/>
				</td>
				<td>
				<xsl:choose>
					<xsl:when test="charge_tenant='1'">
						<input type="checkbox" name="values[charge_tenant]" value="1" checked="checked" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_charge_tenant_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</xsl:when>
					<xsl:otherwise>
						<input type="checkbox" name="values[charge_tenant]" value="1"  onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_charge_tenant_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</xsl:otherwise>
				</xsl:choose>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_calculation"/>
				</td>
				<td>
					<xsl:value-of select="value_calculation"/>
					<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
					<xsl:value-of select="lang_incl_tax"/>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_actual_cost"/>
				</td>
				<td>
					<xsl:value-of select="actual_cost"/>
					<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
				</td>
			</tr>

</table>
</div>

<div id="tabcontent4" class="activetab">
<table class="contenttab" align="left">
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
					<xsl:for-each select="key_responsible_list" >
						<xsl:choose>
							<xsl:when test="selected">
								<xsl:value-of select="name"/>
							</xsl:when>
						</xsl:choose>
					</xsl:for-each>
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

					<input type="text" name="values[mail_address]" value="{value_approval_mail_address}" onMouseout="window.status='';return true;">
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
		<table width="80%" cellpadding="2" cellspacing="2" align="center">
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
  var tab = new Tabs(6,'activetab','inactivetab','tab','tabcontent','','','tabpage','',2);
  tab.init();
  tab.display(2);
</script>
	</xsl:template>

<!--
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
-->
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
					<xsl:value-of select="lang_sum"/>
				</td>
			</tr>
	</xsl:template>


<!-- view -->

	<xsl:template match="view">
		<div align="left">		
		<table cellpadding="2" cellspacing="2" width="80%" align="center">
			<tr>
				<td width="25%" >
					<xsl:value-of select="lang_project_id"/>
				</td>
				<td width="75%">
					<xsl:variable name="project_link"><xsl:value-of select="project_link"/>&amp;id=<xsl:value-of select="value_project_id"/></xsl:variable>
					<a href="{$project_link}"><xsl:value-of select="value_project_id"/></a>
					<input type="hidden" name="values[project_id]" value="{value_project_id}"></input>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_project_name"/>
				</td>
				<td>
					<xsl:value-of select="value_project_name"/>
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
					<xsl:value-of select="lang_charge_tenant"/>
				</td>
				<td>
				<xsl:choose>
					<xsl:when test="charge_tenant='1'">
						X
					</xsl:when>
				</xsl:choose>
				</td>
			</tr>
			<tr>
				<td>
					<xsl:value-of select="lang_workorder_id"/>
				</td>
				<td>
					<xsl:value-of select="value_workorder_id"/>
				</td>
			</tr>	
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_title"/>
				</td>
				<td>
					<xsl:value-of select="value_title"/>
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
				<td valign="top">
					<xsl:value-of select="lang_vendor"/>
				</td>
				<td>
					<xsl:value-of select="value_vendor_id"/>
					<xsl:text> - </xsl:text>
					<xsl:value-of select="value_vendor_name"/>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_b_account"/>
				</td>
				<td>
					<xsl:value-of select="value_b_account_id"/>
					<xsl:text> - </xsl:text>
					<xsl:value-of select="value_b_account_name"/>
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
					<xsl:value-of select="lang_addition_rs"/>
				</td>
				<td>
					<xsl:value-of select="value_addition_rs"/>
					<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_addition_percentage"/>
				</td>
				<td>
					<xsl:value-of select="value_addition_percentage"/>
					<xsl:text> </xsl:text> [ % ]
				</td>
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
				<td>
					<xsl:value-of select="lang_key_fetch"/>
				</td>
				<xsl:for-each select="key_fetch_list" >
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
					<xsl:value-of select="lang_key_deliver"/>
				</td>
				<xsl:for-each select="key_deliver_list" >
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
			<tr>
				<td align="left" colspan="2">
					<table>
						<xsl:call-template name="hour_data_view"/>
					</table>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_actual_cost"/>
				</td>
				<td>
					<xsl:value-of select="actual_cost"/>
					<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
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
