<xsl:template match="ticketinfo" xmlns:php="http://php.net/xsl">
	<xsl:copy-of test="."/>
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

    <div class="yui-navset" id="ticket_tabview">
        <div class="yui-content">
        	<div id="ticketinfo">
        		<ul style="margin: 2em;">
        			<li style="margin-bottom: 1em;">
        				<a href="?menuaction=frontend.uihelpdesk.index"> &lt;&lt; Vis alle avviksmeldinger på bygget</a>
        			</li>
        			<li>
        				<ul>
        					<li style="margin-bottom: 5px;">
        						<img src="frontend/templates/base/images/16x16/comment.png" class="list_image"/> <strong><xsl:value-of select="ticket/subject"/></strong>
        					</li>
        					<li class="ticket_detail">
        						<img src="frontend/templates/base/images/16x16/clock_edit.png" class="list_image"/> Meldt inn <xsl:value-of select="ticket/entry_date"/> av <xsl:value-of select="ticket/user_name"/>
        					</li>
     						<li class="ticket_detail">
     							<img src="frontend/templates/base/images/16x16/timeline_marker.png" class="list_image"/> Status: <xsl:value-of select="ticket/status_name"/>
     						</li>
     						
     						<li class="ticket_detail">
     							<img src="frontend/templates/base/images/16x16/comments.png" class="list_image"/> Kommentarer:<br/>
     							<ul>
		        					<xsl:for-each select="tickethistory/*[starts-with(name(), 'record')]">
						                <li  class="ticket_detail"><xsl:value-of select="user"/> den <xsl:value-of select="date"/><br/>
						                <xsl:value-of select="note"/></li>
						            </xsl:for-each>
				            	</ul>
     						</li>
     					</ul>
        			</li>
        		</ul>
        	</div>
        </div>
    </div>
</xsl:template>


