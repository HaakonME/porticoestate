<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <xsl:variable name="edit_action">
        <xsl:value-of select="organization/edit_link"/>
    </xsl:variable>
    <form class= "pure-form pure-form-aligned" action="{$edit_action}" method="post" id="form" name="form">
        <input type="hidden" name="tab" value=""/>
        <div id="tab-content">
            <xsl:value-of disable-output-escaping="yes" select="organization/tabs"/>
            <div id="organization" class="booking-container">
                <fieldset>
                    <h1><xsl:value-of select="organization/name"/></h1>
                    <div class="pure-control-group">
                        <label><xsl:value-of select="php:function('lang', 'Organization shortname')" /></label>
                        <span><xsl:value-of select="organization/shortname" /></span>
                    </div>
                    <div class="pure-control-group">
                        <label><xsl:value-of select="php:function('lang', 'Organization number')" /></label>
                        <span><xsl:value-of select="organization/organization_number" /></span>
                    </div>
                    <div class="pure-control-group">
                        <label><xsl:value-of select="php:function('lang', 'Customer number')" /></label>
                        <span><xsl:value-of select="organization/customer_number" /></span>
                    </div>
                    <div class="pure-control-group">
                        <label><xsl:value-of select="php:function('lang', 'Activity')" /></label>
                        <span><xsl:value-of select="organization/activity_name" /></span>
                    </div>
                    <div class="pure-control-group">
                        <label><xsl:value-of select="php:function('lang', 'Homepage')" /></label>
                        <xsl:if test="organization/homepage and normalize-space(organization/homepage)">
                            <a target="blank" href="{organization/homepage}"><xsl:value-of select="organization/homepage" /></a>
                        </xsl:if>
                    </div>
                    <div class="pure-control-group">
                        <label><xsl:value-of select="php:function('lang', 'Email')" /></label>
                        <a href="mailto:{organization/email}"><xsl:value-of select="organization/email"/></a>
                    </div>
                    <div class="pure-control-group">
                        <label><xsl:value-of select="php:function('lang', 'Phone')" /></label>
                        <span><xsl:value-of select="organization/phone"/></span>
                    </div>
                    <div class="pure-control-group">
                        <label style="vertical-align:top;"><xsl:value-of select="php:function('lang', 'Description')" /></label>
                        <div style="display:inline-block;"><xsl:value-of select="organization/description" disable-output-escaping="yes"/></div>
                    </div>
                    <xsl:if test="count(organization/contacts/*) &gt; 0">
                        <div class="pure-control-group">
                            <label style="vertical-align:top;"><xsl:value-of select="php:function('lang', 'Admins')" /></label>
                            <ul style="list-style:none;display:inline-block;padding:0;margin:0;">
                                <xsl:if test="organization/contacts[1]">
                                    <li><xsl:value-of select="organization/contacts[1]/name"/></li>
                                </xsl:if>
                                <xsl:if test="organization/contacts[2]">
                                    <li><xsl:value-of select="organization/contacts[2]/name"/></li>
                                </xsl:if>
                            </ul>
                        </div>
                    </xsl:if>
                    <div class="pure-control-group">
                        <xsl:copy-of select="phpgw:booking_customer_identifier_show(organization)"/>
                    </div>
                    <div class="pure-control-group">
                        <label><xsl:value-of select="php:function('lang', 'Street')" /></label>
                        <span><xsl:value-of select="organization/street"/></span>
                    </div>
                    <div class="pure-control-group">
                        <label><xsl:value-of select="php:function('lang', 'Zip code')" /></label>
                        <span><xsl:value-of select="organization/zip_code"/></span>
                    </div>
                    <div class="pure-control-group">
                        <label><xsl:value-of select="php:function('lang', 'Postal City')" /></label>
                        <span><xsl:value-of select="organization/city"/></span>
                    </div>
                    <div class="pure-control-group">
                        <label><xsl:value-of select="php:function('lang', 'District')" /></label>
                        <span><xsl:value-of select="organization/district"/></span>
                    </div>
                </fieldset>
            </div>
        </div>
    </form>
    <div class="form-buttons">
        <button class="pure-button pure-button-primary" onclick="window.location.href='{organization/edit_link}'">
            <xsl:value-of select="php:function('lang', 'Edit')" />
        </button>
    </div>
</xsl:template>
