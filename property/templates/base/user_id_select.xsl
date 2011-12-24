<!-- $Id$ -->

	<xsl:template name="user_id_select">
		<xsl:variable name="lang_user_statustext"><xsl:value-of select="lang_user_statustext"></xsl:value-of></xsl:variable>
		<xsl:variable name="select_user_name"><xsl:value-of select="select_user_name"></xsl:value-of></xsl:variable>
		<select name="{$select_user_name}" class="forms" title="{$lang_user_statustext}" onMouseover="window.status='{$lang_user_statustext}'; return true;" onMouseout="window.status='';return true;">
			<option value=""><xsl:value-of select="lang_no_user"></xsl:value-of></option>
			<xsl:apply-templates select="user_list"></xsl:apply-templates>
		</select>
	</xsl:template>

	<xsl:template match="user_list">
		<xsl:variable name="user_id"><xsl:value-of select="user_id"></xsl:value-of></xsl:variable>
		<xsl:variable name="id"><xsl:value-of select="id"></xsl:value-of></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$user_id}{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$user_id}{$id}"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
