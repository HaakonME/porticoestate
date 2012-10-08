<!-- $Id: activity_item.xsl 10096 2012-10-03 07:10:49Z vator $ -->
<!-- item  -->

<xsl:template match="data" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format"><xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')"/></xsl:variable>

<xsl:call-template name="yui_phpgw_i18n"/>
<div class="yui-navset yui-navset-top">
	<div style="clear: both;margin-bottom: 0;overflow: hidden;padding: 1em;" class="identifier-header">
		<xsl:choose>
			<xsl:when test="activity/id &gt 0">
				<h1 style="float:left;"> 
					<span>
						<xsl:value-of select="php:function('lang', 'Add requirement to activity')" />
					</span>
					<span>
						{activity/name}
					</span>
				</h1>
			</xsl:when>
			<xsl:otherwise>
				<h1 style="float:left;"> 
					<xsl:value-of select="php:function('lang', 'Add requirement')" />
				</h1>
			</xsl:otherwise>
		</xsl:choose>
	</div>

	<div class="yui-content" style="padding: 20px;">
		<div id="details">
			<form action="#" method="post">
				<input type="hidden" name="id" value = "{activity/id}" />
				<input type="hidden" name="project_id" value="{activity/project_id}" />
				<input type="hidden" name="parent_id" value="{parent_activity/id}" />
				
				<dl class="proplist-col">
					<dt>
						<label for="start_date">Startdato</label>
					</dt>
					<dd>
						<xsl:choose>
							<xsl:when test="editable">
								<input class="date" id="start_date" name="start_date" type="text">
						    	<xsl:if test="activity/start_date != ''">
						      	<xsl:attribute name="value"><xsl:value-of select="php:function('date', $date_format, number(activity/start_date))"/></xsl:attribute>
						    	</xsl:if>
					    	</input>	
							</xsl:when>
							<xsl:otherwise>
							<span><xsl:value-of select="php:function('date', $date_format, number(activity/start_date))"/></span>
							</xsl:otherwise>
						</xsl:choose>
					</dd>
					<dt>
						<label for="end_date">Sluttdato</label>
					</dt>
					<dd>
						<xsl:choose>
							<xsl:when test="editable">
								<input class="date" id="end_date" name="end_date" type="text">
						    	<xsl:if test="activity/end_date != ''">
						      	<xsl:attribute name="value"><xsl:value-of select="php:function('date', $date_format, number(activity/end_date))"/></xsl:attribute>
						    	</xsl:if>
					    	</input>	
							</xsl:when>
							<xsl:otherwise>
							<span><xsl:value-of select="php:function('date', $date_format, number(activity/end_date))"/></span>
							</xsl:otherwise>
						</xsl:choose>
					</dd>
					<xsl:choose>
						<xsl:when test="editable">
							<dt>
								<label>BIM</label>
							</dt>
							<dd>
								<select name="location_id" id="location_id">
									<xsl:for-each select="entity_list">
										<option value="{id}">
											<xsl:value-of select="name"/>
										</option>
									</xsl:for-each>
								</select>
							</dd>
							<dt>
								<label>BIM2</label>
							</dt>
							<dd>
								<select name="categories" id="categories">
								</select>
							</dd>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="entity/name" />
							<xsl:value-of select="category/name" />
							<xsl:for-each select="attributes">
								<xsl:value-of select="name" /><br/>
							</xsl:for-each>
						</xsl:otherwise>
					</xsl:choose>
					<dt>
						<label for="no_of_items">Antall</label>
					</dt>
					<dd>
						<xsl:choose>
							<xsl:when test="editable">
								<input style="width: 20px;" id="no_of_items" name="no_of_items" type="text" />
							</xsl:when>
							<xsl:otherwise>
							<span>{requirement/no_of_items}</span>
							</xsl:otherwise>
						</xsl:choose>
					</dd>
				</dl>
				
				<div class="form-buttons">
					<xsl:choose>
						<xsl:when test="editable">
							<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
							<xsl:variable name="lang_cancel"><xsl:value-of select="php:function('lang', 'cancel')" /></xsl:variable>
							<input type="submit" name="save_activity" value="{$lang_save}" title = "{$lang_save}" />
							<input type="submit" name="cancel_activity" value="{$lang_cancel}" title = "{$lang_cancel}" />
						</xsl:when>
						<xsl:otherwise>
							<xsl:variable name="lang_edit"><xsl:value-of select="php:function('lang', 'edit')" /></xsl:variable>
							<input type="submit" name="edit_activity" value="{$lang_edit}" title = "{$lang_edit}" />
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
