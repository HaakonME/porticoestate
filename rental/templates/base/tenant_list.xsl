<xsl:include href="rental/templates/base/common.xsl"/>

<xsl:template name="pageForm" xmlns:php="http://php.net/xsl">
	<script>
		YAHOO.util.Event.addListener(
			'ctrl_reset_button', 
			'click', 
			function(e)
			{    	
	    		YAHOO.util.Event.stopEvent(e);
	        	window.location = 'index.php?menuaction=rental.uitenant.index';
    		}
    	);
    	

	</script>
</xsl:template>

<xsl:template name="pageContent">
	<xsl:call-template name="datatable" />
</xsl:template>

<xsl:template name="datatable" xmlns:php="http://php.net/xsl">
	<div class="datatable">
		<xsl:call-template name="listForm"/>
		<div id="paginator" />
	    <div id="columnshowhide" />
		<div id="dt-dlg">
		    <div class="hd">Velg hvilke kolonner du ønsker å se:</div>
		    <div id="dt-dlg-picker" class="bd"></div>
		</div>
    	<div id="datatable-container"/>
  		<xsl:call-template name="datasource-definition">
  			<xsl:with-param name="number">1</xsl:with-param>
  			<xsl:with-param name="form">list_form</xsl:with-param>
  			<xsl:with-param name="filters">['']</xsl:with-param>
  			<xsl:with-param name="container_name">datatable-container</xsl:with-param>
  			<xsl:with-param name="context_menu_labels">
				['<xsl:value-of select="php:function('lang', 'rental_cm_show')"/>',
				'<xsl:value-of select="php:function('lang', 'rental_cm_edit')"/>']
			</xsl:with-param>
			<xsl:with-param name="context_menu_actions">
					['view',
					'edit']	
			</xsl:with-param>
			<xsl:with-param name="source">index.php?menuaction=rental.uitenant.query&amp;phpgw_return_as=json</xsl:with-param>
			<xsl:with-param name="columnDefinitions">
  				[{
					key: "id",
					label: "<xsl:value-of select="php:function('lang', 'rental_tenant_id')"/>",
				    sortable: true
				},
				{
					key: "name",
					label: "<xsl:value-of select="php:function('lang', 'rental_tenant_name')"/>",
				    sortable: true
				},
				{
					key: "address",
					label: "<xsl:value-of select="php:function('lang', 'rental_tenant_address')"/>",
				    sortable: true
				},
				{
					key: "phone",
					label: "<xsl:value-of select="php:function('lang', 'rental_tenant_phone')"/>",
				    sortable: true
				},
				{
					key: "account",
					label: "<xsl:value-of select="php:function('lang', 'rental_tenant_account')"/>",
				    sortable: true
				},
				{
					key: "actions",
					hidden: true
				}
				]
  			</xsl:with-param>
  		</xsl:call-template>
  	</div>
</xsl:template>  

<xsl:template name="listForm" xmlns:php="http://php.net/xsl">
	<form id="list_form" method="GET">
		<div id="datatableToolbar">
			<table class="datatableToolbar">
				<tr>
					<td class="toolbarlabel">
						<xsl:value-of select="php:function('lang', 'rental_rc_search_options')"/>
					</td>
					<td class="toolbarcol" >
						<label class="toolbar_element_label" for="ctrl_search_query">
							<xsl:value-of select="php:function('lang', 'rental_rc_search_for')"/>
						</label>
						<input id="ctrl_search_query" type="text" name="query" autocomplete="off" />
					</td>
					<td class="toolbarcol">
						<label class="toolbar_element_label" for="ctr_toggle_contract_type">
							<xsl:value-of select="php:function('lang', 'rental_rc_search_where')"/> 
							<select name="search_option" id="ctr_toggle_contract_type">
								<option value="all"><xsl:value-of select="php:function('lang', 'rental_tenant_all')"/></option>
								<option value="id"><xsl:value-of select="php:function('lang', 'rental_tenant_id')"/></option>
								<option value="name"><xsl:value-of select="php:function('lang', 'rental_tenant_name')"/></option>
								<option value="address"><xsl:value-of select="php:function('lang', 'rental_tenant_address')"/></option>
								<option value="ssn"><xsl:value-of select="php:function('lang', 'rental_tenant_ssn')"/></option>
								<option value="result_unit_number"><xsl:value-of select="php:function('lang', 'rental_tenant_result_unit_number')"/></option>
								<option value="organisation_number"><xsl:value-of select="php:function('lang', 'rental_tenant_organisation_number')"/></option>
								<option value="account"><xsl:value-of select="php:function('lang', 'rental_tenant_account')"/></option>
							</select>
						</label>
					</td>
					<td class="toolbarcol" id="searchSubmitContainer">
						<input type="submit" id="ctrl_search_button" name="ctrl_search_button">
							<xsl:attribute name="value">
								<xsl:value-of select="php:function('lang', 'rental_rc_search')"/>
							</xsl:attribute>
						</input>
					</td>
					<td class="toolbarcol" id="resetFormContainer">
						<input type="button" id="ctrl_reset_button">
							<xsl:attribute name="value">        
								<xsl:value-of select="php:function('lang', 'rental_reset')"/>
							</xsl:attribute>
						</input>
					</td>
				</tr>
			</table>
		</div>
		<div id="datatableToolbar">
			<table class="datatableToolbar">
				<tr>	
					<td class="toolbarlabel">
						<label><b>Filtre</b></label>
					</td>
					<td class="toolbarcol">
						<label class="toolbar_element_label" for="ctrl_toggle_tenant_type"><xsl:value-of select="php:function('lang', 'rental_contract_status')"/></label>
						<select name="tenant_type" id="ctrl_toggle_tenant_type">
							<option value="internal"><xsl:value-of select="php:function('lang', 'rental_tenant_internal')"/></option>
							<option value="external"><xsl:value-of select="php:function('lang', 'rental_tenant_external')"/></option>
							<option value="all"><xsl:value-of select="php:function('lang', 'rental_tenant_all')"/></option>
						</select>
					</td>
				</tr>
			</table>
		</div>
	</form>
</xsl:template>


