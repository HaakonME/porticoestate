<func:function name="phpgw:conditional">
	<xsl:param name="test"/>
	<xsl:param name="true"/>
	<xsl:param name="false"/>

	<func:result>
		<xsl:choose>
			<xsl:when test="$test">
	        	<xsl:value-of select="$true"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$false"/>
			</xsl:otherwise>
		</xsl:choose>
  	</func:result>
</func:function>

<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="yui_booking_i18n"/>
	<div id="content">
		<ul class="pathway">
			<li>
				<a href="{reservation/reservations_link}">
					<xsl:value-of select="php:function('lang', 'Completed Reservations')" />
				</a>
			</li>
			<li>
				<xsl:value-of select="php:function('lang', string(reservation/reservation_type))"/>
			</li>
			<li>
				<xsl:value-of select="reservation/id"/>
			</li>
		</ul>
		
		<dl class="proplist-col">
			<dt><xsl:value-of select="php:function('lang', 'Cost')" /></dt>
			<dd><xsl:value-of select="reservation/cost"/></dd>
			
			<dt><xsl:value-of select="php:function('lang', 'Customer Type')" /></dt>
			<dd><xsl:value-of select="reservation/customer_type"/></dd>
			
			<xsl:copy-of select="phpgw:booking_customer_identifier_show(reservation, 'Customer ID')"/>
		</dl>

		<dl class="proplist-col">
			<dt><xsl:value-of select="php:function('lang', 'Exported')" /></dt>
			<dd><xsl:value-of select="phpgw:conditional(not(reservation/exported = ''), string('Yes'), string('No'))"/></dd>
			
			<dt><xsl:value-of select="php:function('lang', 'From')" /></dt>
			<dd><xsl:value-of select="reservation/from_"/></dd>
			
			<dt><xsl:value-of select="php:function('lang', 'To')" /></dt>
			<dd><xsl:value-of select="reservation/to_"/></dd>
		</dl>
		
		<dl class="proplist-col">
			<dt><xsl:value-of select="php:function('lang', 'Season')" /></dt>
			<xsl:choose>
				<xsl:when test="reservation/season_name">
					<dd><a href="{reservation/season_link}"><xsl:value-of select="reservation/season_name"/></a></dd>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="php:function('lang', 'N/A')" />
				</xsl:otherwise>
			</xsl:choose>
			
			<dt><xsl:value-of select="php:function('lang', 'Organization')" /></dt>
			<xsl:choose>
				<xsl:when test="reservation/organization_name">
					<dd><a href="{reservation/organization_link}"><xsl:value-of select="reservation/organization_name"/></a></dd>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="php:function('lang', 'N/A')" />
				</xsl:otherwise>
			</xsl:choose>
		</dl>
		
		<dl class="proplist">
			<dt><xsl:value-of select="php:function('lang', 'Article Description')" /></dt>
			<div class="description"><xsl:value-of select="reservation/article_description" disable-output-escaping="yes"/></div>
		</dl>
		
		<dl class="proplist">
			<dt><xsl:value-of select="php:function('lang', 'Description')" /></dt>
			<div class="description"><xsl:value-of select="reservation/description" disable-output-escaping="yes"/></div>
		</dl>

		<div class="form-buttons">
			<xsl:if test="not(reservation/exported) or reservation/exported = ''">
				<button onclick="window.location.href='{reservation/edit_link}'">
					<xsl:value-of select="php:function('lang', 'Edit')" />
				</button>
			</xsl:if>
			
			<button onclick='window.location.href="{reservation/reservation_link}"'>
				<xsl:value-of select="php:function('ucfirst', php:function('lang', string(reservation/reservation_type)))" />
			</button>
			
			<!-- TODO: Add application_link where necessary -->
			<button onclick='window.location.href="{reservation/application_link}"' disabled="1">
				<xsl:value-of select="php:function('lang', 'Application')" />
			</button>
		</div>
	</div>
</xsl:template>
