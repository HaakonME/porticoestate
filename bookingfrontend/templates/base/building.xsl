<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="yui_booking_i18n"/>
	
	<div id="content">
		<ul class="pathway">
			<li><a href="index.php?menuaction=bookingfrontend.uisearch.index"><xsl:value-of select="php:function('lang', 'Home')" /></a></li>
			<li>
				<a href="{resource/building_link}">
					<xsl:value-of select="building/name"/>
				</a>
			</li>
		</ul>

		<xsl:for-each select="building">	

			<xsl:if test="deactivate_calendar=0">
			<div>
	        	<button onclick="window.location.href='{schedule_link}'"><xsl:value-of select="php:function('lang', 'Building schedule')" /></button>
- 				Søk ledig tid / gå til booking
			</div>
			</xsl:if>

			<dl class="proplist-col main">
				<xsl:if test="normalize-space(description)">
					<dl class="proplist description">
						<h3><xsl:value-of select="php:function('lang', 'Description')" /></h3>
						<dd><xsl:value-of select="description" disable-output-escaping="yes"/></dd>
					</dl>
				</xsl:if>
				
				<xsl:if test="normalize-space(homepage) or normalize-space(email) or normalize-space(phone) or normalize-space(street)">
					<h3><xsl:value-of select="php:function('lang', 'Contact information')" /></h3>
			<xsl:if test="deactivate_sendmessage=0">

			<div>
	        	<button onclick="window.location.href='{message_link}'"><xsl:value-of select="php:function('lang', 'Send message')" /></button>
