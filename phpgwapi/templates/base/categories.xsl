<!-- $Id$ -->

	<xsl:template name="categories">
		<xsl:choose>
			<xsl:when test="cat_filter != ''">
				<xsl:apply-templates select="cat_filter"/>
			</xsl:when>
			<xsl:when test="cat_select != ''">
				<xsl:apply-templates select="cat_select"/>
			</xsl:when>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="cat_filter">
		<xsl:variable name="select_url"><xsl:value-of select="select_url"/></xsl:variable>
		<xsl:variable name="select_name"><xsl:value-of select="select_name"/></xsl:variable>
		<xsl:variable name="lang_submit"><xsl:value-of select="lang_submit"/></xsl:variable>
		<form method="post" action="{$select_url}">
			<select name="{$select_name}" onChange="this.form.submit();" onMouseout="window.status='';return true;">
				<xsl:attribute name="onMouseover">
					<xsl:text>window.status='</xsl:text>
						<xsl:value-of select="lang_cat_statustext"/>
					<xsl:text>'; return true;</xsl:text>
				</xsl:attribute>
				<option value="none"><xsl:value-of select="lang_no_cat"/></option>
					<xsl:apply-templates select="cat_list"/>
			</select>
			<noscript>
				<xsl:text> </xsl:text>
				<input type="submit" name="submit" value="{$lang_submit}"/>
			</noscript>
		</form>
	</xsl:template>

	<xsl:template match="cat_select">
	<xsl:variable name="lang_cat_statustext"><xsl:value-of select="lang_cat_statustext"/></xsl:variable>
	<xsl:variable name="select_name"><xsl:value-of select="select_name"/></xsl:variable>
		<select name="{$select_name}" class="forms" onMouseover="window.status='{$lang_cat_statustext}'; return true;" onMouseout="window.status='';return true;">
			<option value="0"><xsl:value-of select="lang_no_cat"/></option>
				<xsl:apply-templates select="cat_list"/>
		</select>
	</xsl:template>

	<xsl:template match="cat_list">
	<xsl:variable name="cat_id"><xsl:value-of select="cat_id"/></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected != ''">
				<option value="{$cat_id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$cat_id}"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
