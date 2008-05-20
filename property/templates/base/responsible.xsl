
	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="edit_type">
				<xsl:apply-templates select="edit_type"/>
			</xsl:when>
			<xsl:when test="edit_contact">
				<xsl:apply-templates select="edit_contact"/>
			</xsl:when>
			<xsl:when test="list_contact">
				<xsl:apply-templates select="list_contact"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="list_type"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>


	<xsl:template match="list_type">
		<xsl:variable name="responsible_action"><xsl:value-of select="responsible_action"/></xsl:variable>
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
					<xsl:call-template name="filter_location"/>
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
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:apply-templates select="table_header_type"/>
			<xsl:choose>
				<xsl:when test="values != ''">
					<xsl:apply-templates select="values_type"/>
				</xsl:when>
			</xsl:choose>
			<xsl:apply-templates select="table_add"/>
		</table>
	</xsl:template>

	<xsl:template match="table_header_type">
		<xsl:variable name="sort_location"><xsl:value-of select="sort_location"/></xsl:variable>
		<tr class="th">
			<td class="th_text" width="10%" align="left">
				<a href="{$sort_location}"><xsl:value-of select="lang_location"/></a>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_action"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_user"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_supervisor"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_select"/>
			</td>
		</tr>
	</xsl:template>

	<xsl:template match="values_type">
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
					<xsl:value-of select="location"/>
				</td>
				<td align="left">
					<xsl:value-of select="action"/>
				</td>
				<td align="left">
					<xsl:value-of select="user"/>
				</td>
				<td align="left">
					<xsl:value-of select="supervisor"/>
				</td>
				<xsl:choose>
					<xsl:when test="lang_select_responsible_text != ''">
						<td align="center" title="{lang_select_responsible_text}" style="cursor:help">
							<input type="checkbox" name="values[]" value="{}" >
							</input>
						</td>
					</xsl:when>
				</xsl:choose>
			</tr>
	</xsl:template>


	<xsl:template match="list_contact">
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
					<xsl:call-template name="filter_location"/>
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
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:apply-templates select="table_header_contact"/>
			<xsl:choose>
				<xsl:when test="values != ''">
					<xsl:apply-templates select="values_contact"/>
				</xsl:when>
			</xsl:choose>
			<xsl:apply-templates select="table_add"/>
		</table>
	</xsl:template>

	<xsl:template match="table_header_contact">
		<xsl:variable name="sort_location"><xsl:value-of select="sort_location"/></xsl:variable>
		<tr class="th">
			<td class="th_text" width="10%" align="left">
				<a href="{$sort_location}"><xsl:value-of select="lang_location"/></a>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_action"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_user"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_supervisor"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_select"/>
			</td>
		</tr>
	</xsl:template>

	<xsl:template match="values_contact">
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
					<xsl:value-of select="location"/>
				</td>
				<td align="left">
					<xsl:value-of select="action"/>
				</td>
				<td align="left">
					<xsl:value-of select="user"/>
				</td>
				<td align="left">
					<xsl:value-of select="supervisor"/>
				</td>
				<xsl:choose>
					<xsl:when test="lang_select_responsible_text != ''">
						<td align="center" title="{lang_select_responsible_text}" style="cursor:help">
							<input type="checkbox" name="values[]" value="{}" >
							</input>
						</td>
					</xsl:when>
				</xsl:choose>
			</tr>
	</xsl:template>


	<xsl:template match="table_add">
		<xsl:variable name="add_action"><xsl:value-of select="add_action"/></xsl:variable>
		<xsl:variable name="lang_add"><xsl:value-of select="lang_add"/></xsl:variable>
		<tr>
			<td height="50">
				<form method="post" action="{$add_action}">
					<input type="submit" name="add" value="{$lang_add}">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_add_statustext"/>
						</xsl:attribute>
						<xsl:attribute name="style">
							<xsl:text>cursor:help</xsl:text>
						</xsl:attribute>
					</input>
				</form>
			</td>
		</tr>
	</xsl:template>
	
<!-- add / edit responsibility type-->
	<xsl:template match="edit_type">
		<div align="left">
		<xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
		<form method="post" action="{$form_action}">
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
				<xsl:when test="value_id != ''">
				<tr>
				<td valign="top" width="30%">
						<xsl:value-of select="lang_id"/>
					</td>
					<td align="left">
						<xsl:value-of select="value_id"/>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_entry_date"/>
					</td>
					<td>
						<xsl:value-of select="value_entry_date"/>
					</td>
				</tr>
				</xsl:when>
			</xsl:choose>	
			<tr>
				<td>
					<xsl:value-of select="lang_category"/>
				</td>
				<td>
					<xsl:call-template name="categories"/>
				</td>
			</tr>
			<tr>
				<td valign="top" width="10%"  title="{lang_name_status_text}" style="cursor:help">
					<xsl:value-of select="lang_name"/>
				</td>
				<td>
					<input type="text" size="60" name="values[name]" value="{value_name}" onMouseout="window.status='';return true;">
					</input>
				</td>
			</tr>
			<tr>
				<td valign="top"  title="{lang_descr_status_text}" style="cursor:help">
					<xsl:value-of select="lang_descr"/>
				</td>
				<td>
					<textarea cols="60" rows="10" name="values[descr]" wrap="virtual" onMouseout="window.status='';return true;">
						<xsl:value-of select="value_descr"/>		
					</textarea>
				</td>
			</tr>
			<tr>
				<td>
					<xsl:value-of select="lang_active"/>
				</td>
				<td>
					<xsl:choose>
						<xsl:when test="value_active = '1'">
							<input type="checkbox" name="values[active]" value="1" checked="checked" onMouseout="window.status='';return true;">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_active_on_statustext"/>
								</xsl:attribute>
								<xsl:attribute name="style">
									<xsl:text>cursor:help</xsl:text>
								</xsl:attribute>
							</input>
						</xsl:when>
						<xsl:otherwise>
							<input type="checkbox" name="values[active]" value="1" onMouseout="window.status='';return true;">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_active_off_statustext"/>
								</xsl:attribute>
								<xsl:attribute name="style">
									<xsl:text>cursor:help</xsl:text>
								</xsl:attribute>
							</input>
						</xsl:otherwise>
					</xsl:choose>
				</td>
			</tr>
			<tr height="50">
				<td colspan = "2" align = "center">
					<table>
						<tr>
							<td valign="bottom">
								<xsl:variable name="lang_save"><xsl:value-of select="lang_save"/></xsl:variable>
								<input type="submit" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_save_status_text"/>
									</xsl:attribute>
									<xsl:attribute name="style">
										<xsl:text>cursor:help</xsl:text>
									</xsl:attribute>
								</input>
							</td>
							<td valign="bottom">
								<xsl:variable name="lang_apply"><xsl:value-of select="lang_apply"/></xsl:variable>
								<input type="submit" name="values[apply]" value="{$lang_apply}" onMouseout="window.status='';return true;">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_apply_status_text"/>
									</xsl:attribute>
									<xsl:attribute name="style">
										<xsl:text>cursor:help</xsl:text>
									</xsl:attribute>
								</input>
							</td>
							<td align="left" valign="bottom">
								<xsl:variable name="lang_cancel"><xsl:value-of select="lang_cancel"/></xsl:variable>
								<input type="submit" name="values[cancel]" value="{$lang_cancel}" onMouseout="window.status='';return true;">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_cancel_status_text"/>
									</xsl:attribute>
									<xsl:attribute name="style">
										<xsl:text>cursor:help</xsl:text>
									</xsl:attribute>
								</input>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		</form>
		</div>
	</xsl:template>

