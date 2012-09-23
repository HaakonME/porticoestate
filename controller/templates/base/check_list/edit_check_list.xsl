<!-- $Id$ -->
<xsl:template match="data" name="view_check_list" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format"><xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')" /></xsl:variable>

<div id="main_content" class="medium">
		
	<!-- ==================  EDIT CHECKLIST  ========================= -->
	
	<div id="check-list-heading">
		<div class="box-1">
			<h1>Kontroll: <xsl:value-of select="control/title"/></h1>
			<xsl:choose>
				<xsl:when test="type = 'component'">
					<h2><xsl:value-of select="component_array/xml_short_desc"/></h2>
				</xsl:when>
				<xsl:otherwise>
					<xsl:choose>
						<xsl:when test="location_level = 1">
							<h2>Eiendom: <xsl:value-of select="location_array/loc1_name"/></h2>
						</xsl:when>
						<xsl:otherwise>
								<h2>Bygg: <xsl:value-of select="location_array/loc2_name"/></h2>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:otherwise>
			</xsl:choose>
		</div>
		<div class="box-2 select-box">
			<a>
				<xsl:attribute name="href">
					<xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_for_year</xsl:text>
					<xsl:text>&amp;year=</xsl:text>
					<xsl:value-of select="current_year"/>
					<xsl:text>&amp;location_code=</xsl:text>
					<xsl:choose>
					  <xsl:when test="type = 'component'">
						  <xsl:value-of select="building_location_code"/>
						</xsl:when>
						<xsl:otherwise>
						  <xsl:value-of select="location_array/location_code"/>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:attribute>
				Kontrolplan for bygg/eiendom (år)
			</a>
			<a class="last">
				<xsl:attribute name="href">
					<xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_for_month</xsl:text>
					<xsl:text>&amp;year=</xsl:text>
					<xsl:value-of select="current_year"/>
					<xsl:text>&amp;month=</xsl:text>
					<xsl:value-of select="current_month_nr"/>
					<xsl:text>&amp;location_code=</xsl:text>
					<xsl:choose>
					  <xsl:when test="type = 'component'">
						  <xsl:value-of select="building_location_code"/>
						</xsl:when>
						<xsl:otherwise>
						  <xsl:value-of select="location_array/location_code"/>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:attribute>
				Kontrolplan for bygg/eiendom (måned)
			</a>
		</div>
		
		<!-- ==================  CHECKLIST TAB MENU  ===================== -->
		<xsl:call-template name="check_list_tab_menu">
	 		<xsl:with-param name="active_tab">view_details</xsl:with-param>
		</xsl:call-template>
	</div>
	
	<!-- ==================  CHECKLIST DETAILS  ===================== -->
	<div id="check_list_details">
		<h3 class="box_header">Sjekklistedetaljer</h3>
			<xsl:variable name="action_url"><xsl:value-of select="php:function('get_phpgw_link', 'index.php', 'menuaction:controller.uicheck_list.save_check_list')" /></xsl:variable>
			<form id="frm_update_check_list" action="{$action_url}" method="post">	
			<xsl:variable name="check_list_id"><xsl:value-of select="check_list/id"/></xsl:variable>
			<input id="check_list_id" type="hidden" name="check_list_id" value="{$check_list_id}" />
			
			<fieldset class="col_1">
			<div class="row">
				<label>Status</label>
				<xsl:variable name="status"><xsl:value-of select="check_list/status"/></xsl:variable>
				<select id="status" name="status">
					<xsl:choose>
						<xsl:when test="check_list/status = 0">
							<option value="1">Utført</option>
							<option value="0" SELECTED="SELECTED" >Ikke utført</option>
						</xsl:when>
						<xsl:when test="check_list/status = 1">
							<option value="1" SELECTED="SELECTED">Utført</option>
							<option value="0">Ikke utført</option>
						</xsl:when>
						<xsl:otherwise>
							<option value="1">Utført</option>
							<option value="0">Ikke utført</option>
						</xsl:otherwise>
					</xsl:choose>
				</select>
			</div>
			<div class="row">
				<label>Skal utføres innen</label>
				<input class="date">
			      <xsl:attribute name="id">deadline_date</xsl:attribute>
			      <xsl:attribute name="name">deadline_date</xsl:attribute>
			      <xsl:attribute name="type">text</xsl:attribute>
			      <xsl:if test="check_list/deadline != 0 or check_list/deadline != ''">
			      	<xsl:attribute name="value"><xsl:value-of select="php:function('date', $date_format, number(check_list/deadline))"/></xsl:attribute>
				  </xsl:if>
			    </input>
			</div>
			<div class="row">
				<label>Planlagt dato</label>
				<input class="date">
			      <xsl:attribute name="id">planned_date</xsl:attribute>
			      <xsl:attribute name="name">planned_date</xsl:attribute>
			      <xsl:attribute name="type">text</xsl:attribute>
			      <xsl:if test="check_list/planned_date != 0 and check_list/planned_date != ''">
			      	<xsl:attribute name="value"><xsl:value-of select="php:function('date', $date_format, number(check_list/planned_date))"/></xsl:attribute>
			      </xsl:if>
			    </input>
		    </div>
		    <div class="row">
				<label>Utført dato</label>
				<input class="date">
			      <xsl:attribute name="id">completed_date</xsl:attribute>
			      <xsl:attribute name="name">completed_date</xsl:attribute>
			      <xsl:attribute name="type">text</xsl:attribute>
				  <xsl:if test="check_list/completed_date != 0 and check_list/completed_date != ''">
			      	<xsl:attribute name="value"><xsl:value-of select="php:function('date', $date_format, number(check_list/completed_date))"/></xsl:attribute>
			      </xsl:if>
			    </input>
		    </div>
		    </fieldset>
		    <fieldset class="col_2">
			    <div class="row">
					<label>Antall åpne saker</label>
				     <xsl:value-of select="check_list/num_open_cases"/>
			    </div>
			    <div class="row">
					<label>Antall ventende saker</label>
				     <xsl:value-of select="check_list/num_pending_cases"/>
			    </div>
		    </fieldset>
		    
			<div class="comment">
				<label>Kommentar</label>
				<textarea>
				  <xsl:attribute name="name">comment</xsl:attribute>
				  <xsl:value-of select="check_list/comment"/>
				</textarea>
			</div>
			
			<div class="form-buttons">
				<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save_check_list')" /></xsl:variable>
				<input class="btn not_active" type="submit" name="save_control" value="Lagre detaljer" />
			</div>
			</form>
		</div>
	</div>
</xsl:template>
