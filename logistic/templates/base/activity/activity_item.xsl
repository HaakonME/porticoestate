<!-- $Id$ -->
<!-- item  -->

<xsl:template match="data" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format"><xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')"/></xsl:variable>

<xsl:call-template name="yui_phpgw_i18n"/>
<div class="yui-navset yui-navset-top">
		
	<xsl:choose>
		<xsl:when test="parent_activity/id &gt; 0">
			<h1> 
				<xsl:value-of select="parent_activity/name" disable-output-escaping="yes"/>::<xsl:value-of select="php:function('lang', 'Add sub activity')" />
			</h1>
		</xsl:when>
		<xsl:otherwise>
			<h1> 
				<xsl:value-of select="php:function('lang', 'Add activity')" />
			</h1>
		</xsl:otherwise>
	</xsl:choose>
	
	<div id="activity_details" class="content-wrp">
		<div id="details">
			<xsl:variable name="action_url">
				<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:logistic.uiactivity.save')" />
			</xsl:variable>
			<form action="{$action_url}" method="post">
				<input type="hidden" name="id" value = "{activity/id}" />
				<input type="hidden" name="project_id" value="{activity/project_id}" />
				<input type="hidden" name="parent_id" value="{parent_activity/id}" />
				
				<dl class="proplist-col">
					<xsl:if test="parent_activity/id &gt; 0">
					<dt>		
						<xsl:if test="editable">
							<div style="margin-bottom: 1em;width: 88%;" class="select-box">
								<label>Velg en annen hovedaktivitet</label>
								<select id="select_activity" name="parent_activity_id" class="selectLocation">
									<option>Velg aktivitet</option>
									<xsl:for-each select="activities">
					        	<option value="{id}">
					        		<xsl:if test="activity/parent_id = id">
						        		<xsl:attribute name="selected">
					    						selected
					   						</xsl:attribute>
						        	</xsl:if>
					          	<xsl:value-of disable-output-escaping="yes" select="name"/>
						        </option>
								  </xsl:for-each>
								</select>					
							</div>
							</xsl:if>
						</dt>
					</xsl:if>	
					<dt>
						<label for="name"><xsl:value-of select="php:function('lang','Activity name')" /></label>
					</dt>
					<dd>
					<xsl:choose>
						<xsl:when test="editable">
							<xsl:if test="activity/error_msg_array/name != ''">
								<xsl:variable name="error_msg"><xsl:value-of select="activity/error_msg_array/name" /></xsl:variable>
								<div class='input_error_msg'><xsl:value-of select="php:function('lang', $error_msg)" /></div>
							</xsl:if>
							<input type="text" name="name" id="name" value="{activity/name}" size="100"/>
							<span class="help_text line">Angi startdato for aktiviteten</span>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="activity/name" />
						</xsl:otherwise>
					</xsl:choose>
					</dd>
					<dt>
						<label for="description"><xsl:value-of select="php:function('lang', 'Description')" /></label>
					</dt>
					<dd>
					<xsl:choose>
						<xsl:when test="editable">
							<xsl:if test="activity/error_msg_array/description != ''">
								<xsl:variable name="error_msg"><xsl:value-of select="activity/error_msg_array/description" /></xsl:variable>
								<div class='input_error_msg'><xsl:value-of select="php:function('lang', $error_msg)" /></div>
							</xsl:if>
							<textarea id="description" name="description" rows="5" cols="60"><xsl:value-of select="activity/description" disable-output-escaping="yes"/></textarea>
							<span class="help_text line">Angi startdato for aktiviteten</span>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="activity/description" disable-output-escaping="yes"/>
						</xsl:otherwise>
					</xsl:choose>
					</dd>
					<dt>
						<label for="start_date">Startdato</label>
					</dt>
					<dd>
						<xsl:choose>
							<xsl:when test="editable">
								<xsl:if test="activity/error_msg_array/start_date != ''">
									<xsl:variable name="error_msg"><xsl:value-of select="activity/error_msg_array/start_date" /></xsl:variable>
									<div class='input_error_msg'><xsl:value-of select="php:function('lang', $error_msg)" /></div>
								</xsl:if>
								<input class="date" id="start_date" name="start_date" type="text">
						    	<xsl:if test="activity/start_date != ''">
						      	<xsl:attribute name="value"><xsl:value-of select="php:function('date', $date_format, number(activity/start_date))"/></xsl:attribute>
						    	</xsl:if>
					    	</input>
					    	<span class="help_text line">Angi startdato for aktiviteten</span>
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
								<xsl:if test="activity/error_msg_array/end_date != ''">
									<xsl:variable name="error_msg"><xsl:value-of select="activity/error_msg_array/end_date" /></xsl:variable>
									<div class='input_error_msg'><xsl:value-of select="php:function('lang', $error_msg)" /></div>
								</xsl:if>
								<input class="date" id="end_date" name="end_date" type="text">
						    	<xsl:if test="activity/end_date != ''">
						      	<xsl:attribute name="value"><xsl:value-of select="php:function('date', $date_format, number(activity/end_date))"/></xsl:attribute>
						    	</xsl:if>
					    	</input>
					    	<span class="help_text line">Angi startdato for aktiviteten</span>
							</xsl:when>
							<xsl:otherwise>
							<span><xsl:value-of select="php:function('date', $date_format, number(activity/end_date))"/></span>
							</xsl:otherwise>
						</xsl:choose>
					</dd>
					<dt>
						<label for="end_date">Ansvarlig</label>
					</dt>
					<dd>
						<xsl:choose>
							<xsl:when test="editable">
								<xsl:if test="activity/error_msg_array/responsible_user_id != ''">
									<xsl:variable name="error_msg"><xsl:value-of select="activity/error_msg_array/responsible_user_id" /></xsl:variable>
									<div class='input_error_msg'><xsl:value-of select="php:function('lang', $error_msg)" /></div>
								</xsl:if>
								<select name="responsible_user_id">
									<option value="">Velg ansvarlig bruker</option>
					        <xsl:for-each select="responsible_users">
					        	<xsl:variable name="full_name">
					        		<xsl:value-of disable-output-escaping="yes" select="account_firstname"/><xsl:text> </xsl:text>
					        		<xsl:value-of disable-output-escaping="yes" select="account_lastname"/>
					        	</xsl:variable>
					        	<xsl:choose>
					        		<xsl:when test="//activity/responsible_user_id = account_id">
												<option selected="selected" value="{account_id}">
					        				<xsl:value-of disable-output-escaping="yes" select="$full_name"/>
						        		</option>
					        		</xsl:when>
					        		<xsl:otherwise>
					        			<option value="{account_id}">
					        				<xsl:value-of disable-output-escaping="yes" select="$full_name"/>
						        		</option>
					        		</xsl:otherwise>
					        	</xsl:choose>
					        </xsl:for-each>
					      </select>
					      <span class="help_text line">Angi startdato for aktiviteten</span>
					      </xsl:when>
							<xsl:otherwise>
							<span><xsl:value-of select="activity/responsible_user_name"/></span>
							</xsl:otherwise>
						</xsl:choose>
					</dd>
				</dl>
				
				<div class="form-buttons">
					<xsl:choose>
						<xsl:when test="editable">
							<input type="submit" name="save_activity" value="{$lang_save}" title = "{$lang_save}" />
							<input type="submit" name="cancel_activity" value="{$lang_cancel}" title = "{$lang_cancel}" />
						</xsl:when>
						<xsl:otherwise>
							<xsl:variable name="params">
								<xsl:text>menuaction:logistic.uiactivity.edit, parent_id:</xsl:text>
								<xsl:value-of select="activity/id" />
							</xsl:variable>
							<xsl:variable name="edit_url">
								<xsl:value-of select="php:function('get_phpgw_link', '/index.php', $params )" />
							</xsl:variable>
							<a class="btn" href="{$edit_url}"><xsl:value-of select="php:function('lang', 'edit')" /></a>
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
