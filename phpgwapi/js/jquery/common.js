
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//Namespacing
;var JqueryPortico = {};

/* Sigurd: Need to delay tab-rendering to after all tables are finished*/
JqueryPortico.inlineTablesDefined = 0;
JqueryPortico.inlineTablesRendered = 0;

JqueryPortico.formatLink = function(key, oData) {
        
	var name = oData[key];
	var link = oData['link'];
	return '<a href="' + link + '">' + name + '</a>';
};

JqueryPortico.formatLinkEvent = function(key, oData){

    var name = 'Link';
    var link = oData['url'];
    return '<a href="' + link + '">' + name + '</a>';
}

JqueryPortico.formatLinkTenant = function(key, oData) {
        
	var name = oData[key];
	var link = oData['link'];
	return '<a href="/portico/index.php?menuaction=property.uiworkorder.edit&id=' + name + '">' + name + '</a>';
};

JqueryPortico.formatProject = function(key, oData){
	var name = oData[key];
	var link = oData['link'];
	return '<a href="' + link + '&id='+name+'">' + name + '</a>';
}

 JqueryPortico.formatLinkGallery = function(key, oData){
     
        var name = 'Link';
	var link = oData[key];
	return '<a href="' + link + '" target="_blank">' + name + '</a>'; 
 }

JqueryPortico.formatLinkGeneric = function(key, oData) {
        
	if(!oData[key])
	{
		return '';
	}
            
	var data = oData[key];
        if( key == 'opcion_edit'){
                var link = data;
                var name = 'Edit';
        }
        else if ( key == 'opcion_delete'){
                var link = data;
                var name = 'Delete';
        }
        else if( key == 'actions' )
        {
                var link = data;
                var name = 'Delete';
        }
	else if ( typeof(data) == 'object')
	{
		var link = data['href'];
		var name = data['label'];
	}
        else
	{
		var name = 'Link';
		var link = data;
	}

	return '<a href="' + link + '">' + name + '</a>';
};

JqueryPortico.formatLinkGenericLlistAttribute = function(key, oData) {
        
	var link = oData['link'];
        var type = oData['link'].split(".");
        
        var resort = '';
        if(key == 'up')
        {
            resort = 'up';
        }
        else
        {
            resort = 'down';
        }
        
        if(type[2] == 'uiadmin_entity')
        {
            var url = "'"+ link +'&resort='+ resort +"'";
        }else
        {
            var url = "'"+ link +'&resort='+ resort +"',''";
        }
        
	return '<a href="#" onclick="JqueryPortico.move_record('+ url+')">' + key + '</a>';
};

JqueryPortico.move_record = function(sUrl)
{   
    var baseUrl = sUrl + "&confirm=yes&phpgw_return_as=json";
     $.post( baseUrl, function( data ) {
        oTable.fnDraw();
     });
};

JqueryPortico.searchLink = function(key, oData) {
	var name = oData[key];
	var link = oData['query_location'][key];

	return '<a id="' + link + '" onclick="searchData(this.id);">' + name + '</a>';
};

JqueryPortico.formatCheck = function(key, oData) {
	var checked = '';
	var hidden = '';
	if(oData['responsible_item'])
	{
		checked = "checked = 'checked'";
		hidden = "<input type=\"hidden\" class=\"orig_check\"  name=\"values[assign_orig][]\" value=\""+oData['responsible_contact_id']+"_"+oData['responsible_item']+"_"+oData['location_code']+"\"/>";
	}

	return hidden + "<center><input type=\"checkbox\" "+checked+" class=\"mychecks\"  name=\"values[assign][]\" value=\""+oData['location_code']+"\"/></center>";
};

//JqueryPortico.formatCheckEvent = function(key, oData) {
//     
//        var hidden = '';
//        
//        return hidden + "<center><input type=\"checkbox\" class=\"mychecks\"  name=\"values[events]["+oData['id']+"_"+oData['schedule_time']+"]\" value=\""+oData['id']+"\"/></center>";
//}

JqueryPortico.formatCheckUis_agremment = function(key, oData) {
    
        var checked = '';
	var hidden = '';
        
	return hidden + "<center><input type=\"checkbox\" "+checked+" class=\"mychecks\"  name=\"values[alarm]["+oData['id']+"]\" value=\"\" /></center>";
};

JqueryPortico.formatCheckCustom = function(key, oData) {
    
        var checked = '';
	var hidden = '';

	return hidden + "<center><input type=\"checkbox\" "+checked+" class=\"mychecks\"  name=\"values[delete][]\" value=\""+oData['id']+"\" onMouseout=\"window.status='';return true;\" /></center>";
};

JqueryPortico.formatUpDown = function(key, oData){
    
  var linkUp = oData['link_up'];
  var linkDown = oData['link_down'];
  
  return '<a href="' + linkUp + '">up</a> | <a href="' + linkDown + '">down</a>';
}; 

