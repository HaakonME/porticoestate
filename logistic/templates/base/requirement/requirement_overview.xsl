<xsl:template  match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="yui_phpgw_i18n"/>
	
	<div class="content-wrp">
		
		  <form action="" name="acl_form" id="acl_form" method="post">
				<div id="paging"></div>
	
				<div id="requirement-container"></div>

				<div id="allocation-container"></div>
			</form>

			<xsl:apply-templates select="datasource-definition"/>
	</div>
</xsl:template>

<xsl:template match="datasource-definition">
	<script>
	
	YAHOO.portico.columnDefs = [
				<xsl:for-each select="//datatable/field">
					{
						resizeable: true,
						key: "<xsl:value-of select="key"/>",
						<xsl:if test="label">
						label: "<xsl:value-of select="label"/>",
						</xsl:if>
						sortable: <xsl:value-of select="phpgw:conditional(not(sortable = 0), 'true', 'false')"/>,
						<xsl:if test="hidden">
						hidden: true,
						</xsl:if>
						<xsl:if test="formatter">
						formatter: <xsl:value-of select="formatter"/>,
						</xsl:if>
						<xsl:if test="editor">
						editor: <xsl:value-of select="editor"/>,
					    </xsl:if>
						className: "<xsl:value-of select="className"/>"
					}<xsl:value-of select="phpgw:conditional(not(position() = last()), ',', '')"/>
				</xsl:for-each>
			];
	
	
		YAHOO.portico.inlineTableHelper('requirement-container', url, colDefs, null, null);

		YAHOO.portico.inlineTableHelper('allocation-container', url, colDefs, null, null);
	
	

	</script>
</xsl:template>