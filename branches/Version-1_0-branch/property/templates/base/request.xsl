<!-- $Id$ -->

	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="edit">
				<xsl:apply-templates select="edit"/>
			</xsl:when>
			<xsl:when test="view">
				<xsl:apply-templates select="view"/>
			</xsl:when>
			<xsl:when test="priority_key">
				<xsl:apply-templates select="priority_form"/>
			</xsl:when>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="priority_form" xmlns:php="http://php.net/xsl">
		<xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
		<form method="post" action="{$form_action}">
			<div align="left">
				<table width="100%" cellpadding="2" cellspacing="2" align="center">
					<xsl:choose>
						<xsl:when test="msgbox_data != ''">
							<tr>
								<td align="left" colspan="3">
									<xsl:call-template name="msgbox"/>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<xsl:apply-templates select="priority_key"/>
					<tr>
						<td class="small_text" align="left">
							<xsl:value-of select="php:function('lang', 'Authorities Demands')" />
						</td>
						<td class="small_text" align="left">
							<input type="text" size="5" name="values[authorities_demands]" value="{value_authorities_demands}">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'Authorities Demands')" />
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr height="50">
						<td>
							<xsl:variable name="lang_save"><xsl:value-of select="lang_save"/></xsl:variable>
							<input type="submit" name="values[update]" value="{$lang_save}" >
							</input>
						</td>
					</tr>
				</table>
			</div>
		</form>
		<!-- to reload the table -->
		<xsl:choose>
			<xsl:when test="//exchange_values!=''">
				<script type="text/javascript">
					<xsl:value-of select="//exchange_values"/>
				</script>
			</xsl:when>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="priority_key">
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
			<td class="small_text" align="left">
				<xsl:value-of select="name"/>
				<xsl:text>::</xsl:text>
				<xsl:value-of select="descr"/>
			</td>
			<td class="small_text" align="left">
				<input type="text" size="3" name="values[priority_key][{id}]" value="{priority_key}">
					<xsl:attribute name="title">
						<xsl:value-of select="lang_priority_key_statustext"/>
					</xsl:attribute>
				</input>
			</td>
		</tr>
	</xsl:template>

