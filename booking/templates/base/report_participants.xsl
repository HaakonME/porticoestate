<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <form action="" method="POST" class="pure-form pure-form-aligned" id="form" name="form">
        <input type="hidden" name="tab" value=""/>
        <div id="tab-content">
            <xsl:value-of disable-output-escaping="yes" select="data/tabs"/>
            <div id="report_part">
                <div class="pure-control-group">
                    <!--dt><label for="field_from"><xsl:value-of select="php:function('lang', 'From')" /></label></dt>
                    <dd>
                    <div class="date-picker">
                        <input id="field_from" name="from" type="text">
                            <xsl:attribute name="value"><xsl:value-of select="from"/></xsl:attribute>
                        </input>
                    </div>
                    </dd-->
                    <label for="start_date">
                        <h4><xsl:value-of select="php:function('lang', 'From')" /></h4>
                    </label>
                    <input class="datetime" id="from" name="from" type="text">
                        <xsl:attribute name="value"><xsl:value-of select="from"/></xsl:attribute>
                    </input>
                </div>
                <div class="pure-control-group">
                    <!--dt><label for="field_to"><xsl:value-of select="php:function('lang', 'To')" /></label></dt>
                    <dd>
                    <div class="date-picker">
                        <input id="field_to" name="to" type="text">
                            <xsl:attribute name="value"><xsl:value-of select="to"/></xsl:attribute>
                        </input>
                    </div>
                    </dd-->
                    <label for="end_date">
                        <h4><xsl:value-of select="php:function('lang', 'To')" /></h4>
                    </label>
                    <input class="datetime" id="to" name="to" type="text">
                        <xsl:attribute name="value"><xsl:value-of select="to"/></xsl:attribute>
                    </input>
                </div>
                <div class="pure-control-group">
                    <label for="field_building" style="vertical-align:top;">
                        <h4><xsl:value-of select="php:function('lang', 'buildings')"/></h4>
                    </label>
                    <select id="field_building" name="building[]" size="10" multiple="multiple" class="full-width">
                        <xsl:for-each select="buildings">
                            <xsl:sort select="name"/>
                            <option>
                                <xsl:if test="../building = id">
                                    <xsl:attribute name="selected">selected</xsl:attribute>
                                </xsl:if>
                                <xsl:attribute name="value"><xsl:value-of select="id"/></xsl:attribute>
                                <xsl:value-of select="name"/>
                            </option>
                        </xsl:for-each>
                    </select>
                </div>
                <div class="pure-control-group">
                    <label for="otype">
                        <h4><xsl:value-of select="php:function('lang', 'Format')" /></h4>
                    </label>
                    <select id="otype" name="otype">
                        <option value="PDF">PDF</option>
                        <option value="CSV">CSV</option>
                        <option value="XLS">XLS</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="form-buttons">
            <input type="submit" class="pure-button pure-button-primary">
                <xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Create report')"/></xsl:attribute>
            </input>
        </div>
    </form>
</xsl:template>
