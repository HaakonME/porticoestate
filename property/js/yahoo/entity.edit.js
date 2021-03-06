var  myDataSource, myDataTable, myContextMenu;
var  myPaginator_0, myDataTable_0;
var  myPaginator_1, myDataTable_1;
var  myPaginator_2, myDataTable_2;
var  myPaginator_3, myDataTable_3;
var  myPaginator_4, myDataTable_4;

var Button_4_0, Button_4_1, Button_4_2;


/********************************************************************************/
var FormatterCenter = function(elCell, oRecord, oColumn, oData)
{
	elCell.innerHTML = "<center>"+oData+"</center>";
}

/********************************************************************************/
	this.onActionsClick=function()
	{
		flag = false;
		//validate ckecks true
		array_checks = YAHOO.util.Dom.getElementsByClassName('mychecks');
		for ( var i in array_checks )
		{
			if(array_checks[i].checked)
			{
				flag = true;
				break;
			}
		}

		$("#controller_receipt").html("");

		var action = this.get("value");

		if(action === 'add')
		{
			add_control();
		}

		if(flag)
		{
			var ids = [];

			$(".mychecks:checked").each(function () {
					ids.push($(this).val());
			});

			var data = {"ids": ids, "action": action};
			data.repeat_interval = $("#repeat_interval").val();
			data.controle_time = $("#controle_time").val();
			data.service_time = $("#service_time").val();
			data.control_responsible = $("#control_responsible").val();
			data.control_start_date = $("#control_start_date").val();
			data.repeat_type = $("#repeat_type").val();

			var formUrl = $("#form").attr("action");
			var Url = parseURL(formUrl);
			oArgs  = Url.searchObject;
			delete oArgs.click_history;
			oArgs.menuaction = 'property.boentity.update_control_serie';

			var requestUrl = phpGWLink('index.php', oArgs, true);

			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: requestUrl,
				data: data,
				success: function(data) {
					if( data != null)
					{
						$("#controller_receipt").html(data.status + '::' + data.msg);
						if(data.status_kode == 'ok')
						{

						}
					}
				}
			});


			var oArgs2 = {menuaction:'property.uientity.get_controls_at_component', type:oArgs.type, entity_id:oArgs.entity_id, cat_id:oArgs.cat_id, id: oArgs.id};
			var requestUrl2 = phpGWLink('index.php', oArgs2, true);
			execute_async(myDataTable_4, oArgs2);
		}
	}
	
	function parseURL(url)
	{
		var parser = document.createElement('a'),
			searchObject = {},
			queries, split, i;
		// Let the browser do the work
		parser.href = url;
		// Convert query string to object
		queries = parser.search.replace(/^\?/, '').split('&');
		for( i = 0; i < queries.length; i++ ) {
			split = queries[i].split('=');
			searchObject[split[0]] = split[1];
		}
		return {
			protocol: parser.protocol,
			host: parser.host,
			hostname: parser.hostname,
			port: parser.port,
			pathname: parser.pathname,
			search: parser.search,
			searchObject: searchObject,
			hash: parser.hash
		};
	}

	add_control = function()
	{
		var formUrl = $("#form").attr("action");
		var Url = parseURL(formUrl);
		oArgs  = Url.searchObject;
		delete oArgs.click_history;
		oArgs.menuaction = 'property.boentity.add_control';
		oArgs.control_id = $("#control_id").val();
		oArgs.control_responsible = $("#control_responsible").val();
		oArgs.control_start_date = $("#control_start_date").val();
		oArgs.repeat_type = $("#repeat_type").val();
		if(!oArgs.repeat_type)
		{
			alert('velg type serie');
			return;
		}

		oArgs.repeat_interval = $("#repeat_interval").val();
		oArgs.controle_time = $("#controle_time").val();
		oArgs.service_time = $("#service_time").val();
		var requestUrl = phpGWLink('index.php', oArgs, true);
//								alert(requestUrl);

		$("#controller_receipt").html("");

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl,
			success: function(data) {
				if( data != null)
				{
					$("#controller_receipt").html(data.status + '::' + data.msg);
					if(data.status_kode == 'ok')
					{
						$("#control_id").val('');
						$("#control_name").val('');
						$("#control_responsible").val('');
						$("#control_responsible_user_name").val('');
						$("#control_start_date").val('');
						$("#repeat_interval").val('');
						$("#controle_time").val('');
						$("#service_time").val('');
					}
				}
			}
		});

		var oArgs2 = {menuaction:'property.uientity.get_controls_at_component', type:oArgs.type, entity_id:oArgs.entity_id, cat_id:oArgs.cat_id, id: oArgs.id};
		var requestUrl2 = phpGWLink('index.php', oArgs2, true);
		execute_async(myDataTable_4, oArgs2);
	};

	this.myParticularRenderEvent = function()
	{
		this.addFooterDatatable3(myPaginator_3,myDataTable_3);
	}

	var FormatterEdit = function(elCell, oRecord, oColumn, oData)
	{
		var location_id = oRecord.getData('location_id');
		var id = oRecord.getData('id');
		var inventory_id = oRecord.getData('inventory_id');
	  	elCell.innerHTML = "<a href=\"javascript:showlightbox_edit_inventory(" + location_id + ',' + id + ',' + inventory_id + ')">' + oColumn.label + "</a>";
	}	

	var FormatterCalendar = function(elCell, oRecord, oColumn, oData)
	{
		var location_id = oRecord.getData('location_id');
		var id = oRecord.getData('id');
		var inventory_id = oRecord.getData('inventory_id');
	  	elCell.innerHTML = "<a href=\"javascript:showlightbox_show_calendar(" + location_id + ',' + id + ',' + inventory_id + ')">' + oColumn.label + "</a>";
	}	

	var FormatterCenter = function(elCell, oRecord, oColumn, oData)
	{
		var amount = YAHOO.util.Number.format(oData, {decimalPlaces:0, decimalSeparator:",", thousandsSeparator:" "});
		elCell.innerHTML = "<div align=\"right\">"+amount+"</div>";
	}	

	var FormatterAmount0 = function(elCell, oRecord, oColumn, oData)
	{
		var amount = YAHOO.util.Number.format(oData, {decimalPlaces:0, decimalSeparator:",", thousandsSeparator:" "});
		elCell.innerHTML = "<div align=\"right\">"+amount+"</div>";
	}	

  	this.addFooterDatatable3 = function(paginator,datatable)
  	{
  		//call getSumPerPage(name of column) in property.js
  		tmp_sum1 = getTotalSum('inventory',0,paginator,datatable);

  		if(typeof(tableYUI)=='undefined')
  		{
			tableYUI = YAHOO.util.Dom.getElementsByClassName("yui-dt-data","tbody")[3].parentNode;
			tableYUI.setAttribute("id","tableYUI");
  		}
  		else
  		{
  			tableYUI.deleteTFoot();
  		}

		//Create ROW
		newTR = document.createElement('tr');

		td_sum('Sum');
		td_empty(2);
		td_sum(tmp_sum1);
		td_empty(5);

		myfoot = tableYUI.createTFoot();
		myfoot.setAttribute("id","myfoot");
		myfoot.appendChild(newTR);
	}


	this.fileuploader = function()
	{
		var sUrl = phpGWLink('index.php', fileuploader_action);
		var onDialogShow = function(e, args, o)
		{
			var frame = document.createElement('iframe');
			frame.src = sUrl;
			frame.width = "100%";
			frame.height = "400";
			o.setBody(frame);
		};
		lightbox.showEvent.subscribe(onDialogShow, lightbox);
		lightbox.show();
	}

	this.refresh_files = function()
	{
		base_java_url['action'] = 'get_files';
		execute_async(myDataTable_0);
	}

	this.showlightbox_add_inventory = function(location_id, id)
	{
		var oArgs = {menuaction:'property.uientity.add_inventory', location_id:location_id, id: id};
		var sUrl = phpGWLink('index.php', oArgs);

		TINY.box.show({iframe:sUrl, boxid:'frameless',width:750,height:550,fixed:false,maskid:'darkmask',maskopacity:40, mask:true, animate:true,
		close: true,
		closejs:function(){refresh_inventory(location_id, id)}
		});
	}

	this.showlightbox_edit_inventory = function(location_id, id, inventory_id)
	{
		var oArgs = {menuaction:'property.uientity.edit_inventory', location_id:location_id, id: id, inventory_id: inventory_id};
		var sUrl = phpGWLink('index.php', oArgs);

		TINY.box.show({iframe:sUrl, boxid:'frameless',width:750,height:550,fixed:false,maskid:'darkmask',maskopacity:40, mask:true, animate:true,
		close: true,
		closejs:function(){refresh_inventory(location_id, id)}
		});
	}

	this.showlightbox_show_calendar = function(location_id, id, inventory_id)
	{
		var oArgs = {menuaction:'property.uientity.inventory_calendar', location_id:location_id, id: id, inventory_id: inventory_id};
		var sUrl = phpGWLink('index.php', oArgs);

		TINY.box.show({iframe:sUrl, boxid:'frameless',width:750,height:550,fixed:false,maskid:'darkmask',maskopacity:40, mask:true, animate:true,
		close: true,
		closejs:function(){refresh_inventory(location_id, id)}
		});
	}

	this.showlightbox_assigned_history = function(serie_id)
	{
		var oArgs = {menuaction:'property.uientity.get_assigned_history', serie_id:serie_id};
		var sUrl = phpGWLink('index.php', oArgs);

		TINY.box.show({iframe:sUrl, boxid:'frameless',width:400,height:350,fixed:false,maskid:'darkmask',maskopacity:40, mask:true, animate:true,
		close: true,
		closejs:false
		});
	}

	this.refresh_inventory = function(location_id, id)
	{
		var oArgs = {menuaction:'property.uientity.get_inventory', location_id:location_id, id: id};
		var requestUrl = phpGWLink('index.php', oArgs, true);
//alert(requestUrl);
		execute_async(myDataTable_3, oArgs);
	}


