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
	<div id="content">
		<dl class="form">
			<dt class="heading"><xsl:value-of select="php:function('lang', phpgw:conditional(new_form, 'Add', 'Edit'))"/><xsl:text> </xsl:text><xsl:value-of select="php:function('lang', 'Account Codes')"/></dt>
		</dl>

		<xsl:call-template name="msgbox"/>
		<xsl:call-template name="yui_booking_i18n"/>

		<form action="" method="POST">
			
			<dl class="form-col">
				<dt><label for="field_name"><xsl:value-of select="php:function('lang', 'Name')" /></label></dt>
				<dd><input name="name" type="text" id="field_name" value="{account_code_set/name}"/></dd>
			</dl>
			
			<div class="clr"/>
			
			<dl class="form-col">
				<dt><label for="field_object_number"><xsl:value-of select="php:function('lang', 'Object No.')" /></label></dt>
				<dd><input name="object_number" type="text" id="field_object_number" value="{account_code_set/object_number}" maxlength='8'/></dd>

				<dt><label for="field_article"><xsl:value-of select="php:function('lang', 'Article')" /></label></dt>
				<dd><input name="article" type="text" id="field_article" value="{account_code_set/article}" maxlength='15'/></dd>

				<dt><label for="field_unit_number"><xsl:value-of select="php:function('lang', 'Unit No.')" /></label></dt>
				<dd><input name="unit_number" type="text" id="field_unit_number" value="{account_code_set/unit_number}" maxlength='12'/></dd>

				<dt><label for="field_unit_prefix"><xsl:value-of select="php:function('lang', 'Unit Prefix')" /></label></dt>
				<dd><input name="unit_prefix" type="text" id="field_unit_prefix" value="{account_code_set/unit_prefix}" maxlength='1'/></dd>
			</dl>

			<dl class="form-col">
				<dt><label for="field_active"><xsl:value-of select="php:function('lang', 'Active')"/></label></dt>
				<dd>
					<select id="field_active" name="active">
						<xsl:if test="new_form">
							<xsl:attribute name="disabled">disabled</xsl:attribute>
						</xsl:if>
						
						<option value="1">
							<xsl:if test="account_code_set/active=1">
								<xsl:attribute name="selected">selected</xsl:attribute>
							</xsl:if>
							<xsl:value-of select="php:function('lang', 'Active')"/>
						</option>
						<option value="0">
							<xsl:if test="account_code_set/active=0">
								<xsl:attribute name="selected">selected</xsl:attribute>
							</xsl:if>
							<xsl:value-of select="php:function('lang', 'Inactive')"/>
						</option>
					</select>
				</dd>

				<dt><label for="field_responsible_code"><xsl:value-of select="php:function('lang', 'Responsible Code')" /></label></dt>
				<dd><input name="responsible_code" type="text" id="field_responsible_code" value="{account_code_set/responsible_code}" maxlength='6'/></dd>

				<dt><label for="field_service"><xsl:value-of select="php:function('lang', 'Service')" /></label></dt>
				<dd><input name="service" type="text" id="field_service" value="{account_code_set/service}" maxlength='8'/></dd>

				<dt><label for="field_project_number"><xsl:value-of select="php:function('lang', 'Project No.')" /></label></dt>
				<dd><input name="project_number" type="text" id="field_project_number" value="{account_code_set/project_number}" maxlength='12'/></dd>	
			</dl>

			<div class="clr"/>
			<dl class="form">
				<dt><label for="field_invoice_instruction"><xsl:value-of select="php:function('lang', 'Invoice instruction')" /></label></dt>
				<dd>
					<textarea id="field_invoice_instruction" class="full-width" name="invoice_instruction"><xsl:value-of select="account_code_set/invoice_instruction"/></textarea>
				</dd>
			</dl>

			<div class="clr"/>

			<div class="form-buttons">
				<input type="submit" value="{php:function('lang', phpgw:conditional(new_form, 'Create', 'Save'))}"/>
				<a class="cancel" href="{account_code_set/cancel_link}">
					<xsl:value-of select="php:function('lang', 'Cancel')" />
				</a>
			</div>
		</form>
	</div>
</xsl:template>


