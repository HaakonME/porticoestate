$(document).ready(function(){
	
	$("#loc1").change(function () {
		var loc1 = $(this).val();
		$("#loc2").html( "<option></option>" );
		$("#loc3").html( "<option></option>" );
		$("#loc4").html( "<option></option>" );
		$("#loc5").html( "<option></option>" );

		if(!loc1)
		{
			return false;
		}
		var oArgs = {menuaction:'registration.boreg.get_locations', location_code:loc1};
		var requestUrl = phpGWLink('registration/main.php', oArgs, true);
      
		var htmlString = "";

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl,
			success: function(data) {
				if( data != null)
				{
					htmlString  = "<option value = ''>" + data.length + " lokasjone(r) funnet</option>"
					var obj = data;

					$.each(obj, function(i) {
						htmlString  += "<option value='" + obj[i].id + "'>" + obj[i].name + "</option>";
		    			});

					$("#loc2").html( htmlString );
				}
				else
				{
					htmlString  += "<option>Ingen lokasjoner</option>"
					$("#loc2").html( htmlString );
				}
			} 
		});	
    });
	
	$("#loc2").change(function () {
		var loc1 = $("#loc1").val();
		var loc2 = $(this).val();
		$("#loc3").html( "<option></option>" );
		$("#loc4").html( "<option></option>" );
		$("#loc5").html( "<option></option>" );

		if(!loc2)
		{
			return false;
		}

		var oArgs = {menuaction:'registration.boreg.get_locations', location_code:loc1 + "-" + loc2};
		var requestUrl = phpGWLink('registration/main.php', oArgs, true);
      
		var htmlString = "";

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl,
			success: function(data) {
				if( data != null)
				{
					htmlString  = "<option value = ''>" + data.length + " lokasjone(r) funnet</option>"
					var obj = data;
					$.each(obj, function(i) {
						htmlString  += "<option value='" + obj[i].id + "'>"+ obj[i].name + "</option>";
		    			});

					$("#loc3").html( htmlString );
				}
				else
				{
					htmlString  += "<option>Ingen lokasjoner</option>"
					$("#loc3").html( htmlString );
				}
			} 
		});	
    });

	$("#loc3").change(function () {
		var loc1 = $("#loc1").val();
		var loc2 = $("#loc2").val();
		var loc3 = $(this).val();
		$("#loc4").html( "<option></option>" );
		$("#loc5").html( "<option></option>" );

		if(!loc3)
		{
			return false;
		}
		var oArgs = {menuaction:'registration.boreg.get_locations', location_code:loc1 + "-" + loc2 + "-" + loc3};
		var requestUrl = phpGWLink('registration/main.php', oArgs, true);
      
		var htmlString = "";

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl,
			success: function(data) {
				if( data != null)
				{
					htmlString  = "<option value = ''>" + data.length + " lokasjone(r) funnet</option>"
					var obj = data;
					$.each(obj, function(i) {
						htmlString  += "<option value='" + obj[i].id + "'>"+ obj[i].name + "</option>";
		    			});

					$("#loc4").html( htmlString );
				}
				else
				{
					htmlString  += "<option>Ingen lokasjoner</option>"
					$("#loc4").html( htmlString );
				}
			} 
		});	
    });

	$("#loc4").change(function () {
		var loc1 = $("#loc1").val();
		var loc2 = $("#loc2").val();
		var loc3 = $("#loc3").val();
		var loc4 = $(this).val();
		$("#loc5").html( "<option></option>" );
		if(!loc4)
		{
			return false;
		}

		var oArgs = {menuaction:'registration.boreg.get_locations', location_code:loc1 + "-" + loc2 + "-" + loc3 + "-" + loc4};
		var requestUrl = phpGWLink('registration/main.php', oArgs, true);
      
		var htmlString = "";

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl,
			success: function(data) {
				if( data != null)
				{
					htmlString  = "<option value = ''>" + data.length + " lokasjone(r) funnet</option>"
					var obj = data;
					$.each(obj, function(i) {
						htmlString  += "<option value='" + obj[i].id + "'>"+ obj[i].name + "</option>";
		    			});

					$("#loc5").html( htmlString );
				}
				else
				{
					htmlString  += "<option>Ingen lokasjoner</option>"
					$("#loc5").html( htmlString );
				}
			} 
		});	
    });

	$("#loc5").change(function () {
		var loc1 = $("#loc1").val();
		var loc2 = $("#loc2").val();
		var loc3 = $("#loc3").val();
		var loc4 = $("#loc4").val();
		var loc5 = $(this).val();
		$("#loc6").html( "<option></option>" );
		if(!loc5)
		{
			return false;
		}

		var oArgs = {menuaction:'registration.boreg.get_locations', location_code:loc1 + "-" + loc2 + "-" + loc3 + "-" + loc4 + "-" + loc5};
		var requestUrl = phpGWLink('registration/main.php', oArgs, true);
      
		var htmlString = "";

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl,
			success: function(data) {
				if( data != null)
				{
					htmlString  = "<option value = ''>" + data.length + " lokasjone(r) funnet</option>"
					var obj = data;
					$.each(obj, function(i) {
						htmlString  += "<option value='" + obj[i].id + "'>"+ obj[i].name + "</option>";
		    			});

					$("#loc6").html( htmlString );
				}
				else
				{
					htmlString  += "<option>Ingen lokasjoner</option>"
					$("#loc6").html( htmlString );
				}
			} 
		});	
    });
/*

$(".choose_loc").live( "change", function () {
		var thisSelectBox = $(this);
		var loc_code = $(this).val();
		var loc_id = $(this).attr("id");
		var loc_arr = loc_id.split('_');
		var loc_level = parseInt(loc_arr[1]);
		var new_loc_id = "loc_" + (parseInt(loc_level)+1);
		
		var id = "";
		var new_loc_code = "";
		var level;
		for(level = 1;level <= loc_level;level++){
			id = "loc_" + level;
			if(level > 1)
				new_loc_code += "-" + $("#" + id).val();
			else
				new_loc_code += $("#" + id).val();
		}
		
		if(!loc_code)
		{
			return false;
		}
		var oArgs = {menuaction:'registration.boreg.get_locations', location_code:new_loc_code};
		var requestUrl = phpGWLink('registration/main.php', oArgs, true);
      
		var htmlString = "";

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl,
			success: function(data) {
				if( data != null)
				{
					htmlString  = "<select class='choose_loc' name='" + new_loc_id  + "' id='" + new_loc_id  + "' >" +
								  "<option value = ''>" + data.length + " lokasjone(r) funnet</option>";
								  
								  
					var obj = data;

					$.each(obj, function(i) {
						htmlString  += "<option value='" + obj[i].id + "'>" + obj[i].name + "</option>";
		    			});

					htmlString += "</select>";
					
					$(thisSelectBox).after( htmlString );
				}
				else
				{
					htmlString  += "<option>Ingen lokasjoner</option>"
					$(new_loc_id).html( htmlString );
				}
			} 
		});	
    });

*/

});

