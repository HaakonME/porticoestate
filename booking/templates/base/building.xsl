<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">
        <ul class="pathway">
       <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="building/buildings_link"/></xsl:attribute>
                    <xsl:value-of select="php:function('lang', 'Buildings')" />
				</a>
            </li>
            <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="building/building_link"/></xsl:attribute>
                    <xsl:value-of select="building/name"/>
                </a>
            </li>
        </ul>

        <xsl:call-template name="msgbox"/>
		<xsl:call-template name="yui_booking_i18n"/>

        <h4><xsl:value-of select="php:function('lang', 'Description')" /></h4>
        <div class="description"><xsl:value-of select="building/description" disable-output-escaping="yes"/></div>

        <dl class="proplist-col">
			<dt><xsl:value-of select="php:function('lang', 'Street')" /></dt>
            <dd><xsl:value-of select="building/street"/></dd>
			
			<dt><xsl:value-of select="php:function('lang', 'Zip code')" /></dt>
            <dd><xsl:value-of select="building/zip_code"/></dd>

			<dt><xsl:value-of select="php:function('lang', 'Postal City')" /></dt>
            <dd><xsl:value-of select="building/city"/></dd>
			
			<dt><xsl:value-of select="php:function('lang', 'District')" /></dt>
            <dd><xsl:value-of select="building/district"/></dd>

			<xsl:if test="building/location_code">
				<dt><xsl:value-of select="php:function('lang', 'Location Code')" /></dt>
	            <dd><a href="{building/location_link}"><xsl:value-of select="building/location_code"/></a></dd>
			</xsl:if>
        </dl>
        <dl class="proplist-col">
            <dt>
                    <xsl:value-of select="php:function('lang', 'Homepage')" /></dt>
            <dd><a>
                <xsl:attribute name="href"><xsl:value-of select="building/homepage"/></xsl:attribute>
                <xsl:value-of select="building/homepage"/></a>
            </dd>
            <dt>
                    <xsl:value-of select="php:function('lang', 'Email')" /></dt>
            <dd><a>
                <xsl:attribute name="href">mailto:<xsl:value-of select="building/email"/></xsl:attribute>
                <xsl:value-of select="building/email"/></a>
            </dd>

			<dt><xsl:value-of select="php:function('lang', 'Telephone')" /></dt>
            <dd><xsl:value-of select="building/phone"/></dd>
        </dl>

		<div class="clr"/>
		<dl class="form-col">
			<xsl:if test="not(building/campsites='')">				
				<dt><label for="field_campsites"><xsl:value-of select="php:function('lang', 'Campsites')"/></label></dt>
				<dd><xsl:value-of select="building/campsites"/></dd>
			</xsl:if>
			<xsl:if test="not(building/bedspaces='')">				
				<dt><label for="field_bedspaces"><xsl:value-of select="php:function('lang', 'Bedspaces')"/></label></dt>
				<dd><xsl:value-of select="building/bedspaces"/></dd>
			</xsl:if>
			<xsl:if test="not(building/heating='')">				
				<dt><label for="field_heating"><xsl:value-of select="php:function('lang', 'Heating')"/></label></dt>
				<dd><xsl:value-of select="building/heating"/></dd>
			</xsl:if>
			<xsl:if test="not(building/kitchen='')">				
				<dt><label for='field_kitchen'><xsl:value-of select="php:function('lang', 'Kitchen')"/></label></dt>
				<dd><xsl:value-of select="building/kitchen"/></dd>
			</xsl:if>
			<xsl:if test="not(building/swiming='')">				
				<dt><label for='field_swiming'><xsl:value-of select="php:function('lang', 'Swiming')"/></label></dt>
				<dd><xsl:value-of select="building/swiming"/></dd>
			</xsl:if>
			<xsl:if test="not(building/sanitation_facilities='')">				
				<dt><label for='field_sanitation_facilities'><xsl:value-of select="php:function('lang', 'Sanitation facilities')"/></label></dt>
				<dd><xsl:value-of select="building/sanitation_facilities"/></dd>
			</xsl:if>
			<xsl:if test="not(building/handicap='')">				
				<dt><label for='field_handicap'><xsl:value-of select="php:function('lang', 'Handicap')"/></label></dt>
				<dd><xsl:value-of select="building/handicap"/></dd>
			</xsl:if>
			</dl>
			<dl class="form-col">
			<xsl:if test="not(building/water='')">				
				<dt><label for="field_water"><xsl:value-of select="php:function('lang', 'Water')"/></label></dt>
				<dd><xsl:value-of select="building/water"/></dd>
			</xsl:if>
			<xsl:if test="not(building/location='')">				
				<dt><label for="field_location"><xsl:value-of select="php:function('lang', 'Locality')"/></label></dt>
				<dd><xsl:value-of select="building/location"/></dd>
			</xsl:if>
			<xsl:if test="not(building/communication='')">				
				<dt><label for='field_communication'><xsl:value-of select="php:function('lang', 'Communication')"/></label></dt>
				<dd><xsl:value-of select="building/communication"/></dd>
			</xsl:if>
			<xsl:if test="not(building/usage_time='')">				
				<dt><label for='field_usage_time'><xsl:value-of select="php:function('lang', 'Usage time')"/></label></dt>
				<dd><xsl:value-of select="building/usage_time"/></dd>
			</xsl:if>
			<xsl:if test="not(building/internett_phone='')">				
				<dt><label for='field_internett_phone'><xsl:value-of select="php:function('lang', 'Internett/phone')"/></label></dt>
				<dd><xsl:value-of select="building/internett_phone"/></dd>
			</xsl:if>
			<xsl:if test="not(building/animals='')">				
				<dt><label for='field_animals'><xsl:value-of select="php:function('lang', 'Animals')"/></label></dt>
				<dd><xsl:value-of select="building/animals"/></dd>
			</xsl:if>
		</dl>
		<div class="clr"/>
			<xsl:if test="not (building/map_url='')">
			<dl class="form-col">
				<dt><label for="field_map_url"><xsl:value-of select="php:function('lang', 'Map url')"/></label></dt>
				<dd><a target="_blank">
                <xsl:attribute name="href"><xsl:value-of select="building/map_url"/></xsl:attribute>
                <xsl:value-of select="php:function('lang', 'Click here')" /></a>
				</dd>
			</dl>
			</xsl:if>
			<xsl:if test="not (building/weather_url='')">
			<dl class="form-col">
				<dt><label for="field_weather_url"><xsl:value-of select="php:function('lang', 'Weather url')"/></label></dt>
				<dd><a target="_blank">
                <xsl:attribute name="href"><xsl:value-of select="building/weather_url"/></xsl:attribute>
                <xsl:value-of select="php:function('lang', 'Click here')" /></a>
				</dd>
			</dl>
			</xsl:if>
		<div class='clr'/>
		<dl class="form-col">
			<xsl:if test="building/internal_cost!=''">				
				<dt><label for="field_internal_cost"><xsl:value-of select="php:function('lang', 'Internal cost')"/></label></dt>
				<dd><xsl:value-of select="building/internal_cost"/></dd>
			</xsl:if>
		</dl>
		<dl class="form-col">
			<xsl:if test="building/external_cost!=''">				
				<dt><label for="field_external_cost"><xsl:value-of select="php:function('lang', 'External cost')"/></label></dt>
				<dd><xsl:value-of select="building/external_cost"/></dd>
			</xsl:if>
		</dl>
		<dl class="form-col">
			<xsl:if test="building/cost_type!=''">				
				<dt><label for="field_cost_type"><xsl:value-of select="php:function('lang', 'Cost type')"/></label></dt>
	            <dd><xsl:value-of select="php:function('lang', string(building/cost_type))"/></dd>
			</xsl:if>
		</dl>
		<div class='clr'/>

        <div class="form-buttons">
			<xsl:if test="building/permission/write">
				<button>
		            <xsl:attribute name="onclick">window.location.href="<xsl:value-of select="building/edit_link"/>"</xsl:attribute>
	          		<xsl:value-of select="php:function('lang', 'Edit')" />
		        </button>
			</xsl:if>
	        <button>
	            <xsl:attribute name="onclick">window.location.href="<xsl:value-of select="building/schedule_link"/>"</xsl:attribute>
	            <xsl:value-of select="php:function('lang', 'Building schedule')" />
	        </button>
    	</div>

        <h4><xsl:value-of select="php:function('lang', 'Bookable resources')" /></h4>
        <div id="resources_container"/>

		<h4><xsl:value-of select="php:function('lang', 'Documents')" /></h4>
        <div id="documents_container"/>
		<a class='button'>
			<xsl:attribute name="href"><xsl:value-of select="building/add_document_link"/></xsl:attribute>
			<xsl:if test="building/permission/write">
				<xsl:value-of select="php:function('lang', 'Add Document')" />
			</xsl:if>
		</a>
		
		<h4><xsl:value-of select="php:function('lang', 'Permissions')" /></h4>
        <div id="permissions_container"/>
    </div>

