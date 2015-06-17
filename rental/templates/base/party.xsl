  <!-- $Id: party.xsl 12604 2015-01-15 17:06:11Z nelson224 $ -->
<xsl:template match="data">
	<xsl:apply-templates select="edit" />
	<xsl:call-template name="jquery_phpgw_i18n"/>
</xsl:template>

<!-- add / edit  -->
<xsl:template xmlns:php="http://php.net/xsl" match="edit">

	<div>
		<xsl:variable name="form_action">
			<xsl:value-of select="form_action"/>
		</xsl:variable>

		<xsl:value-of select="validator"/>

		<form id="form" name="form" method="post" action="{$form_action}" class="pure-form pure-form-aligned">
			<dl>
				<xsl:choose>
					<xsl:when test="msgbox_data != ''">
						<dt>
							<xsl:call-template name="msgbox"/>
						</dt>
					</xsl:when>
				</xsl:choose>
			</dl>
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div id="details">
					<fieldset>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_identifier"/>
							</label>
							<input type="text" name="identifier" value="{value_identifier}"></input>
							<input type="hidden" name="id" value="{party_id}"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_firstname"/>
							</label>
							<input type="text" name="firstname" value="{value_firstname}"></input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_lastname"/>
							</label>
							<input type="text" name="lastname" value="{value_lastname}"></input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_job_title"/>
							</label>
							<input type="text" name="title" value="{value_job_title}"></input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_company"/>
							</label>
							<input type="text" name="company_name" value="{value_company}"></input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_department"/>
							</label>
							<input type="text" name="department" value="{value_department}"></input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_address"/>
							</label>
							<input type="text" name="address1" value="{value_address1}"></input>
							<input type="text" name="address2" value="{value_address2}"></input>
						</div>						
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_postal_code_place"/>
							</label>
							<input type="text" name="postal_code" value="{value_postal_code}"></input>
							<input type="text" name="place" value="{value_place}"></input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_inactive_party"/>
							</label>
							<input type="checkbox" name="is_inactive" id="is_inactive">
								<xsl:if test="value_inactive_party != ''">
									<xsl:attribute name="checked" value="checked"/>
								</xsl:if>
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_account_number"/>
							</label>
							<input type="text" name="account_number" value="{value_account_number}"></input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_phone"/>
							</label>
							<input type="text" name="phone" value="{value_phone}"></input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_mobile_phone"/>
							</label>
							<input type="text" name="mobile_phone" value="{value_mobile_phone}"></input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_fax"/>
							</label>
							<input type="text" name="fax" value="{value_fax}"></input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_email"/>
							</label>
							<input type="text" name="email" value="{value_email}"></input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_url"/>
							</label>
							<input type="text" name="url" value="{value_url}"></input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_unit_leader"/>
							</label>
							<input type="text" name="unit_leader" value="{value_unit_leader}"></input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_comment"/>
							</label>
							<textarea cols="47" rows="7" name="comment"><xsl:value-of select="value_comment"/></textarea>
						</div>
					</fieldset>
				</div>
				<xsl:choose>
					<xsl:when test="party_id > 0">
						<div id="contracts">
							<div class="pure-control-group">
								<xsl:for-each select="datatable_def">
									<xsl:if test="container = 'datatable-container_0'">
										<xsl:call-template name="table_setup">
											<xsl:with-param name="container" select ='container'/>
											<xsl:with-param name="requestUrl" select ='requestUrl' />
											<xsl:with-param name="ColumnDefs" select ='ColumnDefs' />
											<xsl:with-param name="tabletools" select ='tabletools' />
											<xsl:with-param name="data" select ='data' />
											<xsl:with-param name="config" select ='config' />
										</xsl:call-template>
									</xsl:if>
								</xsl:for-each>
							</div>
						</div>
						<div id="documents">
							<div class="pure-control-group">
								<xsl:for-each select="datatable_def">
									<xsl:if test="container = 'datatable-container_1'">
										<xsl:call-template name="table_setup">
											<xsl:with-param name="container" select ='container'/>
											<xsl:with-param name="requestUrl" select ='requestUrl' />
											<xsl:with-param name="ColumnDefs" select ='ColumnDefs' />
											<xsl:with-param name="tabletools" select ='tabletools' />
											<xsl:with-param name="data" select ='data' />
											<xsl:with-param name="config" select ='config' />
										</xsl:call-template>
									</xsl:if>
								</xsl:for-each>
							</div>
						</div>
					</xsl:when>
				</xsl:choose>
			</div>
			<div class="proplist-col">
				<input type="submit" class="pure-button pure-button-primary" name="save_party" value="{lang_save}" onMouseout="window.status='';return true;">
					<xsl:attribute name="title">
						<xsl:value-of select="php:function('lang', 'Save the record and return to the list')"/>
					</xsl:attribute>
				</input>
				<input type="submit" class="pure-button pure-button-primary" name="synchronize" value="{lang_sync_data}" onMouseout="window.status='';return true;">
					<xsl:attribute name="title">
						<xsl:value-of select="php:function('lang', 'Apply the values')"/>
					</xsl:attribute> 
				</input>
				<input type="button" class="pure-button pure-button-primary" name="party_back" value="{lang_cancel}" onMouseout="window.status='';return true;" onClick="document.cancel_form.submit();">
					<xsl:attribute name="title">
						<xsl:value-of select="php:function('lang', 'Leave the record untouched and return to the list')"/>
					</xsl:attribute>
				</input>
			</div>
		</form>
		<xsl:variable name="cancel_url">
			<xsl:value-of select="cancel_url"/>
		</xsl:variable>
		<form name="cancel_form" id="cancel_form" action="{$cancel_url}" method="post"></form>
	</div>
</xsl:template>
