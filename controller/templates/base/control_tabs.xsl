<!-- separate tabs and  inline tables-->

<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div class="yui-navset yui-navset-top" id="control_tabview">
		<xsl:value-of disable-output-escaping="yes" select="tabs" />
		<div class="yui-content">
			<div id="details">
				<xsl:call-template name="control" />
			</div>
			<div id="control_groups">
				<xsl:call-template name="control_groups" />
			</div>
			<div id="control_items">
				<xsl:call-template name="control_items" />
			</div>
		</div>
	</div>
	<script type="text/javascript">
		var resource_id = <xsl:value-of select="resource/id"/>;
		var lang = <xsl:value-of select="php:function('js_lang', 'Name', 'Category', 'Actions', 'Edit', 'Delete', 'Account', 'Role')"/>;
	</script>
</xsl:template>