<script type="text/javascript">
var building_id = <xsl:value-of select="building/id"/>;
	var lang = <xsl:value-of select="php:function('js_lang', 'Name', 'Category', 'Actions', 'Account', 'Role', 'Edit', 'Delete', 'Resource Type', 'Sort order','Internal Cost','External Cost','Cost Type')"/>;
    <![CDATA[
YAHOO.util.Event.addListener(window, "load", function() {
    var url = 'index.php?menuaction=booking.uiresource.index&sort=name&filter_building_id=' + building_id + '&phpgw_return_as=json&';
    var colDefs = [{key: 'sort', label: lang['Sort order']},{key: 'name', label: lang['Name'], formatter: YAHOO.booking.formatLink}, {key: 'type', label: lang['Resource Type']},{key: 'internal_cost', label: lang['Internal Cost']},{key: 'external_cost', label: lang['External Cost']},{key: 'cost_type', label: lang['Cost Type']}];
    YAHOO.booking.inlineTableHelper('resources_container', url, colDefs);

	var url = 'index.php?menuaction=booking.uidocument_building.index&sort=name&filter_owner_id=' + building_id + '&phpgw_return_as=json&';
	var colDefs = [{key: 'name', label: lang['Name'], formatter: YAHOO.booking.formatLink}, {key: 'category', label: lang['Category']}, {key: 'actions', label: lang['Actions'], formatter: YAHOO.booking.formatGenericLink(lang['Edit'], lang['Delete'])}];
	YAHOO.booking.inlineTableHelper('documents_container', url, colDefs);
	
	var url = 'index.php?menuaction=booking.uipermission_building.index&sort=name&filter_object_id=' + building_id + '&phpgw_return_as=json&';
]]>
	var colDefs = [{key: 'subject_name', label: lang['Account']}, {key: 'role', label: lang['Role']}, {key: 'actions', label: lang['Actions'], formatter: YAHOO.booking.formatGenericLink(lang['Edit'], lang['Delete'])}];
    <![CDATA[
	YAHOO.booking.inlineTableHelper('permissions_container', url, colDefs);
});

]]>
</script>

</xsl:template>
