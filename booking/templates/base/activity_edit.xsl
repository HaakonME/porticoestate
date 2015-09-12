<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <!--div id="content"-->
    <!--h3><xsl:value-of select="php:function('lang', 'Edit Activity')"/></h3-->
    <xsl:call-template name="msgbox"/>
	<!--xsl:call-template name="yui_booking_i18n"/-->
    <form action="" method="POST" id='form' class="pure-form pure-form-aligned" name="form">
        <input type="hidden" name="tab" value=""/>
        <div id="tab-content">
            <xsl:value-of disable-output-escaping="yes" select="activity/tabs"/>
            <div id="activity_edit">
    
                <div class="pure-control-group">
                    <label for="field_name">
                        <h4><xsl:value-of select="php:function('lang', 'Activity')"/></h4>
                    </label>
                    <input id="field_name" name="name" type="text">
                        <xsl:attribute name="value"><xsl:value-of select="activity/name"/></xsl:attribute>
                    </input>
                </div>
                <div class="pure-control-group">
                    <label for="field_active">
                        <xsl:value-of select="php:function('lang', 'Active')" />
                    </label>
                    <select id="field_active" name="active">
                        <option value="1">
                            <xsl:if test="activity/active=1">
                                    <xsl:attribute name="selected">checked</xsl:attribute>
                            </xsl:if>
                            <xsl:value-of select="php:function('lang', 'Active')" />
                        </option>
                        <option value="0">
                            <xsl:if test="activity/active=0">
                                    <xsl:attribute name="selected">checked</xsl:attribute>
                            </xsl:if>
                            <xsl:value-of select="php:function('lang', 'Inactive')" />
                        </option>
                    </select>
                </div>
                <div class="pure-control-group">
                    <label for="field_description">
                        <xsl:value-of select="php:function('lang', 'Description')"/>
                    </label>
                    <textarea rows="5" id="field_description" name="description">
                       <xsl:value-of select="activity/description"/>
                    </textarea>
                </div>
                <div class="pure-control-group">
                    <label for="field_parent_id">
                        <h4><xsl:value-of select="php:function('lang', 'Parent activity')"/></h4>
                    </label>
                    <!--div class="autocomplete"-->
                    <select name="parent_id" id="field_parent_id">
                        <option value="0"><xsl:value-of select="php:function('lang', 'No parent')"/></option>
                        <xsl:for-each select="activities">
                            <option>
                                <xsl:if test="../activity/parent_id = id">
                                    <xsl:attribute name="selected">selected</xsl:attribute>
                                </xsl:if>
                                <xsl:attribute name="value">
                                    <xsl:value-of select="id"/>
                                </xsl:attribute>
                                <xsl:value-of select="name"/>
                            </option>
                        </xsl:for-each>
                    </select>
                    <div id="parent_container"></div>
                    <!--/div-->
                </div>
            </div>
        </div>
        <div class="form-buttons">
            <input type="submit" class="button pure-button pure-button-primary">
                <xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Save')"/></xsl:attribute>
            </input>
            <a class="cancel">
                <xsl:attribute name="href"><xsl:value-of select="activity/cancel_link"></xsl:value-of></xsl:attribute>
                <xsl:value-of select="php:function('lang', 'Cancel')"/>
            </a>			
        </div>
    </form>
    <!--/div-->
</xsl:template>
