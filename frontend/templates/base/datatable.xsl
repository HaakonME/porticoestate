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

<xsl:template match="actions">
    <xsl:apply-templates select="form" />
</xsl:template>


<xsl:template match="form">
    <form>
        <xsl:attribute name="method">
            <xsl:value-of select="phpgw:conditional(not(method), 'POST', method)"/>
        </xsl:attribute>

        <xsl:attribute name="action">
            <xsl:value-of select="phpgw:conditional(not(action), //datatable/config/base_url, action)"/>
        </xsl:attribute>

        <xsl:apply-templates select="fields" />
    </form>
</xsl:template>

<xsl:template match="fields">
    <xsl:apply-templates select="field" />
</xsl:template>

<!--
    Template
    match=field()

    Constructs inputfields with labels that goes into form tags.

    Type of inputfield is decided by the type field.
    If text field is obmitted no label is created. This allows the same code to
    be used for creating for ex. submit buttons.

    Currently the following inputfields are supported:
    * input
    * password
    * hidden
    * checkbox
    * submit

    Label tag attributes:
        id: field/id or unique autogenerated if id is missing
    Label tag innerHTML:
        field/text, label is not rendered if field/text is empty

    Input tag attributes / value:
        id: field/id or unique autogenerated if id is missing
        type: field/type
        name: field/name
        value: field/value
        size: field/size
        checked: if field/type=checkbox and field/checked == 1 checked=checked
-->
<xsl:template match="field">
    <xsl:variable name="id" select="phpgw:conditional(id, id, generate-id())"/>
    <xsl:variable name="align">
        <xsl:choose>
            <xsl:when test="style='filter'">float:left</xsl:when>
            <xsl:otherwise>float:right</xsl:otherwise>
        </xsl:choose>
    </xsl:variable>
    <div style="{$align}" class="field">
        <xsl:if test="text">
            <label for="{$id}">
                <xsl:value-of select="text"/>
                <xsl:text> </xsl:text>
            </label>
        </xsl:if>

        <xsl:choose>
            <xsl:when test="type='link'">
                <a id="{id}" href="#" onclick="{url}" tabindex="{tab_index}"><xsl:value-of select="value"/></a>
            </xsl:when>
            <xsl:when test="type='label_date'">
                <table><tbody><tr><td><span id="txt_start_date"></span></td></tr><tr><td><span id="txt_end_date"></span></td></tr></tbody></table>
            </xsl:when>
            <xsl:when test="type='label'">
                <xsl:value-of select="value"/>
            </xsl:when>
            <xsl:when test="type='img'">
                <img id="{id}" src="{src}" alt="{alt}" title="{alt}" style="cursor:pointer; cursor:hand;" tabindex="{tab_index}" />
            </xsl:when>
            <xsl:otherwise>
                <input id="{$id}" type="{type}" name="{name}" value="{value}" class="{type}"  tabindex="{tab_index}">
                    <xsl:if test="size">
                        <xsl:attribute name="size"><xsl:value-of select="size"/></xsl:attribute>
                    </xsl:if>

                    <xsl:if test="type = 'checkbox' and checked = '1'">
                        <xsl:attribute name="checked">checked</xsl:attribute>
                    </xsl:if>

                    <xsl:if test="readonly">
                        <xsl:attribute name="readonly">'readonly'</xsl:attribute>
                        <xsl:attribute name="onMouseout">window.status='';return true;</xsl:attribute>
                    </xsl:if>

                    <xsl:if test="onkeypress">
                        <xsl:attribute name="onkeypress"><xsl:value-of select="onkeypress"/></xsl:attribute>
                    </xsl:if>
                    <xsl:if test="class">
                        <xsl:attribute name="class"><xsl:value-of select="class"/></xsl:attribute>
                    </xsl:if>

                </input>
            </xsl:otherwise>
        </xsl:choose>

    </div>
</xsl:template>

<!--
    Template
    match=datatable()

    Entrypoint for this datatable. Renders pagination and datatable.
-->
<xsl:template name="datatable" match="datatable">

    <xsl:choose>
        <xsl:when test="//exchange_values!=''">
            <script type="text/javascript">
                //function Exchange_values(thisform)
                function valida(data,param)
                {
                <xsl:value-of select="//valida"/>
                }

                function Exchange_values(data)
                {
                <xsl:value-of select="//exchange_values"/>
                }

            </script>
        </xsl:when>
    </xsl:choose>

    <br/>
    <div id="message"> </div>
   
    <div id="paging"> </div>
    <div class="datatable-container">
        <table class="datatable">
        </table>
    </div>
    
    <div id="datatable-detail" style="background-color:#000000;color:#FFFFFF;display:none">
        <div class="hd" style="background-color:#000000;color:#000000; border:0; text-align:center">
            <xsl:value-of select="//lightbox_name"/>
        </div>
        <div class="bd" style="text-align:center;"> </div>
    </div>
    
    <div id="footer"> </div>
    <xsl:call-template name="datatable-yui-definition" />
</xsl:template>

<!--
    Experimental support for YUI datatable
 -->

<xsl:template name="datatable-yui-definition">
    <script>
        var allow_allrows = "<xsl:value-of select="//datatable/config/allow_allrows"/>";

        var property_js = "<xsl:value-of select="//datatable/property_js"/>";

        var base_java_url = "{<xsl:value-of select="//datatable/config/base_java_url"/>}";

        <xsl:choose>
            <xsl:when test="//datatable/json_data != ''">
                var json_data = <xsl:value-of select="//datatable/json_data" disable-output-escaping="yes" />;
            </xsl:when>
        </xsl:choose>

        var myColumnDefs = [
        <xsl:for-each select="//datatable/headers/header">
            {
            key: "<xsl:value-of select="name"/>",
            label: "<xsl:value-of select="text"/>",
            resizeable:true,
            sortable: <xsl:value-of select="phpgw:conditional(not(sortable = 0), 'true', 'false')"/>,
            visible: <xsl:value-of select="phpgw:conditional(not(visible = 0), 'true', 'false')"/>,
            format: "<xsl:value-of select="format"/>",
            formatter: <xsl:value-of select="formatter"/>,
            source: "<xsl:value-of select="sort_field"/>",
            className: "<xsl:value-of select="className"/>"
            }<xsl:value-of select="phpgw:conditional(not(position() = last()), ',', '')"/>
        </xsl:for-each>
        ];

        var values_combo_box = [
        <xsl:for-each select="//datatable/actions/form/fields/hidden_value">
            {
            id: "<xsl:value-of select="id"/>",
            value: "<xsl:value-of select="value"/>"
            }<xsl:value-of select="phpgw:conditional(not(position() = last()), ',', '')"/>
        </xsl:for-each>
        ];


    </script>
</xsl:template>