JqueryPortico.formatRadio = function(key, oData){
	var checked = '';
	var hidden = '';
	return hidden + "<center><input type=\"radio\" name=\"rad_template\" class=\"myValuesForPHP\" value=\""+oData['template_id']+"\" /></center>";
};

JqueryPortico.showPicture = function(key, oData)
{
	var link = ""
	if(oData['img_id'])
	{
		var img_name = oData['file_name'];
		var img_id	 = oData['img_id'];
		var img_url  = oData['img_url'];
		var thumbnail_flag = oData['thumbnail_flag'];
		link = "<a href='"+ img_url +"' title='"+ img_name +"' id='"+ img_id +"' target='_blank'><img src='"+ img_url +"&"+ thumbnail_flag +"' alt='"+ img_name +"' /></a>";
	} 
	return link;
};
	
JqueryPortico.FormatterAmount0 = function(key, oData) {

	var amount = $.number( oData[key], 0, ',', ' ' );
	return amount;
};

JqueryPortico.FormatterAmount2 = function(key, oData) {

	var amount = $.number( oData[key], 2, ',', ' ' );
	return amount;
};
	
JqueryPortico.FormatterRight = function(key, oData) {
	return "<div align=\"right\">"+oData[key]+"</div>";
};

JqueryPortico.FormatterCenter = function(key, oData) {
	return "<center>"+oData[key]+"</center>";
};

JqueryPortico.inlineTableHelper = function(container, ajax_url, columns, options, data) {

	options = options || {};
	var disablePagination	= options['disablePagination'] || false;
	var disableFilter		= options['disableFilter'] || false;
	var TableTools_def		= options['TableTools'] || false;
	var order				= options['order'] || [0, 'desc'];
//console.log(options);
	data = data || {};

//	if (Object.keys(data).length == 0)
	if(ajax_url)
	{
		var ajax_def = {url: ajax_url, data: {}, type: 'GET'};
		var serverSide_def = true;
	}
	else
	{
		var ajax_def = false;
		var serverSide_def = false;
	}
	if(TableTools_def)
	{
		var sDom_def = 'T<"clear">lfrtip';
	}
	else
	{
		var sDom_def = '<"clear">lfrtip';
	}
//	$(document).ready(function ()
//	{
		JqueryPortico.inlineTablesRendered += 1;

		var oTable = $("#" + container).dataTable({
			paginate:		disablePagination ? false : true,
			filter:			disableFilter ? false : true,
			info:			disableFilter ? false : true,
			order:			order,
			processing:		true,
			serverSide:		serverSide_def,
			responsive:		true,
			deferRender:	true,
			data:			data,
			ajax:			ajax_def,
			fnServerParams: function ( aoData ) {
				if(typeof(aoData.order) != 'undefined')
				{
					var column = aoData.order[0].column;
					var dir = aoData.order[0].dir;
					var column_to_keep = aoData.columns[column];
					delete aoData.columns;
					aoData.columns = {};
					aoData.columns[column] = column_to_keep;
				}
			 },
			fnInitComplete: function (oSettings, json)
			{
				if(JqueryPortico.inlineTablesRendered == JqueryPortico.inlineTablesDefined)
				{
					if(typeof(JqueryPortico.render_tabs) == 'function')
					{
						var delay=15;//allow extra 15 milliseconds to really finish
						setTimeout(function()
						{
							JqueryPortico.render_tabs();

						},delay);
					}
				}
			},
		//	lengthMenu:		JqueryPortico.i18n.lengthmenu(),
		//	language:		JqueryPortico.i18n.datatable(),
			columns: columns,
		//	colVis: {
		//					exclude: exclude_colvis
		//	},
		//	dom:			'lCT<"clear">f<"top"ip>rt<"bottom"><"clear">',
		//	stateSave:		true,
		//	stateDuration: -1, //sessionstorage
		//	tabIndex:		1,
			fnDrawCallback: function () {
					try
					{
						window['local_DrawCallback' + JqueryPortico.inlineTablesRendered](oTable);
					}
					catch(err)
					{
						//nothing
					}
			},
			sDom: sDom_def,
			oTableTools: TableTools_def
		});


//	});
	return oTable;
};

JqueryPortico.updateinlineTableHelper = function(oTable, requestUrl)
{
	if(typeof(oTable) == 'string')
	{
		var _oTable = $("#" + oTable).dataTable();
	}
	else
	{
		var _oTable = oTable;
	}
	if(typeof(requestUrl) == 'undefined')
	{
		_oTable.fnDraw();
	}
	else
	{
		var api = _oTable.api();
		api.ajax.url( requestUrl ).load();
	}
};

