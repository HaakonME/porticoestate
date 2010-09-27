<!-- $Id$ -->

	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="edit">
				<xsl:apply-templates select="edit"/>
			</xsl:when>
			<xsl:when test="view">
				<xsl:apply-templates select="view"/>
			</xsl:when>
			<xsl:when test="empty">
				<xsl:apply-templates select="empty"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="list"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	<xsl:template match="list">
		<xsl:apply-templates select="menu"/> 
		<xsl:choose>
			<xsl:when test="msgbox_data != ''">
				<table align = "center">
				<tr>
					<td align="center" colspan="3">
						<xsl:call-template name="msgbox"/>
					</td>
				</tr>
				</table>
			</xsl:when>
		</xsl:choose>
		<table width="100%"  cellpadding="2" cellspacing="2" align="center">
			<tr>
			<xsl:choose>
				<xsl:when test="group_filters != ''">
					<xsl:variable name="select_action"><xsl:value-of select="select_action"/></xsl:variable>
					<form method="post" name="search" action="{$select_action}">
						<td>
							<xsl:call-template name="cat_select"/>
						</td>
						<xsl:choose>
							<xsl:when test="district_list != ''">
								<td>
									<xsl:call-template name="select_district"/>
								</td>
							</xsl:when>
						</xsl:choose>
						<xsl:choose>
							<xsl:when test="status_list!=''">
								<td align="left">
									<xsl:call-template name="status_select"/>
								</td>
							</xsl:when>
						</xsl:choose>
						<td align="center">
							<xsl:call-template name="user_id_select"/>
						</td>
							<td align="right">
								<xsl:call-template name="search_field_grouped"/>
							</td>
						</form>
					</xsl:when>
				<xsl:otherwise>
					<td>
						<xsl:call-template name="cat_filter"/>
					</td>
					<xsl:choose>
						<xsl:when test="district_list!=''">
							<td align="left">
						<xsl:call-template name="filter_district"/>
							</td>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="status_list!=''">
							<td align="left">
								<xsl:call-template name="status_filter"/>
							</td>
						</xsl:when>
					</xsl:choose>
					<td align="center">
						<xsl:call-template name="user_id_filter"/>
					</td>
					<td align="right">
						<xsl:call-template name="search_field"/>
					</td>
				</xsl:otherwise>
			</xsl:choose>
				<td valign ="top">
				<table>
				<tr>
				<td class="small_text" valign="top" align="left">
					<xsl:variable name="link_download"><xsl:value-of select="link_download"/></xsl:variable>
					<xsl:variable name="lang_download_help"><xsl:value-of select="lang_download_help"/></xsl:variable>
					<xsl:variable name="lang_download"><xsl:value-of select="lang_download"/></xsl:variable>
					<a href="javascript:var w=window.open('{$link_download}','','')"
						onMouseOver="overlib('{$lang_download_help}', CAPTION, '{$lang_download}')"
						onMouseOut="nd()">
						<xsl:value-of select="lang_download"/></a>
				</td>
				</tr>
				<tr>
				<td class="small_text" valign="top" align="left">
					<xsl:variable name="link_columns"><xsl:value-of select="link_columns"/></xsl:variable>
					<xsl:variable name="lang_columns_help"><xsl:value-of select="lang_columns_help"/></xsl:variable>
					<xsl:variable name="lang_columns"><xsl:value-of select="lang_columns"/></xsl:variable>
					<a href="javascript:var w=window.open('{$link_columns}','','width=300,height=600')"
						onMouseOver="overlib('{$lang_columns_help}', CAPTION, '{$lang_columns}')"
						onMouseOut="nd()">
						<xsl:value-of select="lang_columns"/></a>
				</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td colspan="7" width="100%">
					<xsl:call-template name="nextmatchs"/>
				</td>
			</tr>
		</table>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
				<xsl:call-template name="table_header_entity"/>
				<xsl:call-template name="values"/>
				<xsl:choose>
					<xsl:when test="table_add !=''">
						<xsl:apply-templates select="table_add"/>
					</xsl:when>
				</xsl:choose>	
		</table>
	</xsl:template>

	<xsl:template name="table_header_entity">
			<tr class="th">
				<xsl:for-each select="table_header" >
					<td class="th_text" width="{with}" align="{align}">
						<xsl:choose>
							<xsl:when test="sort_link!=''">
								<a href="{sort}" onMouseover="window.status='{header}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="header"/></a>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="header"/>					
							</xsl:otherwise>
						</xsl:choose>
					</td>
				</xsl:for-each>
			</tr>
	</xsl:template>

	<xsl:template name="values">
		<xsl:for-each select="values" >
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
				<xsl:for-each select="row" >
					<xsl:choose>
						<xsl:when test="link">
							<td class="small_text" align="center">
								<a href="{link}" onMouseover="window.status='{statustext}';return true;" onMouseout="window.status='';return true;" target = "{target}"><xsl:value-of select="text"/></a>
							</td>
						</xsl:when>
						<xsl:otherwise>
							<td class="small_text" align="left">
								<xsl:value-of select="value"/>					
							</td>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:for-each>
			</tr>
		</xsl:for-each>
	</xsl:template>

	<xsl:template match="table_add">
			<tr>
				<td height="50">
					<xsl:variable name="add_action"><xsl:value-of select="add_action"/></xsl:variable>
					<xsl:variable name="lang_add"><xsl:value-of select="lang_add"/></xsl:variable>
					<form method="post" action="{$add_action}">
						<input type="submit" name="add" value="{$lang_add}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_add_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</form>
				</td>
			</tr>
	</xsl:template>