YAHOO.util.Event.addListener(window, "load", function()
		{
			var loader = new YAHOO.util.YUILoader();
			loader.addModule({
				name: "anyone",
				type: "js",
			    fullpath: property_js
			    });

			loader.require("anyone");
		    loader.insert();
		});

YAHOO.util.Event.addListener(window, "load", function()
{
		lightbox = new YAHOO.widget.Dialog("lightbox-placeholder",
		{
			width : "600px",
			fixedcenter : true,
			visible : false,
			modal : false
			//draggable: true,
			//constraintoviewport : true
		});

		lightbox.render();

		YAHOO.util.Dom.setStyle('lightbox-placeholder', 'display', 'block');
});

(function() {
	var tree;
	
	function treeInit()
	{
		buildTextNodeTree();
		
		//handler for expanding all nodes
		YAHOO.util.Event.on("expand", "click", function(e) {
			tree.expandAll();
			YAHOO.util.Event.preventDefault(e);
		});
		
		//handler for collapsing all nodes
		YAHOO.util.Event.on("collapse", "click", function(e) {
			tree.collapseAll();
			YAHOO.util.Event.preventDefault(e);
		});

		tree.subscribe('clickEvent',function(oArgs) {
			window.open(oArgs.node.href,oArgs.node.target);
		});
	}
	
	function buildTextNodeTree()
	{
		//instantiate the tree:
		tree = new YAHOO.widget.TreeView("treeDiv1");
		for (var i = 0; i < documents.length; i++)
		{
			var root = tree.getRoot();
			var myobj = { label: documents[i]['text'], href:documents[i]['link'],target:"_blank" };
			var tmpNode = new YAHOO.widget.TextNode(myobj, root);

			if(documents[i]['children'].length)
			{
				buildBranch(tmpNode, documents[i]['children']);
			}
		}

		tree.draw();
	}

	function buildBranch(node, parent)
	{
		for (var i = 0; i < parent.length; i++)
		{
			var tmpNode = new YAHOO.widget.TextNode({label:parent[i]['text'], href:parent[i]['link']}, node, false);
			if(parent[i]['children'])
			{
				buildBranch(tmpNode, parent[i]['children']);
			}
		}
	}

	//When the DOM is done loading, initialize TreeView instance:
	YAHOO.util.Event.onDOMReady(treeInit);
	
})();


