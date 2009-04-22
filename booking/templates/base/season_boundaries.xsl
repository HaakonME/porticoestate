<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">
        <ul class="pathway">
            <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="season/buildings_link"/></xsl:attribute>
                    <xsl:value-of select="php:function('lang', 'Buildings')" />
                </a>
            </li>
            <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="season/building_link"/></xsl:attribute>
                    <xsl:value-of select="season/building_name"/>
                </a>
            </li>
            <li><xsl:value-of select="php:function('lang', 'Seasons')" /></li>
            <li>
				<a>
                    <xsl:attribute name="href"><xsl:value-of select="season/season_link"/></xsl:attribute>
					<xsl:value-of select="season/name"/>
				</a>
			</li>
            <li><xsl:value-of select="php:function('lang', 'Boundaries')" /></li>
        </ul>

        <xsl:call-template name="msgbox"/>

		<table id="boundary-table">
			<thead>
				<tr><th><xsl:value-of select="php:function('lang', 'Week day')" /></th>
				<th><xsl:value-of select="php:function('lang', 'From')" /></th>
				<th><xsl:value-of select="php:function('lang', 'To')" /></th></tr>
			</thead>
			<xsl:for-each select="boundaries">
				<tr>
					<td><xsl:value-of select="wday_name"/></td>
					<td><xsl:value-of select="from_"/></td>
					<td><xsl:value-of select="to_"/></td>
				</tr>
			</xsl:for-each>
			<tbody>
				
			</tbody>
		</table>
		<form action="" method="POST">
		<dl class="form">
			<dt class="heading"><xsl:value-of select="php:function('lang', 'Add Boundary')" /></dt>
			<dd>
			</dd>
			<dt><label for="field_status"><xsl:value-of select="php:function('lang', 'Week day')" /></label></dt>
			<dd>
				<select name="wday">
					<option value="1"><xsl:value-of select="php:function('lang', 'Monday')" /></option>
					<option value="2"><xsl:value-of select="php:function('lang', 'Tuesday')" /></option>
					<option value="3"><xsl:value-of select="php:function('lang', 'Wednesday')" /></option>
					<option value="4"><xsl:value-of select="php:function('lang', 'Thursday')" /></option>
					<option value="5"><xsl:value-of select="php:function('lang', 'Friday')" /></option>
					<option value="6"><xsl:value-of select="php:function('lang', 'Saturday')" /></option>
					<option value="7"><xsl:value-of select="php:function('lang', 'Sunday')" /></option>
				</select>
			</dd>
			<dt><label><xsl:value-of select="php:function('lang', 'From')" /></label></dt>
			<dd>
				<div class="time-picker">
					<input id="field_from" name="from_" type="text">
                    	<xsl:attribute name="value"><xsl:value-of select="boundary/from_"/></xsl:attribute>
					</input>
				</div>
			</dd>
			<dt><label><xsl:value-of select="php:function('lang', 'To')" /></label></dt>
			<dd>
				<div class="time-picker">
					<input id="field_to" name="to_" type="text">
                    	<xsl:attribute name="value"><xsl:value-of select="boundary/to_"/></xsl:attribute>
					</input>
				</div>
			</dd>
		</dl>
		<div class="form-buttons">
			<input type="submit" value="Add"/>
			<a class="cancel">
				<xsl:attribute name="href"><xsl:value-of select="season/cancel_link"/></xsl:attribute>
				Cancel
			</a>
		</div>
	</form>
	</div>
</xsl:template>
