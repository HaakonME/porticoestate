<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">
        
	<dl class="form">
    	<dt class="heading">
			<xsl:if test="new_form">
				<xsl:value-of select="php:function('lang', 'New Organization')" />
			</xsl:if>
			<xsl:if test="not(new_form)">
				<xsl:value-of select="php:function('lang', 'Edit Organization')" />
			</xsl:if>
		</dt>
	</dl>
	
    <xsl:call-template name="msgbox"/>

    <form action="" method="POST">
                <dl class="form-col">
                    <dt><label for="field_name"><xsl:value-of select="php:function('lang', 'Name')" /></label></dt>
                    <dd>
                        <input id="inputs" name="name" type="text">
                            <xsl:attribute name="value"><xsl:value-of select="organization/name"/></xsl:attribute>
                        </input>
                    </dd>
                    <dt><label for="field_name"><xsl:value-of select="php:function('lang', 'Organization number')" /></label></dt>
                    <dd>
                        <input type="text"/>
                    </dd>
                    <dt><label for="field_homepage"><xsl:value-of select="php:function('lang', 'Homepage')" /></label></dt>
                    <dd>
                        <input id="field_homepage" name="homepage" type="text">
                            <xsl:attribute name="value"><xsl:value-of select="organization/homepage"/></xsl:attribute>
                        </input>
                    </dd>
                    <dt><label for="field_phone"><xsl:value-of select="php:function('lang', 'Phone')" /></label></dt>
                    <dd>
                        <input id="field_phone" name="phone" type="text">
                            <xsl:attribute name="value"><xsl:value-of select="organization/phone"/></xsl:attribute>
                        </input>
                    </dd>
                    <dt><label for="field_email"><xsl:value-of select="php:function('lang', 'Email')" /></label></dt>
                    <dd>
                        <input id="field_email" name="email" type="text">
                            <xsl:attribute name="value"><xsl:value-of select="organization/email"/></xsl:attribute>
                        </input>
                    </dd>

                    <dt>
                        <label for="field_admin_primary"><xsl:value-of select="php:function('lang', 'Primary Admin')" /></label><br />
                        <a href="#" onclick="return createContact();">(<xsl:value-of select="php:function('lang', 'Create a new contact')" />)</a>
                    </dt>
                    <dd>
                        <ul>
                            <li style="float: left">
                                <div class="autocomplete">
                                    <input id="field_admin_primary" name="admin_primary" type="hidden">
                                        <xsl:attribute name="value"><xsl:value-of select="organization/admin_primary/id"/></xsl:attribute>
                                    </input>
                                    <input name="admin_primary_name" type="text" id="field_admin_primary_name" >
                                        <xsl:attribute name="value"><xsl:value-of select="organization/admin_primary/name"/></xsl:attribute>
                                    </input>
                                    <div id="primary_admin_container"/>
                                </div>
                            </li>
                            <li style="float: left; margin-left: 10px" id="field_admin_primary_name_edit" class="showit">
                                <a href="#" onclick="return editContact('field_admin_primary');"><xsl:value-of select="php:function('lang', 'Edit')" /></a>
                            </li>
                        </ul>

                    </dd>

                    <dt><label for="field_admin_secondary"><xsl:value-of select="php:function('lang', 'Secondary Admin')" /></label></dt>
                    <dd>
                        <ul>
                            <li style="float: left">
                                <div class="autocomplete">
                                    <input id="field_admin_secondary" name="admin_secondary" type="hidden">
                                        <xsl:attribute name="value"><xsl:value-of select="organization/admin_secondary/id"/></xsl:attribute>
                                    </input>
                                    <input name="admin_secondary_name" type="text" id="field_admin_secondary_name" >
                                        <xsl:attribute name="value"><xsl:value-of select="organization/admin_secondary/name"/></xsl:attribute>
                                    </input>
                                    <div id="secondary_admin_container"/>
                                </div>
                            </li>
                            <li style="float: left; margin-left: 10px" id="field_admin_secondary_name_edit" class="showit">
                                <a href="#" onclick="return editContact('field_admin_secondary');"><xsl:value-of select="php:function('lang', 'Edit')" /></a>
                            </li>
                        </ul>
                    </dd>


                    <dt><label for="field_description"><xsl:value-of select="php:function('lang', 'Description')" /></label></dt>
                    <dd class="yui-skin-sam">
                        <textarea id="field-description" name="description" type="text"><xsl:value-of select="organization/description"/></textarea>
                    </dd>
                </dl>
                <dl class="form-col">
		            <dt><label for="field_street"><xsl:value-of select="php:function('lang', 'Street')"/></label></dt>
		            <dd><input id="field_street" name="street" type="text" value="{organization/street}"/></dd>

					<dt><label for="field_zip_code"><xsl:value-of select="php:function('lang', 'Zip code')"/></label></dt>
		            <dd><input type="text" name="zip_code" id="field_zip_code" value="{organization/zip_code}"/></dd>
		            
					<dt><label for="field_city"><xsl:value-of select="php:function('lang', 'City')"/></label></dt>
		            <dd><input type="text" name="city" id="field_city" value="{organization/city}"/></dd>
		            
					<dt><label for='field_district'><xsl:value-of select="php:function('lang', 'District')"/></label></dt>
		            <dd><input type="text" name="district" id="field_district" value="{organization/district}"/></dd>

					<xsl:if test="not(new_form)">
			            <dt><label for="field_active"><xsl:value-of select="php:function('lang', 'Active')"/></label></dt>
			            <dd>
			                <select id="field_active" name="active">
			                    <option value="1">
			                    	<xsl:if test="organization/active=1">
			                    		<xsl:attribute name="selected">checked</xsl:attribute>
			                    	</xsl:if>
			                        <xsl:value-of select="php:function('lang', 'Active')"/>
			                    </option>
			                    <option value="0">
			                    	<xsl:if test="organization/active=0">
			                    		<xsl:attribute name="selected">checked</xsl:attribute>
			                    	</xsl:if>
			                        <xsl:value-of select="php:function('lang', 'Inactive')"/>
			                    </option>
			                </select>
			            </dd>
					</xsl:if>
                </dl>

