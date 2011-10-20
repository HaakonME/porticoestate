<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">

    <xsl:call-template name="msgbox"/>
	<xsl:call-template name="yui_booking_i18n"/>

   	<dl class="form">
   		<dt class="heading"><xsl:value-of select="php:function('lang', 'Booking system settings')"/></dt>
   	</dl>

    <form action="" method="POST">

       <dl class="form-col">
            <dt><label for="field_user_can_delete"><xsl:value-of select="php:function('lang', 'Frontend users can delete bookings and allocations')"/></label></dt>
			<dd>
				<select id="field_user_can_delete" name="user_can_delete">
                    <option value="no">
                        <xsl:if test="config_data/user_can_delete='no'">
                            <xsl:attribute name="selected">checked</xsl:attribute>
                        </xsl:if>
                        <xsl:value-of select="php:function('lang', 'No')" />
                    </option>
                    <option value="yes">
                        <xsl:if test="config_data/user_can_delete='yes'">
                            <xsl:attribute name="selected">checked</xsl:attribute>
                        </xsl:if>
                        <xsl:value-of select="php:function('lang', 'Yes')" />
		           </option>
		        </select>
			</dd>
        </dl>
		<div class="form-buttons">
			<input type="submit">
			<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Save')"/></xsl:attribute>
			</input>
		</div>
    </form>
    </div>
</xsl:template>
