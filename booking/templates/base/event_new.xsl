<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <style type="text/css">
        #agegroup td {padding: 0 0.3em;}
    </style>
    <xsl:call-template name="msgbox"/>
    <form action="" method="POST" id='form' class="pure-form pure-form-stacked" name="form">
        <input type="hidden" name="tab" value=""/>
        <div id="tab-content">
            <xsl:value-of disable-output-escaping="yes" select="event/tabs"/>
            <div id="event_new" class="booking-container">
                <fieldset>
                    <input type="hidden" name="application_id" value="{event/application_id}"/>
                    <div class="pure-g">
                        <div class="pure-u-1">
                            <div class="heading">
                                <legend><h3><xsl:value-of select="php:function('lang', 'Why')" /></h3></legend>
                            </div>
                            <div class="pure-control-group">
                                <label for="field_activity"><xsl:value-of select="php:function('lang', 'Activity')" /></label>
                                <select name="activity_id" id="field_activity" class="pure-u-1 pure-u-sm-1-2 pure-u-lg-1-3">
                                    <option value=""><xsl:value-of select="php:function('lang', '-- select an activity --')" /></option>
                                    <xsl:for-each select="activities">
                                        <option>
                                            <xsl:if test="../event/activity_id = id">
                                                <xsl:attribute name="selected">selected</xsl:attribute>
                                            </xsl:if>
                                            <xsl:attribute name="value"><xsl:value-of select="id"/></xsl:attribute>
                                            <xsl:value-of select="name"/>
                                        </option>
                                    </xsl:for-each>
                                </select>
                            </div>
                            <div class="pure-control-group">
                                <label for="field_description"><xsl:value-of select="php:function('lang', 'Description')" /></label>
                                <textarea id="field_description" class="full-width pure-u-1 pure-u-sm-1-2 pure-u-lg-1-3" name="description"><xsl:value-of select="event/description"/></textarea>
                            </div>
                            <div class="pure-control-group">
                                <label for="field_public"><xsl:value-of select="php:function('lang', 'Event type')"/></label>
                                <select id="field_public" name="is_public" class="pure-u-1 pure-u-sm-1-2 pure-u-lg-1-3">
                                    <option value="1">
                                          <xsl:if test="event/is_public=1">
                                            <xsl:attribute name="selected">checked</xsl:attribute>
                                          </xsl:if>
                                          <xsl:value-of select="php:function('lang', 'Public event')"/>
                                    </option>
                                    <option value="0">
                                          <xsl:if test="event/is_public=0">
                                            <xsl:attribute name="selected">checked</xsl:attribute>
                                          </xsl:if>
                                          <xsl:value-of select="php:function('lang', 'Private event')"/>
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="pure-g">
                        <div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
                            <div class="heading">
                                <legend><h3><xsl:value-of select="php:function('lang', 'Where')" /></h3></legend>
                            </div>
                            <div class="pure-control-group">
                                <label><xsl:value-of select="php:function('lang', 'Building')" /></label>
                                <input id="field_building_id" name="building_id" type="hidden">
                                    <xsl:attribute name="value"><xsl:value-of select="event/building_id"/></xsl:attribute>
                                </input>
                                <input id="field_building_name" name="building_name" type="text" class="pure-u-1 pure-u-sm-1-2 pure-u-md-1">
                                    <xsl:attribute name="value"><xsl:value-of select="event/building_name"/></xsl:attribute>
                                </input>
                            </div>
                            <div class="pure-control-group">
                                <label><xsl:value-of select="php:function('lang', 'Resources')" /></label>
                                <div id="resources_container"><xsl:value-of select="php:function('lang', 'Select a building first')" /></div>
                            </div>
                        </div>

                        <div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
                            <div class="heading">
                                <legend><h3><xsl:value-of select="php:function('lang', 'When?')" /></h3></legend>
                            </div>
                            <div id="dates-container"  class="pure-control-group">
                                <xsl:for-each select="event/dates">
                                    <div class="date-container">
                                        <a href="javascript:void(0);" class="close-btn btnclose">-</a>
                                        <div class="pure-control-group">
                                            <label for="start_date">
                                                <xsl:value-of select="php:function('lang', 'From')" />
                                            </label>
                                            <input class="datetime pure-input-2-3" id="start_date" name="from_[]" type="text">
                                                <xsl:attribute name="value"><xsl:value-of select="from_"/></xsl:attribute>
                                            </input>
                                        </div>
                                        <div class="pure-control-group">
                                            <label for="end_date">
                                                <xsl:value-of select="php:function('lang', 'To')" />
                                            </label>
                                            <input class="datetime pure-input-2-3" id="end_date" name="to_[]" type="text">
                                                <xsl:attribute name="value"><xsl:value-of select="to_"/></xsl:attribute>
                                            </input>
                                        </div>
                                    </div>
                                </xsl:for-each>
                            </div>
                            <div class="pure-control-group">
                                <a href="javascript:void(0);" id="add-date-link"><xsl:value-of select="php:function('lang', 'Add another date')" /></a>
                            </div>
                        </div>

                        <div class="pure-u-1 pure-u-md-1 pure-u-lg-1-3">
                            <div class="heading">
                                <legend><h3><xsl:value-of select="php:function('lang', 'Who')" /></h3></legend>
                            </div>
                            <div class="pure-g">
                                <div class="pure-control-group pure-u-1 pure-u-md-1-2 pure-u-lg-1">
                                    <label><xsl:value-of select="php:function('lang', 'Target audience')" /></label>
                                    <ul style="list-style:none;">
                                        <xsl:for-each select="audience">
                                            <li>
                                                <label style="display:inline-block;">
                                                    <input type="checkbox" name="audience[]">
                                                        <xsl:attribute name="value"><xsl:value-of select="id"/></xsl:attribute>
                                                        <xsl:if test="../event/audience=id">
                                                            <xsl:attribute name="checked">checked</xsl:attribute>
                                                        </xsl:if>
                                                    </input>
                                                    <xsl:value-of select="name"/>
                                                </label>
                                            </li>
                                        </xsl:for-each>
                                    </ul>
                                </div>
                                <div class="pure-control-group pure-u-1 pure-u-md-1-2 pure-u-lg-1">
                                    <label><xsl:value-of select="php:function('lang', 'Number of participants')" /></label>
                                    <table id="agegroup" class="pure-table pure-table-bordered">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th><xsl:value-of select="php:function('lang', 'Male')" /></th>
                                                <th><xsl:value-of select="php:function('lang', 'Female')" /></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <xsl:for-each select="agegroups">
                                                <xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
                                                <tr>
                                                    <th><xsl:value-of select="name"/></th>
                                                    <td>
                                                        <input type="text" class="input50">
                                                            <xsl:attribute name="name">male[<xsl:value-of select="id"/>]</xsl:attribute>
                                                            <xsl:attribute name="value"><xsl:value-of select="../event/agegroups/male[../agegroup_id = $id]"/></xsl:attribute>
                                                        </input>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="input50">
                                                            <xsl:attribute name="name">female[<xsl:value-of select="id"/>]</xsl:attribute>
                                                            <xsl:attribute name="value"><xsl:value-of select="../event/agegroups/female[../agegroup_id = $id]"/></xsl:attribute>
                                                        </input>
                                                    </td>
                                                </tr>
                                            </xsl:for-each>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="pure-g">
                        <div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
                            <div class="heading">
                                <legend><h3><xsl:value-of select="php:function('lang', 'Cost')" /></h3></legend>
                            </div>
                            <div class="pure-control-group">
                                <label style="margin-top:10px;">&nbsp;</label>
                                <input id="field_cost" name="cost" type="text">
                                    <xsl:attribute name="value"><xsl:value-of select="event/cost"/></xsl:attribute>
                                </input>
                            </div>
                        </div>

                        <div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
                            <div class="heading">
                                <legend><h3><xsl:value-of select="php:function('lang', 'send reminder for participants statistics')" /></h3></legend>
                            </div>
                            <div class="pure-control-group">
                                <label style="margin-top:10px;">&nbsp;</label>
                                <select name="reminder" id="field_reminder">
                                    <xsl:choose>
                                        <xsl:when test="event/reminder = 1">
                                            <option value="1" selected="selected"><xsl:value-of select="php:function('lang', 'Send reminder')" /></option>
                                            <option value="0"><xsl:value-of select="php:function('lang', 'Do not send reminder')" /></option>
                                        </xsl:when>
                                        <xsl:otherwise test="event/reminder = 0">
                                            <option value="1"><xsl:value-of select="php:function('lang', 'Send reminder')" /></option>
                                            <option value="0" selected="selected"><xsl:value-of select="php:function('lang', 'Do not send reminder')" /></option>
                                        </xsl:otherwise>
                                    </xsl:choose>
                                </select>
                            </div>
                        </div>
                        <div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
                            <div class="heading">
                                <legend><h3><xsl:value-of select="php:function('lang', 'Get all contact and invoice information from organization')" /></h3></legend>
                            </div>
                            <div class="pure-control-group">
                                <label for="field_org_name"><xsl:value-of select="php:function('lang', 'Organization')" /></label>
                                <input id="field_org_id" name="organization_id" type="hidden">
                                    <xsl:attribute name="value"><xsl:value-of select="event/customer_organization_id"/></xsl:attribute>
                                </input>
                                <input id="field_org_name" name="organization_name" type="text" class="pure-u-1 pure-u-sm-1-2 pure-u-md-1">
                                    <xsl:attribute name="value"><xsl:value-of select="event/customer_organization_name"/></xsl:attribute>
                                </input>
                            </div>
                            <div class="pure-control-group">
                                <label><xsl:value-of select="php:function('lang', 'Or')" /></label>
                            </div>
                            <div class="pure-control-group">
                                <label for="field_org_id2"><xsl:value-of select="php:function('lang', 'Organization_number')" /></label>
                                <input id="field_org_id2" name="org_id2" type="text" class="pure-u-1 pure-u-sm-1-2 pure-u-md-1">
                                    <xsl:attribute name="value"><xsl:value-of select="event/org_id2"/></xsl:attribute>
                                </input>
                            </div>
                        </div>
                        <div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
                            <div class="heading">
                                <legend><h3><xsl:value-of select="php:function('lang', 'Contact information')" /></h3></legend>
                            </div>
                            <div class="pure-control-group">
                                <label for="field_contact_name"><xsl:value-of select="php:function('lang', 'Name')" /></label>
                                <input id="field_contact_name" name="contact_name" type="text" class="pure-u-1 pure-u-sm-1-2 pure-u-md-1">
                                    <xsl:attribute name="value"><xsl:value-of select="event/contact_name"/></xsl:attribute>
                                </input>
                            </div>
                            <div class="pure-control-group">
                                <label for="field_contact_mail"><xsl:value-of select="php:function('lang', 'Email')" /></label>
                                <input id="field_contact_mail" name="contact_email" type="text" class="pure-u-1 pure-u-sm-1-2 pure-u-md-1">
                                    <xsl:attribute name="value"><xsl:value-of select="event/contact_email"/></xsl:attribute>
                                </input>
                            </div>
                            <div class="pure-control-group">
                                <label for="field_contact_phone"><xsl:value-of select="php:function('lang', 'Phone')" /></label>
                                <input id="field_contact_phone" name="contact_phone" type="text" class="pure-u-1 pure-u-sm-1-2 pure-u-md-1">
                                    <xsl:attribute name="value"><xsl:value-of select="event/contact_phone"/></xsl:attribute>
                                </input>
                            </div>
                        </div>
                        <div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
                            <div class="heading">
                                <legend><h3><xsl:value-of select="php:function('lang', 'Invoice information')" /></h3></legend>
                            </div>
                            <div class="pure-control-group">
                                <xsl:copy-of select="phpgw:booking_customer_identifier(event, '')"/>
                            </div>
                            <div class="pure-control-group">
                                <label><xsl:value-of select="php:function('lang', 'Internal Customer')"/></label>
                                <xsl:copy-of select="phpgw:option_checkbox(event/customer_internal, 'customer_internal')"/>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
        <div class="form-buttons">
            <input type="submit" class="button pure-button pure-button-primary">
                <xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Create')"/></xsl:attribute>
            </input>
            <a class="cancel pure-button pure-button-primary">
                <xsl:attribute name="href"><xsl:value-of select="event/cancel_link"/></xsl:attribute>
                <xsl:value-of select="php:function('lang', 'Cancel')" />
            </a>
        </div>
    </form>
    <script type="text/javascript">
        $('#field_customer_identifier_type,#field_customer_ssn,#field_customer_organization_number').removeClass('pure-input-1').addClass('pure-u-1 pure-u-sm-1-2 pure-u-md-1');
        var initialSelection = <xsl:value-of select="event/resources_json"/>;
        var lang = <xsl:value-of select="php:function('js_lang', 'Name', 'From', 'To', 'Resource Type')"/>;
    </script>
</xsl:template>
