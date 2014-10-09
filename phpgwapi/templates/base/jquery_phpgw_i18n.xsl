	<xsl:template name="jquery_phpgw_i18n" xmlns:php="http://php.net/xsl">
		<xsl:if test="jquery_phpgw_i18n">
			<script type="text/javascript">
				JqueryPortico.i18n = {
				<xsl:for-each select="jquery_phpgw_i18n/*">
					<xsl:value-of select="local-name()"/>: function(cfg)
					{
						cfg = cfg || {};
						<xsl:for-each select="./*">
							<xsl:choose>
								<xsl:when test="local-name() != '_'">
									cfg["<xsl:value-of select="local-name()"/>"] = <xsl:value-of disable-output-escaping="yes" select="."/>;
								</xsl:when>
								<xsl:otherwise>
									cfg = <xsl:value-of disable-output-escaping="yes" select="."/>;
								</xsl:otherwise>
							</xsl:choose>
						</xsl:for-each>
						return cfg;
					}<xsl:value-of select="phpgw:conditional(not(position() = last()), ',', '')"/>
				</xsl:for-each>
				};
			</script>
		</xsl:if>

	</xsl:template>
