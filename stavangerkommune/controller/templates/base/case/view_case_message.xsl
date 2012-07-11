<!-- $Id: edit_check_list.xsl 8374 2011-12-20 07:45:04Z vator $ -->
<xsl:template match="data" name="view_check_list" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format">d/m-Y</xsl:variable>

<div id="main_content" class="medium">
	
	<h1>Utførelse av kontroll: <xsl:value-of select="control/title"/></h1>
	<h2>Sjekkliste for: <xsl:value-of select="location_array/loc1_name"/></h2>
	
	<xsl:call-template name="check_list_tab_menu" />
	
	<!-- =======================  INFO ABOUT MESSAGE  ========================= -->
	<h3 class="box_header ext">Melding registrert</h3>
	<div id="caseMessage" class="box ext">
		
			<a id="showMessage" target="_blank">
				<xsl:attribute name="href">
					<xsl:text>index.php?menuaction=property.uitts.view</xsl:text>
					<xsl:text>&amp;id=</xsl:text>
					<xsl:value-of select="message_ticket_id"/>
				</xsl:attribute>
		      	Vis melding
	   		</a>
		
			<!-- ===  BYGG  === -->
			<div class="row">
				<label>Bygg:</label><span><xsl:value-of select="location_array/loc1_name"/></span>
			</div>
			
		    <!-- === TITLE === -->
		    <div class="row">				
				<label>Tittel på melding:</label><span><xsl:value-of select="message_ticket/subject"/></span>
			</div>
			<!-- === CATEGORY === -->
			<div class="row">
				<label>Kategori</label><span><xsl:value-of select="category"/></span>
			</div>
			<!-- === UPLOAD FILE === -->
			<div class="row">
				<label>Filvedlegg:</label>
					<xsl:for-each select="message_ticket/files">
						<li><xsl:value-of select="."/></li>
					</xsl:for-each>
			</div>
		
		<h3>Meldingen inneholder disse sakene</h3>					
		<ul class="cases">
			<xsl:for-each select="check_items_and_cases">
				<xsl:choose>
				 	<xsl:when test="cases_array/child::node()">
				 		<li class="check_item">
					 		<h4><span><xsl:value-of select="control_item/title"/></span></h4>
					 		<ul>		
								<xsl:for-each select="cases_array">
									<xsl:variable name="cases_id"><xsl:value-of select="id"/></xsl:variable>
									<li><xsl:value-of select="descr"/></li>
								</xsl:for-each>
							</ul>
				 		</li>
				 	</xsl:when>
			 	</xsl:choose>
			</xsl:for-each>
		</ul>
		<a class="btn">
			<xsl:attribute name="href">
				<xsl:text>index.php?menuaction=controller.uicase.create_case_message</xsl:text>
				<xsl:text>&amp;check_list_id=</xsl:text>
				<xsl:value-of select="check_list/id"/>
			</xsl:attribute>
	      Registrer ny melding
	   	</a>
	</div>
</div>
</xsl:template>
