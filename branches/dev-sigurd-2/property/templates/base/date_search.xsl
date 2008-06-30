<!-- $Id: date_search.xsl,v 1.2 2007/01/04 14:36:16 sigurdne Exp $ -->

	<xsl:template name="app_data">
		<xsl:apply-templates select="date_search"/>
	</xsl:template>
	
	<xsl:template match="date_search">
		<script LANGUAGE="JavaScript">
			function ExchangeDate(thisform)
			{
				opener.document.search.start_date.value = thisform.elements[0].value;
				opener.document.search.end_date.value = thisform.elements[1].value;
				window.close()
			}
		</script>

		<form method="post" name="form" action="">
		
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_start_date"/>
				</td>
				<td>
					<input type="text" id="start_date" name="start_date" size="10" value="{value_start_date}" readonly="readonly" onMouseout="window.status='';return true;" >
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_start_date_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<img id="start_date-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;" />

				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_end_date"/>
				</td>
				<td>
					<input type="text" id="end_date" name="end_date" size="10" value="{value_end_date}" readonly="readonly" onMouseout="window.status='';return true;" >
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_end_date_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<img id="end_date-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;" />
				</td>
			</tr>
			<tr>
				<td class="small_text" valign="top">
					<xsl:variable name="lang_submit"><xsl:value-of select="lang_submit"/></xsl:variable>
					<input type="button" name="convert" value="{$lang_submit}" onClick="ExchangeDate(this.form);" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_submit_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
		</table>
		</form>
	</xsl:template>
