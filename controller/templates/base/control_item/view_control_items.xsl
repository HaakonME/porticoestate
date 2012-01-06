<!-- $Id: choose_control_items.xsl 8267 2011-12-11 12:27:18Z sigurdne $ -->

<xsl:template match="view_control_items">

<ul class="groups">
	<xsl:for-each select="saved_groups_with_items_array">
		<li class="drag_group list_item">
			<h3><span class="group_order_nr"><xsl:number/></span>. <xsl:value-of select="control_group/group_name"/></h3>
	
			<form action="index.php?menuaction=controller.uicontrol_item.save_item_order" class="frm_save_order">
			   	<xsl:variable name="control_group_id"><xsl:value-of select="control_group/id"/></xsl:variable>
				<input type="hidden" name="control_group_id" value="{$control_group_id}" />
		
			 	<ul id="list">
					<xsl:for-each select="control_items">
						<xsl:variable name="control_item_id"><xsl:value-of select="id"/></xsl:variable>
						<xsl:variable name="order_tag">
							<xsl:choose>
								<xsl:when test="order_nr > 0">
									<xsl:value-of select="order_nr"/>
								</xsl:when>
								<xsl:otherwise>
									<xsl:number/>
								</xsl:otherwise>
							</xsl:choose>:<xsl:value-of select="id"/>
						</xsl:variable>
														
			 			<li class="list_item">
			 				<span class="drag">
			 					<span class="order_nr"><xsl:number/></span>. <xsl:value-of select="title"/><input type="hidden" name="order_nr[]" value="{$order_tag}" />
			 				</span>
			 				<a class="delete">
								<xsl:attribute name="href">
									<xsl:text>index.php?menuaction=controller.uicontrol_item.delete_item_list</xsl:text>
									<xsl:text>&amp;control_id=</xsl:text>
									<xsl:value-of select="//control_id"/>
									<xsl:text>&amp;control_item_id=</xsl:text>
									<xsl:value-of select="id"/>
								</xsl:attribute>
								<span>x</span>
							</a>
			 			</li>
					</xsl:for-each>
				</ul>
			</form>
		</li>
	</xsl:for-each>
</ul>




</xsl:template>
