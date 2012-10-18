<!-- $Id: activity_item.xsl 10096 2012-10-03 07:10:49Z vator $ -->
<!-- item  -->

<xsl:template name="requirement_values" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format"><xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')"/></xsl:variable>

<div class="yui-content" style="padding: 20px;">
	<div id="details">
		<form id="frm-requirement-values" action="#" method="post">
			<input type="hidden" name="requirement_id" value = "{requirement/id}" />
										
			<dl class="proplist-col">
				<xsl:choose>
					<xsl:when test="editable">
					<dt>
						<label>Legg til kriterier</label>
					</dt>
						<dd>
						<div id="attributes">
							<xsl:for-each select="requirement_attributes_array">
								<div class="attribute">
									<xsl:choose>
										<xsl:when test="cust_attribute/column_info/type = 'T'">
												<label><xsl:value-of select="cust_attribute/input_text"/></label>
												<input class="operator" type='hidden' name="operator" value='eq' />
												<input class="attrib_info" type='text' name="{cust_attribute/column_name}" value='{attrib_value}' />
										</xsl:when>
										<xsl:when test="cust_attribute/column_info/type = 'V' or cust_attribute/column_info/type = 'I'">
											<label><xsl:value-of select="cust_attribute/input_text"/></label>
											
											<xsl:choose>
												<xsl:when test="operator = 'btw'">
													<xsl:variable name="gt-attrib-value"><xsl:value-of select="substring-before(attrib_value, ':')" /></xsl:variable>
													<input class="constraint_1" style="margin-right: 10px;" type='text' name="{cust_attribute/column_name}" value="{$gt-attrib-value}" />
												</xsl:when>
												<xsl:otherwise>
													<xsl:variable name="gt-attrib-value"><xsl:value-of select="substring-before(attrib_value, ':')" /></xsl:variable>
													<input class="constraint_1" style="margin-right: 10px;display:none;" type='text' name="{cust_attribute/column_name}" value="{$gt-attrib-value}" />
												</xsl:otherwise>
											</xsl:choose>
																								
											<select class="operator" name="operator">
												<xsl:choose>
													<xsl:when test="operator = 'eq'">
														<option selected='true' value="eq"><xsl:text>Lik</xsl:text></option>	
													</xsl:when>
													<xsl:otherwise>
														<option value="eq"><xsl:text>Lik</xsl:text></option>
													</xsl:otherwise>
												</xsl:choose>
												<xsl:choose>
													<xsl:when test="operator = 'lt'">
														<option selected='true' value="lt"><xsl:text>Mindre enn</xsl:text></option>	
													</xsl:when>
													<xsl:otherwise>
														<option value="lt"><xsl:text>Mindre enn</xsl:text></option>
													</xsl:otherwise>
												</xsl:choose>
												<xsl:choose>
													<xsl:when test="operator = 'gt'">
														<option selected='true' value="gt"><xsl:text>Større enn</xsl:text></option>
													</xsl:when>
													<xsl:otherwise>
														<option value="gt"><xsl:text>Større enn</xsl:text></option>
													</xsl:otherwise>
												</xsl:choose>
												<xsl:choose>
													<xsl:when test="operator = 'btw'">
														<option selected='true' value="btw"><xsl:text>Mellom</xsl:text></option>
													</xsl:when>
													<xsl:otherwise>
														<option value="btw"><xsl:text>Mellom</xsl:text></option>
													</xsl:otherwise>
												</xsl:choose>
											</select>
											
											<xsl:choose>
												<xsl:when test="operator = 'btw'">
													<xsl:variable name="lt-attrib-value"><xsl:value-of select="substring-after(attrib_value, ':')" /></xsl:variable>
													<input class="constraint_2" style="margin-left: 10px;" type='text' name="{cust_attribute/column_name}" value="{$lt-attrib-value}" />
												</xsl:when>
												<xsl:otherwise>
													<input class="attrib_info" style="margin-left: 10px;" type='text' name="{cust_attribute/column_name}" value="{attrib_value}" />
												</xsl:otherwise>
											</xsl:choose>
										</xsl:when>
										<xsl:when test="cust_attribute/column_info/type = 'LB'">
												<label><xsl:value-of select="cust_attribute/input_text"/></label>
												<input class="operator" type='hidden' name="operator" value='eq' />
												<select class="attrib_info" name="{cust_attribute/column_name}">
													<xsl:for-each select="cust_attribute/choice">
														<xsl:choose>
															<xsl:when test="value = //attrib_value">
																<option selected='true' value="{value}">
																	<xsl:value-of select="value"/>
																</option>
															</xsl:when>
															<xsl:otherwise>
																<option value="{value}">
																	<xsl:value-of select="value"/>
																</option>
															</xsl:otherwise>	
														</xsl:choose>
													</xsl:for-each>
												</select>
										</xsl:when>
									</xsl:choose>
									<input type="hidden" class="cust_attribute_id" name="cust_attribute_id" value="{cust_attribute/id}" />
									<input type="hidden" class="cust_attributes" name="cust_attributes[]" value="" />
								</div>
							</xsl:for-each>
						</div>
						</dd>					
					</xsl:when>
					<xsl:otherwise>
					<dt>
						<label>Kriterier for behovet</label>
					</dt>
						<dd>
						<div id="attributes">
							<xsl:for-each select="requirement_attributes_array">
								<div class="attribute">
									<label style="margin-left:10px;"><xsl:value-of select="cust_attribute/input_text" /></label>
									<xsl:choose>
										<xsl:when test="cust_attribute/column_info/type = 'T'">
											<span style="margin-left:10px;"><xsl:value-of select="attrib_value" /></span>
										</xsl:when>
										<xsl:when test="cust_attribute/column_info/type = 'V' or cust_attribute/column_info/type = 'I'">
					 
												<xsl:if test="operator = 'btw'">
													<span style="margin-left:10px;"><xsl:value-of select="substring-before(attrib_value, ':')" /></span>
												</xsl:if>
										
												<xsl:choose>
													<xsl:when test="operator = 'eq'">
														<span style="margin-left:10px;">Lik</span>
													</xsl:when>
													<xsl:when test="operator = 'gt'">
														<span style="margin-left:10px;">Større enn</span>
													</xsl:when>
													<xsl:when test="operator = 'lt'">
														<span style="margin-left:10px;">Mindre enn</span>
													</xsl:when>
													<xsl:when test="operator = 'btw'">
														<span style="margin-left:10px;">Mellom</span>
													</xsl:when>
												</xsl:choose>
												
											<xsl:choose>
												<xsl:when test="operator = 'btw'">
												<span style="margin-left:10px;"><xsl:value-of select="substring-after(attrib_value, ':')" /></span>
												</xsl:when>
												<xsl:otherwise>
														<span style="margin-left:10px;"><xsl:value-of select="attrib_value" /></span>
												</xsl:otherwise>
											</xsl:choose>
					
										</xsl:when>
										<xsl:when test="cust_attribute/column_info/type = 'LB'">
											<span style="margin-left:10px;"><xsl:value-of select="attrib_value" /></span>
										</xsl:when>
									</xsl:choose>
								</div>
							</xsl:for-each>
						</div>
						</dd>	
					</xsl:otherwise>
				</xsl:choose>
			</dl>
			
			<div class="form-buttons">
				<xsl:choose>
					<xsl:when test="editable">
						<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
						<xsl:variable name="lang_cancel"><xsl:value-of select="php:function('lang', 'cancel')" /></xsl:variable>
						<input type="submit" name="save_requirement_values" value="{$lang_save}" title = "{$lang_save}" />
						<input type="submit" name="cancel_requirement_values" value="{$lang_cancel}" title = "{$lang_cancel}" />
					</xsl:when>
					<xsl:otherwise>
						<xsl:variable name="lang_edit"><xsl:value-of select="php:function('lang', 'edit')" /></xsl:variable>
						<input type="submit" name="edit_requirement_values" value="{$lang_edit}" title = "{$lang_edit}" />
					</xsl:otherwise>
				</xsl:choose>
			</div>
		</form>
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
