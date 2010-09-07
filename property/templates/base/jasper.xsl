<!-- $Id$ -->

	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="edit">
				<xsl:apply-templates select="edit"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="user_input"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>


<!-- add / edit  -->
	<xsl:template match="edit" xmlns:php="http://php.net/xsl">
		<div class="yui-content">
		<xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
		<form ENCTYPE="multipart/form-data" name="form" method="post" action="{$form_action}">		
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
							<td valign="top">
								<xsl:value-of select="php:function('lang', 'id')" />
							</td>
							<td>
								<xsl:value-of select="value_id"/>
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>	

				<xsl:choose>
					<xsl:when test="value_file_name != ''">
						<tr>
							<td valign="top">
								<xsl:value-of select="php:function('lang', 'filename')" />
							</td>
							<td>
								<xsl:value-of select="value_file_name"/>
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>	

				<tr>
					<td valign="top">
						<xsl:value-of select="php:function('lang', 'file')" />
					</td>
					<td>
						<input type="file" size="50" name="file">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'upload file')" />
							</xsl:attribute>
						</input>
					</td>
				</tr>
				<tr>
					<td>
						<xsl:value-of select="php:function('lang', 'title')" />
					</td>
					<td>
						<input type="text" name="values[title]" value="{value_title}" size="60" >
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'title')" />
							</xsl:attribute>
						</input>			
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="php:function('lang', 'descr')" />
					</td>
					<td>
						<textarea cols="60" rows="10" name="values[descr]" wrap="virtual">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'descr')" />
							</xsl:attribute>
							<xsl:value-of select="value_descr"/>		
						</textarea>
					</td>
				</tr>
				<tr>
					<td>
						<xsl:value-of select="php:function('lang', 'application')" />
					</td>
					<td>
						<xsl:value-of select="value_app_translated"/>
						<input type="hidden" name="values[app]" value="{value_app}" >
						</input>			
					</td>
				</tr>

				<tr>
					<td>
						<xsl:value-of select="php:function('lang', 'location')" />
					</td>
					<td>
						<select name="values[location]">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'Select submodule')" />
							</xsl:attribute>
							<option value="">
								<xsl:value-of select="php:function('lang', 'No location')" />
							</option>
							<xsl:apply-templates select="location_list"/>
						</select>			
					</td>
				</tr>
				<tr>
					<td valign='top'>
						<xsl:value-of select="php:function('lang', 'format type')" />
					</td>
					<td>
						<table>
							<xsl:apply-templates select="format_type_list"/>
						</table>
					</td>
				</tr>

				<tr>
					<td class="th_text" valign="top">
						<xsl:value-of select="php:function('lang', 'details')" />
					</td>
					<td>
						<table width="100%" cellpadding="2" cellspacing="2" align="center">
							<!--  DATATABLE 0-->
							<td><div id="paging_0"></div><div id="datatable-container_0"></div> </td>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<xsl:value-of select="php:function('lang', 'input type')" />
					</td>
					<td>
						<select name="values[input_type]">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'input type')" />
							</xsl:attribute>
							<option value="">
								<xsl:value-of select="php:function('lang', 'input type')" />
							</option>
							<xsl:apply-templates select="input_type_list"/>
						</select>			
					</td>
				</tr>
				<tr>
					<td>
						<xsl:value-of select="php:function('lang', 'input name')" />
					</td>
					<td>
						<input type="text" name="values[input_name]" value="{value_input_name}" size="12" >
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'input name')" />
							</xsl:attribute>
						</input>			
					</td>
				</tr>
				<tr>
					<td>
						<xsl:value-of select="php:function('lang', 'is id')" />
					</td>
					<td>
						<input type="checkbox" name="values[is_id]" value="1">
						</input>
					</td>
				</tr>

				<tr>
					<td>
						<xsl:value-of select="php:function('lang', 'private')" />
					</td>
					<td>
						<input type="checkbox" name="values[access]" value="True">
							<xsl:if test="value_access = 'private'">
								<xsl:attribute name="checked">
									checked
								</xsl:attribute>
							</xsl:if>
						</input>
					</td>
				</tr>

			</table>
			<table cellpadding="2" cellspacing="2" width="50%" align="center">
				<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
				<xsl:variable name="lang_apply"><xsl:value-of select="php:function('lang', 'apply')" /></xsl:variable>
				<xsl:variable name="lang_cancel"><xsl:value-of select="php:function('lang', 'cancel')" /></xsl:variable>
				<tr height="50">
					<td>
						<input type="submit" name="values[save]" value="{$lang_save}">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'save')" />
							</xsl:attribute>
						</input>
					</td>
					<td>
						<input type="submit" name="values[apply]" value="{$lang_apply}">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'apply')" />
							</xsl:attribute>
						</input>
					</td>
					<td>
						<input type="submit" name="values[cancel]" value="{$lang_cancel}">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'cancel')" />
							</xsl:attribute>
						</input>
					</td>
				</tr>
			</table>
		</form>
		</div>
		<!--  DATATABLE DEFINITIONS-->
		<script>
			var property_js = <xsl:value-of select="property_js" />;
			var base_java_url = <xsl:value-of select="base_java_url" />;
			var datatable = new Array();
			var myColumnDefs = new Array();
			var myButtons = new Array();
		    var td_count = <xsl:value-of select="td_count" />;

			<xsl:for-each select="datatable">
				datatable[<xsl:value-of select="name"/>] = [
				{
					values			:	<xsl:value-of select="values"/>,
					total_records	: 	<xsl:value-of select="total_records"/>,
					is_paginator	:  	<xsl:value-of select="is_paginator"/>,
			<!--		permission		:	<xsl:value-of select="permission"/>, -->
					footer			:	<xsl:value-of select="footer"/>
				}
				]
			</xsl:for-each>
			<xsl:for-each select="myColumnDefs">
				myColumnDefs[<xsl:value-of select="name"/>] = <xsl:value-of select="values"/>
			</xsl:for-each>
			<xsl:for-each select="myButtons">
				myButtons[<xsl:value-of select="name"/>] = <xsl:value-of select="values"/>
			</xsl:for-each>
		</script>
	</xsl:template>

	<xsl:template match="format_type_list">
		<tr>
			<td>
				<xsl:value-of select="name"/>
			</td>
			<td>
				<input type="checkbox" name="values[formats][]" value="{id}">
					<xsl:if test="selected != 0">
						<xsl:attribute name="checked">
							checked
						</xsl:attribute>
					</xsl:if>
				</input>
			</td>
		</tr>
	</xsl:template>

	<xsl:template match="input_type_list">
		<option value="{id}">
			<xsl:if test="selected != 0">
				<xsl:attribute name="selected" value="selected" />
			</xsl:if>
			<xsl:value-of disable-output-escaping="yes" select="name"/>
		</option>
	</xsl:template>


	<xsl:template match="location_list">
		<option value="{id}">
			<xsl:if test="selected != 0">
				<xsl:attribute name="selected" value="selected" />
			</xsl:if>
			<xsl:value-of disable-output-escaping="yes" select="name"/>
		</option>
	</xsl:template>


<!-- user_input  -->
	<xsl:template match="user_input" xmlns:php="http://php.net/xsl">
		<div class="yui-content">
		<xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
		<form  name="form" method="post" action="{$form_action}">		
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

			</table>
			<table cellpadding="2" cellspacing="2" width="50%" align="center">
				<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
				<xsl:variable name="lang_apply"><xsl:value-of select="php:function('lang', 'apply')" /></xsl:variable>
				<xsl:variable name="lang_cancel"><xsl:value-of select="php:function('lang', 'cancel')" /></xsl:variable>
				<tr height="50">
					<td>
						<input type="submit" name="values[save]" value="{$lang_save}">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'save')" />
							</xsl:attribute>
						</input>
					</td>
					<td>
						<input type="submit" name="values[apply]" value="{$lang_apply}">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'apply')" />
							</xsl:attribute>
						</input>
					</td>
					<td>
						<input type="submit" name="values[cancel]" value="{$lang_cancel}">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'cancel')" />
							</xsl:attribute>
						</input>
					</td>
				</tr>
			</table>
		</form>
		</div>
	</xsl:template>
