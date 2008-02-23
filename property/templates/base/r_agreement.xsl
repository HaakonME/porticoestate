
	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="edit">
				<xsl:apply-templates select="edit"/>
			</xsl:when>
			<xsl:when test="edit_item">
				<xsl:apply-templates select="edit_item"/>
			</xsl:when>
			<xsl:when test="view_item">
				<xsl:apply-templates select="view_item"/>
			</xsl:when>
			<xsl:when test="view">
				<xsl:apply-templates select="view"/>
			</xsl:when>
			<xsl:when test="list_attribute">
				<xsl:apply-templates select="list_attribute"/>
			</xsl:when>
			<xsl:when test="edit_attrib">
				<xsl:apply-templates select="edit_attrib"/>
			</xsl:when>
			<xsl:when test="edit_common">
				<xsl:apply-templates select="edit_common"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="list"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="list">

		<script language="JavaScript">
			self.name="first_Window";
			function tenant_lookup()
			{
				Window1=window.open('<xsl:value-of select="tenant_link"/>',"Search","width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");
			}		
			function property_lookup()
			{
				Window1=window.open('<xsl:value-of select="property_link"/>',"Search","width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");
			}		
		</script>

		<xsl:apply-templates select="menu"/>
		<xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
		<form method="post" name="form" action="{$form_action}">

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
				<xsl:choose>
					<xsl:when test="member_of_list != ''">
					<td align="left" valign = 'top'>
							<xsl:call-template name="filter_member_of"/>
						</td>
					</xsl:when>
				</xsl:choose>

				<td align="left" valign = 'top'>
					<xsl:call-template name="cat_select"/>
				</td>

				<td align="left" valign = 'top'>
					<input type="text" name="tenant_id" value="{customer_id}" size="4"  onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_tenant_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<a href="javascript:tenant_lookup()"
					onMouseOver="overlib('{lang_select_tenant_statustext}', CAPTION, '{lang_tenant}')"
					onMouseOut="nd()">
					<xsl:value-of select="lang_tenant"/></a>					

					<input type="hidden" name="first_name"></input>
					<input type="hidden" name="last_name"></input>
				</td>

				<td align="left" valign = 'top'>
					<input type="text" name="loc1" value="{loc1}" size="4" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_property_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<a href="javascript:property_lookup()"
					onMouseOver="overlib('{lang_select_property_statustext}', CAPTION, '{lang_property}')"
					onMouseOut="nd()">
					<xsl:value-of select="lang_property"/></a>					

					<input type="hidden" name="loc1_name"></input>
				</td>

				<td align="right" colspan = '3'>
					<xsl:call-template name="search_field_grouped"/>
				<xsl:text> </xsl:text>
				</td>
				<td>
				<input type="checkbox" name="reset_query" value="True"  onMouseout="window.status='';return true;">
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_reset_query_statustext"/>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>

				</td>
				<td valign ="top">
				<table>
				<tr>
				<td class="small_text" valign="top" align="left">
					<xsl:variable name="link_columns"><xsl:value-of select="link_columns"/></xsl:variable>
					<xsl:variable name="lang_columns_help"><xsl:value-of select="lang_columns_help"/></xsl:variable>
					<xsl:variable name="lang_columns"><xsl:value-of select="lang_columns"/></xsl:variable>
					<a href="javascript:var w=window.open('{$link_columns}','','width=300,height=600')"
						onMouseOver="overlib('{$lang_columns_help}', CAPTION, '{$lang_columns}')"
						onMouseOut="nd()">
						<xsl:value-of select="lang_columns"/></a>
				</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td colspan="10" width="100%">
					<xsl:call-template name="nextmatchs"/>
				</td>
			</tr>
		</table>
		</form>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:call-template name="table_header"/>
			<xsl:call-template name="values"/>
			<xsl:choose>
				<xsl:when test="table_add!=''">
					<xsl:apply-templates select="table_add"/>
				</xsl:when>
			</xsl:choose>
		</table>
	</xsl:template>

	<xsl:template name="table_header">
			<tr class="th">
				<xsl:for-each select="table_header" >
					<td class="th_text" width="{with}" align="{align}">
						<xsl:choose>
							<xsl:when test="sort_link!=''">
								<a href="{sort}" onMouseover="window.status='{header}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="header"/></a>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="header"/>					
							</xsl:otherwise>
						</xsl:choose>
					</td>
				</xsl:for-each>
			</tr>
	</xsl:template>


	<xsl:template name="values">
		<xsl:for-each select="values" >
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
				<xsl:for-each select="row" >
					<xsl:choose>
						<xsl:when test="link">
							<td class="small_text" align="center">
								<a href="{link}" onMouseover="window.status='{statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text"/></a>
							</td>
						</xsl:when>
						<xsl:otherwise>
							<td class="small_text" align="left">
								<xsl:value-of select="value"/>				
							</td>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:for-each>
				<xsl:choose>
					<xsl:when test="//acl_manage != '' and cost!=''">
						<td align="center">
							<input type="hidden" name="values[item_id][{item_id}]" value="{item_id}" ></input>
							<input type="hidden" name="values[id][{item_id}]" value="{index_count}" ></input>
							<input type="checkbox" name="values[select][{item_id}]" value="{cost}"  onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
										<xsl:value-of select="lang_select_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</xsl:when>
				</xsl:choose>
			</tr>
		</xsl:for-each>
	</xsl:template>

	<xsl:template name="values2">
		<xsl:for-each select="values" >
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
				<xsl:for-each select="row" >
					<xsl:choose>
						<xsl:when test="link">
							<td class="small_text" align="center">
								<a href="{link}" onMouseover="window.status='{statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text"/></a>
							</td>
						</xsl:when>
						<xsl:otherwise>
							<td class="small_text" align="left">
								<xsl:value-of select="value"/>				
							</td>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:for-each>
				<xsl:choose>
					<xsl:when test="//acl_manage != '' and cost!=''">
							<input type="hidden" name="values[item_id][{item_id}]" value="{item_id}" ></input>
							<input type="hidden" name="values[id][{item_id}]" value="{index_count}" ></input>
							<input type="hidden" name="values[select][{item_id}]" value="{cost}" ></input>
					</xsl:when>
				</xsl:choose>
			</tr>
		</xsl:for-each>
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

	<xsl:template match="table_add_space">
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


	<xsl:template match="table_add_space">
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
	<xsl:template match="table_add_service">
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
	<xsl:template match="table_add_common">
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
		<script language="JavaScript">
			self.name="first_Window";
			<xsl:value-of select="lookup_functions"/>
		</script>
		<div align="left">
		<xsl:variable name="edit_url"><xsl:value-of select="edit_url"/></xsl:variable>
		<table cellpadding="2" cellspacing="2"  width="90%" align="center">
			<tr><td>
		<form ENCTYPE="multipart/form-data" method="post" name="form" action="{$edit_url}">
		<table cellpadding="2" cellspacing="2" width="90%" align="left">
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
				<xsl:when test="value_r_agreement_id!=''">
					<tr >
						<td align="left">
							<xsl:value-of select="lang_id"/>
						</td>
						<td align="left">
							<xsl:value-of select="value_r_agreement_id"/>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>

			<tr>
				<td valign="top">
					<xsl:value-of select="lang_name"/>
				</td>
				<td>
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
			<tr >
				<td align="left">
					<xsl:value-of select="lang_category"/>
				</td>
				<td align="left">
					<xsl:call-template name="cat_select"/>
				</td>
			</tr>
			<xsl:call-template name="tenant_form"/>
			<xsl:call-template name="b_account_form"/>
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
					<xsl:value-of select="lang_termination_date"/>
				</td>
				<td>
					<input type="text" id="values_termination_date" name="values[termination_date]" size="10" value="{value_termination_date}" readonly="readonly" onMouseout="window.status='';return true;" >
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_termination_date_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<img id="values_termination_date-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;" />
				</td>
			</tr>
			
			<xsl:choose>
				<xsl:when test="files!=''">
			<tr>
				<td align="left" valign="top">
					<xsl:value-of select="//lang_files"/>
				</td>
				<td>
				<table>
					<tr class="th">
						<td class="th_text" width="85%" align="left">
							<xsl:value-of select="lang_filename"/>
						</td>
						<td class="th_text" width="15%" align="center">
							<xsl:value-of select="lang_delete_file"/>
						</td>
					</tr>
				<xsl:for-each select="files" >
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
						<xsl:choose>
							<xsl:when test="//link_to_files!=''">
								<xsl:variable name="link_to_file"><xsl:value-of select="//link_to_files"/>/<xsl:value-of select="directory"/>/<xsl:value-of select="file_name"/></xsl:variable>
								<a href="{$link_to_file}" target="_blank" onMouseover="window.status='{//lang_view_file_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="name"/></a>
							</xsl:when>
							<xsl:otherwise>
								<xsl:variable name="link_view_file"><xsl:value-of select="//link_view_file"/>&amp;file_name=<xsl:value-of select="file_name"/></xsl:variable>
								<a href="{$link_view_file}" target="_blank" onMouseover="window.status='{//lang_view_file_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="name"/></a>
							</xsl:otherwise>
						</xsl:choose>
						<xsl:text> </xsl:text>
					</td>
					<td align="center">
						<input type="checkbox" name="values[delete_file][]" value="{name}"  onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="//lang_delete_file_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</td>
					</tr>
				</xsl:for-each>
				</table>
				</td>
			</tr>
				</xsl:when>
			</xsl:choose>

			<xsl:choose>
				<xsl:when test="fileupload = 1">
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_upload_file"/>
						</td>
						<td>
							<input type="file" name="file" size="40" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
										<xsl:value-of select="lang_file_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>

			<xsl:choose>
				<xsl:when test="attributes_values != ''">
					<tr>
						<td colspan="2" width="50%" align="left">				
							<xsl:call-template name="attributes_form"/>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="member_of_list != ''">
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_member_of"/>
					</td>
					<td>
						<xsl:variable name="lang_member_of_statustext"><xsl:value-of select="lang_member_of_statustext"/></xsl:variable>
							<select name="values[member_of][]" class="forms" multiple="multiple" onMouseover="window.status='{$lang_member_of_statustext}'; return true;" onMouseout="window.status='';return true;">
								<xsl:apply-templates select="member_of_list"/>
							</select>
					</td>
				</tr>
				</xsl:when>
			</xsl:choose>
			<tr height="50">
				<td colspan = '2'>
				<table>
				<tr>
				<td valign="bottom">
					<xsl:variable name="lang_save"><xsl:value-of select="lang_save"/></xsl:variable>
					<input type="submit" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_save_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
				<td valign="bottom">
					<xsl:variable name="lang_apply"><xsl:value-of select="lang_apply"/></xsl:variable>
					<input type="submit" name="values[apply]" value="{$lang_apply}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_apply_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
				<td align="right" valign="bottom">
					<xsl:variable name="lang_cancel"><xsl:value-of select="lang_cancel"/></xsl:variable>
					<input type="submit" name="values[cancel]" value="{$lang_cancel}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_cancel_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
				</tr>
				</table>
				</td>
			</tr>
		</table>
		</form>
		</td>
		</tr>
		<tr>
		<td>
		<form method="post" name="alarm" action="{$edit_url}">
			<input type="hidden" name="values[entity_id]" value="{value_r_agreement_id}" ></input>
			<table>
				<tr>
					<td class="th_text" align="left" colspan="5">
						<xsl:value-of select="lang_alarm"/>
					</td>
				</tr>
				<xsl:call-template name="alarm_form"/>
			</table>
		</form>
		</td>
		</tr>
		</table>
		<xsl:choose>
			<xsl:when test="value_r_agreement_id!=''">
			<table>
				<tr>
					<xsl:attribute name="class">
						<xsl:text>row_on</xsl:text>
					</xsl:attribute>
					<td class="th_text" align="left" colspan="5">
					<HR/>
					<xsl:value-of select="lang_space"/>
					</td>
				</tr>
				<tr>
					<xsl:attribute name="class">
						<xsl:text>row_off</xsl:text>
					</xsl:attribute>
	
					<td>
						<table width="100%" cellpadding="2" cellspacing="2" align="center">
							<tr>
								<xsl:for-each select="set_column" >
									<td></td>
								</xsl:for-each>
								<td class="small_text" valign="bottom" align="center">
									<xsl:variable name="link_download"><xsl:value-of select="link_download"/></xsl:variable>
									<xsl:variable name="lang_download_help"><xsl:value-of select="lang_download_help"/></xsl:variable>
									<xsl:variable name="lang_download"><xsl:value-of select="lang_download"/></xsl:variable>
									<a href="javascript:var w=window.open('{$link_download}','','')"
										onMouseOver="overlib('{$lang_download_help}', CAPTION, '{$lang_download}')"
										onMouseOut="nd()">
									<xsl:value-of select="lang_download"/></a>
								</td></tr>
	
							<xsl:call-template name="table_header"/>
							<xsl:call-template name="values"/>
							<tr>
								<xsl:for-each select="set_column" >
									<td></td>
								</xsl:for-each>
								<td align="center">
									<xsl:variable name="img_check"><xsl:value-of select="img_check"/></xsl:variable>
									 <a href="javascript:check_all_checkbox2('values[select]')"><img src="{$img_check}" border="0" height="16" width="21" alt="{lang_select_all}"/></a>
								</td>
							</tr>
						</table>
						<xsl:choose>
							<xsl:when test="table_update!=''">
							<xsl:variable name="update_action"><xsl:value-of select="update_action"/></xsl:variable>
								<form method="post" name="form2" action="{$update_action}">
									<input type="hidden" name="values[agreement_id]" value="{value_r_agreement_id}" ></input>
									<table width="70%" cellpadding="2" cellspacing="2" align="center">
										<xsl:apply-templates select="table_update"/>
									</table>
								</form>
							</xsl:when>
						</xsl:choose>						
						<table width="100%" cellpadding="2" cellspacing="2" align="center">
							<xsl:apply-templates select="table_add_space"/>
						</table>
					</td>
				</tr>
			<tr>
				<xsl:attribute name="class">
					<xsl:text>row_on</xsl:text>
				</xsl:attribute>
				
				<td class="th_text" align="left" colspan="5">
				<HR/>
					<xsl:value-of select="lang_service"/>
				</td>
			</tr>
				<xsl:apply-templates select="table_add_service"/>
	
			<tr>
				<xsl:attribute name="class">
					<xsl:text>row_on</xsl:text>
				</xsl:attribute>
	
				<td class="th_text" align="left" colspan="10">
				<HR/>
					<xsl:value-of select="lang_common_costs"/>
				</td>
			</tr>
			<tr>
			<td>
			<table>
				<xsl:apply-templates select="table_header_common"/>
				<xsl:apply-templates select="values_common"/>
				<xsl:apply-templates select="table_add_common"/>
			</table>
			</td>
			</tr>
			</table>
			</xsl:when>
		</xsl:choose>						
		</div>
	</xsl:template>


	<xsl:template match="table_header_common">		
			<tr class="th">
				<td width="10%" align="right">
					<xsl:value-of select="lang_id"/>
				</td>
				<td align="left">
					<xsl:value-of select="lang_b_account"/>
				</td>
				<td align="center">
					<xsl:value-of select="lang_from_date"/>
				</td>
				<td align="left">
					<xsl:value-of select="lang_to_date"/>
				</td>
				<td align="left">
					<xsl:value-of select="lang_budget_cost"/>
				</td>
				<td align="left">
					<xsl:value-of select="lang_actual_cost"/>
				</td>
				<td align="left">
					<xsl:value-of select="lang_fraction"/>
				</td>
				<td align="left">
					<xsl:value-of select="lang_override_fraction"/>
				</td>
				<td width="5%" align="center">
					<xsl:value-of select="lang_view"/>
				</td>
				<td width="5%" align="center">
					<xsl:value-of select="lang_edit"/>
				</td>
				<td width="5%" align="center">
					<xsl:value-of select="lang_delete"/>
				</td>
			</tr>
	</xsl:template>

	<xsl:template match="values_common">
		<xsl:variable name="lang_view_statustext"><xsl:value-of select="lang_view_statustext"/></xsl:variable>
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
					<xsl:value-of select="c_id"/>
				</td>
				<td align="left">
					<xsl:value-of select="b_account_id"/>
				</td>
				<td align="center">
					<xsl:value-of select="from_date"/>
				</td>
				<td align="left">
					<xsl:value-of select="to_date"/>
				</td>
				<td align="left">
					<xsl:value-of select="budget_cost"/>
				</td>
				<td align="left">
					<xsl:value-of select="actual_cost"/>
				</td>
				<td align="left">
					<xsl:value-of select="fraction"/>
				</td>
				<td align="left">
					<xsl:value-of select="override_fraction"/>
				</td>
				<td align="center">
					<xsl:variable name="link_view"><xsl:value-of select="link_view"/></xsl:variable>
					<a href="{$link_view}" onMouseover="window.status='{$lang_view_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_view"/></a>
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


