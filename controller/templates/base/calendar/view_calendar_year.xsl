<!-- $Id$ -->
<xsl:template match="data"  xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format">d/m-Y</xsl:variable>

<div id="main_content">

	<div id="control_plan">
		<div class="top">
			<h1>Kontrollplan for bygg/eiendom: <xsl:value-of select="current_location/loc1_name"/></h1>
			<h3>Kalenderoversikt for <span class="year"><xsl:value-of select="current_year"/></span></h3>
			
			<!-- =====================  SELECT MY LOCATIONS  ================= -->
			<xsl:call-template name="select_my_locations" />
			
		</div>
		<div class="middle">
					
			<!-- =====================  COLOR ICON MAP  ================= -->
			<xsl:call-template name="icon_color_map" />
			
			<!-- =====================  CALENDAR NAVIGATION  ================= -->
			<div id="calNav">
				<a class="showPrev">
					<xsl:attribute name="href">
						<xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_for_year</xsl:text>
						<xsl:text>&amp;year=</xsl:text>
						<xsl:value-of select="current_year - 1"/>
						<xsl:text>&amp;location_code=</xsl:text>
						<xsl:value-of select="current_location/location_code"/>
					</xsl:attribute>
					<img height="17" src="controller/images/left_arrow_simple_light_blue.png" />
					<xsl:value-of select="current_year - 1"/>
				</a>
				<span class="current">
						<xsl:value-of select="current_year"/>
				</span>
				<a class="showNext">
						<xsl:attribute name="href">
						<xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_for_year</xsl:text>
						<xsl:text>&amp;year=</xsl:text>
						<xsl:value-of select="current_year + 1"/>
						<xsl:text>&amp;location_code=</xsl:text>
						<xsl:value-of select="current_location/location_code"/>
					</xsl:attribute>
					<xsl:value-of select="current_year + 1"/>
					<img height="17" src="controller/images/right_arrow_simple_light_blue.png" />
				</a>
			</div>
			
		</div>
		 
		<div id="cal_wrp">
		<table id="calendar">
				<tr class="heading">
						<th class="title"><span>Tittel</span></th>
						<th class="assigned"><span>Tildelt</span></th>
						<th class="frequency"><span>Frekvens</span></th>
					<xsl:for-each select="heading_array">
						<th>
							<a>
								<xsl:attribute name="href">
									<xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_for_month</xsl:text>
									<xsl:text>&amp;year=</xsl:text>
									<xsl:value-of select="//current_year"/>
									<xsl:text>&amp;location_code=</xsl:text>
									<xsl:value-of select="current_location/location_code"/>
									<xsl:text>&amp;month=</xsl:text>
									<xsl:number/>
								</xsl:attribute>
								
								<xsl:variable name="month_str">short_month <xsl:number/> capitalized</xsl:variable>
								<xsl:value-of select="php:function('lang', $month_str)" />
							</a>				
						</th>
					</xsl:for-each>
				</tr>
			
			<xsl:choose>
				<xsl:when test="controls_calendar_array/child::node()">
				
			  	<xsl:for-each select="controls_calendar_array">
			  		<xsl:variable name="control_id"><xsl:value-of select="control/id"/></xsl:variable>
			  	
			  		<tr>				
						<xsl:choose>
					        <xsl:when test="(position() mod 2) != 1">
					            <xsl:attribute name="class">odd</xsl:attribute>
					        </xsl:when>
					        <xsl:otherwise>
					            <xsl:attribute name="class">even</xsl:attribute>
					        </xsl:otherwise>
					    </xsl:choose>
							<td class="title">
				      			<span><xsl:value-of select="control/title"/></span>
							</td>
							<td class="assigned">
				      			<span><xsl:value-of select="control/responsibility_name"/></span>
							</td>
							<td class="frequency">
				      			<span>
					      			<xsl:choose>
					      				<xsl:when test="control/repeat_interval = 1">
					      					<span class="pre">Hver</span>
					      				</xsl:when>
					      				<xsl:when test="control/repeat_interval = 2">
					      					<span class="pre">Annenhver</span>
					      				</xsl:when>
					      				<xsl:when test="control/repeat_interval > 2">
					      					<span class="pre">Hver</span><span><xsl:value-of select="control/repeat_interval"/>.</span>
					      				</xsl:when>
					      			</xsl:choose>
					      			
					      			<span class="val"><xsl:value-of select="control/repeat_type_label"/></span>
				      			</span>
							</td>
							<xsl:for-each select="calendar_array">
								<xsl:call-template name="check_list_status_checker" >
									<xsl:with-param name="location_code"><xsl:value-of select="//current_location/location_code"/></xsl:with-param>
								</xsl:call-template>
							</xsl:for-each>
					</tr>	
				</xsl:for-each>	
			</xsl:when>
			<xsl:otherwise>
				<tr class="cal_info_msg"><td colspan="3">Ingen sjekklister for bygg i angitt periode</td></tr>
			</xsl:otherwise>
		</xsl:choose>
	</table>
	</div>
</div>
</div>
</xsl:template>
