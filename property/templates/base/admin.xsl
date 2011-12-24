<!-- $Id$ -->

	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="edit_id">
				<xsl:apply-templates select="edit_id"></xsl:apply-templates>
			</xsl:when>
			<xsl:when test="contact_info">
				<xsl:apply-templates select="contact_info"></xsl:apply-templates>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="list_permission"></xsl:apply-templates>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="list_permission">
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:choose>
				<xsl:when test="msgbox_data != ''">
					<tr>
						<td align="left" colspan="4">
							<xsl:call-template name="msgbox"></xsl:call-template>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<tr>
				<td>
					<xsl:call-template name="filter_location"></xsl:call-template>
				</td>
				<td align="center">
					<xsl:call-template name="cat_filter"></xsl:call-template>
				</td>

				<xsl:choose>
					<xsl:when test="is_admin != '' and grant= 1">
						<td align="center">
							<xsl:call-template name="group_filter"></xsl:call-template>
						</td>
					</xsl:when>
				</xsl:choose>
				<td align="right">
					<xsl:call-template name="search_field"></xsl:call-template>
				</td>
			</tr>
			<tr>
				<td colspan="4" width="100%">
					<xsl:call-template name="nextmatchs"></xsl:call-template>
				</td>
			</tr>
		</table>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:variable name="form_action"><xsl:value-of select="form_action"></xsl:value-of></xsl:variable>
			<form method="post" action="{$form_action}">
				<tr height="30">
					<td valign="top" align="left">
						<xsl:value-of select="lang_enable_inheritance"></xsl:value-of>
						<xsl:text>: </xsl:text>
						<input type="checkbox" name="enable_inheritance" value="true" title="{lang_enable_inheritance_statustext}"></input>
					</td>
				</tr>
				<tr class="th">
					<xsl:choose>
						<xsl:when test="values_groups!=''">
							<td class="th_text" width="10%" align="center">
								<xsl:value-of select="lang_groups"></xsl:value-of>
							</td>
							<td>
							</td>
						</xsl:when>
					</xsl:choose>
				</tr>
				<xsl:apply-templates select="values_groups"></xsl:apply-templates>
				<tr class="th">
					<xsl:choose>
						<xsl:when test="values_users!=''">
							<td class="th_text" width="10%" align="center">
								<xsl:value-of select="lang_users"></xsl:value-of>
							</td>
							<td>
							</td>
						</xsl:when>
					</xsl:choose>
				</tr>
				<xsl:apply-templates select="values_users"></xsl:apply-templates>

				<tr height="50">
					<td>
						<xsl:variable name="location"><xsl:value-of select="location"></xsl:value-of></xsl:variable>
						<xsl:variable name="processed"><xsl:value-of select="processed"></xsl:value-of></xsl:variable>
						<xsl:variable name="lang_save"><xsl:value-of select="lang_save"></xsl:value-of></xsl:variable>
						<INPUT type="hidden" name="location" value="{$location}"></INPUT>
						<INPUT type="hidden" name="processed" value="{$processed}"></INPUT>

						<input type="submit" name="set_permission" value="{$lang_save}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_save_statustext"></xsl:value-of>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>

					</td>
				</tr>
			</form>
			<tr>
				<td>
					<xsl:variable name="done_action"><xsl:value-of select="done_action"></xsl:value-of></xsl:variable>
					<xsl:variable name="lang_done"><xsl:value-of select="lang_done"></xsl:value-of></xsl:variable>
					<form method="post" action="{$done_action}">
						<input type="submit" name="done" value="{$lang_done}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_done_statustext"></xsl:value-of>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</form>
				</td>
			</tr>
		</table>

	</xsl:template>


	<xsl:template match="values_groups">
		<xsl:choose>
			<xsl:when test="account_id !=''">
				<tr>
					<xsl:attribute name="class">
						<xsl:choose>
							<xsl:when test="@class">
								<xsl:value-of select="@class"></xsl:value-of>
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
						<xsl:value-of select="name"></xsl:value-of>
					</td>
					<td>
						<table width="100%" cellpadding="2" cellspacing="2" align="center">
							<tr class="th">
								<xsl:call-template name="value_header"></xsl:call-template>
							</tr>
							<tr>
								<xsl:call-template name="right"></xsl:call-template>
							</tr>
							<tr>
								<xsl:call-template name="mask"></xsl:call-template>
							</tr>
							<tr>
								<xsl:call-template name="result"></xsl:call-template>
							</tr>
						</table>
					</td>

				</tr>
			</xsl:when>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="values_users">
		<xsl:choose>
			<xsl:when test="account_id !=''">
				<tr>
					<xsl:attribute name="class">
						<xsl:choose>
							<xsl:when test="@class">
								<xsl:value-of select="@class"></xsl:value-of>
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
						<xsl:value-of select="name"></xsl:value-of>
					</td>
					<td>
						<table width="100%" cellpadding="2" cellspacing="2" align="center">
							<tr class="th">
								<xsl:call-template name="value_header"></xsl:call-template>
							</tr>
							<tr>
								<xsl:call-template name="right"></xsl:call-template>
							</tr>
							<tr>
								<xsl:call-template name="mask"></xsl:call-template>
							</tr>
							<tr>
								<xsl:call-template name="result"></xsl:call-template>
							</tr>
						</table>
					</td>

				</tr>
			</xsl:when>
		</xsl:choose>
	</xsl:template>

	<xsl:template name="value_header">
		<td>
		</td>
		<td class="th_text" align="center">
			<xsl:value-of select="lang_read"></xsl:value-of>
		</td>
		<td class="th_text" align="center">
			<xsl:value-of select="lang_add"></xsl:value-of>
		</td>
		<td class="th_text" align="center">
			<xsl:value-of select="lang_edit"></xsl:value-of>
		</td>
		<td class="th_text" align="center">
			<xsl:value-of select="lang_delete"></xsl:value-of>
		</td>
		<xsl:choose>
			<xsl:when test="//permission= 1">
				<td class="th_text" align="center">
					<xsl:value-of select="lang_manage"></xsl:value-of>
				</td>
			</xsl:when>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="//location='.invoice' and //permission= 1 ">
				<td class="th_text" align="center">
					<xsl:value-of select="lang_janitor"></xsl:value-of>
				</td>
				<td class="th_text" align="center">
					<xsl:value-of select="lang_supervisor"></xsl:value-of>
				</td>
				<td class="th_text" align="center">
					<xsl:value-of select="lang_budget_responsible"></xsl:value-of>
				</td>
				<td class="th_text" align="center">
					<xsl:value-of select="lang_initials"></xsl:value-of>
				</td>						
			</xsl:when>
		</xsl:choose>
	</xsl:template>

	<xsl:template name="right">
		<td align="left">
			<xsl:value-of select="lang_right"></xsl:value-of>
		</td>
		<td align="center">

			<xsl:choose>
				<xsl:when test="type = 'users'">
					<xsl:choose>
						<xsl:when test="read_right = 'checked'">
							<input type="checkbox" name="values[right][{account_id}_1]" value="1" checked="checked"></input>
						</xsl:when>
						<xsl:when test="read_right = 'from_group'">
							<input type="checkbox" name="" checked="checked" disabled="disabled"></input>
						</xsl:when>
						<xsl:otherwise>
							<input type="checkbox" name="values[right][{account_id}_1]" value="1"></input>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:when>
				<xsl:otherwise>
					<xsl:choose>
						<xsl:when test="read_right = 'checked'">
							<input type="checkbox" name="values[right][{account_id}_1]" value="1" checked="checked"></input>
						</xsl:when>
						<xsl:when test="read_right = 'from_group'">
							<input type="checkbox" name="values[right][{account_id}_1]" value="1" checked="checked"></input>
						</xsl:when>
						<xsl:otherwise>
							<input type="checkbox" name="values[right][{account_id}_1]" value="1"></input>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:otherwise>
			</xsl:choose>
		</td>
		<td align="center">
			<xsl:choose>
				<xsl:when test="type = 'users'">
					<xsl:choose>
						<xsl:when test="add_right = 'checked'">
							<input type="checkbox" name="values[right][{account_id}_2]" value="2" checked="checked"></input>
						</xsl:when>
						<xsl:when test="add_right = 'from_group'">
							<input type="checkbox" name="" checked="checked" disabled="disabled"></input>
						</xsl:when>
						<xsl:otherwise>
							<input type="checkbox" name="values[right][{account_id}_2]" value="2"></input>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:when>
				<xsl:otherwise>
					<xsl:choose>
						<xsl:when test="add_right = 'checked'">
							<input type="checkbox" name="values[right][{account_id}_2]" value="2" checked="checked"></input>
						</xsl:when>
						<xsl:when test="add_right = 'from_group'">
							<input type="checkbox" name="values[right][{account_id}_2]" value="2" checked="checked"></input>
						</xsl:when>
						<xsl:otherwise>
							<input type="checkbox" name="values[right][{account_id}_2]" value="2"></input>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:otherwise>
			</xsl:choose>
		</td>
		<td align="center">
			<xsl:choose>
				<xsl:when test="type = 'users'">
					<xsl:choose>
						<xsl:when test="edit_right = 'checked'">
							<input type="checkbox" name="values[right][{account_id}_4]" value="4" checked="checked"></input>
						</xsl:when>
						<xsl:when test="edit_right = 'from_group'">
							<input type="checkbox" name="" checked="checked" disabled="disabled"></input>
						</xsl:when>
						<xsl:otherwise>
							<input type="checkbox" name="values[right][{account_id}_4]" value="4"></input>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:when>
				<xsl:otherwise>
					<xsl:choose>
						<xsl:when test="edit_right = 'checked'">
							<input type="checkbox" name="values[right][{account_id}_4]" value="4" checked="checked"></input>
						</xsl:when>
						<xsl:when test="edit_right = 'from_group'">
							<input type="checkbox" name="values[right][{account_id}_4]" value="4" checked="checked"></input>
						</xsl:when>
						<xsl:otherwise>
							<input type="checkbox" name="values[right][{account_id}_4]" value="4"></input>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:otherwise>
			</xsl:choose>
		</td>
		<td align="center">
			<xsl:choose>
				<xsl:when test="type = 'users'">
					<xsl:choose>
						<xsl:when test="delete_right = 'checked'">
							<input type="checkbox" name="values[right][{account_id}_8]" value="8" checked="checked"></input>
						</xsl:when>
						<xsl:when test="delete_right = 'from_group'">
							<input type="checkbox" name="" checked="checked" disabled="disabled"></input>
						</xsl:when>
						<xsl:otherwise>
							<input type="checkbox" name="values[right][{account_id}_8]" value="8"></input>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:when>
				<xsl:otherwise>
					<xsl:choose>
						<xsl:when test="delete_right = 'checked'">
							<input type="checkbox" name="values[right][{account_id}_8]" value="8" checked="checked"></input>
						</xsl:when>
						<xsl:when test="delete_right = 'from_group'">
							<input type="checkbox" name="values[right][{account_id}_8]" value="8" checked="checked"></input>
						</xsl:when>
						<xsl:otherwise>
							<input type="checkbox" name="values[right][{account_id}_8]" value="8"></input>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:otherwise>
			</xsl:choose>
		</td>
		<xsl:choose>
			<xsl:when test="//permission= 1">
				<td align="center">
					<xsl:choose>
						<xsl:when test="type = 'users'">
							<xsl:choose>
								<xsl:when test="manage_right = 'checked'">
									<input type="checkbox" name="values[right][{account_id}_16]" value="16" checked="checked"></input>
								</xsl:when>
								<xsl:when test="manage_right = 'from_group'">
									<input type="checkbox" name="" checked="checked" disabled="disabled"></input>
								</xsl:when>
								<xsl:otherwise>
									<input type="checkbox" name="values[right][{account_id}_16]" value="16"></input>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:when>
						<xsl:otherwise>
							<xsl:choose>
								<xsl:when test="manage_right = 'checked'">
									<input type="checkbox" name="values[right][{account_id}_16]" value="16" checked="checked"></input>
								</xsl:when>
								<xsl:when test="manage_right = 'from_group'">
									<input type="checkbox" name="values[right][{account_id}_16]" value="16" checked="checked"></input>
								</xsl:when>
								<xsl:otherwise>
									<input type="checkbox" name="values[right][{account_id}_16]" value="16"></input>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:otherwise>
					</xsl:choose>
				</td>
			</xsl:when>
		</xsl:choose>

		<xsl:choose>
			<xsl:when test="//location='.invoice' and //permission= 1 and type= 'users'">
				<td align="center">
					<xsl:choose>
						<xsl:when test="janitor_right = 'checked'">
							<INPUT type="checkbox" name="values[right][{account_id}_32]" value="32" checked="checked"></INPUT>
						</xsl:when>
						<xsl:otherwise>
							<INPUT type="checkbox" name="values[right][{account_id}_32]" value="32"></INPUT>
						</xsl:otherwise>
					</xsl:choose>
				</td>
				<td align="center">
					<xsl:choose>
						<xsl:when test="supervisor_right = 'checked'">
							<INPUT type="checkbox" name="values[right][{account_id}_64]" value="64" checked="checked"></INPUT>
						</xsl:when>
						<xsl:otherwise>
							<INPUT type="checkbox" name="values[right][{account_id}_64]" value="64"></INPUT>
						</xsl:otherwise>
					</xsl:choose>
				</td>
				<td align="center">
					<xsl:choose>
						<xsl:when test="budget_responsible_right = 'checked'">
							<INPUT type="checkbox" name="values[right][{account_id}_128]" value="128" checked="checked"></INPUT>
						</xsl:when>
						<xsl:otherwise>
							<INPUT type="checkbox" name="values[right][{account_id}_128]" value="128"></INPUT>
						</xsl:otherwise>
					</xsl:choose>
				</td>
				<td align="center">
					<INPUT type="text" size="4" name="initials[{account_id}]" value="{initials}"></INPUT>
				</td>						
			</xsl:when>
			<xsl:otherwise>
				<td>
				</td>
				<td>
				</td>
				<td>
				</td>
				<td>
				</td>						
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template name="mask">
		<td align="left">
			<xsl:value-of select="lang_mask"></xsl:value-of>
		</td>
		<td align="center">
			<xsl:choose>
				<xsl:when test="type = 'users'">
					<xsl:choose>
						<xsl:when test="read_mask = 'checked'">
							<input type="checkbox" name="values[mask][{account_id}_1]" value="1" checked="checked"></input>
						</xsl:when>
						<xsl:when test="read_mask = 'from_group'">
							<input type="checkbox" name="" checked="checked" disabled="disabled"></input>
						</xsl:when>
						<xsl:otherwise>
							<input type="checkbox" name="values[mask][{account_id}_1]" value="1"></input>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:when>
				<xsl:otherwise>
					<xsl:choose>
						<xsl:when test="read_mask = 'checked'">
							<input type="checkbox" name="values[mask][{account_id}_1]" value="1" checked="checked"></input>
						</xsl:when>
						<xsl:when test="read_mask = 'from_group'">
							<input type="checkbox" name="values[mask][{account_id}_1]" value="1" checked="checked"></input>
						</xsl:when>
						<xsl:otherwise>
							<input type="checkbox" name="values[mask][{account_id}_1]" value="1"></input>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:otherwise>
			</xsl:choose>
		</td>
		<td align="center">
			<xsl:choose>
				<xsl:when test="type = 'users'">
					<xsl:choose>
						<xsl:when test="add_mask = 'checked'">
							<input type="checkbox" name="values[mask][{account_id}_2]" value="2" checked="checked"></input>
						</xsl:when>
						<xsl:when test="add_mask = 'from_group'">
							<input type="checkbox" name="" checked="checked" disabled="disabled"></input>
						</xsl:when>
						<xsl:otherwise>
							<input type="checkbox" name="values[mask][{account_id}_2]" value="2"></input>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:when>
				<xsl:otherwise>
					<xsl:choose>
						<xsl:when test="add_mask = 'checked'">
							<input type="checkbox" name="values[mask][{account_id}_2]" value="2" checked="checked"></input>
						</xsl:when>
						<xsl:when test="add_mask = 'from_group'">
							<input type="checkbox" name="values[mask][{account_id}_2]" value="2" checked="checked"></input>
						</xsl:when>
						<xsl:otherwise>
							<input type="checkbox" name="values[mask][{account_id}_2]" value="2"></input>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:otherwise>
			</xsl:choose>
		</td>
		<td align="center">
			<xsl:choose>
				<xsl:when test="type = 'users'">
					<xsl:choose>
						<xsl:when test="edit_mask = 'checked'">
							<input type="checkbox" name="values[mask][{account_id}_4]" value="4" checked="checked"></input>
						</xsl:when>
						<xsl:when test="edit_mask = 'from_group'">
							<input type="checkbox" name="" checked="checked" disabled="disabled"></input>
						</xsl:when>
						<xsl:otherwise>
							<input type="checkbox" name="values[mask][{account_id}_4]" value="4"></input>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:when>
				<xsl:otherwise>
					<xsl:choose>
						<xsl:when test="edit_mask = 'checked'">
							<input type="checkbox" name="values[mask][{account_id}_4]" value="4" checked="checked"></input>
						</xsl:when>
						<xsl:when test="edit_mask = 'from_group'">
							<input type="checkbox" name="values[mask][{account_id}_4]" value="4" checked="checked"></input>
						</xsl:when>
						<xsl:otherwise>
							<input type="checkbox" name="values[mask][{account_id}_4]" value="4"></input>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:otherwise>
			</xsl:choose>
		</td>
		<td align="center">
			<xsl:choose>
				<xsl:when test="type = 'users'">
					<xsl:choose>
						<xsl:when test="delete_mask = 'checked'">
							<input type="checkbox" name="values[mask][{account_id}_8]" value="8" checked="checked"></input>
						</xsl:when>
						<xsl:when test="delete_mask = 'from_group'">
							<input type="checkbox" name="" checked="checked" disabled="disabled"></input>
						</xsl:when>
						<xsl:otherwise>
							<input type="checkbox" name="values[mask][{account_id}_8]" value="8"></input>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:when>
				<xsl:otherwise>
					<xsl:choose>
						<xsl:when test="delete_mask = 'checked'">
							<input type="checkbox" name="values[mask][{account_id}_8]" value="8" checked="checked"></input>
						</xsl:when>
						<xsl:when test="delete_mask = 'from_group'">
							<input type="checkbox" name="values[mask][{account_id}_8]" value="8" checked="checked"></input>
						</xsl:when>
						<xsl:otherwise>
							<input type="checkbox" name="values[mask][{account_id}_8]" value="8"></input>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:otherwise>
			</xsl:choose>
		</td>
		<xsl:choose>
			<xsl:when test="//permission= 1">
				<td align="center">
					<xsl:choose>
						<xsl:when test="type = 'users'">
							<xsl:choose>
								<xsl:when test="manage_mask = 'checked'">
									<input type="checkbox" name="values[mask][{account_id}_16]" value="16" checked="checked"></input>
								</xsl:when>
								<xsl:when test="manage_mask = 'from_group'">
									<input type="checkbox" name="" checked="checked" disabled="disabled"></input>
								</xsl:when>
								<xsl:otherwise>
									<input type="checkbox" name="values[mask][{account_id}_16]" value="16"></input>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:when>
						<xsl:otherwise>
							<xsl:choose>
								<xsl:when test="manage_mask = 'checked'">
									<input type="checkbox" name="values[mask][{account_id}_16]" value="16" checked="checked"></input>
								</xsl:when>
								<xsl:when test="manage_mask = 'from_group'">
									<input type="checkbox" name="values[mask][{account_id}_16]" value="16" checked="checked"></input>
								</xsl:when>
								<xsl:otherwise>
									<input type="checkbox" name="values[mask][{account_id}_16]" value="16"></input>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:otherwise>
					</xsl:choose>
				</td>
			</xsl:when>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="//location='.invoice' and //permission= 1 and type= 'users'">
				<td align="center">
					<xsl:choose>
						<xsl:when test="janitor_mask = 'checked'">
							<INPUT type="checkbox" name="values[mask][{account_id}_32]" value="32" checked="checked"></INPUT>
						</xsl:when>
						<xsl:otherwise>
							<INPUT type="checkbox" name="values[mask][{account_id}_32]" value="32"></INPUT>
						</xsl:otherwise>
					</xsl:choose>
				</td>
				<td align="center">
					<xsl:choose>
						<xsl:when test="supervisor_mask = 'checked'">
							<INPUT type="checkbox" name="values[mask][{account_id}_64]" value="64" checked="checked"></INPUT>
						</xsl:when>
						<xsl:otherwise>
							<INPUT type="checkbox" name="values[mask][{account_id}_64]" value="64"></INPUT>
						</xsl:otherwise>
					</xsl:choose>
				</td>
				<td align="center">
					<xsl:choose>
						<xsl:when test="budget_responsible_mask = 'checked'">
							<INPUT type="checkbox" name="values[mask][{account_id}_128]" value="128" checked="checked"></INPUT>
						</xsl:when>
						<xsl:otherwise>
							<INPUT type="checkbox" name="values[mask][{account_id}_128]" value="128"></INPUT>
						</xsl:otherwise>
					</xsl:choose>
				</td>
				<td>
				</td>						
			</xsl:when>
		</xsl:choose>
	</xsl:template>

	<xsl:template name="result">
		<td align="left">
			<xsl:value-of select="lang_result"></xsl:value-of>
		</td>
		<td align="center">
			<xsl:choose>
				<xsl:when test="read_result = 'checked'">
					<INPUT type="checkbox" name="" checked="checked" disabled="disabled"></INPUT>
				</xsl:when>
				<xsl:otherwise>
					<INPUT type="checkbox" name="" disabled="disabled"></INPUT>
				</xsl:otherwise>
			</xsl:choose>
		</td>
		<td align="center">
			<xsl:choose>
				<xsl:when test="add_result = 'checked'">
					<INPUT type="checkbox" name="" checked="checked" disabled="disabled"></INPUT>
				</xsl:when>
				<xsl:otherwise>
					<INPUT type="checkbox" name="" disabled="disabled"></INPUT>
				</xsl:otherwise>
			</xsl:choose>
		</td>
		<td align="center">
			<xsl:choose>
				<xsl:when test="edit_result = 'checked'">
					<INPUT type="checkbox" name="" checked="checked" disabled="disabled"></INPUT>
				</xsl:when>
				<xsl:otherwise>
					<INPUT type="checkbox" name="" disabled="disabled"></INPUT>
				</xsl:otherwise>
			</xsl:choose>
		</td>
		<td align="center">
			<xsl:choose>
				<xsl:when test="delete_result = 'checked'">
					<INPUT type="checkbox" name="" checked="checked" disabled="disabled"></INPUT>
				</xsl:when>
				<xsl:otherwise>
					<INPUT type="checkbox" name="" disabled="disabled"></INPUT>
				</xsl:otherwise>
			</xsl:choose>
		</td>
		<xsl:choose>
			<xsl:when test="//permission= 1">
				<td align="center">
					<xsl:choose>
						<xsl:when test="manage_result = 'checked'">
							<INPUT type="checkbox" name="" checked="checked" disabled="disabled"></INPUT>
						</xsl:when>
						<xsl:otherwise>
							<INPUT type="checkbox" name="" disabled="disabled"></INPUT>
						</xsl:otherwise>
					</xsl:choose>
				</td>
			</xsl:when>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="//location='.invoice' and //permission= 1 and type= 'users'">
				<td align="center">
					<xsl:choose>
						<xsl:when test="janitor_result = 'checked'">
							<INPUT type="checkbox" name="" checked="checked" disabled="disabled"></INPUT>
						</xsl:when>
						<xsl:otherwise>
							<INPUT type="checkbox" name="" disabled="disabled"></INPUT>
						</xsl:otherwise>
					</xsl:choose>
				</td>
				<td align="center">
					<xsl:choose>
						<xsl:when test="supervisor_result = 'checked'">
							<INPUT type="checkbox" name="" checked="checked" disabled="disabled"></INPUT>
						</xsl:when>
						<xsl:otherwise>
							<INPUT type="checkbox" name="" disabled="disabled"></INPUT>
						</xsl:otherwise>
					</xsl:choose>
				</td>
				<td align="center">
					<xsl:choose>
						<xsl:when test="budget_responsible_result = 'checked'">
							<INPUT type="checkbox" name="" checked="checked" disabled="disabled"></INPUT>
						</xsl:when>
						<xsl:otherwise>
							<INPUT type="checkbox" name="" disabled="disabled"></INPUT>
						</xsl:otherwise>
					</xsl:choose>
				</td>
				<td>
				</td>						
			</xsl:when>
		</xsl:choose>
	</xsl:template>