<script type="text/javascript">
var endpoint = '<xsl:value-of select="module" />';
<![CDATA[
var descEdit = new YAHOO.widget.SimpleEditor('field-description', {
    height: '300px',
    width: '522px',
    dompath: true,
    animate: true,
	handleSubmit: true
});
descEdit.render();

// Autocomplete primary admin
YAHOO.booking.autocompleteHelper('index.php?menuaction=' + endpoint + '.uicontactperson.index&phpgw_return_as=json&', 
    'field_admin_primary_name',
    'field_admin_primary',
    'primary_admin_container'
);
// Autocomplete secondary contact
YAHOO.booking.autocompleteHelper('index.php?menuaction=' + endpoint + '.uicontactperson.index&phpgw_return_as=json&', 
    'field_admin_secondary_name',
    'field_admin_secondary',
    'secondary_admin_container'
);


YAHOO.util.Event.addListener(YAHOO.util.Dom.get("field_admin_primary_name"), "change", showIfNotEmpty, "field_admin_primary_name"); 
YAHOO.util.Event.addListener(YAHOO.util.Dom.get("field_admin_secondary_name"), "change", showIfNotEmpty, "field_admin_secondary_name"); 

showIfNotEmpty(null, "field_admin_primary_name"); 
showIfNotEmpty(null, "field_admin_secondary_name"); 
]]>
</script>
        <div class="form-buttons">
            <input type="submit">
				<xsl:if test="new_form">
					<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Create')" /></xsl:attribute>
				</xsl:if>
				<xsl:if test="not(new_form)">
					<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Save')" /></xsl:attribute>
				</xsl:if>
			</input>
            <a class="cancel">
                <xsl:attribute name="href"><xsl:value-of select="organization/cancel_link"/></xsl:attribute>
                <xsl:value-of select="php:function('lang', 'Cancel')" />
            </a>
        </div>
    </form>
    </div>

    <xsl:call-template name="contactpersonmagic" />

</xsl:template>


