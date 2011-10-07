<xsl:template name="control_items_receipt" xmlns:php="http://php.net/xsl">

<div class="yui-content">
	<div>
	
	  <!-- ===========================  SHOW CONTROL ITEMS RECEIPT   =============================== -->
	 
  	  <h2>Kvittering</h2>
	  <form action="#" method="post">	
		
		<xsl:variable name="control_id"><xsl:value-of select="control_id"/></xsl:variable>
		<input type="hidden" name="control_id" value="{control_id}" />
		
		<ul class="proplist-col control_items">
			<xsl:for-each select="control_receipt_items">
			<ul>
	    		<li>
		         	<h3><xsl:value-of select="control_group/group_name"/></h3>
		         	<div id="play">
		         	<ul>		
						<xsl:for-each select="control_items">
							<xsl:variable name="control_item_id"><xsl:value-of select="id"/></xsl:variable>
			     			<li><xsl:number/>. <xsl:value-of select="title"/></li>	
						</xsl:for-each>
					</ul>
					</div>
				</li>
			</ul>      
			</xsl:for-each>
		</ul>	
		
		<div class="form-buttons">
		<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
		<input type="submit" name="show_receipt" value="{$lang_save}" title = "{$lang_save}" />
		</div>
	</form>
						
	</div>
</div>
</xsl:template>