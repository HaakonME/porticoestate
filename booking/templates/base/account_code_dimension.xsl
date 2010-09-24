<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">

    <xsl:call-template name="msgbox"/>
	<xsl:call-template name="yui_booking_i18n"/>

	<dl class="form">
		<dt class="heading"><xsl:value-of select="php:function('lang', 'Account Codes')"/> - <xsl:value-of select="php:function('lang', 'Labels')"/></dt>
	</dl>

	<p><xsl:value-of select="php:function('lang', 'account_code_dimension_helptext')"/></p>

    <form action="" method="POST">
        <dl class="form-col">
            <dt><label for="field_dim_1">Dim1 (pos 862 - 869)</label></dt>
            <dd>
				<input id="field_dim_1" name="dim_1" type="text">
					<xsl:attribute name="value"><xsl:value-of select="config_data/dim_1"/></xsl:attribute>
				</input>
            </dd>
            <dt><label for="field_dim_2">Dim2 (pos 870 - 877)</label></dt>
            <dd>
				<input id="field_dim_2" name="dim_2" type="text">
					<xsl:attribute name="value"><xsl:value-of select="config_data/dim_2"/></xsl:attribute>
				</input>
            </dd>
            <dt><label for="field_dim_3">Dim3 (pos 878 - 885)</label></dt>
            <dd>
				<input id="field_dim_3" name="dim_3" type="text">
					<xsl:attribute name="value"><xsl:value-of select="config_data/dim_3"/></xsl:attribute>
				</input>
            </dd>
            <dt><label for="field_dim_4">Dim4 (pos 886 - 893)</label></dt>
            <dd>
				<input id="field_dim_4" name="dim_4" type="text">
					<xsl:attribute name="value"><xsl:value-of select="config_data/dim_4"/></xsl:attribute>
				</input>
            </dd>
            <dt><label for="field_dim_5">Dim5 (pos 894 - 905)</label></dt>
            <dd>
				<input id="field_dim_5" name="dim_5" type="text">
					<xsl:attribute name="value"><xsl:value-of select="config_data/dim_5"/></xsl:attribute>
				</input>
            </dd>
        </dl>
        <dl class="form-col">
            <dt><label for="field_dim_value_1">Dim_value_1 (pos 914 - 925)</label></dt>
            <dd>
				<input id="field_dim_value_1" name="dim_value_1" type="text">
					<xsl:attribute name="value"><xsl:value-of select="config_data/dim_value_1"/></xsl:attribute>
				</input>
            </dd>
            <dt><label for="field_dim_value_4">Dim_value_4 (pos 950 - 961)</label></dt>
            <dd>
				<input id="field_dim_value_4" name="dim_value_4" type="text">
					<xsl:attribute name="value"><xsl:value-of select="config_data/dim_value_4"/></xsl:attribute>
				</input>
            </dd>
            <dt><label for="field_dim_value_5">Dim_value_5 (pos 962 - 973)</label></dt>
            <dd>
				<input id="field_dim_value_5" name="dim_value_5" type="text">
					<xsl:attribute name="value"><xsl:value-of select="config_data/dim_value_5"/></xsl:attribute>
				</input>
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
