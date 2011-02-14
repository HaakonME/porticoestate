<!-- $Id$ -->

	<xsl:template name="project_group_form">
		<xsl:apply-templates select="project_group_data"/>
	</xsl:template>

	<xsl:template match="project_group_data">
		<script type="text/javascript">
			self.name="first_Window";
			function project_group_lookup()
			{
				Window1=window.open('<xsl:value-of select="project_group_url"/>',"Search","left=50,top=100,width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");
			}		
		</script>

		<tr>
			<td align="left" valign="top">
				<a href="javascript:project_group_lookup()" title="{lang_select_project_group_help}" ><xsl:value-of select="lang_project_group"/>
				</a>
			</td>
			<td align="left" >
				<input size="9" type="text" name="project_group" value="{value_project_group}">
					<xsl:attribute name="title">
						<xsl:value-of select="lang_select_project_group_help"/>
					</xsl:attribute>
				</input>
				<input  size="30" type="text" name="project_group_descr" value="{value_project_group_descr}"  onClick="project_group_lookup();" readonly="readonly"> 
					<xsl:attribute name="title">
						<xsl:value-of select="lang_select_project_group_help"/>
					</xsl:attribute>
				</input>
			</td>
		</tr>

	</xsl:template>