<!-- edit_id -->	

	<xsl:template xmlns:php="http://php.net/xsl" match="id_values">
		<xsl:variable name="value"><xsl:value-of select="value"></xsl:value-of></xsl:variable>
		<xsl:variable name="key_id"><xsl:value-of select="key_id"></xsl:value-of></xsl:variable>
		<xsl:variable name="descr"><xsl:value-of select="descr"></xsl:value-of></xsl:variable>
		<xsl:variable name="name"><xsl:value-of select="name"></xsl:value-of></xsl:variable>
		<tr>
			<xsl:attribute name="class">
				<xsl:choose>
					<xsl:when test="@class">
						<xsl:value-of select="@class"></xsl:value-of>
					</xsl:when>
					<xsl:when test="position() mod 2 = 0">
						<xsl:text>row_off</xsl:text>
					</xsl:when>
					<xsl:otherwise>
						<xsl:text>row_on</xsl:text>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:attribute>

			<td align="center">
				<INPUT type="checkbox" name="values[select][]" value="{$name}">
					<xsl:if test="old = '1'">
						<xsl:attribute name="disabled">
							<xsl:text>disabled</xsl:text>
						</xsl:attribute>
					</xsl:if>
				</INPUT>
			</td>
			<td align="left">
				<xsl:value-of select="descr"></xsl:value-of>
			</td>
			<td>
				<INPUT type="text" name="values[{$name}]" value="{$value}"></INPUT>
			</td>
			<td>
			<xsl:choose>
				<xsl:when test="old != '1'">
					<input type="text" id="date_{$name}" name="values[start_date][{$name}]" size="10" value="{start_date}" readonly="readonly">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_date_statustext"></xsl:value-of>
						</xsl:attribute>
					</input>
					<img id="date_{$name}-trigger" src="{//img_cal}" alt="{//lang_datetitle}" title="{//lang_datetitle}" style="cursor:pointer; cursor:hand;"></img>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="start_date"></xsl:value-of>
				</xsl:otherwise>
			</xsl:choose>
			</td>
		</tr>
	</xsl:template>

	<xsl:template xmlns:php="http://php.net/xsl" name="id_table_header">
		<tr class="th">
			<td class="th_text" width="10%" align="center">
				<xsl:value-of select="php:function('lang', 'select')"></xsl:value-of>
			</td>
			<td class="th_text" width="40%" align="left">
				<xsl:value-of select="php:function('lang', 'descr')"></xsl:value-of>
			</td>
			<td class="th_text" width="30%">
				<xsl:value-of select="php:function('lang', 'value')"></xsl:value-of>
			</td>
			<td class="th_text" width="20%" align="center">
				<xsl:value-of select="php:function('lang', 'date')"></xsl:value-of>
			</td>
		</tr>
	</xsl:template>

	<xsl:template xmlns:php="http://php.net/xsl" match="edit_id">
		<table cellpadding="2" cellspacing="2" width="90%" align="center">
			<xsl:choose>
				<xsl:when test="msgbox_data != ''">
					<tr>
						<td align="left" colspan="4">
							<xsl:call-template name="msgbox"></xsl:call-template>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<xsl:variable name="form_action"><xsl:value-of select="form_action"></xsl:value-of></xsl:variable>
			<form method="post" action="{$form_action}">
				<xsl:call-template name="id_table_header"></xsl:call-template> 
				<xsl:apply-templates select="id_values"></xsl:apply-templates> 
				<tr height="50">
					<td>
						<xsl:variable name="lang_save"><xsl:value-of select="lang_save"></xsl:value-of></xsl:variable>
						<input type="submit" name="values[save]" value="{$lang_save}">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_save_statustext"></xsl:value-of>
							</xsl:attribute>
						</input>
					</td>
				</tr>
			</form>
			<tr>
				<td>
					<xsl:variable name="done_action"><xsl:value-of select="done_action"></xsl:value-of></xsl:variable>
					<xsl:variable name="lang_done"><xsl:value-of select="lang_done"></xsl:value-of></xsl:variable>
					<form method="post" action="{$done_action}">
						<input type="submit" name="done" value="{$lang_done}">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_done_statustext"></xsl:value-of>
							</xsl:attribute>
						</input>
					</form>
				</td>
			</tr>
		</table>
	</xsl:template>


	<xsl:template match="contact_info">
		<div align="left">
			<table cellpadding="2" cellspacing="2" width="80%" align="center">
				<xsl:choose>
					<xsl:when test="msgbox_data != ''">
						<tr>
							<td align="left" colspan="3">
								<xsl:call-template name="msgbox"></xsl:call-template>
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>
				<tr>
					<td align="left">
						<xsl:value-of select="lang_user"></xsl:value-of>
					</td>
					<td align="left">
						<xsl:call-template name="user_id_filter"></xsl:call-template>
					</td>
				</tr>
				<xsl:variable name="form_action"><xsl:value-of select="form_action"></xsl:value-of></xsl:variable>
				<form method="post" name="form" action="{$form_action}">
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_email"></xsl:value-of>
						</td>
						<td>
							<input type="hidden" name="user_id" value="{value_user_id}"></input>
							<input type="hidden" name="values[old_email]" value="{value_old_email}"></input>
							<input type="text" size="30" name="values[email]" value="{value_email}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_email_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_phone"></xsl:value-of>
						</td>
						<td>
							<input type="hidden" name="values[old_phone]" value="{value_old_phone}"></input>
							<input type="text" size="30" name="values[phone]" value="{value_phone}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_phone_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_approval_from"></xsl:value-of>
						</td>
						<td align="left">
							<input type="hidden" name="values[old_approval_from]" value="{value_old_approval_from}"></input>
							<xsl:variable name="lang_approval_from_statustext"><xsl:value-of select="lang_approval_from_statustext"></xsl:value-of></xsl:variable>
							<select name="values[approval_from]" class="forms" onMouseover="window.status='{$lang_approval_from_statustext}'; return true;" onMouseout="window.status='';return true;">
								<option value=""><xsl:value-of select="lang_no_user"></xsl:value-of></option>
								<xsl:apply-templates select="approval_from"></xsl:apply-templates>
							</select>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_default_vendor_category"></xsl:value-of>
						</td>
						<td align="left">
							<input type="hidden" name="values[old_default_vendor_category]" value="{value_old_default_vendor_category}"></input>
							<xsl:variable name="lang_default_vendor_category_statustext"><xsl:value-of select="lang_default_vendor_category_statustext"></xsl:value-of></xsl:variable>
							<select name="values[default_vendor_category]" class="forms" onMouseover="window.status='{$lang_default_vendor_category_statustext}'; return true;" onMouseout="window.status='';return true;">
								<option value=""><xsl:value-of select="lang_no_cat"></xsl:value-of></option>
								<xsl:apply-templates select="vendor_category"></xsl:apply-templates>
							</select>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_default_tts_category"></xsl:value-of>
						</td>
						<td align="left">
							<input type="hidden" name="values[old_default_tts_category]" value="{value_old_default_tts_category}"></input>
							<select name="values[default_tts_category]" class="forms" title="{lang_default_tts_category_statustext}">
								<option value=""><xsl:value-of select="lang_no_cat"></xsl:value-of></option>
								<xsl:apply-templates select="tts_category"></xsl:apply-templates>
							</select>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_account_select"></xsl:value-of>
						</td>
						<td align="left">
							<input type="hidden" name="values[old_assigntodefault]" value="{value_old_assigntodefault}"></input>
							<select name="values[assigntodefault]" class="forms">
								<option value=""><xsl:value-of select="lang_no_assigntodefault"></xsl:value-of></option>
								<xsl:apply-templates select="account_list_tts"></xsl:apply-templates>
							</select>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_group_select"></xsl:value-of>
						</td>
						<td align="left">
							<input type="hidden" name="values[old_groupdefault]" value="{value_old_groupdefault}"></input>
							<select name="values[groupdefault]" class="forms">
								<option value=""><xsl:value-of select="lang_no_groupdefault"></xsl:value-of></option>
								<xsl:apply-templates select="group_list_tts"></xsl:apply-templates>
							</select>
						</td>
					</tr>
					<tr height="50">
						<td>
							<xsl:variable name="lang_save"><xsl:value-of select="lang_save"></xsl:value-of></xsl:variable>
							<input type="submit" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_save_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>

				</form>
				<tr>
					<td>
						<xsl:variable name="done_action"><xsl:value-of select="done_action"></xsl:value-of></xsl:variable>
						<xsl:variable name="lang_done"><xsl:value-of select="lang_done"></xsl:value-of></xsl:variable>
						<form method="post" action="{$done_action}">
							<input type="submit" name="done" value="{$lang_done}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_done_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</form>
					</td>
				</tr>
			</table>
		</div>
	</xsl:template>

	<xsl:template match="approval_from">
		<xsl:variable name="user_id"><xsl:value-of select="user_id"></xsl:value-of></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$user_id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$user_id}"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="vendor_category">
		<xsl:variable name="cat_id"><xsl:value-of select="cat_id"></xsl:value-of></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected='selected'">
				<option value="{$cat_id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$cat_id}"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="tts_category">
		<xsl:choose>
			<xsl:when test="selected='selected'">
				<option value="{cat_id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{cat_id}"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="group_list_tts">
		<xsl:choose>
			<xsl:when test="selected='1'">
				<option value="{id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{id}"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="account_list_tts">
		<xsl:choose>
			<xsl:when test="selected='1'">
				<option value="{id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{id}"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