<!-- add / edit -->

	<xsl:template match="edit">
		<xsl:choose>
			<xsl:when test="mode = 'edit'">
				<script type="text/javascript">
					self.name="first_Window";
					<xsl:value-of select="lookup_functions"/>
				</script>
			</xsl:when>
		</xsl:choose>
		
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
							
		<div class="yui-navset" id="entity_edit_tabview">
			<xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
			<form ENCTYPE="multipart/form-data" method="post" name="form" action="{$form_action}">

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
				<xsl:when test="value_id !=''">
					<tr>
						<td class="th_text" valign ="top">
							<a href="{link_pdf}" target="_blank">PDF</a>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>

			<xsl:choose>
				<xsl:when test="mode = 'edit'">
					<tr>
						<td colspan = "2" align = "center">
							<xsl:apply-templates select="table_apply"/>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>

		</table>
		<table cellpadding="2" cellspacing="2" width="80%" align="center">
			<xsl:call-template name="target"/>
			<xsl:for-each select="origin_list" >
				<tr>
					<td class="th_text">
						<xsl:value-of select="name"/>
					</td>
					<td class="th_text">
						<a href="{link}"  title="{statustext}"><xsl:value-of select="id"/></a>
					</td>
				</tr>
			</xsl:for-each>

			<xsl:choose>
				<xsl:when test="value_ticket_id!=''">
					<tr>
						<td>
							<xsl:value-of select="lang_ticket"/>
						</td>
						<td class="th_text"  align="left">
							<xsl:for-each select="value_ticket_id" >
									<xsl:variable name="link_ticket"><xsl:value-of select="//link_ticket"/>&amp;id=<xsl:value-of select="id"/></xsl:variable>
									<a href="{$link_ticket}"  onMouseover="window.status='{//lang_ticket_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="id"/></a>
									<xsl:text> </xsl:text>
							</xsl:for-each>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>

			<tr>
				<td class="th_text">
					<xsl:value-of select="lang_entity"/>
				</td>
				<td class="th_text">
					<xsl:value-of select="entity_name"/>
				</td>
			</tr>
			<tr>
				<td class="th_text">
					<xsl:value-of select="lang_category"/>
					<input type="hidden" name="values[origin]" value="{value_origin_type}"></input>
					<input type="hidden" name="values[origin_id]" value="{value_origin_id}"></input>
				</td>
				<td class="th_text">
					<xsl:choose>
						<xsl:when test="cat_list=''">
							<xsl:value-of select="category_name"/>
						</xsl:when>
						<xsl:otherwise>
							<xsl:call-template name="cat_select"/>							
						</xsl:otherwise>
					</xsl:choose>
				</td>
			</tr>
			<xsl:choose>
				<xsl:when test="value_id!=''">
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_id"/>
						</td>
						<td>
							<xsl:value-of select="value_num"/>
							<input type="hidden" name="location_code" value="{location_code}"></input>
							<input type="hidden" name="lookup_tenant" value="{lookup_tenant}"></input>
							<input type="hidden" name="values[id]" value="{value_id}"></input>
							<input type="hidden" name="values[num]" value="{value_num}"></input>
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
								<a href="{link}"  title="{statustext}"><xsl:value-of select="id"/></a>
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

		</table>
		<xsl:value-of disable-output-escaping="yes" select="tabs" />
		<div class="yui-content">		

			<xsl:choose>
				<xsl:when test="location_data!=''">
					<div id="location">
						<table>
							<xsl:choose>
								<xsl:when test="mode='edit'">
									<xsl:call-template name="location_form"/>
								</xsl:when>
								<xsl:otherwise>
									<xsl:call-template name="location_view"/>
								</xsl:otherwise>
							</xsl:choose>
						</table>
					</div>
				</xsl:when>
			</xsl:choose>

		<xsl:call-template name="attributes_values"/>
		  
		<xsl:choose>
			<xsl:when test="files!='' or fileupload = 1">
				<div id="files">
					<table cellpadding="2" cellspacing="2" width="80%" align="center">
					<!-- <xsl:call-template name="file_list"/> -->
						<tr>
							<td align="left" valign="top">
								<xsl:value-of select="//lang_files"/>
							</td>
							<td>
								<div id="datatable-container_0"></div>
							</td>
						</tr>
						<xsl:choose>
							<xsl:when test="cat_list='' and fileupload = 1 and mode = 'edit'">
								<xsl:call-template name="file_upload"/>
							</xsl:when>
						</xsl:choose>
				</table>
			</div>
		</xsl:when>
	</xsl:choose>

	<xsl:choose>
		<xsl:when test="integration!=''">
			<div id="integration">
				<iframe id = "integration_content" width="100%" height="500">
					<p>Your browser does not support iframes.</p>
				</iframe>
			</div>
			<div id="test" >
				<div class="hd" style="background-color:#000000;color:#FFFFFF; border:0; text-align:center"> Integration </div>
				<div class="bd" style="text-align:center;"> </div>
			</div>
		</xsl:when>
	</xsl:choose>

	<xsl:choose>
		<xsl:when test="related_link != ''">
			<div id="related">
				<table cellpadding="2" cellspacing="2" width="80%" align="center">
					<tr>
						<td>
							<table width="100%" cellpadding="2" cellspacing="2" align="center">
								<xsl:apply-templates select="related_link"/>
							</table>
						</td>
					</tr>
					</table>
				</div>
			</xsl:when>
	</xsl:choose>

	<xsl:choose>
		<xsl:when test="mode = 'edit'">
			<table cellpadding="2" cellspacing="2" width="80%" align="center">
				<tr height="50">
					<td colspan="2" align = "center">
						<xsl:apply-templates select="table_apply"/>
					</td>
				</tr>
			</table>
		</xsl:when>
	</xsl:choose>

		</div>
		</form>
		<xsl:choose>
			<xsl:when test="value_id!=''">
				<table cellpadding="2" cellspacing="2" width="80%" align="center">
					<xsl:choose>
						<xsl:when test="start_project!=''">
							<tr>
								<td valign="top">
									<xsl:variable name="project_link"><xsl:value-of select="project_link"/></xsl:variable>
									<form method="post" action="{$project_link}">
										<xsl:variable name="lang_start_project"><xsl:value-of select="lang_start_project"/></xsl:variable>
										<input type="submit" name="location" value="{$lang_start_project}">
											<xsl:attribute name="title">
												<xsl:value-of select="lang_start_project_statustext"/>
											</xsl:attribute>
										</input>
									</form>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="start_ticket!=''">
							<tr>
								<td valign="top">
									<xsl:variable name="ticket_link"><xsl:value-of select="ticket_link"/></xsl:variable>
									<form method="post" action="{$ticket_link}">
										<xsl:variable name="lang_start_ticket"><xsl:value-of select="lang_start_ticket"/></xsl:variable>
										<input type="submit" name="location" value="{$lang_start_ticket}">
											<xsl:attribute name="title">
												<xsl:value-of select="lang_start_ticket_statustext"/>
											</xsl:attribute>
										</input>
									</form>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
				</table>
				</xsl:when>
		</xsl:choose>
		</div>
	</xsl:template>


	<xsl:template match="table_apply">
		<table>
			<tr>
				<td valign="bottom">
					<xsl:variable name="lang_save"><xsl:value-of select="lang_save"/></xsl:variable>
					<input type="submit" name="values[save]" value="{$lang_save}">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_save_statustext"/>
						</xsl:attribute>
					</input>
				</td>
				<td valign="bottom">
					<xsl:variable name="lang_apply"><xsl:value-of select="lang_apply"/></xsl:variable>
					<input type="submit" name="values[apply]" value="{$lang_apply}">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_apply_statustext"/>
						</xsl:attribute>
					</input>
				</td>
				<td align="right" valign="bottom">
					<xsl:variable name="lang_cancel"><xsl:value-of select="lang_cancel"/></xsl:variable>
					<input type="submit" name="values[cancel]" value="{$lang_cancel}">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_cancel_statustext"/>
						</xsl:attribute>
					</input>
				</td>
			</tr>
		</table>
	</xsl:template>

