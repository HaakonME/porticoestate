	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="blablabla">
				<xsl:apply-templates select="email_list"/>
			</xsl:when>
			<xsl:when test="uimessage">
				<xsl:apply-templates select="uimessage"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="index"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="email_list">
		<xsl:call-template name="app_header"/>
		<p>
		Dude I am Clueless
		</p>
	</xsl:template>
	
	<xsl:template match="uimessage">
		<xsl:call-template name="app_header"/>
		<xsl:value-of disable-output-escaping="yes" select="email_page"/>
	</xsl:template>
	
	<xsl:template match="index">
		<xsl:call-template name="app_header"/>
		<xsl:value-of disable-output-escaping="yes" select="widget_toolbar"/>
		<xsl:value-of disable-output-escaping="yes" select="V_arrows_form_table"/>
		<xsl:value-of disable-output-escaping="yes" select="stats_data_display"/>
		<table border="0" cellpadding="4" cellspacing="1" width="95%" align="center">
		<tr class="th">
			<td class="th_text" width="3%" align="center">
				&nbsp;
			</td>
			<td class="th_text" width="2%">
				&nbsp;
			</td>
			<td class="th_text" width="20%">
				<strong><xsl:value-of select="hdr_from"/></strong>
			</td>
			<td class="th_text" width="39%">
				<strong><xsl:value-of select="hdr_subject"/></strong>
			</td>
			<td class="th_text" width="10%" align="center">
				<strong><xsl:value-of select="hdr_date"/></strong>
			</td>
			<td class="th_text" width="4%" align="center">
				<strong><small><xsl:value-of select="hdr_size"/></small></strong>
			</td>
		</tr>
		</table>
		<xsl:value-of disable-output-escaping="yes" select="email_page"/>
	</xsl:template>
	
	

	<xsl:template match="msg_list_dsp">
		<xsl:variable name="mlist_checkbox_name"><xsl:value-of disable-output-escaping="yes" select="mlist_checkbox_name"/></xsl:variable>
		<xsl:variable name="uri"><xsl:value-of disable-output-escaping="yes" select="uri"/></xsl:variable>
		<xsl:variable name="subject_link"><xsl:value-of disable-output-escaping="yes" select="subject_link"/></xsl:variable>
		<tr>
			<xsl:attribute name="class">
				<xsl:choose>
					<xsl:when test="@class">
						<xsl:value-of select="@class"/>
					</xsl:when>
					<xsl:when test="position() mod 2 = 0">
						<xsl:text>row_off</xsl:text>
					</xsl:when>
					<xsl:otherwise>
						<xsl:text>row_on</xsl:text>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:attribute>
			<td align="center">
				<xsl:value-of disable-output-escaping="yes" select="V_mlist_form_init"/>
				<input type="checkbox" name="delmov_list" value="uri">
			</td>
			<td align="center">
				<xsl:choose>
					<xsl:when test="has_attachment">
						<xsl:value-of select="mlist_attach"/>
					</xsl:when>
					<xsl:otherwise>
						<xsl:text>  </xsl:text>
					</xsl:otherwise>
				</xsl:choose>
			</td>
			<td align="left">
				<xsl:choose>
					<xsl:when test="is_unseen">
						<strong>
						<xsl:value-of select="from_name"/>
						<xsl:value-of select="display_address_from"/>
						</strong>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="from_name"/>
						<xsl:value-of select="display_address_from"/>
					</xsl:otherwise>
				</xsl:choose>
			</td>
			<td align="left">
				<xsl:choose>
					<xsl:when test="is_unseen">
						<strong>
						<a href="{$subject_link}"><xsl:value-of select="subject"/></a>
						</strong>
					</xsl:when>
					<xsl:otherwise>
						<a href="{$subject_link}"><xsl:value-of select="subject"/></a>
					</xsl:otherwise>
				</xsl:choose>
			</td>
			<td align="center">
				<xsl:value-of select="msg_date"/>
			</td>
			<td align="center">
				<small><xsl:value-of select="size"/></small>
			</td>
		</tr>
	</xsl:template>