<!-- add item / edit item -->

	<xsl:template match="edit_item">
		<xsl:variable name="main_form_name"><xsl:value-of select="main_form_name"/></xsl:variable>
		<xsl:variable name="update_form_name"><xsl:value-of select="update_form_name"/></xsl:variable>

		<script language="JavaScript">
			self.name="first_Window";
			<xsl:value-of select="lookup_functions"/>
		</script>
		<xsl:variable name="edit_url"><xsl:value-of select="edit_url"/></xsl:variable>
		<div align="left">
		<form name="{$main_form_name}" method="post" action="{$edit_url}">
		<table cellpadding="2" cellspacing="2" width="100%" align="center">
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
				<xsl:when test="value_r_agreement_id!=''">
					<tr >
						<td align="left">
							<xsl:value-of select="lang_agreement"/>
						</td>
						<td align="left">
							<xsl:value-of select="value_r_agreement_id"/>
							<xsl:text> [</xsl:text>
							<xsl:value-of select="agreement_name"/>
							<xsl:text>] </xsl:text>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="value_id!=''">
					<tr >
						<td align="left">
							<xsl:value-of select="lang_id"/>
						</td>
						<td align="left">
							<xsl:value-of select="value_id"/>
						</td>
					</tr>
					<xsl:call-template name="location_view"/>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_rental_type"/>
						</td>
						<td>
							<xsl:for-each select="rental_type_list[selected='selected']" >
								<xsl:value-of select="name"/>
								<xsl:if test="position() != last()">, </xsl:if>
							</xsl:for-each>
						</td>
					</tr>

				</xsl:when>
				<xsl:otherwise>
					<xsl:call-template name="location_form"/>
					<xsl:call-template name="tenant_form"/>

					<tr>
						<td valign="top">
							<xsl:value-of select="lang_rental_type"/>
						</td>
						<td valign="top">
							<xsl:variable name="lang_rental_type_statustext"><xsl:value-of select="lang_rental_type_statustext"/></xsl:variable>
							<select name="values[rental_type_id]" class="forms" onMouseover="window.status='{$lang_rental_type_statustext}'; return true;" onMouseout="window.status='';return true;">
								<option value=""><xsl:value-of select="lang_select_rental_type"/></option>
								<xsl:apply-templates select="rental_type_list"/>
							</select>
						</td>
					</tr>
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
				</xsl:otherwise>
			</xsl:choose>

			<tr>
				<td valign="top">
					<xsl:value-of select="lang_cost"/>
				</td>
				<td>
					<input type="text" name="values[cost]" value="{value_cost}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_cost_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>

			<xsl:choose>
				<xsl:when test="attributes_values != ''">
					<tr>
						<td colspan="2" width="50%" align="left">				
							<xsl:call-template name="attributes_form"/>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<tr height="50">
			<td colspan = '2'>
			<table>
			<tr>
				<td valign="bottom">
					<xsl:variable name="lang_save"><xsl:value-of select="lang_save"/></xsl:variable>
					<input type="submit" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_save_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
				<td valign="bottom">
					<xsl:variable name="lang_apply"><xsl:value-of select="lang_apply"/></xsl:variable>
					<input type="submit" name="values[apply]" value="{$lang_apply}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_apply_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
				<td align="right" valign="bottom">
					<xsl:variable name="lang_cancel"><xsl:value-of select="lang_cancel"/></xsl:variable>
					<input type="submit" name="values[cancel]" value="{$lang_cancel}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_cancel_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			</table>
			</td>
			</tr>
		</table>
		</form>
		
		<xsl:choose>
			<xsl:when test="values != ''">
		
				<xsl:variable name="update_action"><xsl:value-of select="update_action"/></xsl:variable>
				<form method="post" name="{$update_form_name}" action="{$update_action}">
					<input type="hidden" name="values[agreement_id]" value="{value_r_agreement_id}" ></input>
					<input type="hidden" name="values[item_id]" value="{value_id}" ></input>
					<table width="100%" cellpadding="2" cellspacing="2" align="center">
						<xsl:call-template name="table_header"/>
						<xsl:call-template name="values2"/>
					</table>
					<table width="70%" cellpadding="2" cellspacing="2" align="left">
					<xsl:choose>
						<xsl:when test="table_update_item!=''">
							<xsl:apply-templates select="table_update_item"/>
						</xsl:when>
					</xsl:choose>
						<tr>
							<td class="small_text" align="left">
								<a href="{delete_action}" onMouseover="window.status='{lang_delete_last_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="lang_delete_last"/></a>
							</td>
						</tr>

					</table>
				</form>
			</xsl:when>
		</xsl:choose>
		</div>
	</xsl:template>


	<xsl:template match="table_update_item">
			<tr>
				<td>
					<xsl:value-of select="lang_new_index"/>
				</td>
				<td>
					<input type="text" name="values[new_index]"  size="12" onMouseout="window.status='';return true;" >
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_new_index_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<tr>
				<td>
					<xsl:value-of select="lang_index_date"/>
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
			<xsl:call-template name="tenant_form"/>

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
				<td height="50" colspan = '2' align = 'left'>
					<xsl:variable name="lang_update"><xsl:value-of select="lang_update"/></xsl:variable>
						<input type="submit" name="values[update]" value="{$lang_update}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_update_statustext"/>
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
						<input type="submit" name="values[update]" value="{$lang_update}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_update_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
				</td>
			</tr>
	</xsl:template>


