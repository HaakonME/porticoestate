<!--
	Function
	phpgw:conditional( expression $test, mixed $true, mixed $false )
	Evaluates test expression and returns the contents in the true variable if
	the expression is true and the contents of the false variable if its false

	Returns mixed
-->
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

<xsl:include href="rental/templates/base/datasource_definition.xsl"/>

<xsl:template match="/">
	<script>
		YAHOO.rental.numberOfDatatables = <xsl:value-of select="count(//datatable)"/>;
	</script>
	<xsl:apply-templates select="//form"/>
	<div id="rental_user_error"></div>
	<div id="rental_user_message"></div>
	<xsl:apply-templates select="//datatable" />
</xsl:template>

<xsl:template match="form">
	<form id="queryForm">
		<xsl:attribute name="method">
			<xsl:value-of select="phpgw:conditional(not(method), 'GET', method)"/>
		</xsl:attribute>

		<xsl:attribute name="action">
			<xsl:value-of select="phpgw:conditional(not(action), '', action)"/>
		</xsl:attribute>
        <xsl:for-each select="*">
        	<xsl:if test="./toolbar">
        		<xsl:call-template name="toolbar"/>
        	</xsl:if>
        </xsl:for-each>
	</form>
</xsl:template>

<xsl:template name="toolbar">
    <div id="toolbar"><table class="toolbartable"><tr>
    	<td class="toolbarlabel"><label><b><xsl:value-of select="./label"/></b></label></td>
        <xsl:for-each select="*">
        	<div class="toolbarelement">
	        	<xsl:if test="control = 'input'">
	        		<td class="toolbarcol">
					<label class="toolbar_element_label">
				    <xsl:attribute name="for"><xsl:value-of select="phpgw:conditional(not(id), '', id)"/></xsl:attribute>
				    <xsl:value-of select="phpgw:conditional(not(text), '', text)"/>
				    </label>
				    <input>
			        	<xsl:attribute name="id"><xsl:value-of select="phpgw:conditional(not(id), '', id)"/></xsl:attribute>
			    		<xsl:attribute name="type"><xsl:value-of select="phpgw:conditional(not(type), '', type)"/></xsl:attribute>
			    		<xsl:attribute name="name"><xsl:value-of select="phpgw:conditional(not(name), '', name)"/></xsl:attribute>
			    		<xsl:attribute name="onClick"><xsl:value-of select="phpgw:conditional(not(onClick), '', onClick)"/></xsl:attribute>
			    		<xsl:attribute name="value"><xsl:value-of select="phpgw:conditional(not(value), '', value)"/></xsl:attribute>
			    		<xsl:attribute name="href"><xsl:value-of select="phpgw:conditional(not(href), '', href)"/></xsl:attribute>
			    		<!-- <xsl:attribute name="class">yui-button yui-menu-button yui-skin-sam yui-split-button yui-button-hover button</xsl:attribute> -->
				    </input>
				    </td>
				</xsl:if>
				<xsl:if test="control = 'select'">
					<td class="toolbarcol">
					<label class="toolbar_element_label">
				    <xsl:attribute name="for"><xsl:value-of select="phpgw:conditional(not(id), '', id)"/></xsl:attribute>
				    <xsl:value-of select="phpgw:conditional(not(text), '', text)"/>
				    </label>
				    <select>
					<xsl:attribute name="id"><xsl:value-of select="phpgw:conditional(not(id), '', id)"/></xsl:attribute>
					<xsl:attribute name="name"><xsl:value-of select="phpgw:conditional(not(name), '', name)"/></xsl:attribute>
					<xsl:attribute name="onchange"><xsl:value-of select="phpgw:conditional(not(onchange), '', onchange)"/></xsl:attribute>
			   		<xsl:for-each select="keys">
			   			<xsl:variable name="p" select="position()" />
			   			<option>
			   				<xsl:attribute name="value"><xsl:value-of select="text()"/></xsl:attribute>
			   				<xsl:if test="text() = ../default"><xsl:attribute name="default"/></xsl:if>
			   				<xsl:value-of select="../values[$p]"/>
			   			</option>
			   		</xsl:for-each>
			   		</select>
			   		</td>
				</xsl:if>
			</div>
        </xsl:for-each> 
    </tr></table></div>
</xsl:template>

<xsl:template match="datatable" name="datatable" xmlns:php="http://php.net/xsl">
	<div class="datatable">
		<div id="paginator"/>
	    <div id="columnshowhide"></div>
		<div id="dt-dlg">
		    <div class="hd">Velg hvilke kolonner du ønsker å se:</div>
		    <div id="dt-dlg-picker" class="bd"></div>
		</div>
    	<div id="datatable-container"/>
  		<xsl:call-template name="datasource-definition">
  			<xsl:with-param name="number">1</xsl:with-param>
  			<xsl:with-param name="form">queryForm</xsl:with-param>
  			<xsl:with-param name="filters">ctrl_toggle_active_rental_composites</xsl:with-param>
  			<xsl:with-param name="container_name">datatable-container</xsl:with-param>
  			<xsl:with-param name="context_menu_labels">
				['<xsl:value-of select="php:function('lang', 'rental_cm_show')"/>',
				'<xsl:value-of select="php:function('lang', 'rental_cm_edit')"/>']
			</xsl:with-param>
			<xsl:with-param name="context_menu_actions">
					['view',
					'edit']	
			</xsl:with-param>
  		</xsl:call-template>
  	</div>
</xsl:template>
