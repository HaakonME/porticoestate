<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div id="content">
		<ul class="pathway">
			<li>
				<xsl:value-of select="php:function('lang', 'Buildings')" />
			</li>
			<li>
				<a href="{resource/building_link}">
					<xsl:value-of select="resource/building_name"/>
				</a>
			</li>
            <li>
                <a href="{resource/resources_link}">
                    <xsl:value-of select="php:function('lang', 'Resources')" />
                </a>
            </li>
			<li>
                <xsl:value-of select="resource/name"/>
			</li>
		</ul>

		<dl class="proplist">
			<dt><xsl:value-of select="php:function('lang', 'Resource Name')" /></dt>
			<dd><xsl:value-of select="resource/name"/></dd>
			
			<dt><xsl:value-of select="php:function('lang', 'Building')" /></dt>
			<dd><xsl:value-of select="resource/building_name"/></dd>
			
			<xsl:if test="resource/description and normalize-space(resource/description)">
				<dt><xsl:value-of select="php:function('lang', 'Description')" /></dt>
				<dd><xsl:value-of select="resource/description"/></dd>
			</xsl:if>
			
			<xsl:if test="resource/activity_name and normalize-space(resource/activity_name)">
				<dt><xsl:value-of select="php:function('lang', 'Activity')" /></dt>
				<dd><xsl:value-of select="resource/activity_name"/></dd>
			</xsl:if>
		</dl>

        <button onclick="window.location.href='{resource/schedule_link}'">
            <xsl:value-of select="php:function('lang', 'Resource schedule')" />
        </button>

		<h3><xsl:value-of select="php:function('lang', 'Documents')" /></h3>
		<div id="documents_container"/>
		
		<div id="images_container">
			<h3><xsl:value-of select="php:function('lang', 'Images')" /></h3>
		</div>
	</div>

	<script type="text/javascript">
		var resource_id = <xsl:value-of select="resource/id"/>;
		var lang = <xsl:value-of select="php:function('js_lang', 'Equipment Name', 'Document Name', 'category', 'Activity')"/>;
<![CDATA[
	YAHOO.util.Event.addListener(window, "load", function() {

	var url = 'index.php?menuaction=bookingfrontend.uidocument_resource.index&sort=name&filter_owner_id=' + resource_id + '&phpgw_return_as=json&';
	var colDefs = [{key: 'name', label: lang['Document Name'], formatter: YAHOO.booking.formatLink}, {key: 'category', label: lang['category']}];
	YAHOO.booking.inlineTableHelper('documents_container', url, colDefs);
	
	var url = 'index.php?menuaction=bookingfrontend.uidocument_resource.index_images&sort=name&filter_owner_id=' + resource_id + '&phpgw_return_as=json&';
	YAHOO.booking.inlineImages('images_container', url);
});
]]>
	</script>

</xsl:template>