<!-- view -->

	<xsl:template match="view">
		<div align="left">
		<table cellpadding="2" cellspacing="2" align="center">
			<tr><td>
			<table cellpadding="2" cellspacing="2" width="79%" align="center">
				<tr >
					<td align="left">
						<xsl:value-of select="lang_id"/>
					</td>
					<td align="left">
						<xsl:value-of select="value_r_agreement_id"/>
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
						<xsl:value-of select="lang_descr"/>
					</td>
					<td>
						<textarea disabled="disabled" cols="60" rows="6" name="values[descr]" wrap="virtual" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_descr_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
							<xsl:value-of select="value_descr"/>		
						</textarea>
					</td>
				</tr>
				<tr >
					<td align="left">
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
				<xsl:call-template name="tenant_view"/>
				<xsl:call-template name="b_account_view"/>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_start_date"/>
					</td>
					<td>
						<input type="text" id="start_date" name="start_date" size="10" value="{value_start_date}" readonly="readonly" onMouseout="window.status='';return true;" ></input>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_end_date"/>
					</td>
					<td>
						<input type="text" id="end_date" name="end_date" size="10" value="{value_end_date}" readonly="readonly" onMouseout="window.status='';return true;" ></input>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_termination_date"/>
					</td>
					<td>
						<input type="text" id="termination_date" name="termination_date" size="10" value="{value_termination_date}" readonly="readonly" onMouseout="window.status='';return true;" ></input>
					</td>
				</tr>
				<xsl:choose>
					<xsl:when test="files!=''">
					<tr>
						<td align="left" valign="top">
							<xsl:value-of select="//lang_files"/>
						</td>
						<td>
						<table>
							<tr class="th">
								<td class="th_text" width="85%" align="left">
									<xsl:value-of select="lang_filename"/>
								</td>
							</tr>
								<xsl:for-each select="files" >
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
											<xsl:choose>
												<xsl:when test="//link_to_files!=''">
													<xsl:variable name="link_to_file"><xsl:value-of select="//link_to_files"/>/<xsl:value-of select="directory"/>/<xsl:value-of select="file_name"/></xsl:variable>
													<a href="{$link_to_file}" target="_blank" onMouseover="window.status='{//lang_view_file_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="name"/></a>
												</xsl:when>
												<xsl:otherwise>
													<xsl:variable name="link_view_file"><xsl:value-of select="//link_view_file"/>&amp;file_name=<xsl:value-of select="file_name"/></xsl:variable>
													<a href="{$link_view_file}" target="_blank" onMouseover="window.status='{//lang_view_file_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="name"/></a>
												</xsl:otherwise>
											</xsl:choose>
											<xsl:text> </xsl:text>
										</td>
									</tr>
								</xsl:for-each>
							</table>
						</td>
					</tr>
					</xsl:when>
				</xsl:choose>				
				<xsl:choose>
					<xsl:when test="attributes_view != ''">
						<tr>
							<td colspan="2" width="50%" align="left">				
								<xsl:apply-templates select="attributes_view"/>
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="member_of_list != ''">
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_member_of"/>
						</td>
					<!--	<td valign="top">
							<xsl:for-each select="member_of_list[selected='selected']" >
								<xsl:value-of select="name"/>
								<xsl:if test="position() != last()">, </xsl:if>
							</xsl:for-each>
						</td>-->
	
						<td>
							<xsl:variable name="lang_member_of_statustext"><xsl:value-of select="lang_member_of_statustext"/></xsl:variable>
								<select disabled="disabled" name="values[member_of][]" class="forms" multiple="multiple" onMouseover="window.status='{$lang_member_of_statustext}'; return true;" onMouseout="window.status='';return true;">
									<xsl:apply-templates select="member_of_list"/>
								</select>
						</td>
					</tr>
					</xsl:when>
				</xsl:choose>
			</table>
			</td></tr>
			<tr><td>
			<table>
				<tr>
					<td class="th_text" align="left" colspan="4">
						<xsl:value-of select="lang_alarm"/>
					</td>
				</tr>
				<xsl:call-template name="alarm_view"/>
			</table>
			</td>
			</tr>
			</table>
			<xsl:choose>
				<xsl:when test="values!=''">
					<table width="100%" cellpadding="2" cellspacing="2" align="center">
						<tr><td align = "center" colspan="10">	
						<xsl:value-of select="lang_total_records"/>
						<xsl:text> </xsl:text>
						<xsl:value-of select="total_records"/>
						</td></tr>

						<xsl:call-template name="table_header"/>
						<xsl:call-template name="values"/>
					</table>
				</xsl:when>
			</xsl:choose>						
			<table width="80%" cellpadding="2" cellspacing="2" align="center">

			<xsl:variable name="edit_url"><xsl:value-of select="edit_url"/></xsl:variable>
			<form name="form" method="post" action="{$edit_url}">
				<tr height="50">
					<td align="left" valign="bottom">
						<xsl:variable name="lang_cancel"><xsl:value-of select="lang_cancel"/></xsl:variable>
						<input type="submit" name="values[cancel]" value="{$lang_cancel}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_cancel_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</td>
				</tr>
			</form>
			</table>
		</div>
	</xsl:template>

