<xsl:template match="ticketinfo" xmlns:php="http://php.net/xsl">
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
			<a href="?menuaction=frontend.uihelpdesk.index">Vis alle</a>
            <h1><xsl:value-of select="ticket/subject"/></h1>
            <div id="ticketinfo">
                <ul>
                    <li><xsl:value-of select="ticket/last_opened"/></li>
                    <li><strong>Meldt inn av: </strong><xsl:value-of select="ticket/user_name"/></li>
                    <li><strong>Status: </strong><xsl:value-of select="ticket/status_name"/></li>
                </ul>
                <p><strong>Beskrivelse</strong><br/>
                <xsl:value-of select="ticket/details"/></p>

            </div>

            <xsl:for-each select="tickethistory/*[starts-with(name(), 'record')]">
            	<xsl:copy-of select="."/>
                <hr/>
                Sak oppdatert av <xsl:value-of select="user"/> den <xsl:value-of select="date"/><br/>
                <xsl:choose>
                    <xsl:when test="action">
                        <xsl:value-of select="action"/>: <xsl:value-of select="old_value"/> -> <xsl:value-of select="new_value"/><br/>
                    </xsl:when>
                    <xsl:otherwise>
                        <p>
                            <xsl:value-of select="note"/>
                        </p>
                    </xsl:otherwise>
                </xsl:choose>

            </xsl:for-each>
        </div>
    </div>
</xsl:template>


