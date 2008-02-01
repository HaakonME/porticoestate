<!-- $Id: filter_select.xsl,v 1.1 2005/01/17 10:03:18 sigurdne Exp $ -->

	<xsl:template name="filter_select">
	<xsl:variable name="lang_filter_statustext"><xsl:value-of select="lang_filter_statustext"/></xsl:variable>
	<xsl:variable name="filter_name"><xsl:value-of select="filter_name"/></xsl:variable>
		<select name="{$filter_name}" class="forms" onMouseover="window.status='{$lang_filter_statustext}'; return true;" onMouseout="window.status='';return true;">
			<option value=""><xsl:value-of select="lang_show_all"/></option>
				<xsl:apply-templates select="filter_list"/>
		</select>
	</xsl:template>

	<xsl:template match="filter_list">
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