<!-- view item -->

	<xsl:template match="view_item">
		<div align="left">
		<table cellpadding="2" cellspacing="2" width="79%" align="center">
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
				<xsl:when test="value_r_agreement_id!=''">
					<tr >
						<td align="left">
							<xsl:value-of select="lang_agreement"/>
						</td>
						<td align="left">
							<xsl:value-of select="value_r_agreement_id"/>
							<xsl:text> [</xsl:text>
							<xsl:value-of select="agreement_name"/>
							<xsl:text>] </xsl:text>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="value_id!=''">
					<tr >
						<td align="left">
							<xsl:value-of select="lang_id"/>
						</td>
						<td align="left">
							<xsl:value-of select="value_id"/>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<xsl:call-template name="location_view"/>
			<xsl:call-template name="tenant_view"/>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_rental_type"/>
				</td>
				<td>
					<xsl:for-each select="rental_type_list[selected='selected']" >
						<xsl:value-of select="name"/>
						<xsl:if test="position() != last()">, </xsl:if>
					</xsl:for-each>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_cost"/>
				</td>
				<td>
					<xsl:value-of select="value_cost"/>
				</td>
			</tr>

			<xsl:choose>
				<xsl:when test="attributes_view != ''">
					<tr>
						<td colspan="2" width="50%" align="left">				
							<xsl:apply-templates select="attributes_view"/>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
		</table>
		<xsl:choose>
			<xsl:when test="values != ''">
		
				<xsl:variable name="update_action"><xsl:value-of select="update_action"/></xsl:variable>
					<table width="100%" cellpadding="2" cellspacing="2" align="center">
						<xsl:call-template name="table_header"/>
						<xsl:call-template name="values2"/>
					</table>
			</xsl:when>
		</xsl:choose>
		<xsl:variable name="edit_url"><xsl:value-of select="edit_url"/></xsl:variable>
		<form name="form" method="post" action="{$edit_url}">
		<table width="80%" cellpadding="2" cellspacing="2" align="center">
			<tr height="50">
				<td align="left" valign="bottom">
					<xsl:variable name="lang_cancel"><xsl:value-of select="lang_cancel"/></xsl:variable>
					<input type="submit" name="cancel" value="{$lang_cancel}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_cancel_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
		</table>
		</form>
		
		</div>
	</xsl:template>



	<xsl:template match="table_add2">
			<tr>
				<td height="50">
					<xsl:variable name="add_action"><xsl:value-of select="add_action"/></xsl:variable>
					<xsl:variable name="lang_add"><xsl:value-of select="lang_add"/></xsl:variable>
					<form method="post" action="{$add_action}">
						<input type="submit" name="add" value="{$lang_add}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_add_standardtext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</form>
				</td>
				<td height="50">
					<xsl:variable name="done_action"><xsl:value-of select="done_action"/></xsl:variable>
					<xsl:variable name="lang_done"><xsl:value-of select="lang_done"/></xsl:variable>
					<form method="post" action="{$done_action}">
						<input type="submit" name="add" value="{$lang_done}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_add_standardtext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</form>
				</td>
			</tr>
	</xsl:template>



