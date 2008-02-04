<!-- $Id: no_access.xsl,v 1.1 2007/01/03 17:37:49 sigurdne Exp $ -->

	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="no_access">
				<xsl:apply-templates select="no_access"/>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
	
	<xsl:template match="no_access">
		
		<xsl:choose>
			<xsl:when test="links !=''">
				<xsl:apply-templates select="menu"/> 
			</xsl:when>
		</xsl:choose>
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
		</table>
	</xsl:template>