// jquery
$(document).ready(function(){

	$("#edit_inventory").on("submit", function(e){

		e.preventDefault();

		var thisForm = $(this);
		var submitBnt = $(thisForm).find("input[type='submit']");
		var requestUrl = $(thisForm).attr("action");
		$.ajax({
			type: 'POST',
			url: requestUrl + "&phpgw_return_as=json&" + $(thisForm).serialize(),
			success: function(data) {
				if(data)
				{
					if(data.sessionExpired)
					{
						alert('Sesjonen er utløpt - du må logge inn på nytt');
						return;
					}

	    			var obj = data;
		    	
	    			var submitBnt = $(thisForm).find("input[type='submit']");
	    			if(obj.status == "updated")
	    			{
		    			$(submitBnt).val("Lagret");
					}
					else
					{
		    			$(submitBnt).val("Feil ved lagring");					
					}
		    				 
		    		// Changes text on save button back to original
		    		window.setTimeout(function() {
						$(submitBnt).val('Lagre');
						$(submitBnt).addClass("not_active");
		    		}, 1000);

					var ok = true;
					var htmlString = "";
	   				if(data['receipt'] != null)
	   				{
		   				if(data['receipt']['error'] != null)
		   				{
							ok = false;
							for ( var i = 0; i < data['receipt']['error'].length; ++i )
							{
								htmlString += "<div class=\"error\">";
								htmlString += data['receipt']['error'][i]['msg'];
								htmlString += '</div>';
							}
	   				
		   				}
		   				if(typeof(data['receipt']['message']) != 'undefined')
		   				{
							for ( var i = 0; i < data['receipt']['message'].length; ++i )
							{
								htmlString += "<div class=\"msg_good\">";
								htmlString += data['receipt']['message'][i]['msg'];
								htmlString += '</div>';
							}
	   				
		   				}
		   				$("#receipt").html(htmlString);
		   			}
	   				
					if(ok)
					{
						parent.closeJS_remote();
					//	parent.hide_popupBox();
					}
				}
			}
		});
	});

	$("#workorder_cancel").on("submit", function(e){
		if($("#lean").val() == 0)
		{
			return;
		}
		e.preventDefault();
		parent.closeJS_remote();
//		parent.hide_popupBox();
	});

});


$(document).ready(function(){

	$("#cases_time_span").change(function(){
		var oArgs = {menuaction:'property.uientity.get_cases', location_id:location_id,	 id:item_id, year:$(this).val()};
		execute_async(myDataTable_5, oArgs);
	});
});