- 				Meldig til saksbehandler for sted/utstyr
			</div>
			</xsl:if>


					<dl class="contactinfo">
						<xsl:if test="homepage and normalize-space(homepage)">
							<dt><xsl:value-of select="php:function('lang', 'Homepage')" /></dt>
							<dd><a href="{homepage}"><xsl:value-of select="homepage"/></a></dd>
						</xsl:if>
					
						<xsl:if test="email and normalize-space(email)">
							<dt><xsl:value-of select="php:function('lang', 'Email')" /></dt>
							<dd><a href='mailto:{email}'><xsl:value-of select="email"/></a></dd>
						</xsl:if>
					
						<xsl:if test="phone and normalize-space(phone)">
							<dt><xsl:value-of select="php:function('lang', 'Telephone')" /></dt>
							<dd><xsl:value-of select="phone"/></dd>
						</xsl:if>
					
						<xsl:if test="street and normalize-space(street)">
							<dt><xsl:value-of select="php:function('lang', 'Address')" /></dt>
							<dd>
								<xsl:value-of select="street"/><br/>
								<xsl:value-of select="zip_code"/><span>&nbsp; </span>
								<xsl:value-of select="city"/><br/>
								<xsl:value-of select="district"/>
							</dd>
						</xsl:if>
						<xsl:if test="map_url">
							<dt><label for="field_map_url"><xsl:value-of select="php:function('lang', 'Map url')"/></label></dt>
							<dd><a target="_blank">
			                <xsl:attribute name="href"><xsl:value-of select="map_url"/></xsl:attribute>
			                <xsl:value-of select="php:function('lang', 'Click here')" /></a>
							</dd>
						</xsl:if>
						<xsl:if test="weather_url">
							<dt><label for="field_weather_url"><xsl:value-of select="php:function('lang', 'Weather url')"/></label></dt>
							<dd><a target="_blank">
			                <xsl:attribute name="href"><xsl:value-of select="weather_url"/></xsl:attribute>
			                <xsl:value-of select="php:function('lang', 'Click here')" /></a>
							</dd>
						</xsl:if>

					</dl>
				</xsl:if>
				
				<h3><xsl:value-of select="php:function('lang', 'Bookable resources')" /></h3>
				<div id="resources_container"/>

				<xsl:if test="layout='bergen'">
					<h3><xsl:value-of select="php:function('lang', 'Building users')" /></h3>
					<div id="building_users_container"/>
				</xsl:if>

				<h3><xsl:value-of select="php:function('lang', 'Documents')" /></h3>
				<div id="documents_container"/>
			</dl>
			<dl class="proplist-col images">	
			<xsl:if test="not(campsites='')">				
				<dt><label for="field_campsites"><xsl:value-of select="php:function('lang', 'Campsites')"/></label></dt>
				<dd><xsl:value-of select="campsites"/></dd>
			</xsl:if>
			<xsl:if test="not(bedspaces='')">				
				<dt><label for="field_bedspaces"><xsl:value-of select="php:function('lang', 'Bedspaces')"/></label></dt>
				<dd><xsl:value-of select="bedspaces"/></dd>
			</xsl:if>
			<xsl:if test="not(heating='')">				
				<dt><label for="field_heating"><xsl:value-of select="php:function('lang', 'Heating')"/></label></dt>
				<dd><xsl:value-of select="heating"/></dd>
			</xsl:if>
			<xsl:if test="not(kitchen='')">				
				<dt><label for='field_kitchen'><xsl:value-of select="php:function('lang', 'Kitchen')"/></label></dt>
				<dd><xsl:value-of select="kitchen"/></dd>
			</xsl:if>
			<xsl:if test="not(water='')">				
				<dt><label for="field_water"><xsl:value-of select="php:function('lang', 'Water')"/></label></dt>
				<dd><xsl:value-of select="water"/></dd>
			</xsl:if>
			<xsl:if test="not(location='')">				
				<dt><label for="field_location"><xsl:value-of select="php:function('lang', 'Locality')"/></label></dt>
				<dd><xsl:value-of select="location"/></dd>
			</xsl:if>
			<xsl:if test="not(communication='')">				
				<dt><label for='field_communication'><xsl:value-of select="php:function('lang', 'Communication')"/></label></dt>
				<dd><xsl:value-of select="communication"/></dd>
			</xsl:if>
			<xsl:if test="not(usage_time='')">				
				<dt><label for='field_usage_time'><xsl:value-of select="php:function('lang', 'Usage time')"/></label></dt>
				<dd><xsl:value-of select="usage_time"/></dd>
			</xsl:if>
			<xsl:if test="not(swiming='')">				
				<dt><label for='field_swiming'><xsl:value-of select="php:function('lang', 'Swiming')"/></label></dt>
				<dd><xsl:value-of select="swiming"/></dd>
			</xsl:if>
			<xsl:if test="not(sanitation_facilities='')">				
				<dt><label for='field_sanitation_facilities'><xsl:value-of select="php:function('lang', 'Sanitation facilities')"/></label></dt>
				<dd><xsl:value-of select="sanitation_facilities"/></dd>
			</xsl:if>
			<xsl:if test="not(animals='')">				
				<dt><label for='field_animals'><xsl:value-of select="php:function('lang', 'Animals')"/></label></dt>
				<dd><xsl:value-of select="animals"/></dd>
			</xsl:if>
			<xsl:if test="not(internett_phone='')">				
				<dt><label for='field_internett_phone'><xsl:value-of select="php:function('lang', 'Internett/phone')"/></label></dt>
				<dd><xsl:value-of select="internett_phone"/></dd>
			</xsl:if>
			<xsl:if test="not(handicap='')">				
				<dt><label for='field_handicap'><xsl:value-of select="php:function('lang', 'Handicap')"/></label></dt>
				<dd><xsl:value-of select="handicap"/></dd>
			</xsl:if>
			<div id="images_container"></div>

				

			</dl>
			<script type="text/javascript">
				var building_id = <xsl:value-of select="id"/>;
				var lang = <xsl:value-of select="php:function('js_lang', 'Name', 'category', 'Activity', 'Resource Type','Internal Cost','External Cost','Cost Type')"/>;
				<![CDATA[
				
				YAHOO.util.Event.addListener(window, "load", function() {
				var url = 'index.php?menuaction=bookingfrontend.uiresource.index_json&sort=name&filter_building_id=' + building_id + '&phpgw_return_as=json&';
				var colDefs = [{key: 'name', label: lang['Name'], formatter: YAHOO.booking.formatLink}, {key: 'type', label: lang['Resource Type']}, {key: 'activity_name', label: lang['Activity']},{key: 'internal_cost', label: lang['Internal Cost']},{key: 'external_cost', label: lang['External Cost']},{key: 'cost_type', label: lang['Cost Type']}];
				YAHOO.booking.inlineTableHelper('resources_container', url, colDefs);
				});
				
				var url = 'index.php?menuaction=bookingfrontend.uidocument_building.index&sort=name&no_images=1&filter_owner_id=' + building_id + '&phpgw_return_as=json&';
				var colDefs = [{key: 'description', label: lang['Name'], formatter: YAHOO.booking.formatLink}];
				YAHOO.booking.inlineTableHelper('documents_container', url, colDefs);
				
				var url = 'index.php?menuaction=bookingfrontend.uidocument_building.index_images&sort=name&filter_owner_id=' + building_id + '&phpgw_return_as=json&';
				YAHOO.booking.inlineImages('images_container', url);
				
				var url = 'index.php?menuaction=bookingfrontend.uiorganization.building_users&sort=name&building_id=' + building_id + '&phpgw_return_as=json&';
				var colDefs = [{key: 'name', label: lang['Name'], formatter: YAHOO.booking.formatLink}, {key: 'activity_name', label: lang['Activity']}];
				YAHOO.booking.inlineTableHelper('building_users_container', url, colDefs);
				]]>
			</script>
		</xsl:for-each>
	</div>
</xsl:template>

