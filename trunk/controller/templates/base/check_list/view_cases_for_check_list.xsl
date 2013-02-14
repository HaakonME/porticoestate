<!-- $Id: edit_check_list.xsl 8513 2012-01-07 10:38:09Z vator $ -->
<xsl:template match="data" name="view_check_list" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format"><xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')" /></xsl:variable>
<xsl:variable name="session_url">&amp;<xsl:value-of select="php:function('get_phpgw_session_url')" /></xsl:variable>

<div id="main_content" class="medium">
	<script>
	
		// ======================  LASTER FANEN view_open_errors NÅR SIDEN LASTES FØRSTE GANG  ===================
		$(document).ready(function() {
			var requestUrl = $("#view_open_cases").attr("href");
		 	load_tab(requestUrl);
		});
			
		// ======================  FUNKSJONALITET FOR LASTING AV TABS ===================
		$(function() {
	
			$("#register_case").live("click", function(){
				var requestUrl = $(this).attr("href");
				load_tab(requestUrl);
			
				return false;
			});
						
			$("#view_open_cases").live("click", function(){
				var requestUrl = $(this).attr("href");
				load_tab(requestUrl);
			
				return false;
			});
			
			$("#view_closed_cases").live("click", function(){
				var requestUrl = $(this).attr("href");
				load_tab(requestUrl);
			
				return false;
			});
			
			$("#view_measurements").live("click", function(){
				var requestUrl = $(this).attr("href");
				load_tab(requestUrl);
			
				return false;
			});
		});

		function load_tab(requestUrl){
			$.ajax({
				  type: 'POST',
				  url: requestUrl,
				  success: function(data) {
				  	$("#load_view_content").html(data);
				  },
				  error: function(XMLHttpRequest, textStatus, errorThrown) {
        		if (XMLHttpRequest.status === 401) {
        	  	location.href = '/';
        	  }
        	}
			});
		}
	</script>
		
		<div id="check-list-heading">
		<div class="box-1">
			<h1>Kontroll: <xsl:value-of select="control/title"/></h1>
			<xsl:choose>
				<xsl:when test="type = 'component'">
					<h2><xsl:value-of select="component_array/xml_short_desc"/></h2>
				</xsl:when>
				<xsl:otherwise>
					<xsl:choose>
						<xsl:when test="location_level = 1">
							<h2>Eiendom: <xsl:value-of select="location_array/loc1_name"/></h2>
						</xsl:when>
						<xsl:otherwise>
								<h2>Bygg: <xsl:value-of select="location_array/loc2_name"/></h2>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:otherwise>
			</xsl:choose>
		</div>
		<div class="box-2 select-box">
      <xsl:call-template name="nav_control_plan" />
		</div>
		
		<!-- ==================  CHECKLIST TAB MENU  ===================== -->
		<xsl:call-template name="check_list_tab_menu">
	 		<xsl:with-param name="active_tab">view_cases</xsl:with-param>
		</xsl:call-template>
	</div>
	
		<a id="view_open_cases">
			<xsl:attribute name="href">
				<xsl:text>index.php?menuaction=controller.uicheck_list.view_open_cases</xsl:text>
				<xsl:text>&amp;check_list_id=</xsl:text>
				<xsl:value-of select="check_list/id"/>
				<xsl:text>&amp;phpgw_return_as=stripped_html</xsl:text>
				<xsl:value-of select="$session_url"/>
			</xsl:attribute>
		</a>
	
		<div style="margin:20px 0;">
			<xsl:call-template name="select_buildings_on_property" />
		</div>
		<div id="load_view_content"></div>	
</div>
</xsl:template>