<!-- list attribute -->

	<xsl:template match="list_attribute">
		
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
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
				<xsl:apply-templates select="table_header_attrib"/>
				<xsl:apply-templates select="values_attrib"/>
				<xsl:apply-templates select="table_add2"/>
		</table>
	</xsl:template>
	<xsl:template match="table_header_attrib">
		<xsl:variable name="sort_sorting"><xsl:value-of select="sort_sorting"/></xsl:variable>
		<xsl:variable name="sort_id"><xsl:value-of select="sort_id"/></xsl:variable>
		<xsl:variable name="sort_name"><xsl:value-of select="sort_name"/></xsl:variable>
		<tr class="th">
			<td class="th_text" width="10%" align="left">
				<a href="{$sort_name}"><xsl:value-of select="lang_name"/></a>
			</td>
			<td class="th_text" width="10%" align="left">
				<xsl:value-of select="lang_descr"/>
			</td>
			<td class="th_text" width="1%" align="center">
				<xsl:value-of select="lang_datatype"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<a href="{$sort_sorting}"><xsl:value-of select="lang_sorting"/></a>
			</td>
			<td class="th_text" width="1%" align="center">
				<xsl:value-of select="lang_search"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_edit"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_delete"/>
			</td>
		</tr>
	</xsl:template>

	<xsl:template match="values_attrib"> 
		<xsl:variable name="lang_up_text"><xsl:value-of select="lang_up_text"/></xsl:variable>
		<xsl:variable name="lang_down_text"><xsl:value-of select="lang_down_text"/></xsl:variable>
		<xsl:variable name="lang_attribute_attribtext"><xsl:value-of select="lang_delete_attribtext"/></xsl:variable>
		<xsl:variable name="lang_edit_attribtext"><xsl:value-of select="lang_edit_attribtext"/></xsl:variable>
		<xsl:variable name="lang_delete_attribtext"><xsl:value-of select="lang_delete_attribtext"/></xsl:variable>
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
					<xsl:value-of select="column_name"/>
				</td>
				<td align="left">
					<xsl:value-of select="input_text"/>
				</td>
				<td align="left">
					<xsl:value-of select="datatype"/>
				</td>
				<td>
					<table align="left">
						<tr>
							<td>
								<xsl:value-of select="sorting"/>
							</td>

							<td align="left">
								<xsl:variable name="link_up"><xsl:value-of select="link_up"/></xsl:variable>
								<a href="{$link_up}" onMouseover="window.status='{$lang_up_text}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_up"/></a>
								<xsl:text> | </xsl:text>
								<xsl:variable name="link_down"><xsl:value-of select="link_down"/></xsl:variable>
								<a href="{$link_down}" onMouseover="window.status='{$lang_down_text}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_down"/></a>
							</td>

						</tr>
					</table>
				</td>
				<td align="center">
					<xsl:value-of select="search"/>
				</td>
				<td align="center">
					<xsl:variable name="link_edit"><xsl:value-of select="link_edit"/></xsl:variable>
					<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_attribtext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_edit"/></a>
				</td>
				<td align="center">
					<xsl:variable name="link_delete"><xsl:value-of select="link_delete"/></xsl:variable>
					<a href="{$link_delete}" onMouseover="window.status='{$lang_delete_attribtext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_delete"/></a>
				</td>
			</tr>
	</xsl:template>


