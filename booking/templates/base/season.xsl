<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <!--div id="content">
        <ul class="pathway">
            <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="season/buildings_link"/></xsl:attribute>
                    <xsl:value-of select="php:function('lang', 'Buildings')" />
                </a>
            </li>
            <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="season/building_link"/></xsl:attribute>
                    <xsl:value-of select="season/building_name"/>
                </a>
            </li>
            <li><xsl:value-of select="php:function('lang', 'Season')" /></li>
            <li><a href=""><xsl:value-of select="season/name"/></a></li>
        </ul-->

        <xsl:call-template name="msgbox"/>
		<!--xsl:call-template name="yui_booking_i18n"/-->  
    
	<form action="" method="POST" id='form'  class="pure-form pure-form-aligned" name="form">
		<div id="tab-content">
			<input type="hidden" name="tab" value=""/>
			<xsl:value-of disable-output-escaping="yes" select="season/tabs"/>
			<div id="season_show" class="booking-container"> 
            
                <div class="pure-control-group">
                    <label>
                        <xsl:value-of select="php:function('lang', 'Case officer')" />
                    </label>
                    <xsl:value-of select="season/officer_name"/>
                </div>
                <div class="pure-control-group">
                    <label>
                        <xsl:value-of select="php:function('lang', 'From')" />
                    </label>
                    <xsl:value-of select="php:function('pretty_timestamp', season/from_)"/>
                </div>
                <div class="pure-control-group">
                    <label>
                        <xsl:value-of select="php:function('lang', 'To')" />
                    </label>
                    <xsl:value-of select="php:function('pretty_timestamp', season/to_)"/>
                </div>
                <div class="pure-control-group">
                    <label>
                        <xsl:value-of select="php:function('lang', 'Building')" />
                    </label>
                    <xsl:value-of select="season/building_name"/>
                </div>
                <div class="pure-control-group">
                    <label>
                        <xsl:value-of select="php:function('lang', 'Status')" />
                    </label>
                    <xsl:value-of select="season/status"/>
                </div>
                <div class="pure-control-group">
                    <label>
                        <xsl:value-of select="php:function('lang', 'Resources')" />
                    </label>
                    <div id="resources_container" class="custom-container"></div>
                </div>
                <div class="pure-control-group">
                    <label>
                        <xsl:value-of select="php:function('lang', 'Permissions')" />
                    </label>
                    <div id="permissions_container" class="custom-container"></div>   
                </div>                
			</div>
		</div>
		<div class="form-buttons">
			<xsl:if test="season/permission/write">
				<input type="button" class="pure-button pure-button-primary" name="edit">
					<xsl:attribute name="onclick">window.location.href="<xsl:value-of select="season/edit_link"/>"</xsl:attribute>
					<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Edit')" /></xsl:attribute>	
				</input>
			</xsl:if>
			<input type="button" class="pure-button pure-button-primary" name="boundaries">
				<xsl:attribute name="onclick">window.location.href="<xsl:value-of select="season/boundaries_link"/>"</xsl:attribute>
				<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Boundaries')" /></xsl:attribute>	
			</input>
			<input type="button" class="pure-button pure-button-primary" name="week_template">
				<xsl:attribute name="onclick">window.location.href="<xsl:value-of select="season/wtemplate_link"/>"</xsl:attribute>
				<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Week template')" /></xsl:attribute>	
			</input>
		</div>		
	</form>

    <!--/div-->

