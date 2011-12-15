<xsl:template match="data" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format">d/m-Y</xsl:variable>


<script>
	$(function() {
		$( "#planned_date" ).datepicker({ 
			monthNames: ['Januar','Februar','Mars','April','Mai','Juni','Juli','August','September','Oktober','November','Desember'],
			dayNamesMin: ['Sø', 'Ma', 'Ti', 'On', 'To', 'Fr', 'Lø'],
			dateFormat: 'd/m-yy' 
		});
		$( "#completed_date" ).datepicker({ 
			monthNames: ['Januar','Februar','Mars','April','Mai','Juni','Juli','August','September','Oktober','November','Desember'],
			dayNamesMin: ['Sø', 'Ma', 'Ti', 'On', 'To', 'Fr', 'Lø'],
			dateFormat: 'd/m-yy' 
		});		
	});
</script>
		

<div id="main_content">
	
	<h1>Registrere sjekkliste for <xsl:value-of select="location_array/loc1_name"/></h1>

	<fieldset class="check_list_details">
		<label>Tittel</label><xsl:value-of select="control_array/title"/><br/>
		<label>Startdato</label>
		<xsl:if test="control_array/start_date != ''">
			<xsl:value-of select="php:function('date', $date_format, number(control_array/start_date))"/><br/>
		</xsl:if>
		<label>Sluttdato</label>
		<xsl:if test="control_array/end_date != ''">
			<xsl:value-of select="php:function('date', $date_format, number(control_array/end_date))"/><br/>
		</xsl:if>
		<label>Frekvenstype</label><xsl:value-of select="control_array/repeat_type"/><br/>
		<label>Frekvens</label><xsl:value-of select="control_array/repeat_interval"/><br/>
	</fieldset>
	
	<form action="index.php?menuaction=controller.uicheck_list_for_location.save_check_list_for_location" method="post">
		<input type="hidden" id="deadline_date" name="deadline_date" value=""/>
		
		<div id="calendar_dates">
			<p>Velg fristdato</p>
			<xsl:for-each select="calendar_array">
				<xsl:variable name="cal_date"><xsl:value-of select="."/></xsl:variable>
					
				<span><xsl:value-of select="php:function('date', $date_format, number( $cal_date ) )"/></span>
			</xsl:for-each>
		</div>

	
		<xsl:variable name="location_code"><xsl:value-of select="location_array/location_code"/></xsl:variable>
		<xsl:variable name="control_id"><xsl:value-of select="control_array/id"/></xsl:variable>
	
		<input type="hidden" name="control_id" value="{$control_id}" />
		<input type="hidden" name="location_code" value="{$location_code}" />
	
		<fieldset class="add_check_list">
			<div>
				<label>Status</label>
				<select name="status">
					<option value="0">Utført</option>
					<option value="0">Ikke utført</option>
				</select>
			</div>
			<div>
				<label>Planlagt dato</label>
				<input>
			      <xsl:attribute name="id">planned_date</xsl:attribute>
			      <xsl:attribute name="name">planned_date</xsl:attribute>
			      <xsl:attribute name="type">text</xsl:attribute>
			      <xsl:if test="check_list/planned_date != ''">
			      	<xsl:attribute name="value"><xsl:value-of select="php:function('date', $date_format, number(check_list/planned_date))"/></xsl:attribute>
			      </xsl:if>
			    </input>
		    </div>
		    <div>
				<label>Utført dato</label>
				<input>
			      <xsl:attribute name="id">completed_date</xsl:attribute>
			      <xsl:attribute name="name">completed_date</xsl:attribute>
			      <xsl:attribute name="type">text</xsl:attribute>
				  <xsl:if test="check_list/completed_date != ''">
			      	<xsl:attribute name="value"><xsl:value-of select="php:function('date', $date_format, number(check_list/completed_date))"/></xsl:attribute>
			      </xsl:if>
			    </input>
		    </div>
			<div><label>Utstyr</label><input name="equipment_id" /></div>
		</fieldset>
		<div>
			<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
			<input type="submit" name="save" value="{$lang_save}" title = "{$lang_save}" />
		</div>
	</form>
	
	 </div>
</xsl:template>