<!-- add attribute / edit attribute -->

	<xsl:template match="edit_attrib">
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
			<form method="post" action="{$form_action}">
			<tr>
				<td valign="top">
					<xsl:choose>
						<xsl:when test="value_id != ''">
							<xsl:value-of select="lang_id"/>
						</xsl:when>
						<xsl:otherwise>
						</xsl:otherwise>
					</xsl:choose>
				</td>
				<td>
					<xsl:choose>
						<xsl:when test="value_id != ''">
							<xsl:value-of select="value_id"/>
						</xsl:when>
						<xsl:otherwise>
						</xsl:otherwise>
					</xsl:choose>	
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_column_name"/>
				</td>
				<td>
					<input type="text" name="values[column_name]" value="{value_column_name}" maxlength="20" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_column_name_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_input_text"/>
				</td>
				<td>
					<input type="text" name="values[input_text]" value="{value_input_text}" size ="60" maxlength="50" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_input_text_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_statustext"/>
				</td>
				<td>
					<textarea cols="60" rows="10" name="values[statustext]" wrap="virtual" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_statustext_attribtext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
						<xsl:value-of select="value_statustext"/>		
					</textarea>

				</td>
			</tr>

			<tr>
				<td valign="top">
					<xsl:value-of select="lang_datatype"/>
				</td>
				<td valign="top">
					<xsl:variable name="lang_datatype_statustext"><xsl:value-of select="lang_datatype_statustext"/></xsl:variable>
					<select name="values[column_info][type]" class="forms" onMouseover="window.status='{$lang_datatype_statustext}'; return true;" onMouseout="window.status='';return true;">
						<option value=""><xsl:value-of select="lang_no_datatype"/></option>
						<xsl:apply-templates select="datatype_list"/>
					</select>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_precision"/>
				</td>
				<td>
					<input type="text" name="values[column_info][precision]" value="{value_precision}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_precision_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_scale"/>
				</td>
				<td>
					<input type="text" name="values[column_info][scale]" value="{value_scale}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_scale_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_default"/>
				</td>
				<td>
					<input type="text" name="values[column_info][default]" value="{value_default}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_default_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_nullable"/>
				</td>
				<td valign="top">
					<xsl:variable name="lang_nullable_statustext"><xsl:value-of select="lang_nullable_statustext"/></xsl:variable>
					<select name="values[column_info][nullable]" class="forms" onMouseover="window.status='{$lang_nullable_statustext}'; return true;" onMouseout="window.status='';return true;">
						<option value=""><xsl:value-of select="lang_select_nullable"/></option>
						<xsl:apply-templates select="nullable_list"/>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<xsl:value-of select="lang_list"/>
				</td>
				<td>
					<xsl:choose>
							<xsl:when test="value_list = 1">
								<input type="checkbox" name="values[list]" value="1" checked="checked" onMouseout="window.status='';return true;">
									<xsl:attribute name="onMouseover">
										<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_list_statustext"/>
										<xsl:text>'; return true;</xsl:text>
									</xsl:attribute>
								</input>
							</xsl:when>
							<xsl:otherwise>
								<input type="checkbox" name="values[list]" value="1" onMouseout="window.status='';return true;">
									<xsl:attribute name="onMouseover">
										<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_list_statustext"/>
										<xsl:text>'; return true;</xsl:text>
									</xsl:attribute>
								</input>
							</xsl:otherwise>
					</xsl:choose>
				</td>
			</tr>
			<tr>
				<td>
					<xsl:value-of select="lang_include_search"/>
				</td>
				<td>
					<xsl:choose>
							<xsl:when test="value_search = 1">
								<input type="checkbox" name="values[search]" value="1" checked="checked" onMouseout="window.status='';return true;">
									<xsl:attribute name="onMouseover">
										<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_include_search_statustext"/>
										<xsl:text>'; return true;</xsl:text>
									</xsl:attribute>
								</input>
							</xsl:when>
							<xsl:otherwise>
								<input type="checkbox" name="values[search]" value="1" onMouseout="window.status='';return true;">
									<xsl:attribute name="onMouseover">
										<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_include_search_statustext"/>
										<xsl:text>'; return true;</xsl:text>
									</xsl:attribute>
								</input>
							</xsl:otherwise>
					</xsl:choose>
				</td>
			</tr>
			<xsl:choose>
				<xsl:when test="multiple_choice != ''">
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_choice"/>
						</td>
						<td align="right">
							<xsl:call-template name="choice"/>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<tr height="50">
				<td>
					<xsl:variable name="lang_save"><xsl:value-of select="lang_save"/></xsl:variable>
					<input type="submit" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_save_attribtext"/>
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
									<xsl:value-of select="lang_done_attribtext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</form>
				</td>
			</tr>
		</table>
		</div>
	</xsl:template>


