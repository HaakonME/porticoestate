<!-- $Id: pricebook.xsl,v 1.4 2007/01/05 14:33:36 sigurdne Exp $ -->

	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="edit_activity">
				<xsl:apply-templates select="edit_activity"/>
			</xsl:when>
			<xsl:when test="edit_agreement_group">
				<xsl:apply-templates select="edit_agreement_group"/>
			</xsl:when>
			<xsl:when test="view">
				<xsl:apply-templates select="view"/>
			</xsl:when>
			<xsl:when test="list_activities">
				<xsl:apply-templates select="list_activities"/>
			</xsl:when>
			<xsl:when test="agreement_group">
				<xsl:apply-templates select="agreement_group"/>
			</xsl:when>
			<xsl:when test="prizing">
				<xsl:apply-templates select="prizing"/>
			</xsl:when>
			<xsl:when test="list_activity_vendor">
				<xsl:apply-templates select="list_activity_vendor"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="list"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	<xsl:template match="list">
		<xsl:call-template name="menu"/> 
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
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
				<td align="left">
					<xsl:call-template name="cat_filter"/>
				</td>
				<td align="right">
					<xsl:call-template name="search_field"/>
				</td>
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
				<td colspan="3" width="100%">
					<xsl:call-template name="nextmatchs"/>
				</td>
			</tr>
		</table>
		<xsl:variable name="update_action"><xsl:value-of select="update_action"/></xsl:variable>
		<form method="post" name="form" action="{$update_action}">
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
				<xsl:apply-templates select="table_header"/>
				<xsl:choose>
					<xsl:when test="values[activity_id]!=''">
						<xsl:apply-templates select="values"/>
						<xsl:choose>
							<xsl:when test="table_update!=''">
								<tr>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
								    	<td align="center">
										<xsl:variable name="img_check"><xsl:value-of select="img_check"/></xsl:variable>
									    	<a href="javascript:check_all_checkbox('values[update]')"><img src="{$img_check}" border="0" height="16" width="21" alt="{lang_select_all}"/></a>
								    	</td>
								  </tr>
							</xsl:when>
						</xsl:choose>						
					</xsl:when>
				</xsl:choose>						
		</table>
		<table width="70%" cellpadding="2" cellspacing="2" align="center">
				<xsl:choose>
					<xsl:when test="table_update!=''">
						<xsl:apply-templates select="table_update"/>
					</xsl:when>
				</xsl:choose>						
		</table>
		</form>
	</xsl:template>

	<xsl:template match="table_header">
		<xsl:variable name="sort_num"><xsl:value-of select="sort_num"/></xsl:variable>
		<xsl:variable name="sort_total_cost"><xsl:value-of select="sort_total_cost"/></xsl:variable>
			<tr class="th">
				<td class="th_text" width="10%" align="right">
					<a href="{$sort_num}"><xsl:value-of select="lang_num"/></a>
				</td>
				<td class="th_text" width="5%" align="right">
					<xsl:value-of select="lang_vendor"/>
				</td>
				<td class="th_text" width="5%" align="right">
					<xsl:value-of select="lang_branch"/>
				</td>
				<td class="th_text" width="20%" align="right">
					<xsl:value-of select="lang_descr"/>
				</td>
				<td class="th_text" width="5%" align="right">
					<xsl:value-of select="lang_unit"/>
				</td>
				<td class="th_text" width="10%" align="right">
					<xsl:value-of select="lang_w_cost"/>
				</td>
				<td class="th_text" width="10%" align="right">
					<xsl:value-of select="lang_m_cost"/>
				</td>
				<td class="th_text" width="10%" align="right">
					<a href="{$sort_total_cost}"><xsl:value-of select="lang_total_cost"/></a>
				</td>
				<td class="th_text" width="5%" align="right">
					<xsl:value-of select="lang_last_index"/>
				</td>
				<td class="th_text" width="5%" align="right">
					<xsl:value-of select="lang_index_count"/>
				</td>
				<td class="th_text" width="5%" align="right">
					<xsl:value-of select="lang_prizing"/>
				</td>
				<td class="th_text" width="5%" align="center">
					<xsl:value-of select="lang_edit"/>
				</td>
				<td class="th_text" width="5%" align="center">
					<xsl:value-of select="lang_select"/>
				</td>
			</tr>
	</xsl:template>

	<xsl:template match="values">
		<xsl:variable name="lang_edit_statustext"><xsl:value-of select="lang_edit_statustext"/></xsl:variable>
		<xsl:variable name="lang_prizing_statustext"><xsl:value-of select="lang_prizing_statustext"/></xsl:variable>
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
					<xsl:value-of select="num"/>
					<input type="hidden" name="values[activity_id][{counter}]" value="{activity_id}" ></input>
					<input type="hidden" name="values[vendor_id][{counter}]" value="{vendor_id}" ></input>
					<input type="hidden" name="values[old_m_cost][{counter}]" value="{m_cost}" ></input>
					<input type="hidden" name="values[old_w_cost][{counter}]" value="{w_cost}" ></input>
					<input type="hidden" name="values[old_total_cost][{counter}]" value="{total_cost}" ></input>
				</td>
				<td align="right">
					<xsl:value-of select="vendor_id"/>
				</td>
				<td align="left">
					<xsl:value-of select="branch"/>
				</td>
				<td align="left">
					<xsl:value-of select="descr"/>
				</td>
				<td align="left">
					<xsl:value-of select="unit"/>
				</td>
				<td align="right">
					<xsl:value-of select="w_cost"/>
				</td>
				<td align="right">
					<xsl:value-of select="m_cost"/>
				</td>
				<td align="right">
					<xsl:value-of select="total_cost"/>
				</td>
				<td align="right">
					<xsl:value-of select="this_index"/>
				</td>
				<td align="right">
					<xsl:value-of select="index_count"/>
				</td>
				<td align="center">
					<xsl:variable name="link_prizing"><xsl:value-of select="link_prizing"/></xsl:variable>
					<a href="{$link_prizing}" onMouseover="window.status='{$lang_prizing_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_prizing"/></a>
				</td>
				<td align="center">
					<xsl:variable name="link_edit"><xsl:value-of select="link_edit"/></xsl:variable>
					<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_edit"/></a>
				</td>
				<td align="center">
				<xsl:choose>
					<xsl:when test="//table_update!=''">
					<xsl:choose>
						<xsl:when test="total_cost!=''">
							<input type="checkbox" name="values[update][{counter}]" value="{counter}"  onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
										<xsl:value-of select="lang_select_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</xsl:when>
					</xsl:choose>						
						</xsl:when>
					</xsl:choose>						
				</td>
			</tr>
	</xsl:template>

	<xsl:template match="agreement_group">
		<xsl:call-template name="menu"/> 
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
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
				<td align="left">
					<xsl:call-template name="status_filter"/>
				</td>
				<td align="right">
					<xsl:call-template name="search_field"/>
				</td>
			</tr>
			<tr>
				<td colspan="3" width="100%">
					<xsl:call-template name="nextmatchs"/>
				</td>
			</tr>
		</table>
		<xsl:variable name="update_action"><xsl:value-of select="update_action"/></xsl:variable>
		<form method="post" name="form" action="{$update_action}">
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
				<xsl:apply-templates select="table_header_agreement_group"/>
				<xsl:choose>
					<xsl:when test="values_agreement_group[agreement_group_id]!=''">
						<xsl:apply-templates select="values_agreement_group"/>
					</xsl:when>
				</xsl:choose>						
		</table>
		</form>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
				<xsl:apply-templates select="table_add"/>
		</table>
	</xsl:template>

	<xsl:template match="table_header_agreement_group">
		<xsl:variable name="sort_num"><xsl:value-of select="sort_num"/></xsl:variable>
			<tr class="th">
				<td class="th_text" width="5%" align="right">
					<xsl:value-of select="lang_id"/>
				</td>
				<td class="th_text" width="10%" align="right">
					<a href="{$sort_num}"><xsl:value-of select="lang_num"/></a>
				</td>
				<td class="th_text" width="40%" align="right">
					<xsl:value-of select="lang_descr"/>
				</td>
				<td class="th_text" width="5%" align="center">
					<xsl:value-of select="lang_status"/>
				</td>
				<td class="th_text" width="5%" align="center">
					<xsl:value-of select="lang_edit"/>
				</td>
				<td class="th_text" width="5%" align="right">
					<xsl:value-of select="lang_delete"/>
				</td>
			</tr>
	</xsl:template>

	<xsl:template match="values_agreement_group">
		<xsl:variable name="lang_edit_statustext"><xsl:value-of select="lang_edit_statustext"/></xsl:variable>
		<xsl:variable name="lang_delete_statustext"><xsl:value-of select="lang_delete_statustext"/></xsl:variable>
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
					<xsl:value-of select="agreement_group_id"/>
				</td>
				<td align="left">
					<xsl:value-of select="num"/>
				</td>
				<td align="left">
					<xsl:value-of select="descr"/>
				</td>
				<td align="right">
					<xsl:value-of select="status"/>
				</td>
				<td align="center">
					<xsl:variable name="link_edit"><xsl:value-of select="link_edit"/></xsl:variable>
					<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_edit"/></a>
				</td>
				<td align="center">
					<xsl:variable name="link_delete"><xsl:value-of select="link_delete"/></xsl:variable>
					<a href="{$link_delete}" onMouseover="window.status='{$lang_delete_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_delete"/></a>
				</td>
			</tr>
	</xsl:template>

	<xsl:template match="prizing">
		<div align="left">
		<xsl:call-template name="menu"/> 
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
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
				<td class="th_text" align = "left">
					<xsl:value-of select="lang_vendor"/> :
				</td>
				<td class="th_text" align = "left">
					<xsl:value-of select="value_vendor_name"/>
				</td>
			</tr>
			<tr>
				<td class="th_text" align = "left">
					<xsl:value-of select="lang_activity"/> :
				</td>
				<td class="th_text" align = "left">
					<xsl:value-of select="value_activity_code"/>
				</td>
			</tr>
			<tr>
				<td colspan="3" width="100%">
					<xsl:call-template name="nextmatchs"/>
				</td>
			</tr>
		</table>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
				<xsl:apply-templates select="table_header_prizing"/>
				<xsl:choose>
					<xsl:when test="values_prizing!=''">
						<xsl:apply-templates select="values_prizing"/>
					</xsl:when>
				</xsl:choose>						
		</table>

		<table width="50%" cellpadding="2" cellspacing="2" align="center">
		<xsl:variable name="update_action"><xsl:value-of select="update_action"/></xsl:variable>
		<form method="post" name="form" action="{$update_action}">
		<input type="hidden" name="values[activity_id][0]" value="{value_activity_id}" ></input>
		<input type="hidden" name="values[vendor_id][0]" value="{value_vendor_id}" ></input>
		<input type="hidden" name="values[old_m_cost][0]" value="{value_m_cost}" ></input>
		<input type="hidden" name="values[old_w_cost][0]" value="{value_w_cost}" ></input>
		<input type="hidden" name="values[old_total_cost][0]" value="{value_total_cost}" ></input>
		<input type="hidden" name="values[update][0]" value="update" ></input>

				<xsl:choose>
					<xsl:when test="value_total_cost!=''">
						<xsl:apply-templates select="table_update"/>
					</xsl:when>
					<xsl:otherwise>
						<xsl:apply-templates select="table_first_entry"/>
					</xsl:otherwise>
				</xsl:choose>						

		</form>
			<tr>
				<td>
				</td>
				<td>
				</td>
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
		</div>
	</xsl:template>

	<xsl:template match="table_header_prizing">
			<tr class="th">
				<td class="th_text" width="5%" align="right">
					<xsl:value-of select="lang_index_count"/>
				</td>
				<td class="th_text" width="5%" align="right">
					<xsl:value-of select="lang_last_index"/>
				</td>
				<td class="th_text" width="10%" align="right">
					<xsl:value-of select="lang_w_cost"/>
				</td>
				<td class="th_text" width="10%" align="right">
					<xsl:value-of select="lang_m_cost"/>
				</td>
				<td class="th_text" width="10%" align="right">
					<xsl:value-of select="lang_total_cost"/>
				</td>
				<td class="th_text" width="5%" align="right">
					<xsl:value-of select="lang_date"/>
				</td>
				<td class="th_text" width="5%" align="center">
					<xsl:value-of select="lang_delete"/>
				</td>
			</tr>
	</xsl:template>

	<xsl:template match="values_prizing">
		<xsl:variable name="lang_delete_statustext"><xsl:value-of select="lang_delete_statustext"/></xsl:variable>
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
					<xsl:value-of select="index_count"/>
				</td>
				<td align="right">
					<xsl:value-of select="this_index"/>
				</td>
				<td align="right">
					<xsl:value-of select="w_cost"/>
				</td>
				<td align="right">
					<xsl:value-of select="m_cost"/>
				</td>
				<td align="right">
					<xsl:value-of select="total_cost"/>
				</td>
				<td align="right">
					<xsl:value-of select="date"/>
				</td>
				<td align="center">
					<xsl:choose>
						<xsl:when test="current_index!=''">
							<xsl:variable name="link_delete"><xsl:value-of select="link_delete"/></xsl:variable>
							<a href="{$link_delete}" onMouseover="window.status='{$lang_delete_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_delete"/></a>
						</xsl:when>
					</xsl:choose>
				</td>
			</tr>
	</xsl:template>

	<xsl:template match="list_activities">		
		<xsl:call-template name="menu"/> 
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
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
				<td align="left">
					<xsl:call-template name="cat_filter"/>
				</td>
				<td align="right">
					<xsl:call-template name="search_field"/>
				</td>
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
				<td colspan="3" width="100%">
					<xsl:call-template name="nextmatchs"/>
				</td>
			</tr>
		</table>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
				<xsl:apply-templates select="table_header_activities"/>
				<xsl:apply-templates select="values_activities"/>
		</table>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
				<xsl:apply-templates select="table_add"/>
		</table>
	</xsl:template>

	<xsl:template match="table_header_activities">
		<xsl:variable name="sort_num"><xsl:value-of select="sort_num"/></xsl:variable>
			<tr class="th">
				<td class="th_text" width="10%" align="right">
					<a href="{$sort_num}"><xsl:value-of select="lang_num"/></a>
				</td>
				<td class="th_text" width="20%" align="right">
					<xsl:value-of select="lang_descr"/>
				</td>
				<td class="th_text" width="5%" align="right">
					<xsl:value-of select="lang_unit"/>
				</td>
				<td class="th_text" width="5%" align="right">
					<xsl:value-of select="lang_ns3420"/>
				</td>
				<td class="th_text" width="5%" align="right">
					<xsl:value-of select="lang_base_descr"/>
				</td>
				<td class="th_text" width="5%" align="right">
					<xsl:value-of select="lang_branch"/>
				</td>
				<td class="th_text" width="5%" align="right">
					<xsl:value-of select="lang_vendor"/>
				</td>
				<td class="th_text" width="5%" align="right">
					<xsl:value-of select="lang_dim_d"/>
				</td>
				<td class="th_text" width="5%" align="center">
					<xsl:value-of select="lang_edit"/>
				</td>
				<td class="th_text" width="5%" align="center">
					<xsl:value-of select="lang_delete"/>
				</td>
			</tr>
	</xsl:template>

	<xsl:template match="values_activities">
		<xsl:variable name="lang_edit_statustext"><xsl:value-of select="lang_edit_statustext"/></xsl:variable>
		<xsl:variable name="lang_delete_statustext"><xsl:value-of select="lang_delete_statustext"/></xsl:variable>
		<xsl:variable name="lang_vendor_statustext"><xsl:value-of select="lang_vendor_statustext"/></xsl:variable>
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
					<xsl:value-of select="num"/>
				</td>
				<td align="left">
					<xsl:value-of select="descr"/>
				</td>
				<td align="left">
					<xsl:value-of select="unit"/>
				</td>
				<td align="left">
					<xsl:value-of select="ns3420"/>
				</td>
				<td align="left">
					<xsl:value-of select="base_descr"/>
				</td>
				<td align="left">
					<xsl:value-of select="branch"/>
				</td>
				<td align="center">
					<xsl:variable name="link_vendor"><xsl:value-of select="link_vendor"/></xsl:variable>
					<a href="{$link_vendor}" onMouseover="window.status='{$lang_vendor_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_vendor"/></a>
				</td>
				<td align="right">
					<xsl:value-of select="dim_d"/>
				</td>
				<td align="center">
					<xsl:variable name="link_edit"><xsl:value-of select="link_edit"/></xsl:variable>
					<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_edit"/></a>
				</td>
				<td align="center">
					<xsl:variable name="link_delete"><xsl:value-of select="link_delete"/></xsl:variable>
					<a href="{$link_delete}" onMouseover="window.status='{$lang_delete_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_delete"/></a>
				</td>
			</tr>
	</xsl:template>

	<xsl:template match="list_activity_vendor">
		
		<xsl:call-template name="menu"/> 
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
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
				<td align="right">
					<xsl:call-template name="search_field"/>
				</td>
			</tr>
			<tr>
				<td colspan="3" width="100%">
					<xsl:call-template name="nextmatchs"/>
				</td>
			</tr>
		</table>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
				<xsl:apply-templates select="table_header_activity_vendor"/>
				<xsl:apply-templates select="values_activity_vendor"/>
		</table>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
		<xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
	<!--	<form method="post" name="form" action="{$form_action}">
				<xsl:call-template name="vendor_form"/>
				<input type="hidden" name="values[activity_id]" value="{activity_id}" ></input>
			<tr height="50">
				<td>
					<xsl:variable name="lang_save"><xsl:value-of select="lang_save"/></xsl:variable>
					<input type="submit" name="values[add]" value="{$lang_save}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_save_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			</form>
	-->
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
	</xsl:template>

	<xsl:template match="table_header_activity_vendor">
		<xsl:variable name="sort_vendor"><xsl:value-of select="sort_vendor"/></xsl:variable>
			<tr class="th">
				<td class="th_text" width="10%" align="left">
					<xsl:value-of select="lang_num"/>
				</td>
				<td class="th_text" width="5%" align="left">
					<xsl:value-of select="lang_branch"/>
				</td>
				<td class="th_text" width="20%" align="left">
					<a href="{$sort_vendor}"><xsl:value-of select="lang_vendor"/></a>
				</td>
				<td class="th_text" width="5%" align="left">
					<xsl:value-of select="lang_prizing"/>
				</td>
				<td class="th_text" width="5%" align="center">
					<xsl:value-of select="lang_delete"/>
				</td>
			</tr>
	</xsl:template>

	<xsl:template match="values_activity_vendor">
		<xsl:variable name="lang_prizing_statustext"><xsl:value-of select="lang_prizing_statustext"/></xsl:variable>
		<xsl:variable name="lang_delete_statustext"><xsl:value-of select="lang_delete_statustext"/></xsl:variable>
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
					<xsl:value-of select="num"/>
				</td>
				<td align="left">
					<xsl:value-of select="branch"/>
				</td>
				<td align="left">
					<xsl:value-of select="vendor_name"/>
				</td>
				<td align="center">
					<xsl:variable name="link_prizing"><xsl:value-of select="link_prizing"/></xsl:variable>
					<a href="{$link_prizing}" onMouseover="window.status='{$lang_prizing_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_prizing"/></a>
				</td>
				<td align="center">
					<xsl:variable name="link_delete"><xsl:value-of select="link_delete"/></xsl:variable>
					<a href="{$link_delete}" onMouseover="window.status='{$lang_delete_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_delete"/></a>
				</td>
			</tr>
	</xsl:template>


	<xsl:template match="table_first_entry">
			<tr>
				<td>
					<xsl:value-of select="lang_w_cost"/>
				</td>
				<td>
					<input type="text" name="values[w_cost]"  size="12" onMouseout="window.status='';return true;" >
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_w_cost_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<tr>
				<td>
					<xsl:value-of select="lang_m_cost"/>
				</td>
				<td>
					<input type="text" name="values[m_cost]"  size="12" onMouseout="window.status='';return true;" >
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_m_cost_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<tr>
				<td>
					<xsl:value-of select="lang_date"/>
				</td>
				<td>
					<input type="text" id="values_date" name="values[date]" size="10" value="{date}" readonly="readonly" onMouseout="window.status='';return true;" >
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_date_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<img id="values_date-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;" />
				</td>
			</tr>
			<tr>
				<td height="50">
					<xsl:variable name="lang_add"><xsl:value-of select="lang_add"/></xsl:variable>
						<input type="submit" name="values[submit_add]" value="{$lang_add}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_date_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
				</td>
			</tr>
	</xsl:template>


	<xsl:template match="table_update">
			<tr>
				<td>
					<xsl:value-of select="lang_new_index"/>
					<input type="text" name="values[new_index]"  size="12" onMouseout="window.status='';return true;" >
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_new_index_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
				<td>
					<input type="text" id="values_date" name="values[date]" size="10" value="{date}" readonly="readonly" onMouseout="window.status='';return true;" >
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_date_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<img id="values_date-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;" />
				</td>
				<td height="50">
					<xsl:variable name="lang_update"><xsl:value-of select="lang_update"/></xsl:variable>
						<input type="submit" name="values[submit_update]" value="{$lang_update}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_update_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
				</td>
			</tr>
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

	<xsl:template match="edit_activity">
		<script language="JavaScript">
			self.name="first_Window";
			function ns3420_lookup()
			{
				Window1=window.open('<xsl:value-of select="ns3420_link"/>',"Search","width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");
			}		
		</script>
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
			<xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
			<form method="post" name="form" action="{$form_action}">
			<xsl:choose>
				<xsl:when test="value_activity_id !=''">
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_activity_id"/>
						</td>
						<td class="th_text" >
							<xsl:value-of select="value_activity_id"/>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_num"/>
				</td>
				<td>
					<input type="text"  name="values[num]" value="{value_num}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_num_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
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
				<td valign="top">
					<xsl:value-of select="lang_descr"/>
				</td>
				<td>
					<textarea cols="60" rows="4" name="values[descr]" wrap="virtual" onMouseout="window.status='';return true;">
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
				<td valign="top">
					<xsl:value-of select="lang_base_descr"/>
				</td>
				<td>
					<textarea cols="60" rows="4" name="values[base_descr]" wrap="virtual" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_base_descr_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
						<xsl:value-of select="value_base_descr"/>		
					</textarea>
				</td>
			</tr>
			<tr>
				<td>
					<xsl:value-of select="lang_unit"/>
				</td>
				<td>
					<xsl:call-template name="unit_select"/>
				</td>
			</tr>
			<tr>
				<td>
					<xsl:value-of select="lang_dim_d"/>
				</td>
				<td>
					<xsl:call-template name="dim_d_select"/>
				</td>
			</tr>
			<tr>
				<td>
					<xsl:value-of select="lang_branch"/>
				</td>
				<td>
					<xsl:call-template name="branch_select"/>
				</td>
			</tr>
			<tr>
				<td>
					<a href="javascript:ns3420_lookup()" onMouseover="window.status='{lang_ns3420_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="lang_ns3420"/></a>
				</td>
				<td>
					<input  type="text" name="ns3420_id" value="{value_ns3420_id}"  onClick="ns3420_lookup();" readonly="readonly">
					<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_ns3420_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<input type="hidden" name="ns3420_descr"></input>					
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
		</div>

	</xsl:template>

