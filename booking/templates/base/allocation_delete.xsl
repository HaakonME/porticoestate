<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">

	<dl class="form">
    	<dt class="heading"><xsl:value-of select="php:function('lang', 'Delete allocation')"/></dt>
	</dl>
    <xsl:call-template name="msgbox"/>
	<xsl:call-template name="yui_booking_i18n"/>
	<dl class="form">
    	<dd><xsl:value-of select="php:function('lang', 'Delete Information')"/></dd>
    	<dd><xsl:value-of select="php:function('lang', 'Delete Information2')"/></dd>
	</dl>
    <form action="" method="POST">
		<input type="hidden" name="application_id" value="{allocation/application_id}"/>
        <input id="field_org_id" name="organization_id" type="hidden" value="{allocation/organization_id}" />
        <input id="field_building_id" name="building_id" type="hidden" value="{allocation/building_id}" />
        <input id="field_from" name="from_" type="hidden" value="{allocation/from_}" />
        <input id="field_to" name="to_" type="hidden" value="{allocation/to_}" />

        <dl class="form-col">

            <dt><label for="field_building"><xsl:value-of select="php:function('lang', 'Building')" /></label></dt>
            <dd>
                <div class="autocomplete">
                        <xsl:value-of select="allocation/building_name"/>
                </div>
            </dd>


            <dt><label for="field_from"><xsl:value-of select="php:function('lang', 'From')" /></label></dt>
            <dd>
                <div>
                    <xsl:value-of select="allocation/from_"/>
                </div>
            </dd>
			<dd>
                <div> </div>
			</dd>
			<dt><label for="field_repeat_until"><xsl:value-of select="php:function('lang', 'Recurring allocation deletion')" /></label></dt>
			<dd>
				<label>
					<input type="checkbox" name="outseason" id="outseason">
						<xsl:if test="outseason='on'">
							<xsl:attribute name="checked">checked</xsl:attribute>
						</xsl:if>
					</input>
					<xsl:value-of select="php:function('lang', 'Out season')" />
				</label>
			</dd>
			<dd>
				<label>
					<input type="checkbox" name="recurring" id="recurring">
						<xsl:if test="recurring='on'">
							<xsl:attribute name="checked">checked</xsl:attribute>
						</xsl:if>
					</input>
					<xsl:value-of select="php:function('lang', 'Delete until')" />
				</label>
			</dd>
			<dd class="date-picker">
				<input id="field_repeat_until" name="repeat_until" type="text">
					<xsl:attribute name="value"><xsl:value-of select="repeat_until"/></xsl:attribute>
				</input>
			</dd>
			<dt><xsl:value-of select="php:function('lang', 'Interval')" /></dt>
			<dd>
				<xsl:value-of select="../field_interval" />
				<select id="field_interval" name="field_interval">
					<option value="1">
						<xsl:if test="interval=1">
							<xsl:attribute name="selected">selected</xsl:attribute>
						</xsl:if>
						<xsl:value-of select="php:function('lang', '1 week')" />
					</option>
					<option value="2">
						<xsl:if test="interval=2">
							<xsl:attribute name="selected">selected</xsl:attribute>
						</xsl:if>
						<xsl:value-of select="php:function('lang', '2 weeks')" />
					</option>
					<option value="3">
						<xsl:if test="interval=3">
							<xsl:attribute name="selected">selected</xsl:attribute>
						</xsl:if>
						<xsl:value-of select="php:function('lang', '3 weeks')" />
					</option>
					<option value="4">
						<xsl:if test="interval=4">
							<xsl:attribute name="selected">selected</xsl:attribute>
						</xsl:if>
						<xsl:value-of select="php:function('lang', '4 weeks')" />
					</option>
				</select>
			</dd>
        </dl>
        <dl class="form-col">
            <dt><label for="field_org"><xsl:value-of select="php:function('lang', 'Organization')" /></label></dt>
            <dd>
                <div class="autocomplete">
                        <xsl:value-of select="allocation/organization_name"/>
                </div>
            </dd>
            <dt><label for="field_to"><xsl:value-of select="php:function('lang', 'To')" /></label></dt>
            <dd>
                <div>
                    <xsl:value-of select="allocation/to_"/>
                </div>
            </dd>
        </dl>
        <div class="form-buttons">
            <input type="submit">
				<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Delete')"/></xsl:attribute>
			</input>
            <a class="cancel">
                <xsl:attribute name="href"><xsl:value-of select="allocation/cancel_link"/></xsl:attribute>
                <xsl:value-of select="php:function('lang', 'Cancel')" />
            </a>
        </div>
    </form>
    </div>
    <script type="text/javascript">
        YAHOO.booking.season_id = '<xsl:value-of select="allocation/season_id"/>';
        YAHOO.booking.initialSelection = <xsl:value-of select="allocation/resources_json"/>;
		var lang = <xsl:value-of select="php:function('js_lang', 'Resource Type')"/>;
    </script>
</xsl:template>
