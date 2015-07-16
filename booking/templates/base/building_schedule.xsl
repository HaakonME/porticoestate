<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<!--xsl:call-template name="yui_booking_i18n"/-->
	<iframe id="yui-history-iframe" src="phpgwapi/js/yahoo/history/assets/blank.html" style="position:absolute;top:0; left:0;width:1px; height:1px;visibility:hidden;"></iframe>
	<input id="yui-history-field" type="hidden"/>
	
    <!--div id="content">
        <ul class="pathway">
            <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="building/buildings_link"/></xsl:attribute>
                    <xsl:value-of select="php:function('lang', 'Buildings')"/>
                </a>
            </li>
            <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="building/building_link"/></xsl:attribute>
                    <xsl:value-of select="building/name"/>
                </a>
            </li>
            <li><xsl:value-of select="php:function('lang', 'Schedule')"/></li>
        </ul-->

        <xsl:call-template name="msgbox"/>
    <form action="" method="POST" id='form'  class="pure-form pure-form-aligned" name="form">  
    <input type="hidden" name="tab" value=""/>
    <div id="tab-content">
        <xsl:value-of disable-output-escaping="yes" select="building/tabs"/>
        <div id="building_schedule"> 
    
		<ul id="week-selector">
			<li><a href="#" onclick="YAHOO.booking.prevWeek(); return false"><xsl:value-of select="php:function('lang', 'Previous week')"/></a></li>
			<li id="cal_container"/>
			<li><a href="#" onclick="YAHOO.booking.nextWeek(); return false"><xsl:value-of select="php:function('lang', 'Next week')"/></a></li>
		</ul>

        <div id="schedule_container"></div>
        
        </div>
    </div>
    </form>
    <!--/div-->

<script type="text/javascript">
YAHOO.util.Event.addListener(window, "load", function() {
	YAHOO.booking.setupWeekPicker('cal_container');
	YAHOO.booking.datasourceUrl = '<xsl:value-of select="building/datasource_url"/>';
    var handleHistoryNavigation = function (state) {
		YAHOO.booking.date = parseISO8601(state);
		YAHOO.booking.renderSchedule('schedule_container', YAHOO.booking.datasourceUrl, YAHOO.booking.date, YAHOO.booking.backendScheduleColorFormatter, true);
    };
    var initialRequest = YAHOO.util.History.getBookmarkedState("date") || '<xsl:value-of select="building/date"/>';
    YAHOO.util.History.register("date", initialRequest, handleHistoryNavigation);
    YAHOO.util.History.onReady(function() {
		var state = YAHOO.util.History.getBookmarkedState("date") || initialRequest;
		if(state)
			handleHistoryNavigation(state);
    });
   	YAHOO.util.History.initialize("yui-history-field", "yui-history-iframe");	
});
</script>

</xsl:template>
