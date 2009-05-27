<xsl:preserve-space elements="data"/>

<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <h3><xsl:value-of select="php:function('lang', 'Showing')" />: <xsl:value-of select="data/name"/></h3>

		<div id="composite_edit_tabview" class="yui-navset">
			<xsl:value-of disable-output-escaping="yes" select="tabs" />
			<div class="yui-content">
				
				<div id="details">
					<form action="#" method="post">
						<dl class="proplist-col">
							<dt>
								<label for="name"><xsl:value-of select="php:function('lang', 'Name')" /></label>
							</dt>
							<dd>
								<input type="text" name="name" id="name">
									<xsl:attribute name="value"><xsl:value-of select="data/name"/></xsl:attribute>
								</input>
							</dd>
							
							<dt><xsl:value-of select="php:function('lang', 'Address')" /></dt>
							<dd>
								<xsl:value-of select="data/adresse1"/>
								<xsl:if test="data/adresse2 != ''">
									<br /><xsl:value-of select="data/adresse2"/>
								</xsl:if>
								<xsl:if test="data/postnummer != ''">
									<br /><xsl:value-of select="data/postnummer"/>&#160;<xsl:value-of select="data/poststed"/>
								</xsl:if>
							</dd>
							
							<dt>
								<label for="address_1"><xsl:value-of select="php:function('lang', 'Address')" /></label>
								/ <label for="house_number"><xsl:value-of select="php:function('lang', 'Number')" /></label>
							</dt>
							<dd>
								<input type="text" name="address_1" id="address_1">
									<xsl:attribute name="value"><xsl:value-of select="data/address_1"/></xsl:attribute>
								</input>
								<input type="text" name="house_number" id="house_number">
									<xsl:attribute name="value"><xsl:value-of select="data/house_number"/></xsl:attribute>
								</input>
							</dd>
							<dd>
								<input type="text" name="address_2" id="address_2">
									<xsl:attribute name="value"><xsl:value-of select="data/address_2"/></xsl:attribute>
								</input>
							</dd>
							
							<dt>
								<label for="postcode"><xsl:value-of select="php:function('lang', 'Postcode')" /></label> / <label for="place"><xsl:value-of select="php:function('lang', 'Place')" /></label>
							</dt>
							<dd>
								<input type="text" name="postcode" id="postcode" class="postcode">
									<xsl:attribute name="value"><xsl:value-of select="data/postcode"/></xsl:attribute>
								</input>
								<input type="text" name="place" id="place">
									<xsl:attribute name="value"><xsl:value-of select="data/place"/></xsl:attribute>
								</input>
							</dd>
						</dl>
						
						<dl class="proplist-col">
							<dt><xsl:value-of select="php:function('lang', 'Number')" /></dt>
							<dd><xsl:value-of select="data/composite_id"/></dd>
							<dt><xsl:value-of select="php:function('lang', 'Area')" /></dt>
							<dd><xsl:value-of select="data/area"/> m<sup>2</sup></dd>
							<dt><xsl:value-of select="php:function('lang', 'Property id')" /></dt>
							<dd><xsl:value-of select="data/gab_id"/></dd>
							
							<dt>
								<label for="is_active"><xsl:value-of select="php:function('lang', 'Is active')" /></label>
							</dt>
							<dd>
								<input type="checkbox" name="is_active" id="is_active">
									<xsl:if test="data/is_active = 1">
										<xsl:attribute name="checked">checked</xsl:attribute>
									</xsl:if>
								</input>
							</dd>
						</dl>
						
						<dl class="rental-description-edit">
							<dt>
								<label for="description"><xsl:value-of select="php:function('lang', 'Description')" /></label>
							</dt>
							<dd>
								<textarea name="description" id="description" rows="10" cols="50">
									<xsl:value-of select="data/description"/>
								</textarea>
							</dd>
						</dl>
						
						<div class="form-buttons">
							<input type="submit">
								<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Save')"/></xsl:attribute>
							</input>
							<a class="cancel">
			        	<xsl:attribute name="href"><xsl:value-of select="cancel_link"></xsl:value-of></xsl:attribute>
			       		<xsl:value-of select="php:function('lang', 'Cancel')"/>
			        </a>
						</div>
					</form>
				</div>
				
				<div id="elements">
					<p>elementer</p>
				</div>
				
				<div id="contracts">
					<p>kontrakter</p>
				</div>
				
				<div id="documents">
					<p>dokumenter</p>
				</div>
			</div>
		</div>
</xsl:template>
