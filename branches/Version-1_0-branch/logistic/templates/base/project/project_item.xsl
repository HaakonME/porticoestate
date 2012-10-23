<!-- $Id$ -->
<!-- item  -->

<xsl:template match="data" xmlns:php="http://php.net/xsl">

<xsl:call-template name="yui_phpgw_i18n"/>
<div class="yui-navset yui-navset-top">
	
	<h1>
			<xsl:value-of select="php:function('lang', 'Project')" />
	</h1>
	
	<div id="project_details" class="content-wrp">
		<div id="details">
			<xsl:variable name="action_url">
				<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:logistic.uiproject.save')" />
			</xsl:variable>
			<form action="{$action_url}" method="post">
				<input type="hidden" name="id" value = "{value_id}">
				</input>
				<dl class="proplist-col">
					<dt>
						<label for="name"><xsl:value-of select="php:function('lang','Project title')" /></label>
					</dt>
					<dd>
					<xsl:choose>
						<xsl:when test="editable">
							<xsl:if test="project/error_msg_array/name != ''">
								<xsl:variable name="error_msg"><xsl:value-of select="project/error_msg_array/name" /></xsl:variable>
								<div class='input_error_msg'><xsl:value-of select="php:function('lang', $error_msg)" /></div>
							</xsl:if>
							<div style="margin-left:0; margin-bottom: 3px;" class="help_text line">Angi startdato for aktiviteten</div>
							<input type="text" name="name" id="name" value="{project/name}" size="100"/>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="project/name" />
						</xsl:otherwise>
					</xsl:choose>
					</dd>
					<dt>
						<label for="project_type"><xsl:value-of select="php:function('lang','Project_type')" /></label>
					</dt>
					<dd>
					<xsl:choose>
						<xsl:when test="editable">
							<xsl:if test="project/error_msg_array/project_type_id != ''">
								<xsl:variable name="error_msg"><xsl:value-of select="project/error_msg_array/project_type_id" /></xsl:variable>
								<div class='input_error_msg'><xsl:value-of select="php:function('lang', $error_msg)" /></div>
							</xsl:if>
							<div style="margin-left:0; margin-bottom: 3px;" class="help_text line">Angi startdato for aktiviteten</div>
							<select id="project_type_id" name="project_type_id">
								<xsl:apply-templates select="options"/>
							</select>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="project/project_type_label" />
						</xsl:otherwise>
					</xsl:choose>
					</dd>
					<dt>
						<label for="description"><xsl:value-of select="php:function('lang', 'Description')" /></label>
					</dt>
					<dd>
					<xsl:choose>
						<xsl:when test="editable">
							<xsl:if test="project/error_msg_array/description != ''">
								<xsl:variable name="error_msg"><xsl:value-of select="project/error_msg_array/description" /></xsl:variable>
								<div class='input_error_msg'><xsl:value-of select="php:function('lang', $error_msg)" /></div>
							</xsl:if>
							<div style="margin-left:0; margin-bottom: 3px;" class="help_text line">Angi startdato for aktiviteten</div>
							<textarea id="description" name="description" rows="5" cols="60"><xsl:value-of select="project/description" disable-output-escaping="yes"/></textarea>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="project/description" disable-output-escaping="yes"/>
						</xsl:otherwise>
					</xsl:choose>
					</dd>
				</dl>

				<div class="form-buttons">
					<xsl:choose>
						<xsl:when test="editable">
							<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
							<xsl:variable name="lang_cancel"><xsl:value-of select="php:function('lang', 'cancel')" /></xsl:variable>
							<input type="submit" name="save_project" value="{$lang_save}" title = "{$lang_save}" />
							<input type="submit" name="cancel_project" value="{$lang_cancel}" title = "{$lang_cancel}" />
						</xsl:when>
						<xsl:otherwise>
							<xsl:variable name="lang_edit"><xsl:value-of select="php:function('lang', 'edit')" /></xsl:variable>
							<xsl:variable name="lang_new_activity"><xsl:value-of select="php:function('lang', 't_new_activity')" /></xsl:variable>
							<input type="submit" name="edit_project" value="{$lang_edit}" title = "{$lang_edit}" />
							<input type="submit" name="new_activity" value="{$lang_new_activity}" title = "{$lang_new_activity}" />
						</xsl:otherwise>
					</xsl:choose>
				</div>
			</form>
		</div>
	</div>
</div>
</xsl:template>

<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected">
			<xsl:attribute name="selected" value="selected" />
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>