<!-- add / edit agreement_group -->

	<xsl:template match="edit_agreement_group">
		<script language="JavaScript">
			self.name="first_Window";
			function ns3420_lookup()
			{
				Window1=window.open('<xsl:value-of select="ns3420_link"/>',"Search","width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");
			}		
		</script>
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
			<xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
			<form method="post" name="form" action="{$form_action}">
			<xsl:choose>
				<xsl:when test="value_agreement_group_id !=''">
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_agreement_group_id"/>
						</td>
						<td class="th_text" >
							<xsl:value-of select="value_agreement_group_id"/>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_num"/>
				</td>
				<td>
					<input type="text"  name="values[num]" value="{value_num}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_num_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
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
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_descr"/>
				</td>
				<td>
					<textarea cols="60" rows="4" name="values[descr]" wrap="virtual" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_descr_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
						<xsl:value-of select="value_descr"/>		
					</textarea>
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
		</div>

	</xsl:template>

<!-- view -->

	<xsl:template match="view">
		<div align="left">
		
		<table cellpadding="2" cellspacing="2" width="80%" align="center">
			<xsl:call-template name="location_view"/>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_building_id"/>
				</td>
				<td>
					<xsl:value-of select="value_building_id"/>
				</td>
			</tr>
			<tr>
				<td>
					<xsl:value-of select="lang_category"/>
				</td>
				<td>
					<xsl:value-of select="value_category"/>
				</td>
			</tr>
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
					<xsl:value-of select="lang_general_address"/>
				</td>
				<td>
					<xsl:value-of select="value_general_address"/>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_remark"/>
				</td>
				<td>
					<xsl:value-of select="value_remark"/>		
				</td>
			</tr>
			<tr>
				<tr>
					<td>
						<xsl:value-of select="lang_attributes"/>
					</td>
					<td colspan="2" width="50%">
						<xsl:call-template name="attributes_form"/>							
					</td>
				</tr>
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
		</div>
	</xsl:template>
