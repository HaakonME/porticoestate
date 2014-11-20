
	this.addFooterDatatable2 = function(nRow, aaData, iStart, iEnd, aiDisplay, oTable)
	{
		var api = oTable.api();
		var json = api.ajax.json();

		var columns = JqueryPortico.columns;

		tmp_sum_budget = json.sum_budget;
		tmp_sum_actual_cost =json.sum_actual_cost;
		tmp_sum_difference = json.sum_difference;

		var j = 0;

		for(i=0;i<columns.length;i++)
		{
			if(columns[i].visible = true)
			{
				if (columns[i].data == 'estimate')
				{
					if(typeof(nRow.getElementsByTagName('th')[j]) != 'undefined')
					{
						nRow.getElementsByTagName('th')[j].innerHTML = tmp_sum_budget;
					}
					break;
				}
				j++;
			}
		}

		var show_actual_cost = false;

		j = 0;
		for(i=0;i<columns.length;i++)
		{
			if(columns[i].visible = true)
			{
				if (columns[i].data == 'actual_cost')
				{
					if(typeof(nRow.getElementsByTagName('th')[j]) != 'undefined')
					{
						nRow.getElementsByTagName('th')[j].innerHTML = tmp_sum_actual_cost;
					}

					show_actual_cost = true;
					break;
				}
				j++;
			}
		}

		if(show_actual_cost)
		{
			j++;
			nRow.getElementsByTagName('th')[0].innerHTML = "Sum:";
			if(typeof(nRow.getElementsByTagName('th')[j]) != 'undefined')
			{
				nRow.getElementsByTagName('th')[j].innerHTML = tmp_sum_difference;
			}
		}
	}

$(document).ready(function() {

		$("#datatable-container").on("click", "tr", function() {

			$('td', this).removeClass('priority1');
			$('td', this).removeClass('priority2');
			$('td', this).removeClass('priority3');

		});

});

JqueryPortico.formatLinkRelated = function(key, oData) {

	if(!oData['child_date'])
	{
		return '';
	}

	var child_date = oData['child_date'][key];
	var date_info = child_date.date_info;
	if(!date_info.length)
	{
		return '';
	}

	var name = date_info[0]['entry_date'];
	var link = date_info[0]['link'];
	var title = child_date['statustext']['statustext'] || '';
	

	return '<a href="' + link + '" title="' + title + '">' + name + '</a>';
};
