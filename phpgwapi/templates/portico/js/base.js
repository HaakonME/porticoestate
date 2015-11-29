	var pageLayout;

	$(document).ready(function(){
		// create page layout
		pageLayout = $('body').layout({
			stateManagement__enabled:	true
		,	defaults: {
			}
		,	north: {
				size:					"auto"
			,	spacing_open:			0
			,	closable:				false
			,	resizable:				false
			}
		,	west: {
				size:					250
			,	spacing_closed:			22
			,	togglerLength_closed:	140
			,	togglerAlign_closed:	"top"
			,	togglerContent_closed:	"C<BR>o<BR>n<BR>t<BR>e<BR>n<BR>t<BR>s"
			,	togglerTip_closed:		"Open & Pin Contents"
			,	sliderTip:				"Slide Open Contents"
			,	slideTrigger_open:		"mouseover"
			,	initClosed:				true
			}
		,	south: {
			maxSize:				200
		,	spacing_closed:			0			// HIDE resizer & toggler when 'closed'
		,	spacing_open:			0
		,	slidable:				false		// REFERENCE - cannot slide if spacing_closed = 0
		,	initClosed:				false
		,	resizable:				false
			}

		});


		/**
		 * Experimental : requires live update of js and css
		 * @param {type} requestUrl
		 */
		update_content = function(requestUrl)
		{

			window.location.href = requestUrl;
			return false;
			requestUrl += '&phpgw_return_as=stripped_html';
			$.ajax({
				type: 'GET',
				url: requestUrl,
				success: function (data) {
					if (data != null)
					{
						$("#center_content").html(data);
					}
				}
			});

		}


	});