<!-- add / edit -->

	<xsl:template match="edit" xmlns:php="http://php.net/xsl">
		<div align="left">
			<table cellpadding="2" cellspacing="2" width="80%" align="center">
				<xsl:choose>
					<xsl:when test="msgbox_data != ''">
						<tr>
							<td align="left" colspan="3">
								<xsl:call-template name="msgbox"/>
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="value_request_id!='' and acl_add_project='1'">
						<tr>
						<td valign="top" align="center">
							<xsl:variable name="generate_project_action"><xsl:value-of select="generate_project_action"/></xsl:variable>
							<xsl:variable name="lang_generate_project"><xsl:value-of select="lang_generate_project"/></xsl:variable>
							<form method="post" action="{$generate_project_action}">
								<input type="hidden" name="origin" value="{value_acl_location}"></input>
								<input type="hidden" name="origin_id" value="{value_request_id}"></input>
								<input type="hidden" name="location_code" value="{location_code}"></input>
								<input type="hidden" name="bypass" value="true"></input>
								<input type="hidden" name="descr" value="{value_descr}"></input>
								<input type="hidden" name="tenant_id" value="{tenant_id}"></input>
								<input type="hidden" name="p_num" value="{p_num}"></input>
								<input type="hidden" name="p_entity_id" value="{p_entity_id}"></input>
								<input type="hidden" name="p_cat_id" value="{p_cat_id}"></input>
								<input type="submit" class="forms" name="generate_project" value="{$lang_generate_project}">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_generate_project_statustext"/>
									</xsl:attribute>
								</input>
							</form>
						</td>
						</tr>
					</xsl:when>
				</xsl:choose>
			</table>
		</div>

		<xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
		<form ENCTYPE="multipart/form-data" method="post" name="form" action="{$form_action}">
			<div class="yui-navset yui-navset-top" id="project_tabview">
				<xsl:value-of disable-output-escaping="yes" select="tabs" />
				<div class="yui-content">
					<div id="general">
						<table cellpadding="2" cellspacing="2" width="80%" align="center">
							<input type="hidden" name="values[origin]" value="{value_origin_type}"></input>
							<input type="hidden" name="values[origin_id]" value="{value_origin_id}"></input>
							<xsl:choose>
								<xsl:when test="value_request_id!=''">
									<tr>
										<td>
											<xsl:value-of select="lang_copy_request"/>
										</td>
										<td>
											<input type="checkbox" name="values[copy_request]" value="True" >
												<xsl:attribute name="title">
													<xsl:value-of select="lang_copy_request_statustext"/>
												</xsl:attribute>
											</input>
										</td>
									</tr>
									<tr>
										<td>
											<xsl:value-of select="lang_request_id"/>
										</td>
										<td>
											<xsl:value-of select="value_request_id"/>
										</td>
									</tr>
									<tr>
										<td>
											<xsl:value-of select="php:function('lang', 'entry date')" />
										</td>
										<td>
											<xsl:value-of select="value_entry_date"/>
										</td>
									</tr>
									<tr>
										<td>
											<xsl:value-of select="php:function('lang', 'in progress date')" />
										</td>
										<td>
											<xsl:value-of select="value_in_progress_date"/>
										</td>
									</tr>
									<tr>
										<td>
											<xsl:value-of select="php:function('lang', 'delivered date')" />
										</td>
										<td>
											<xsl:value-of select="value_delivered_date"/>
										</td>
									</tr>
									<tr>
										<td>
											<xsl:value-of select="php:function('lang', 'closed date')" />
										</td>
										<td>
											<xsl:value-of select="value_closed_date"/>
										</td>
									</tr>

									<xsl:for-each select="value_origin" >
										<tr>
											<td class="th_text" valign ="top">
												<xsl:value-of select="descr"/>
											</td>
											<td>
												<table>
													<xsl:for-each select="data">
														<tr>
															<td class="th_text"  align="left" >
																<a href="{link}"  title="{statustext}" ><xsl:value-of select="id"/></a>
																<xsl:text> </xsl:text>
																<xsl:choose>
																	<xsl:when test="location ='.project.request'">
																		<input type="checkbox" name="values[delete_request][]" value="{id}" >
																			<xsl:attribute name="title">
																				<xsl:value-of select="//lang_delete_request_statustext"/>
																			</xsl:attribute>
																		</input>
																	</xsl:when>
																</xsl:choose>
															</td>
														</tr>
													</xsl:for-each>
												</table>
											</td>
										</tr>
									</xsl:for-each>
								</xsl:when>
								<xsl:otherwise>
									<xsl:for-each select="value_origin" >
										<tr>
											<td class="th_text" valign ="top">
												<xsl:value-of select="descr"/>
											</td>
											<td>
												<table>
													<xsl:for-each select="data">
														<tr>
															<td class="th_text"  align="left" >
																<a href="{link}"  title="{statustext}"><xsl:value-of select="id"/></a>
																<xsl:text> </xsl:text>
															</td>
														</tr>
													</xsl:for-each>
												</table>
											</td>
										</tr>
									</xsl:for-each>
								</xsl:otherwise>
							</xsl:choose>
							<xsl:for-each select="value_target" >
								<tr>
									<td class="th_text" valign ="top">
										<xsl:value-of select="descr"/>
									</td>
									<td class="th_text"  align="left" >
										<xsl:for-each select="data">
											<a href="{link}"  title="{statustext}"><xsl:value-of select="id"/></a>
											<xsl:text> </xsl:text>
										</xsl:for-each>
									</td>
								</tr>
							</xsl:for-each>

							<xsl:variable name="lang_request_title"><xsl:value-of select="php:function('lang', 'enter request title')" /></xsl:variable>
							<tr>
								<td valign="top" title= "{$lang_request_title}">
									<xsl:value-of select="php:function('lang', 'request title')" />
								</td>
								<td>
									<input type="text" name="values[title]" value="{value_title}" size="60" title= "{$lang_request_title}">
									</input>
								</td>
							</tr>
							<xsl:variable name="lang_request_description"><xsl:value-of select="php:function('lang', 'enter a description of the request')" /></xsl:variable>
							<tr>
								<td valign="top" title= "{$lang_request_description}">
									<xsl:value-of select="php:function('lang', 'request description')" />
								</td>
								<td>
									<textarea cols="60" rows="6" name="values[descr]" title= "{$lang_request_description}">
										<xsl:value-of select="value_descr"/>
									</textarea>
								</td>
							</tr>
							<tr>
								<td>
									<xsl:value-of select="php:function('lang', 'cost categories')" />
								</td>
								<td>
									<xsl:call-template name="categories"/>
								</td>
							</tr>
							<xsl:choose>
								<xsl:when test="mode ='edit'">
									<xsl:call-template name="location_form"/>
								</xsl:when>
								<xsl:otherwise>
									<xsl:call-template name="location_view"/>
									<xsl:choose>
										<xsl:when test="contact_phone !=''">
											<tr>
												<td class="th_text"  align="left">
													<xsl:value-of select="lang_contact_phone"/>
												</td>
												<td  align="left">
													<xsl:value-of select="contact_phone"/>
												</td>
											</tr>
										</xsl:when>
									</xsl:choose>
								</xsl:otherwise>
							</xsl:choose>

							<xsl:choose>
								<xsl:when test="suppressmeter =''">
									<tr>
										<td valign="top">
											<xsl:value-of select="lang_power_meter"/>
										</td>
										<td>
											<input type="text" name="values[power_meter]" value="{value_power_meter}" size="12" >
												<xsl:attribute name="title">
													<xsl:value-of select="lang_power_meter_statustext"/>
												</xsl:attribute>
											</input>
										</td>
									</tr>
								</xsl:when>
							</xsl:choose>

							<tr>
								<td>
									<xsl:value-of select="php:function('lang', 'building part')" />
								</td>
								<td>
									<select name="values[building_part]" >
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'select building part')" />
										</xsl:attribute>
										<option value="0">
											<xsl:value-of select="php:function('lang', 'select building part')" />
										</option>
										<xsl:apply-templates select="building_part_list/options"/>
									</select>
								</td>
							</tr>
							<tr>
								<td>
									<xsl:value-of select="lang_coordinator"/>
								</td>
								<td>
									<xsl:call-template name="user_id_select"/>
								</td>
							</tr>
							<tr>
								<td valign="top">
									<xsl:value-of select="php:function('lang', 'status')" />
								</td>
								<td>
									<select name="values[status]" class="forms">
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'Set the status of the ticket')" />
										</xsl:attribute>
										<option value="0"><xsl:value-of select="php:function('lang', 'no status')" /></option>
										<xsl:apply-templates select="status_list/options"/>
									</select>			
								</td>
							</tr>

							<xsl:choose>
								<xsl:when test="show_dates !=''">
									<tr>
										<td valign="top">
											<xsl:value-of select="lang_start_date"/>
										</td>
										<td>
											<input type="text" id="values_start_date" name="values[start_date]" size="10" value="{value_start_date}" readonly="readonly" >
												<xsl:attribute name="title">
													<xsl:value-of select="lang_start_date_statustext"/>
												</xsl:attribute>
											</input>
											<img id="values_start_date-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;" />
										</td>
									</tr>
									<tr>
										<td valign="top">
											<xsl:value-of select="lang_end_date"/>
										</td>
										<td>
											<input type="text" id="values_end_date" name="values[end_date]" size="10" value="{value_end_date}" readonly="readonly" >
												<xsl:attribute name="title">
													<xsl:value-of select="lang_end_date_statustext"/>
												</xsl:attribute>
											</input>
											<img id="values_end_date-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;" />
										</td>
									</tr>
								</xsl:when>
							</xsl:choose>

							<tr>
								<td valign="top">
									<xsl:value-of select="php:function('lang', 'total cost estimate')" />
								</td>
								<td>
									<input type="text" name="values[budget]" value="{value_budget}">
										<xsl:attribute name="title">
											<xsl:value-of select="lang_budget_statustext"/>
										</xsl:attribute>
									</input>
									<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
								</td>
							</tr>
							<tr>
								<td valign="top">
									<xsl:value-of select="php:function('lang', 'not allocated')" />
								</td>
								<td>
									<xsl:value-of select="value_diff"/>
									<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
								</td>
							</tr>
							<tr>
								<td valign="top">
									<xsl:value-of select="php:function('lang', 'residual demand')" />
								</td>
								<td>
									<xsl:value-of select="value_diff2"/>
									<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
								</td>
							</tr>

							<tr>
								<td valign="top">
										<xsl:value-of select="php:function('lang', 'planning date')" />
								</td>
								<td>
									<input type="text" id="values_planning_date" name="values[planning_date]" size="10" value="" readonly="readonly" >
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'planning date')" />
										</xsl:attribute>
									</input>
									<img id="values_planning_date-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;" />
								</td>
							</tr>
							<tr>
								<td valign="top">
										<xsl:value-of select="php:function('lang', 'planning value')" />
								</td>
								<td>
									<input type="text" id="values_planning_value" name="values[planning_value]" size="10" value="{value_planning}" >
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'planning value')" />
										</xsl:attribute>
									</input>
									<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
								</td>
							</tr>

							<tr>
								<td width="19%" align="left" valign="top">
									<xsl:value-of select="php:function('lang', 'planning serie')" />
								</td>
								<td>
									<div id="datatable-container_2"></div>
								</td>
							</tr>

							<tr>
								<td valign="top">
										<xsl:value-of select="php:function('lang', 'consume date')" />
								</td>
								<td>
									<input type="text" id="values_consume_date" name="values[consume_date]" size="10" value="" readonly="readonly" >
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'consume date')" />
										</xsl:attribute>
									</input>
									<img id="values_consume_date-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;" />
								</td>
							</tr>
							<tr>
								<td valign="top">
										<xsl:value-of select="php:function('lang', 'consume value')" />
								</td>
								<td>
									<input type="text" id="values_consume_value" name="values[consume_value]" size="10" value="{value_consume}" >
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'consume value')" />
										</xsl:attribute>
									</input>
									<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
								</td>
							</tr>

							<tr>
								<td width="19%" align="left" valign="top">
									<xsl:value-of select="php:function('lang', 'consume history')" />
								</td>
								<td>
									<div id="datatable-container_3"></div>
								</td>
							</tr>

