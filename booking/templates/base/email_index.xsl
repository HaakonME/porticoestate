<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <xsl:call-template name="msgbox"/>
    <form action="" method="POST" class="pure-form pure-form-aligned" id="form" name="form">
        <input type="hidden" name="tab" value=""/>
        <div id="tab-content">
            <xsl:value-of disable-output-escaping="yes" select="building/tabs"/>
            <div id="building" class="booking-container">
                <input type="hidden" name="step" value="0"/>
                <div class="pure-control-group">
                    <label for="field_building_name"><xsl:value-of select="php:function('lang', 'Building')" /></label>
                    <input id="field_building_id" name="building_id" type="hidden">
                        <xsl:attribute name="value"><xsl:value-of select="building_id"/></xsl:attribute>
                    </input>
                    <input id="field_building_name" name="building_name" type="text">
                        <xsl:attribute name="value"><xsl:value-of select="building_name"/></xsl:attribute>
                    </input>
                </div>
                <div class="pure-control-group">
                    <label style="vertical-align:top;"><xsl:value-of select="php:function('lang', 'Season')" /></label>
                    <div id="season_container" style="display:inline-block;"><xsl:value-of select="php:function('lang', 'Select a building first')" /></div>
                </div>
                <div class="pure-control-group">
                    <label for="field_mailsubject"><xsl:value-of select="php:function('lang', 'Mail subject')" /></label>
                    <input type="text" id="field_mailsubject" name="mailsubject" class="full-width">
                        <xsl:attribute name="value"><xsl:value-of select="mailsubject"/></xsl:attribute>
                    </input>
                </div>
                <div class="pure-control-group">
                    <label for="field_mailbody"><xsl:value-of select="php:function('lang', 'Mail body')" /></label>
                    <textarea id="field_mailbody" name="mailbody" class="full-width"><xsl:value-of select="mailbody"/></textarea>
                </div>
            </div>
        </div>
        <div class="form-buttons">
            <input type="submit" class="pure-button pure-button-primary">
                <xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'preview')"/></xsl:attribute>
            </input>
        </div>
    </form>
    <script type="text/javascript">
        var lang = <xsl:value-of select="php:function('js_lang', 'Name')"/>;
    </script>
</xsl:template>
