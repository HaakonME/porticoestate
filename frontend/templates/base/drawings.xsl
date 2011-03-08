<xsl:template match="drawings" xmlns:php="http://php.net/xsl">
    <xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
    <div class="yui-navset" id="drawing_tabview">
        <xsl:value-of disable-output-escaping="yes" select="tabs" />
        <div class="yui-content">
        	<xsl:choose>
				<xsl:when test="normalize-space(//header/selected_location) != ''">
					<div class="toolbar-container">
		                <div class="toolbar">
		                    <xsl:apply-templates select="datatable/actions" />  
		                </div>
		            </div>
		            <div class="drawings">
		            	<table cellpadding="2" cellspacing="2" width="95%" align="center">
					        <xsl:choose>
					            <xsl:when test="msgbox_data != ''">
					                <tr>
					                    <td align="left" colspan="3">
					                        <xsl:call-template name="msgbox"/>
					                    </td>
					                </tr>
					            </xsl:when>
					        </xsl:choose>
					    </table>

		            </div>
				</xsl:when>
				<xsl:otherwise>
					<div class="tickets">
		            	<xsl:value-of select="php:function('lang', 'no_buildings')"/>
		            </div>
				</xsl:otherwise>
			</xsl:choose>
            
        </div>
    </div>

</xsl:template>