<!--
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_branch"/>
								</td>
								<td>
									<xsl:variable name="lang_branch_statustext"><xsl:value-of select="lang_branch_statustext"/></xsl:variable>
									<select name="values[branch_id]" class="forms" title="{$lang_branch_statustext}">
										<option value=""><xsl:value-of select="lang_no_branch"/></option>
										<xsl:apply-templates select="branch_list/options"/>
									</select>
								</td>
							</tr>
-->
							<tr>
								<xsl:choose>
									<xsl:when test="notify='yes'">
										<td valign="top">
											<xsl:value-of select="lang_notify"/>
										</td>
										<td>
											<input type="checkbox" name="values[notify]" value="True" >
												<xsl:attribute name="title">
													<xsl:value-of select="lang_notify_statustext"/>
												</xsl:attribute>
											</input>

											<input type="text" name="values[mail_address]" value="{value_notify_mail_address}">
												<xsl:attribute name="title">
													<xsl:value-of select="lang_notify_statustext"/>
												</xsl:attribute>
											</input>
										</td>
									</xsl:when>
								</xsl:choose>
							</tr>
						</table>
					</div>
					<div id="condition">
						<table>
							<xsl:apply-templates select="custom_attributes/attributes"/>
							<tr>
								<td colspan='2'>
									<table border="1" width="100%" cellpadding="2" cellspacing="2" align="center">
										<xsl:call-template name="table_header_importance"/>
										<xsl:apply-templates select="condition_list"/>
									</table>
								</td>
							</tr>
							<tr>
								<td>
									<br/>
								</td>
							</tr>

								<xsl:choose>
									<xsl:when test="authorities_demands/options!=''">
										<tr>
											<td align="left">
												<xsl:value-of select="php:function('lang', 'Authorities Demands')" />
											</td>

											<td>
												<select name="values[authorities_demands]" class="forms">
													<xsl:attribute name="title">
														<xsl:value-of select="php:function('lang', 'Is there a demand from the authorities to correct this condition?')" />
														<xsl:text> + </xsl:text>
														<xsl:value-of select="value_authorities_demands"/>
													</xsl:attribute>
													<option value="0"><xsl:value-of select="php:function('lang', 'no authorities demands')" /></option>
													<xsl:apply-templates select="authorities_demands/options"/>
												</select>
											</td>
										</tr>
										<tr>
											<td>
												<br/>
											</td>
										</tr>
									</xsl:when>
								</xsl:choose>