JqueryPortico.fnGetSelected = function(oTable)
{
	var aReturn = new Array();
	var aTrs = oTable.fnGetNodes();
	for ( var i=0 ; i < aTrs.length ; i++ )
	{
		if ( $(aTrs[i]).hasClass('selected') )
		{
			aReturn.push( i );
		}
	}
	return aReturn;
};

JqueryPortico.execute_ajax = function(requestUrl, callback, data,type, dataType)
{                                       
	type = typeof type !== 'undefined' ? type : 'POST';
	dataType = typeof dataType !== 'undefined' ? dataType : 'html';
	data = typeof data !== 'undefined' ? data : {};

	$.ajax({
		type: type,
		dataType: dataType,
		data: data,
		url: requestUrl,
		success: function(result) 
		{
			callback(result);
		}
	});
};

JqueryPortico.substr_count = function(haystack, needle, offset, length)
{
	var pos = 0, cnt = 0;

	haystack += '';
	needle += '';
	if(isNaN(offset)) offset = 0;
	if(isNaN(length)) length = 0;
	offset--;

	while( (offset = haystack.indexOf(needle, offset+1)) != -1 )
	{
		if(length > 0 && (offset+needle.length) > length)
		{
			return false;
		}
		else
		{
			cnt++;
		}
	}
	return cnt;
};
		
		
JqueryPortico.autocompleteHelper = function(baseUrl, field, hidden, container, label_attr) {
	$(document).ready(function () 
	{
		$("#" + field).autocomplete({
			source: function( request, response ) {
				//console.log(request.term);
				$.ajax({
					url: baseUrl,
					dataType: "json",
					data: {
						//location_name: request.term,
						query: request.term,
						phpgw_return_as: "json"
					},
					success: function( data ) {
						response( $.map( data.ResultSet.Result, function( item ) {
							return {
								label: item.name,
								value: item.id
							};
						}));
					}
				});
			},
			focus: function (event, ui) {
				$(event.target).val(ui.item.label);
				return false;
			},
			minLength: 1,
			select: function( event, ui ) {
				$("#" + hidden).val(ui.item.value);
				// have to wait to set the value
				setTimeout(function() { $("#" + field).val(ui.item.label); }, 1);
			}
        });
	});


};	

	JqueryPortico.openPopup = function(oArgs,options)
	{
		options = options || {};
		var width = options['width'] || 750;
		var height = options['height'] || 450;
		var closeAction = options['closeAction'] || false;
		

		var requestUrl = phpGWLink('index.php', oArgs);
		TINY.box.show({iframe:requestUrl, boxid:'frameless',width:width,height:height,fixed:false,maskid:'darkmask',maskopacity:40, mask:true, animate:true, close: true,closejs:function(){JqueryPortico.onPopupClose(closeAction);}});
	};

	JqueryPortico.onPopupClose =function(closeAction)
	{
		if(closeAction=='reload')
		{
			location.reload();
		}
		if(closeAction=='close')
		{
			TINY.box.hide();
			
			if(typeof(afterPopupClose) == 'function')
			{
				afterPopupClose();
			}
		}
	};

	JqueryPortico.lightboxlogin = function()
	{
		var oArgs = {lightbox:1};
		var strURL = phpGWLink('login.php', oArgs);
		TINY.box.show({iframe:strURL, boxid:'frameless',width:$(window).width(),height:400,fixed:false,maskid:'darkmask',maskopacity:40, mask:true, animate:true, close: false,closejs:false});
	};

	JqueryPortico.showlightbox_history = function(sUrl)
	{
		TINY.box.show({iframe:sUrl, boxid:'frameless',width:650,height:400,fixed:false,maskid:'darkmask',maskopacity:40, mask:true, animate:true, close: true});
	}

	JqueryPortico.checkAll = function(myclass)
  	{
		$("." + myclass).each(function()
		{
			if($(this).prop("checked"))
			{
				$(this).prop("checked", false);
			}
			else
			{
				$(this).prop("checked", true);
			}
		});
	}

	JqueryPortico.CreateRowChecked = function(Class)
	{
		//create the anchor node
		myA=document.createElement("A");
		url = "javascript:JqueryPortico.checkAll(\""+Class+"\")";
		myA.setAttribute("href",url);
		//create the image node
		url = "property/templates/portico/images/check.png";
		myImg=document.createElement("IMG");
		myImg.setAttribute("src",url);
		myImg.setAttribute("width","16");
		myImg.setAttribute("height","16");
		myImg.setAttribute("border","0");
		myImg.setAttribute("alt","Select All");
		// Appends the image node to the anchor
		myA.appendChild(myImg);
		// Appends myA to mydiv
		mydiv=document.createElement("div");
		mydiv.setAttribute("align","center");
		mydiv.appendChild(myA);
		return mydiv;
	}
