<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div>
        <div id="details">
            <xsl:if test="message != ''">
                <div class="success">
                    <xsl:value-of select="message" disable-output-escaping="yes" />
                </div>
            </xsl:if>
            <xsl:if test="error != ''">
                <div class="error">
                    <xsl:value-of select="error" disable-output-escaping="yes" />
                </div>
            </xsl:if>
        </div>
        <div class="pageTop">
            <h1><xsl:value-of select="php:function('lang', 'activity')" /></h1>
            <div>
                <xsl:value-of select="php:function('lang', 'required_fields')" />
            </div>
        </div>
        <form action="" method="post" name="form" id="form">
            <input type="hidden" name="id">
                <xsl:attribute name="value">
                    <xsl:value-of select="activity/id" />
                </xsl:attribute>
            </input>
            <dl class="proplist-col">
                <fieldset>
                    <xsl:attribute name="title">
                        <xsl:value-of select="php:function('lang', 'what')" />
                    </xsl:attribute>
                    <legend>Hva</legend>
                    <dt>
                        <label for="title">
                            <xsl:value-of select="php:function('lang', 'activity_title')" /> (*) 
                            <a href="javascript:void(0);">
                                <xsl:attribute name="onclick">
                                    alert('<xsl:value-of select="php:function('lang', 'help_new_activity_title')" />');return false;
                                </xsl:attribute>
                                <img alt="Hjelp" src="{helpImg}" />
                            </a>
                        </label>
                    </dt>
                    <dd>
                        <input type="text" name="title" id="title" size="83" maxlength="254">
                            <xsl:attribute name="value">
                                <xsl:value-of select="activity/title" />
                            </xsl:attribute>
                        </input>
                    </dd>
                    <dt>
                        <label for="org_description">
                            <xsl:value-of select="php:function('lang', 'description')" /> (*) 
                            <a href="javascript:void(0);">
                                <xsl:attribute name="onclick">
                                    alert('<xsl:value-of select="php:function('lang', 'help_new_activity_description')" />');return false;
                                </xsl:attribute>
                                <img alt="Hjelp" src="{helpImg}" />
                            </a>
                        </label>
                    </dt>
                    <dd>
                        <textarea cols="80" rows="4" name="description" id="description">
                            <xsl:value-of select="activity/description" />
                        </textarea>
                    </dd>
                    <dt>
                        <label for="category">
                            <xsl:value-of select="php:function('lang', 'category')" /> (*) 
                            <a href="javascript:void(0);">
                                <xsl:attribute name="onclick">
                                    alert('<xsl:value-of select="php:function('lang', 'help_new_activity_category')" />');return false;
                                </xsl:attribute>
                                <img alt="Hjelp" src="{helpImg}" />
                            </a>
                        </label>
                    </dt>
                    <dd>
                        <select name="category" id="category">
                            <option value="">Ingen kategori valgt</option>
                            <xsl:for-each select="categories/list">
                                <option>
                                    <xsl:attribute name="value">
                                        <xsl:value-of select="id" />
                                    </xsl:attribute>
                                    <xsl:if test="../current_category_id = id">
                                        <xsl:attribute name="selected">
                                            <xsl:value-of select="selected" />
                                        </xsl:attribute>
                                    </xsl:if>
                                    <xsl:value-of select="name" />
                                </option>
                            </xsl:for-each>
                        </select>
                    </dd>
                </fieldset>
                <fieldset id="hvem">
                    <legend>For hvem</legend>
                    <dt>
                        <label for="target">
                            <xsl:value-of select="php:function('lang', 'target')" /> (*) 
                            <a href="javascript:void">
                                <xsl:attribute name="onclick">
                                    alert('<xsl:value-of select="php:function('lang', 'help_new_activity_target')" />');return false;
                                </xsl:attribute>
                                <img alt="Hjelp" src="{helpImg}" />
                            </a>
                        </label>
                    </dt>
                    <dd>
                        <xsl:for-each select="targets">
                            <input name="target[]" type="checkbox">
                                <xsl:if test="checked != ''">
                                    <xsl:attribute name="checked">
                                        <xsl:value-of select="checked" />
                                    </xsl:attribute>
                                </xsl:if>
                                <xsl:attribute name="value">
                                    <xsl:value-of select="id" />
                                </xsl:attribute>
                            </input>
                            <xsl:value-of select="name" />
                            <br />
                        </xsl:for-each>
                    </dd>
                    <dt>
                        <input type="checkbox" name="special_adaptation" id="special_adaptation">
                            <xsl:if test="activity/special_adaptation = 1">
                                <xsl:attribute name="checked">
                                    checked
                                </xsl:attribute>
                            </xsl:if>
                        </input>
                        <label for="special_adaptation">
                            <xsl:value-of select="php:function('lang', 'special_adaptation')" />
                            <a href="javascript:void(0);">
                                <xsl:attribute name="onclick">
                                    alert('<xsl:value-of select="php:function('lang', 'help_new_activity_spec_adapt')" />');return false;
                                </xsl:attribute>
                                <img alt="Hjelp" src="{helpImg}" />
                            </a>
                        </label>
                    </dt>
                </fieldset>
                <fieldset title="hvor">
                    <legend>Hvor og når</legend>
                    <dt>
                        <br />
                        <label for="arena">
                            <xsl:value-of select="php:function('lang', 'location')" /> (*) 
                            <a href="javascript:void(0);">
                                <xsl:attribute name="onclick">
                                    alert('<xsl:value-of select="php:function('lang', 'help_edit_activity_location')" />');return false;
                                </xsl:attribute>
                                <img alt="Hjelp" src="{helpImg}" />
                            </a>
                        </label>
                        <br />
                    </dt>
                    <dd>
                        <select name="internal_arena_id" id="internal_arena_id" style="width:200px">
                            <option value="">Lokale ikke valgt</option>
                            <optgroup>
                                <xsl:attribute name="label">
                                    <xsl:value-of select="php:function('lang', 'building')" />
                                </xsl:attribute>
                                <xsl:for-each select="buildings/list">
                                    <option>
                                        <xsl:if test="../selected_internal_arena = id">
                                            <xsl:attribute name="selected">
                                                selected
                                            </xsl:attribute>
                                        </xsl:if>
                                        <xsl:attribute name="value">
                                            <xsl:value-of select="concat('i_', id)" />
                                        </xsl:attribute>
                                        <xsl:value-of select="name" />
                                    </option>
                                </xsl:for-each>
                            </optgroup>
                            <optgroup>
                                <xsl:attribute name="label">
                                    <xsl:value-of select="php:function('lang', 'external_arena')" />
                                </xsl:attribute>
                                <xsl:for-each select="arenas/list">
                                    <option>
                                        <xsl:if test="../selected_arena = id">
                                            <xsl:attribute name="selected">
                                                selected
                                            </xsl:attribute>
                                        </xsl:if>
                                        <xsl:attribute name="value">
                                            <xsl:value-of select="id" />
                                        </xsl:attribute>
                                        <xsl:attribute name="title">
                                            <xsl:value-of select="name" />
                                        </xsl:attribute>
                                        <xsl:value-of select="name" />
                                    </option>
                                </xsl:for-each>
                            </optgroup>
                        </select>
                        <br />
                    </dd>
                    <dt>
                        <label for="district">
                            <xsl:value-of select="php:function('lang', 'district')" /> (*) 
                            <a href="javascript:void(0);">
                                <xsl:attribute name="onclick">
                                    alert('<xsl:value-of select="php:function('lang', 'help_new_activity_district')" />');return false;
                                </xsl:attribute>
                                <img alt="Hjelp" src="{helpImg}" />
                            </a>
                        </label>
                    </dt>
                    <dd>
                        <xsl:for-each select="districts/list">
                            <input type="radio" name="district">
                                <xsl:attribute name="value">
                                    <xsl:value-of select="part_of_town_id" />
                                </xsl:attribute>
                                <xsl:if test="../current_district_id = part_of_town_id">
                                    <xsl:attribute name="checked">
                                        <xsl:value-of select="checked" />
                                    </xsl:attribute>
                                </xsl:if>
                            </input>
                            <xsl:value-of select="name" /><br />
                        </xsl:for-each>
                    </dd>
                    <dt>
                        <label for="time">
                            <xsl:value-of select="php:function('lang', 'time')" /> (*) 
                            <a href="javascript:void(0);">
                                <xsl:attribute name="onclick">
                                    alert('<xsl:value-of select="php:function('lang', 'help_new_activity_time')" />');return false;
                                </xsl:attribute>
                                <img alt="Hjelp" src="{helpImg}" />
                            </a>
                        </label>
                    </dt>
                    <dd>
                        <input type="text" name="time" id="time" size="80" maxlength="254">
                            <xsl:attribute name="value">
                                <xsl:value-of select="activity/time" />
                            </xsl:attribute>
                        </input>
                    </dd>
                </fieldset>
                <fieldset id="arr">
                    <legend>Kontaktperson</legend>
                    <br />
                    Kontaktperson for aktiviteten 
                    <a href="javascript:void(0);">
                        <xsl:attribute name="onclick">
                            alert('<xsl:value-of select="php:function('lang', 'help_new_activity_contact_person')" />');return false;
                        </xsl:attribute>
                        <img alt="Hjelp" src="{helpImg}" />
                    </a>
                    <br />
                    <dt><label for="contact_name">Navn (*)</label></dt>
                    <dd>
                        <input type="text" name="contact_name" id="contact_name" size="80">
                            <xsl:attribute name="value">
                                <xsl:value-of select="activity/contact1_name" />
                            </xsl:attribute>
                        </input>
                    </dd>
                    <dt><label for="contact_phone">Telefon (*)</label></dt>
                    <dd>
                        <input type="text" name="contact_phone" id="contact_phone">
                            <xsl:attribute name="value">
                                <xsl:value-of select="activity/contact1_phone" />
                            </xsl:attribute>
                        </input>
                    </dd>
                    <dt><label for="contact_mail">E-post (*)</label></dt>
                    <dd>
                        <input type="text" name="contact_mail" id="contact_mail" size="50">
                            <xsl:attribute name="value">
                                <xsl:value-of select="activity/contact1_mail" />
                            </xsl:attribute>
                        </input>
                    </dd>
                    <dt><label for="contact_mail2">Gjenta e-post (*)</label></dt>
                    <dd>
                        <input type="text" name="contact_mail2" id="contact_mail2" size="50">
                            <xsl:attribute name="value">
                                <xsl:value-of select="activity/contact1_mail" />
                            </xsl:attribute>
                        </input>
                    </dd>                    
                </fieldset>
                <fieldset>
                    <br />
                    <dt>
                        <label for="office">
                            Hvilket kulturkontor skal motta registreringen (*) 
                            <a href="javascript:void(0);">
                                <xsl:attribute name="onclick">
                                    alert('<xsl:value-of select="php:function('lang', 'help_new_activity_office')" />');return false;
                                </xsl:attribute>
                                <img alt="Hjelp" src="{helpImg}" />
                            </a>
                        </label>
                    </dt>
                    <dd>
                        <select name="office" id="office">
                            <option value="0">Ingen kontor valgt</option>
                            <xsl:for-each select="offices/list">
                                <option>
                                    <xsl:if test="../selected_office = id">
                                        <xsl:attribute name="selected">
                                            selected
                                        </xsl:attribute>
                                    </xsl:if>
                                    <xsl:attribute name="value">
                                        <xsl:value-of select="id" />
                                    </xsl:attribute>
                                    <xsl:value-of select="name" />
                                </option>
                            </xsl:for-each>
                        </select>
                    </dd>
                </fieldset>
                <div class="form-buttons">
                    <xsl:if test="editable">
                        <input type="submit" name="save_activity" onclick="return allOK();">
                            <xsl:attribute name="value">
                                <xsl:value-of select="php:function('lang', 'save')" />
                            </xsl:attribute>
                        </input>
                    </xsl:if>
                </div>
            </dl>
        </form>
    </div>
    <script type="text/javascript">
        var org_id = $('#organization_id').val();
        <xsl:if test="activity/group_id">
            var group_id = '<xsl:value-of select="activity/group_id" />';
            var availableGroupsURL = phpGWLink('activitycalendarfrontend/', {menuaction: 'activitycalendarfrontend.uiactivity.get_organization_groups', orgid: org_id, groupid: group_id}, true);
        </xsl:if>
        <xsl:if test="not(activity/group_id)">
            var availableGroupsURL = phpGWLink('activitycalendarfrontend/', {menuaction: 'activitycalendarfrontend.uiactivity.get_organization_groups', orgid: org_id}, true);
        </xsl:if>
    </script>
</xsl:template>