<!-- emtpy -->

	<xsl:template match="empty">
		<xsl:apply-templates select="menu"/> 
		<table width="100%"  cellpadding="2" cellspacing="2" align="center">
			<tr>
				<td>
					<xsl:call-template name="cat_filter"/>
				</td>
			</tr>
			<tr>
				<td colspan="4" width="100%">
					<xsl:call-template name="nextmatchs"/>
				</td>
			</tr>
		</table>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
				<xsl:call-template name="table_header_entity"/>
		</table>
	</xsl:template>

	<xsl:template match="attributes_header">
			<tr class="th">
				<td class="th_text" width="15%" align="left">
					<xsl:value-of select="lang_name"/>
				</td>
				<td class="th_text" width="55%" align="right">
					<xsl:value-of select="lang_value"/>
				</td>
			</tr>
	</xsl:template>

	<xsl:template name="target">
				<xsl:choose>
				<xsl:when test="value_target!=''">
					<xsl:for-each select="value_target" >
						<tr>
							<td class="th_text" valign ="top">
								<xsl:value-of select="//lang_target"/>
							</td>
							<td>
								<table>							
									<xsl:for-each select="data">
										<tr>
											<td class="th_text"  align="left" >
												<a href="{link}"  title="{//lang_target_statustext}"><xsl:value-of select="type"/><xsl:text> #</xsl:text> <xsl:value-of select="id"/></a>
												<xsl:text> </xsl:text>
											</td>
										</tr>
									</xsl:for-each>
								</table>
							</td>
						</tr>
					</xsl:for-each>
				</xsl:when>
			</xsl:choose>
	</xsl:template>

	<xsl:template match="related_link">
		<xsl:variable name="lang_entity_statustext"><xsl:value-of select="lang_entity_statustext"/></xsl:variable>
		<xsl:variable name="entity_link"><xsl:value-of select="entity_link"/></xsl:variable>
			<tr >
				<td class="small_text" align="left">
					<a href="{$entity_link}" onMouseover="window.status='{$lang_entity_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_entity"/></a>
				</td>
			</tr>
	</xsl:template>