<!--
										<tr>
											<td valign='top' align="left">
												<xsl:value-of select="php:function('lang', 'regulations')" />
											</td>
											<td colspan='3'>
												<table cellpadding="2" cellspacing="2" width="50%" align="left">
													<xsl:for-each select="regulations" >
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
															<td align="left" title='{descr}'>
																<xsl:text> </xsl:text>
																<xsl:choose>
																	<xsl:when test="external_ref!=''">
																		<a href="{external_ref}" target="_blank"><xsl:value-of select="name"/></a>
																	</xsl:when>
																	<xsl:otherwise>
																		<xsl:value-of select="name"/>
																	</xsl:otherwise>
																</xsl:choose>
															</td>
															<td align="left">
																<input type="checkbox" name="values[regulations][]" value="{id}">
																	<xsl:if test="selected = '1'">
																		<xsl:attribute name="checked">
																			<xsl:text>checked</xsl:text>
																		</xsl:attribute>
																	</xsl:if>
																	<xsl:attribute name="title">
																		<xsl:value-of select="descr"/>
																	</xsl:attribute>
																</input>
															</td>
														</tr>
													</xsl:for-each>
												</table>
											</td>
										</tr>
-->
										<tr>
											<td>
												<br/>
											</td>
										</tr>
										<tr>
											<td align="left">
												<xsl:value-of select="lang_score"/>
											</td>
											<td>
												<xsl:value-of select="value_score"/>
											</td>
										</tr>
						</table>
					</div>

					<div id="documents">
						<table>
							<xsl:choose>
								<xsl:when test="files!=''">
									<!-- <xsl:call-template name="file_list"/> -->
									<tr>
										<td width="19%" align="left" valign="top">
											<xsl:value-of select="//lang_files"/>
										</td>
										<td>
											<div id="datatable-container_1"></div>
										</td>
									</tr>
								</xsl:when>
							</xsl:choose>

							<xsl:choose>
								<xsl:when test="fileupload = 1">
									<xsl:call-template name="file_upload"/>
								</xsl:when>
							</xsl:choose>

							<tr height="50">
								<td>
									<xsl:variable name="lang_save"><xsl:value-of select="lang_save"/></xsl:variable>
									<input type="submit" name="values[save]" value="{$lang_save}">
										<xsl:attribute name="title">
											<xsl:value-of select="lang_save_statustext"/>
										</xsl:attribute>
									</input>
								</td>
							</tr>
						</table>
					</div>

					<div id="history">
						<div id="paging_0"> </div>
						<div id="datatable-container_0"></div>
						<div id="contextmenu_0"></div>

						<script type="text/javascript">
							var property_js = <xsl:value-of select="property_js" />;
							var datatable = new Array();
							var myColumnDefs = new Array();

							<xsl:for-each select="datatable">
								datatable[<xsl:value-of select="name"/>] = [
								{
								values			:	<xsl:value-of select="values"/>,
								total_records	: 	<xsl:value-of select="total_records"/>,
								edit_action		:  	<xsl:value-of select="edit_action"/>,
								is_paginator	:  	<xsl:value-of select="is_paginator"/>,
								footer			:	<xsl:value-of select="footer"/>
								}
								]
							</xsl:for-each>

							<xsl:for-each select="myColumnDefs">
								myColumnDefs[<xsl:value-of select="name"/>] = <xsl:value-of select="values"/>
							</xsl:for-each>
						</script>
					</div>
				</div>
			</div>
			<xsl:choose>
				<xsl:when test="mode = 'edit'">
					<table>
						<tr height="50">
							<td>
								<xsl:variable name="lang_save"><xsl:value-of select="lang_save"/></xsl:variable>
								<input type="submit" name="values[save]" value="{$lang_save}">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_save_statustext"/>
									</xsl:attribute>
								</input>
							</td>
						</tr>
					</table>
				</xsl:when>
			</xsl:choose>
		</form>

		<table>
			<tr>
				<td>
					<xsl:variable name="done_action"><xsl:value-of select="done_action"/></xsl:variable>
					<xsl:variable name="lang_done"><xsl:value-of select="lang_done"/></xsl:variable>
					<form method="post" action="{$done_action}">
						<input type="submit" name="done" value="{$lang_done}">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_done_statustext"/>
							</xsl:attribute>
						</input>
					</form>
				</td>
				<xsl:choose>
					<xsl:when test="mode = 'view'">
						<td>
							<xsl:variable name="edit_action"><xsl:value-of select="edit_action"/></xsl:variable>
							<xsl:variable name="lang_edit"><xsl:value-of select="php:function('lang', 'edit')" /></xsl:variable>
							<form method="post" action="{$edit_action}">
								<input type="submit" class="forms" name="edit" value="{$lang_edit}">
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'edit')" />
									</xsl:attribute>
								</input>
							</form>
						</td>
					</xsl:when>
				</xsl:choose>
			</tr>
		</table>

	</xsl:template>

	<xsl:template match="condition_list" xmlns:php="http://php.net/xsl">
		<tr>
			<xsl:choose>
				<xsl:when test="condition_type_list != ''">
				</xsl:when>
				<xsl:otherwise>
					<td class="small_text" align="left">
						<xsl:value-of select="condition_type_name"/>
					</td>
				</xsl:otherwise>
			</xsl:choose>
		<!--
			<td class="small_text" align="center">
				<select name="values[condition][{condition_type}][reference]" class="forms">
					<xsl:attribute name="title">
						<xsl:value-of select="php:function('lang', 'select value')" />
					</xsl:attribute>
					<xsl:apply-templates select="reference/options"/>
				</select>
			</td>
		-->
			<td class="small_text" align="center">
				<select name="values[condition][{condition_type}][degree]" class="forms">
					<xsl:attribute name="title">
						<xsl:value-of select="php:function('lang', 'select value')" />
					</xsl:attribute>
					<xsl:apply-templates select="degree/options"/>
				</select>
			</td>
			<xsl:choose>
				<xsl:when test="condition_type_list != ''">
					<td class="small_text" align="left">
						<select name="values[condition][{condition_type}][condition_type]" class="forms">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'select value')" />
							</xsl:attribute>
							<xsl:apply-templates select="condition_type_list/options"/>
						</select>
					</td>
				</xsl:when>
			</xsl:choose>

			<td class="small_text" align="center">
				<select name="values[condition][{condition_type}][consequence]" class="forms">
					<xsl:attribute name="title">
						<xsl:value-of select="php:function('lang', 'select value')" />
					</xsl:attribute>
					<xsl:apply-templates select="consequence/options"/>
				</select>
			</td>
			<td class="small_text" align="center">
				<select name="values[condition][{condition_type}][probability]" class="forms">
					<xsl:attribute name="title">
						<xsl:value-of select="php:function('lang', 'select value')" />
					</xsl:attribute>
					<xsl:apply-templates select="probability/options"/>
				</select>
			</td>
		<!--
			<td class="small_text" align="center">
				<xsl:value-of select="failure"/>
			</td>
		-->
			<td class="small_text" align="right">
				<xsl:value-of select="weight"/>
			</td>
			<td class="small_text" align="right">
				<xsl:value-of select="risk"/>
			</td>
			<td class="small_text" align="right">
				<xsl:value-of select="score"/>
			</td>
		</tr>
	</xsl:template>

	<xsl:template match="condition_list_view">
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
			<td class="small_text" align="left">
				<xsl:value-of select="condition_type_name"/>
			</td>
			<td class="small_text" align="center">
				<select  disabled='' class="forms" >
					<xsl:apply-templates select="degree"/>
				</select>
			</td>
			<td class="small_text" align="center">
				<select  disabled='' class="forms" >
					<xsl:apply-templates select="probability"/>
				</select>
			</td>
			<td class="small_text" align="center">
				<select  disabled='' class="forms" >
					<xsl:apply-templates select="consequence"/>
				</select>
			</td>
		</tr>
	</xsl:template>

	<xsl:template match="options">
		<option value="{id}">
			<xsl:if test="selected != 0">
				<xsl:attribute name="selected" value="selected" />
			</xsl:if>
			<xsl:if test="descr != ''">
				<xsl:attribute name="title">
					<xsl:value-of disable-output-escaping="yes" select="descr"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:value-of disable-output-escaping="yes" select="name"/>
		</option>
	</xsl:template>

	<xsl:template match="table_header_history">
		<tr class="th">
			<td class="th_text" width="20%" align="left">
				<xsl:value-of select="lang_date"/>
			</td>
			<td class="th_text" width="10%" align="left">
				<xsl:value-of select="lang_user"/>
			</td>
			<td class="th_text" width="30%" align="left">
				<xsl:value-of select="lang_action"/>
			</td>
			<td class="th_text" width="10%" align="left">
				<xsl:value-of select="lang_new_value"/>
			</td>
		</tr>
	</xsl:template>

	<xsl:template match="record_history">
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
			<td align="left">
				<xsl:value-of select="value_date"/>
			</td>
			<td align="left">
				<xsl:value-of select="value_user"/>
			</td>
			<td align="left">
				<xsl:value-of select="value_action"/>
			</td>
			<td align="left">
				<xsl:value-of select="value_new_value"/>
			</td>
		</tr>
	</xsl:template>

	<xsl:template name="table_header_importance" xmlns:php="http://php.net/xsl">
		<tr>
			<xsl:choose>
				<xsl:when test="//condition_type_list != ''">
				</xsl:when>
				<xsl:otherwise>
					<th width="20%" align="left">
						<xsl:attribute name="title">
							<xsl:text>Konsekvenstype - tema for tilstand</xsl:text>
						</xsl:attribute>
						<xsl:value-of select="php:function('lang', 'consequence type')" />
					</th>
				</xsl:otherwise>
			</xsl:choose>
			<!--
			<th width="20%" align="left">
				<xsl:value-of select="php:function('lang', 'reference level')" />
			</th>
			-->
			<th width="20%" align="center">
				<xsl:attribute name="title">
						<xsl:text>Tilstandsgrad iht NS 3424</xsl:text>
				</xsl:attribute>
				<xsl:value-of select="php:function('lang', 'condition degree')" />
			</th>
			<xsl:choose>
				<xsl:when test="//condition_type_list != ''">
					<th width="20%" align="left">
						<xsl:attribute name="title">
							<xsl:text>Konsekvenstype - tema for tilstand</xsl:text>
						</xsl:attribute>
						<xsl:value-of select="php:function('lang', 'consequence type')" />
					</th>
				</xsl:when>
			</xsl:choose>

			<th width="20%" align="center">
				<xsl:attribute name="title">
						<xsl:text>Konsekvensgrad iht NS 3424</xsl:text>
				</xsl:attribute>
				<xsl:value-of select="php:function('lang', 'Consequence')" />
			</th>
			<th width="20%" align="center">
				<xsl:attribute name="title">
						<xsl:text>Sannsynlighet iht NS 3424</xsl:text>
				</xsl:attribute>
				<xsl:value-of select="php:function('lang', 'Probability')" />
			</th>

		<!--
			<th width="5%" align="center">
				<xsl:value-of select="php:function('lang', 'failure')" />
			</th>
		-->
			<th width="5%" align="center">
					<xsl:attribute name="title">
						<xsl:text>Vekt = konfigurerbar verdi pr konsekvenstype</xsl:text>
					</xsl:attribute>
				<xsl:value-of select="php:function('lang', 'weight')" />
			</th>
			<th width="5%" align="center">
					<xsl:attribute name="title">
						<xsl:text>Risiko = Konsekvensgrad x Sannsynlighetsgrad</xsl:text>
					</xsl:attribute>
				<xsl:value-of select="php:function('lang', 'risk')" />
			</th>
			<th width="5%" align="center">
					<xsl:attribute name="title">
						<xsl:text>Poeng = Tilstandsgrad x Risiko x Vekt</xsl:text>
					</xsl:attribute>

				<xsl:value-of select="php:function('lang', 'score')" />
			</th>
		</tr>
	</xsl:template>
