<xsl:template match="data" xmlns:php="http://php.net/xsl">
	
<xsl:call-template name="yui_booking_i18n"/>

<xsl:if test="navi/add">
	<div style="padding: 2em;">
	    <a class="add" style="text-decoration: none;font-size: 14px;">
	        <xsl:attribute name="href"><xsl:value-of select="navi/add"/></xsl:attribute>
	        <xsl:value-of select="php:function('lang', 'Add Activity')" />
	    </a>
	</div>
</xsl:if>

<div style="padding: 0 2em">

<h3><xsl:value-of select="php:function('lang', 'Current Activities')" /></h3>

<script type="text/javascript">
YAHOO.util.Event.addListener(window, "load", function() {
	var tree = new YAHOO.widget.TreeView("tree_container", <xsl:value-of select="treedata"/>); 

<xsl:if test="navi/add">
	tree.subscribe("labelClick", function(node) {
		window.location.href = node.href;
	});
</xsl:if>
	tree.render(); 
});
</script>
	<div id="tree_container"></div>
</div>
</xsl:template>
