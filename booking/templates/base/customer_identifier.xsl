<func:function name="phpgw:booking_customer_identifier" xmlns:php="http://php.net/xsl">
	<xsl:param name="entity"/>
	<xsl:param name="label" select="string('Invoice information')"/>

	<func:result>
            <dl>
		<dt>
			<xsl:if test="not(normalize-space($label))"><xsl:attribute name="style">display:none</xsl:attribute></xsl:if>
			<xsl:value-of select="php:function('lang', $label)" />
			
		</dt>
		
		<dd>
			<select name='customer_identifier_type' id='field_customer_identifier_type' class="pure-input-1">
				<option value=''><xsl:value-of select="php:function('lang', 'None')" /></option>
				<xsl:for-each select="$entity/customer_identifier_types/*">
					<option>
						<xsl:if test="../../customer_identifier_type = local-name()">
							<xsl:attribute name="selected">selected</xsl:attribute>
						</xsl:if>
				
						<xsl:attribute name="value"><xsl:value-of select="local-name()"/></xsl:attribute>
						<xsl:value-of select="php:function('lang', string(node()))"/>
					</option>
				</xsl:for-each>
			</select>
			<input name="customer_ssn" type="text" id="field_customer_ssn" class="pure-input-1" value="{$entity/customer_ssn}"/>
			<input name="customer_organization_number" type="text" id="field_customer_organization_number" class="pure-input-1" value="{$entity/customer_organization_number}"/>
		</dd>
            </dl>
	</func:result>
</func:function>

<func:function name="phpgw:booking_customer_identifier_show" xmlns:php="http://php.net/xsl">
	<xsl:param name="entity"/>
	<xsl:param name="label" select="string('Invoice information')"/>

	<func:result>		
		<dt>
			<xsl:value-of select="php:function('lang', $label)" />
		</dt>
		<xsl:if test="$entity/customer_identifier_label">
			<dd>
				<xsl:value-of select="concat(php:function('lang', string($entity/customer_identifier_label)), ': ')"/><xsl:value-of select="$entity/customer_identifier_value"/>
			</dd>
		</xsl:if>
		
		<xsl:if test="not($entity/customer_identifier_label)">
			<dd><xsl:value-of select="php:function('lang', 'None')"/></dd>
		</xsl:if>
		
	</func:result>
</func:function>