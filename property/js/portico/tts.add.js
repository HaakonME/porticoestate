	this.confirm_session = function(action)
	{
		var oArgs = {menuaction:'property.bocommon.confirm_session'};
		var strURL = phpGWLink('index.php', oArgs, true);

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: strURL,
			success: function(data) {
				if( data != null)
				{
					if(data['sessionExpired'] == true)
					{
						window.alert('sessionExpired - please log in');
						JqueryPortico.lightboxlogin();//defined in common.js
					}
					else
					{
						document.getElementById(action).value = 1;
						try
						{
							validate_submit();
						}
						catch (e)
						{
							document.form.submit();
						}
					}
				}
			},
			failure: function(o)
			{
				window.alert('failure - try again - once')
			},
			timeout: 5000
		});
	}