<script type="text/javascript">
    var season_id = <xsl:value-of select="season/id"/>;
    var resourceIds = '<xsl:value-of select="season/resource_ids"/>';
    var lang = <xsl:value-of select="php:function('js_lang', 'Name', 'Account', 'Role', 'Actions', 'Edit', 'Delete', 'Resource Type')"/>;
                
    <![CDATA[
        var resourcesURL    = 'index.php?menuaction=booking.uiresource.index&sort=name&phpgw_return_as=json&' + resourceIds;
        var permissionsURL   = 'index.php?menuaction=booking.uipermission_season.index&sort=name&filter_object_id=' + season_id + '&phpgw_return_as=json&';
    ]]>
    var colDefsRespurces = [
        {key: 'name', label: lang['Name'], formatter: genericLink},
        {key: 'type', label: lang['Resource Type']}
    ];
    var colDefsPermissions = [
        {key: 'subject_name', label: lang['Account']},
        {key: 'role', label: lang['Role']},
        {key: 'actions', label: lang['Actions'], formatter: genericLink(lang['Edit'], lang['Delete'])}
    ];
    createTable('resources_container', resourcesURL, colDefsRespurces);
    createTable('permissions_container', permissionsURL, colDefsPermissions);


/*
    $.get(resourcesURL, function(resourcesData){
        var resourcesBody = '';
        var resourcesTableClass = "pure-table";
        if (resourcesData.data.length === 0){
            resourcesBody = '<tr><td colspan="2">'+lang['No records found']+'</td></tr>';
        }else{
            resourcesTableClass = "pure-table pure-table-striped";
            $.each(resourcesData.data , function(index,value){
                <![CDATA[
                resourcesBody += '<tr><td><a href='+value.link+'>'+value.name+'</a></td><td>'+value.type+'</td></tr>';
                ]]>
            });
        }
        <![CDATA[
            var resourcesTable = '<table class="'+resourcesTableClass+'"><thead><tr><th>'+lang['Name']+'</th><th>'+lang['Resource Type']+'</th></tr></thead><tbody>'+resourcesBody+'</tbody></table>';
        ]]>
        $('#resources_container').html(resourcesTable);
    });
    
    $.get(permissionsURL, function(permissionData){
        var permissionBody = '';
        var permissionTableClass = "pure-table";
        <![CDATA[
            var permissionHead = '<tr><th>'+lang['Account']+'</th><th>'+lang['Role']+'</th><th>'+lang['Actions']+'</th></tr>';
        ]]>;
        if (permissionData.data.length === 0) {
            permissionBody = '<tr><td colspan="3">'+lang['No records found']+'</td></tr>';
        }else {
            permissionTableClass = "pure-table pure-table-striped";
            $.each(permissionData.data, function(index, value) {
                <![CDATA[
                permissionBody += '<tr><td>'+value.subject_name+'</td><td>'+value.role+'</td><td><a href="'+value.opcion_edit+'">'+lang['Edit']+'</a>&nbsp;<a href="'+value.opcion_delete+'">'+lang['Delete']+'</a></td></tr>';
                ]]>
            });
        };
        <![CDATA[
            var permissionTable = '<table class="'+permissionTableClass+'"><thead>'+permissionHead+'</thead><tbody>'+permissionBody+'</tbody></table>'
        ]]>
        $('#permissions_container').html(permissionTable);
    });
*/
</script>
<!--script>
            
        YAHOO.util.Event.addListener(window, "load", function() {
	    <![CDATA[
	    var url = 'index.php?menuaction=booking.uiresource.index&sort=name&phpgw_return_as=json&' + resourceIds;
	        ]]>
	    var colDefs = [{key: 'name', label: lang['Name'], formatter: YAHOO.booking.formatLink}, {key: 'type', label: lang['Resource Type']}];
	    YAHOO.booking.inlineTableHelper('resources_container', url, colDefs);
	
		<![CDATA[ var url = 'index.php?menuaction=booking.uipermission_season.index&sort=name&filter_object_id=' + season_id + '&phpgw_return_as=json&'; ]]>
		var colDefs = [{key: 'subject_name', label: lang['Account']}, {key: 'role', label: lang['Role']}, {key: 'actions', label: lang['Actions'], formatter: YAHOO.booking.formatGenericLink(lang['Edit'], lang['Delete'])}];
		YAHOO.booking.inlineTableHelper('permissions_container', url, colDefs);
	});
</script-->

</xsl:template>
