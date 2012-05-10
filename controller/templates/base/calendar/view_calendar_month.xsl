<!-- $Id$ -->
<xsl:template match="data" name="view_check_lists" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format">d/m-Y</xsl:variable>
<xsl:variable name="location_code"><xsl:value-of select="location_array/location_code"/></xsl:variable>
<xsl:variable name="view_location_code"><xsl:value-of select="view_location_code"/></xsl:variable>

<div id="main_content">
	<div id="control_plan">
		<div class="top">
			<h1><xsl:value-of select="location_array/loc1_name"/></h1>
			<h3>Kalenderoversikt for <xsl:value-of select="period"/><span class="year"><xsl:value-of select="year"/></span></h3>
		
			<form action="#">
				<input type="hidden" name="period_type" value="view_year" />
				<input type="hidden" name="year">
			      <xsl:attribute name="value">
			      	<xsl:value-of select="year"/>
			      </xsl:attribute>
				</input>

				<select id="choose_my_location">
					<xsl:for-each select="my_locations">
						<xsl:variable name="loc_code"><xsl:value-of select="location_code"/></xsl:variable>
						<xsl:choose>
							<xsl:when test="location_code = $view_location_code">
								<option value="{$loc_code}" selected="selected">
									<xsl:value-of disable-output-escaping="yes" select="loc1_name"/>
								</option>
							</xsl:when>
							<xsl:otherwise>
								<option value="{$loc_code}">
									<xsl:value-of disable-output-escaping="yes" select="loc1_name"/>
								</option>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:for-each>
				</select>					
			</form>
		</div>
		
		<div class="middle">
			
			<xsl:call-template name="icon_color_map" />
			
			<a style="display:block;font-weight: bold;font-size: 14px;float:left;">
				<xsl:attribute name="href">
					<xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_for_year</xsl:text>
					<xsl:text>&amp;year=</xsl:text>
					<xsl:value-of select="year"/>
					<xsl:text>&amp;location_code=</xsl:text>
					<xsl:value-of select="$location_code"/>
				</xsl:attribute>
				Årsoversikt
			</a>
			<!-- 
 				<select id="loc_1" class="choose_loc">
					<xsl:for-each select="property_array">
						<xsl:variable name="loc_code"><xsl:value-of select="location_code"/></xsl:variable>
						<xsl:choose>
							<xsl:when test="location_code = $view_location_code">
								<option value="{$loc_code}" selected="selected">
									<xsl:value-of disable-output-escaping="yes" select="loc1_name"/>
								</option>
							</xsl:when>
							<xsl:otherwise>
								<option value="{$loc_code}">
									<xsl:value-of disable-output-escaping="yes" select="loc1_name"/>
								</option>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:for-each>
				</select>				
			 -->
		</div>
		
		
		<div id="cal_wrp">
			<table id="calendar" class="month">
				<tr class="heading">
						<th class="title"><span>Tittel</span></th>
						<th class="assigned"><span>Tildelt</span></th>
						<th class="frequency"><span>Frekvens</span></th>
						<xsl:for-each select="heading_array">
							<th><span><xsl:value-of select="."/></span></th>
						</xsl:for-each>
				</tr>
				<xsl:choose>	
					<xsl:when test="controls_calendar_array/child::node()">
			  	<xsl:for-each select="controls_calendar_array">

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
			      				<xsl:value-of select="control/repeat_type_label"/>
			      				<xsl:value-of select="control/repeat_interval"/>
			      			</span>
						</td>
				
				<xsl:for-each select="calendar_array">
					
					<xsl:call-template name="check_list_status_checker" >
						<xsl:with-param name="location_code"><xsl:value-of select="$view_location_code"/></xsl:with-param>
					</xsl:call-template>
					
				</xsl:for-each>
				</tr>
				</xsl:for-each>
				
					</xsl:when>
					<xsl:otherwise>
						<tr class="cal_info_msg"><td colspan="10">Ingen sjekklister for bygg i angitt periode</td></tr>
					</xsl:otherwise>
				</xsl:choose>
			
			</table>
		</div>
	</div>
</div>
</xsl:template>
