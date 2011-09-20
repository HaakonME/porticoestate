<!-- separate tabs and  inline tables-->

<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div class="yui-navset yui-navset-top" id="example_tabview">
		<xsl:value-of disable-output-escaping="yes" select="tabs" />
		<div class="yui-content">
			<div id="details">
				<xsl:call-template name="control" />
			</div>
			<div id="list">
				<h4><xsl:value-of select="php:function('lang', 'list')" /></h4>
			</div>
			<div id="list">
				<h4><xsl:value-of select="php:function('lang', 'dates')" /></h4>
				<xsl:value-of disable-output-escaping="yes" select="date"/>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		var resource_id = <xsl:value-of select="resource/id"/>;
		var lang = <xsl:value-of select="php:function('js_lang', 'Name', 'Category', 'Actions', 'Edit', 'Delete', 'Account', 'Role')"/>;
	</script>
</xsl:template>

<xsl:template name="control" xmlns:php="http://php.net/xsl">

<xsl:call-template name="yui_booking_i18n"/>
<div class="identifier-header">
<h1><img src="{img_go_home}" /> 
		<xsl:value-of select="php:function('lang', 'Control')" />
</h1>
</div>

<div class="yui-content">
		<div id="details">
			<form action="#" method="post">
				<input type="hidden" name="id" value = "{value_id}">
				</input>
				<dl class="proplist-col">
					<dt>
						<label for="title">Tittel</label>
					</dt>
					<dd>
						<input type="text" name="title" id="title" value="" />
					</dd>
					<dt>
						<label for="description">Beskrivelse</label>
					</dt>
					<dd>
						<textarea cols="70" rows="5" name="description" id="description" value=""></textarea>
					</dd>
					<dt>
						<label for="start_date">Startdato</label>
					</dt>
					<dd>
						<xsl:value-of disable-output-escaping="yes" select="date"/>
					</dd>
					<dt>
						<label for="end_date">Sluttdato</label>
					</dt>
					<dd>
						<xsl:value-of disable-output-escaping="yes" select="date"/>
					</dd>
					<dt>
						<label>Frekvenstype</label>
					</dt>
					<dd>
						<select id="repeat_type" name="repeat_type">
							<option value="0">Ikke angitt</option>
							<option value="1">Daglig</option>
							<option value="2">Ukentlig</option>
							<option value="3">Månedlig pr dato</option>
							<option value="4">Månedlig pr dag</option>
							<option value="5">Årlig</option>
						</select>
					</dd>
					<dt>
						<label>Frekvens</label>
					</dt>
					<dd>
						<input size="2" type="text" name="repeat_interval" value="" />
					</dd>
					<dt>
						<label>Prosedyre</label>
					</dt>
					<dd>
						<select id="procedure" name="procedure">
							<xsl:apply-templates select="procedure_options_array/options"/>
						</select>
					</dd>
				</dl>
				
				<div class="form-buttons">
					<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
					<input type="submit" name="save_control" value="{$lang_save}" title = "{$lang_save}">
					</input>
				</div>
				
			</form>
						
		</div>
	</div>
</xsl:template>
	
<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected != 0">
			<xsl:attribute name="selected" value="selected" />
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>