<!-- add common / edit common -->

	<xsl:template match="edit_common">
		<script language="JavaScript">
			self.name="first_Window";
			<xsl:value-of select="lookup_functions"/>
		</script>
		<xsl:variable name="edit_url"><xsl:value-of select="edit_url"/></xsl:variable>
		<div align="left">
		<form name="form" method="post" action="{$edit_url}">
		<table cellpadding="2" cellspacing="2" width="100%" align="center">
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
				<xsl:when test="value_r_agreement_id!=''">
					<tr >
						<td align="left">
							<xsl:value-of select="lang_agreement"/>
						</td>
						<td align="left">
							<xsl:value-of select="value_r_agreement_id"/>
							<xsl:text> [</xsl:text>
							<xsl:value-of select="agreement_name"/>
							<xsl:text>] </xsl:text>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="value_id!=''">
					<tr >
						<td align="left">
							<xsl:value-of select="lang_id"/>
						</td>
						<td align="left">
							<xsl:value-of select="value_id"/>
						</td>
					</tr>
					<xsl:call-template name="b_account_view"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:call-template name="b_account_form"/>
				</xsl:otherwise>
			</xsl:choose>

			<tr>
				<td valign="top">
					<xsl:value-of select="lang_budget_cost"/>
				</td>
				<td>
					<input type="text" name="values[budget_cost]" value="{value_budget_cost}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_budget_cost_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>

			<tr>
				<td valign="top">
					<xsl:value-of select="lang_override_fraction"/>
				</td>
				<td>
					<input type="text" name="values[override_fraction]" value="{value_override_fraction}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_override_fraction_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>

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


			<xsl:choose>
				<xsl:when test="value_id=''">

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
				</xsl:when>
			</xsl:choose>

			<tr height="50">
			<td colspan = '2'>
			<table>
			<tr>
				<td valign="bottom">
					<xsl:variable name="lang_save"><xsl:value-of select="lang_save"/></xsl:variable>
					<input type="submit" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_save_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
				<td valign="bottom">
					<xsl:variable name="lang_apply"><xsl:value-of select="lang_apply"/></xsl:variable>
					<input type="submit" name="values[apply]" value="{$lang_apply}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_apply_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
				<td align="right" valign="bottom">
					<xsl:variable name="lang_cancel"><xsl:value-of select="lang_cancel"/></xsl:variable>
					<input type="submit" name="values[cancel]" value="{$lang_cancel}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_cancel_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			</table>
			</td>
			</tr>
		</table>
		</form>
		<xsl:choose>
			<xsl:when test="values_common_history != ''">
				<table width="100%" cellpadding="2" cellspacing="2" align="center">
					<xsl:apply-templates select="table_header_common_history"/>
					<xsl:apply-templates select="values_common_history"/>
				</table>	
			</xsl:when>
		</xsl:choose>
		</div>
	</xsl:template>


	<xsl:template match="table_header_common_history">		
			<tr class="th">
				<td width="10%" align="right">
					<xsl:value-of select="lang_id"/>
				</td>
				<td align="center">
					<xsl:value-of select="lang_from_date"/>
				</td>
				<td align="left">
					<xsl:value-of select="lang_to_date"/>
				</td>
				<td align="left">
					<xsl:value-of select="lang_budget_cost"/>
				</td>
				<td align="left">
					<xsl:value-of select="lang_actual_cost"/>
				</td>
				<td align="left">
					<xsl:value-of select="lang_fraction"/>
				</td>
				<td align="left">
					<xsl:value-of select="lang_override_fraction"/>
				</td>
				<td width="5%" align="center">
					<xsl:value-of select="lang_view"/>
				</td>
				<td width="5%" align="center">
					<xsl:value-of select="lang_edit"/>
				</td>
				<td width="5%" align="center">
					<xsl:value-of select="lang_delete"/>
				</td>
			</tr>
	</xsl:template>

	<xsl:template match="values_common_history">
		<xsl:variable name="lang_view_statustext"><xsl:value-of select="lang_view_statustext"/></xsl:variable>
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
					<xsl:value-of select="id"/>
				</td>
				<td align="center">
					<xsl:value-of select="from_date"/>
				</td>
				<td align="left">
					<xsl:value-of select="to_date"/>
				</td>
				<td align="left">
					<xsl:value-of select="budget_cost"/>
				</td>
				<td align="left">
					<xsl:value-of select="actual_cost"/>
				</td>
				<td align="left">
					<xsl:value-of select="fraction"/>
				</td>
				<td align="left">
					<xsl:value-of select="override_fraction"/>
				</td>
				<td align="center">
					<xsl:variable name="link_view"><xsl:value-of select="link_view"/></xsl:variable>
					<a href="{$link_view}" onMouseover="window.status='{$lang_view_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_view"/></a>
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



<!-- datatype_list -->	

	<xsl:template match="datatype_list">
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

<!-- nullable_list -->	

	<xsl:template match="nullable_list">
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

	<xsl:template match="member_of_list">
	<xsl:variable name="id"><xsl:value-of select="cat_id"/></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected='selected'">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="rental_type_list">
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


	<xsl:template name="filter_member_of">
		<xsl:variable name="select_action"><xsl:value-of select="select_action"/></xsl:variable>
		<xsl:variable name="member_of_name"><xsl:value-of select="member_of_name"/></xsl:variable>
		<xsl:variable name="lang_submit"><xsl:value-of select="lang_submit"/></xsl:variable>
			<select name="{$member_of_name}"  onMouseout="window.status='';return true;">
				<xsl:attribute name="onMouseover">
					<xsl:text>window.status='</xsl:text>
						<xsl:value-of select="lang_cat_statustext"/>
					<xsl:text>'; return true;</xsl:text>
				</xsl:attribute>
				<option value=""><xsl:value-of select="lang_no_member"/></option>
					<xsl:apply-templates select="member_of_list"/>
			</select>
	</xsl:template>
