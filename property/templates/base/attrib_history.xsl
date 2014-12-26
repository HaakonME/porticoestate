  <!-- $Id$ -->
<!-- attrib_history -->
   
<xsl:template match="attrib_history">
	<div id="tab-content">
		<fieldset>
			<div class="pure-control-group">
				<xsl:for-each select="datatable_def">
						<xsl:if test="container = 'datatable-container_0'">
							<xsl:call-template name="table_setup">
							  <xsl:with-param name="container" select ='container'/>
							  <xsl:with-param name="requestUrl" select ='requestUrl' />
							  <xsl:with-param name="ColumnDefs" select ='ColumnDefs' />
							  <xsl:with-param name="tabletools" select ='tabletools' />
							  <xsl:with-param name="config" select ='config' />
							</xsl:call-template>
						</xsl:if>
				</xsl:for-each>
			</div>
		</fieldset>
	</div>
</xsl:template>