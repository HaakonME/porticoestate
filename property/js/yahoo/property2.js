	var myDataSource,myDataTable, myContextMenu,tableYUI;


/********************************************************************************/
	this.create_formatters = function()
	{
		for(y=0;y<myColumnDefs.length;y++)
		{
			for(z=0;z<myColumnDefs[y].length;z++)
			{
				if(typeof(myColumnDefs[y][z]['formatter'])!="undefined")
				{
					formatter = myColumnDefs[y][z]['formatter'];
					myColumnDefs[y][z]['formatter'] = eval(formatter);
				}
			}
		}
	}

/********************************************************************************/

	this.getSumPerPage = function(name_column,round,paginator,datatable)
	{
		begin = end = 0;
		if( (paginator.getPageRecords()[1] - paginator.getPageRecords()[0] + 1 ) == datatable.getRecordSet().getLength() )
		{
			begin	= 0;
			end		= paginator.getPageRecords()[1] - paginator.getPageRecords()[0];
		}
		else
		{
			begin	= paginator.getPageRecords()[0];
			end		= paginator.getPageRecords()[1];
		}

		tmp_sum = 0;
		for(i = begin; i <= end; i++)
		{
			tmp_sum = tmp_sum + parseFloat(datatable.getRecordSet().getRecords(0)[i].getData(name_column));
		}

		return tmp_sum = YAHOO.util.Number.format(tmp_sum, {decimalPlaces:round, decimalSeparator:",", thousandsSeparator:" "});
	}

/********************************************************************************/

  	this.td_empty = function(colspan)
  	{
		newTD = document.createElement('td');
		newTD.colSpan = colspan;
		newTD.style.borderTop="1px solid #000000";
		newTD.appendChild(document.createTextNode(''));
		newTR.appendChild(newTD);
  	}
 /********************************************************************************/

  	this.td_sum = function(sum)
  	{
		newTD = document.createElement('td');
		newTD.colSpan = 1;
		newTD.style.borderTop="1px solid #000000";
		newTD.style.fontWeight = 'bolder';
		newTD.style.textAlign = 'right';
		newTD.style.paddingRight = '0.8em';
		newTD.appendChild(document.createTextNode(sum));
		newTR.appendChild(newTD);
  	}
 /********************************************************************************/
 	this.init_datatable = function(data,container,pager,myColumnDefs,num)
	{
		myDataSource = new YAHOO.util.DataSource(data[0]["values"]);
        myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;

        fields = new Array();
		for(i=0; i < myColumnDefs.length;i++)
		{
			fields[i] = myColumnDefs[i].key;
		}

		myDataSource.responseSchema =
		{
			fields		: fields
		};

		if(data[0]["is_paginator"]==1)
		{

			myPaginatorConfig = {
									containers			: pager,
									totalRecords		: data[0]["total_records"],
									pageLinks			: 10,
									rowsPerPage			: 3
								}

			eval("myPaginator_"+num+" = new YAHOO.widget.Paginator(myPaginatorConfig)");

			myTableConfig = { paginator	: eval("myPaginator_"+num)};
			eval("myDataTable_" + num + " = new YAHOO.widget.DataTable(container, myColumnDefs, myDataSource, myTableConfig)");
		}
		else
		{
			eval("myDataTable_" + num + " = new YAHOO.widget.DataTable(container, myColumnDefs, myDataSource)");
		}

		eval("myDataTable_" + num).subscribe("renderEvent", function(){
			myParticularRenderEvent();
		});

	}

 /********************************************************************************/

	this.create_formatters();

	for(j=0;j<datatable.length;j++)
	{
		if(YAHOO.util.Dom.inDocument("datatable-container_"+j))
		{
			pager = YAHOO.util.Dom.get("paging_"+j);
			div   = YAHOO.util.Dom.get("datatable-container_"+j);

			this.init_datatable(datatable[j],div,pager,myColumnDefs[j],j);
		}
	}
