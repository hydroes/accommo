var Accommodation = {};

Accommodation.Uploader = 
{
	instance: null,
	file_limit: 6,
	state: null,
	current_tab: null, // current tab that the uploader is in
	initialize: function()
	{
		// Setup flash version
		$("#uploader").pluploadQueue({
			// General settings
			runtimes : 'flash,html5,silverlight,html4',
			chunk_size : '256kb',
			url : $('#accommodation_image_upload_url').val(),
			max_file_size : '10mb',
			urlstream_upload: true, // Should allow sesion var to be sent thru
			unique_names : true,
			multiple_queues: true,
			preinit: 
			{
				Init: function(up, info) 
				{
					Accommodation.Uploader.instance = up;
				},
				Destroy: function(uploader)
				{
					// Accommodation.Dialog.error.dialog('open').html('uploader was destroyed');
				},
				UploadFile: function(up, file) 
				{
					/**
					 * TODO: put session id here.
					 * 
					 */
					up.settings.multipart_params = 
						{
							total_file_size: file.size,
							runtime: up.runtime,
							PHPSESSID : $('#ms').val(),
							accommodation_id: $($('.accommodation_id')[0]).val()
						};
				}
			},
			init: 
			{
				// i need to know when the uploader has finished uploading
				StateChanged: function(up) 
				{
					// show loading animation while uploads taking place
					if ( up.state == plupload.UPLOADING )
					{
						$('#uploading_animation').show();
						// disable all tabs except the current
						var tab_length = $('#tabs').tabs('length');
						for (var i = 0; i < tab_length; i++ )
						{
							if ( i == Accommodation.Uploader.current_tab )
							{
								continue;
							}
							$('#tabs').tabs('disable', i);
						}
					}
					else
					{
						$('#uploading_animation').hide();
						var tab_length = $('#tabs').tabs('length');
						for (var i = 0; i < tab_length; i++ )
						{
							if ( i == Accommodation.Uploader.current_tab )
							{
								continue;
							}
							$('#tabs').tabs('enable', i);
						}
						up.refresh();
					}
					Accommodation.Uploader.state = up.state;
					
				},
				// called as files added to queue
				// dont allow user to add more files than the limit
				FilesAdded: function(up, files) 
				{
					// get current tab selected so that the rest of the tabs can be disabled when uploading files
					Accommodation.Uploader.current_tab = $("#tabs").tabs('option', 'selected');
					// check image upload limit has been reached
					var num_images = parseInt($('#uploaded_images img.listing_preview').length);
					if ( num_images >= Accommodation.Uploader.file_limit )
					{
						up.splice(0, num_files);
						up.refresh();
						Accommodation.Dialog.error.dialog('open').html('no more than ' + Accommodation.Uploader.file_limit + ' images can be uploaded');
					}
				},
				FileUploaded: function(up, file, info) 
				{
				    // Called when a file has finished uploading
					var response = $.parseJSON(info.response);
					if ( response.status == 'success' )
					{
						var image_html = '<img class="listing_preview" name="name" src="'+ URL + '/listing_images/'+ response.file_thumb +'" width="'+response.thumb_width+'" height="'+response.thumb_height+'" alt="image preview" />';
						
						// display loading images while newly uploaded images thumb downloads
						var $available_div = $($('#uploaded_images .listing_preview_container.empty')[0]);
						$available_div.addClass('loading');
						var new_thumb = new Image(); 
						new_thumb.src = URL + '/listing_images/'+ response.file_thumb;
						new_thumb.width = parseInt(response.thumb_width);
						new_thumb.height = parseInt(response.thumb_height);
						new_thumb.onload = function()
						{
							// load image into first available empty div.
							$available_div.removeClass('empty loading').css({'width': response.thumb_width + 'px', 'height': response.thumb_height});
							$available_div.find('a').after(image_html).removeClass('hide').addClass('show').attr('href', response.delete_url);
						}
						
					}
					if( response.status == 'error' )
					{
						// manipulate queue according to error code
						if ( response.error_code == '101' )
						{
							// No more than 6 images can be uploaded.
							// clear queue
							up.splice(0, up.files.length);
						}
						if ( response.error_code == '102' )
						{
							// 102 - Image is too small to be used on this site.
							// show file name in error msg
							Accommodation.Dialog.error.dialog('open').html(file.name + response.message);
							up.removeFile(file);
							return;
						}
						
						Accommodation.Dialog.error.dialog('open').html(response.message);
						
					}
					
				},
				ChunkUploaded: function(up, file, info) 
				{
					/**
					 * Rather let file uploaded method take care of errors
					 */
					var response = $.parseJSON(info.response);
					if ( response.status == 'error' )
					{
					}
				},
				Error: function(up, args) 
				{
					Accommodation.Dialog.error.dialog('open').html('An uploading error occured, please try again later');
				}
			},
			filters : [
				{title : "Image files", extensions : "jpg,jpeg,gif,png"}
			],
			// addon settings
			flash_swf_url : URL + '/js/vendors/plupload/js/plupload.flash.swf',
			silverlight_xap_url : URL + '/js/vendors/plupload/js/plupload.silverlight.xap'
			
		});
		
	}

};

Accommodation.Map = 
{
	instance: null,
	marker: null,
	geocoderResult: null,
	googleMapApiLoaded: false,
	/**
	 * The google maps are async loaded, this method runs when the maps api is finished loading
	 */
	mapReady: function()
	{
		Accommodation.Map.googleMapApiLoaded = true;
	},
	/**
	 * Initializes map on south africa, or if locationData is passed it will
	 * focus and place marker on a specific location.
	 */
	initialize: function(LocationData)
	{
		/**
		 * Reset any previous vars that may have been set when the init func was
		 * called previously.
		 */
		this.instance = this.marker = this.geocoderResult = null;
	
		try
		{
			var latlng = new google.maps.LatLng(parseFloat(-30.559482), parseFloat(22.937505999999985));
		}
		catch(err)
		{
			// map js not loaded (working offline)
			return;
		}
		
		var defaultMapSettings = 
		{
		  zoom: parseInt(4),
		  center: latlng,
		  mapTypeId: google.maps.MapTypeId.ROADMAP,
		  streetViewControl: false,
		  zoomControl: true,
		  mapTypeControl: false,
		  panControl: true
		};
	
		var LocationData = LocationData || undefined;
		if (LocationData != undefined)
		{
			// overwrite lat long
			var latLng = new google.maps.LatLng(parseFloat(LocationData.lat), parseFloat(LocationData.lng));
			LocationData.center = latLng;
			$.extend(defaultMapSettings, LocationData);
		}
		
		// load google map, center it on south africa
		var map = null;
		this.instance = map = new google.maps.Map(document.getElementById("map_canvas"), defaultMapSettings);
		
		// place marker if it was defined
		if (LocationData != undefined)
		{
			Accommodation.Map.placeMarker(latLng);
		}
		
		// add event listner for placing marker on map when its clicked
		google.maps.event.addListener(this.instance, 'click', function(event) 
		{
			var latLng = event.latLng;
			
			// place marker
			Accommodation.Map.placeMarker(latLng);
			
		});
		
		// add event listener for auto complete
		var input = document.getElementById('Accommodation_accommodation_address');
		var autocomplete = new google.maps.places.Autocomplete(input);
		autocomplete.bindTo('bounds', this.instance);
		autocomplete.setTypes(['geocode']);

		var marker = new google.maps.Marker({map: this.instance});

		google.maps.event.addListener(autocomplete, 'place_changed', function() 
		{
			var place = autocomplete.getPlace();
			if (place.geometry.viewport) 
			{
				map.fitBounds(place.geometry.viewport);
			} else 
			{
				map.setCenter(place.geometry.location);
				map.setZoom(17);  // Why 17? Because it looks good.
			}
			Accommodation.Map.placeMarker(place.geometry.location);
		});

	},
	/**
	 * Places a marker on the map, saves the coords in hidden fields and the 
	 * marker object in the maps marker property.
	 * 
	 * @param latLong google object
	 * 
	 * @return void
	 */
	placeMarker: function(latLng)
	{
		// remove old marker if it exists
		if ( this.marker !== null )
		{
			this.marker.setMap(null);
		}
		
		this.marker = new google.maps.Marker(
		{
			position: latLng, 
			map: this.instance,
			animation: google.maps.Animation.DROP,
			icon: URL + '/images/layout/maps/iconb.png', // custom icon
			draggable:true
		});
		
		google.maps.event.addListener(this.marker, 'dragend', function() 
		{
			$('#Accommodation_accommodation_lat').val(Accommodation.Map.marker.position.lat());
			$('#Accommodation_accommodation_lng').val(Accommodation.Map.marker.position.lng());
		});
		
		// save accommodation coords and zoom level
		$('#Accommodation_accommodation_lat').val(latLng.lat());
		$('#Accommodation_accommodation_lng').val(latLng.lng());
		$('#Accommodation_accommodation_zoom').val(this.instance.getZoom());
		
		Accommodation.Map.reverseGeocode();
		
	},
	reverseGeocode: function()
	{
	    var lat = parseFloat($('#Accommodation_accommodation_lat').val());
	    var lng = parseFloat($('#Accommodation_accommodation_lng').val());
	    var latlng = new google.maps.LatLng(lat, lng);
	    var geocoder = new google.maps.Geocoder();
	    geocoder.geocode({'latLng': latlng}, function(results, status) 
	    {
			if (status == google.maps.GeocoderStatus.OK) 
			{
				if (results[0]) 
				{
					//console.log(results[0]);
					// store result
					Accommodation.Map.geocoderResult = results[0];
					Accommodation.Map.buildMapData();
					return true;
				}
			} 
			else 
			{
				Accommodation.Dialog.error.dialog('open').html("Geocoder failed due to: " + status);
				Accommodation.Map.geocoderResult = null;
			    return false;
			}
	    });

	},
	/**
	 * Takes an address string and geocodes it (converts it into coords).
	 * Stores result Accommodation.Map.geocoderResult
	 */
	geocode: function()
	{
		// get address, clean it as new line chars broke the response
		var address = $('#Accommodation_accommodation_address').val().trim().replace(/(\r\n|[\r\n])/g, '');
		
		// geocode and handle response
		var geocoder = new google.maps.Geocoder();
		geocoder.geocode( { 'address': address}, function(results, status) 
		{
			if (status == google.maps.GeocoderStatus.OK) 
			{
				// zoom & center map
				Accommodation.Map.instance.fitBounds(results[0].geometry.viewport);
				
				// place marker
				Accommodation.Map.placeMarker(results[0].geometry.location);
				
				Accommodation.Map.geocoderResult = results[0];
				return true;
			} 
			else 
			{
				if( status == 'ZERO_RESULTS' )
				{
					Accommodation.Dialog.error.dialog('open').html("Your address could not be found, please indicate your exact location by placing a marker on the map. \n Place a marker by clicking on the map.");
				}
				else
				{
					Accommodation.Dialog.error.dialog('open').html("Geocode was not successful for the following reason: " + status);
				}
			}
		});
		
		Accommodation.Map.geocoderResult = null;
	    return false;

	},
	/**
	 * Converts google map info response to JSON string and saves it to the map object.
	 * 
	 * @return true on success || false on failure.
	 */
	buildMapData: function()
	{
		// clear previous data
		$('#map_data').html('');
		
		if ( Accommodation.Map.geocoderResult != null )
		{
			json_location = JSON.stringify(Accommodation.Map.geocoderResult.address_components);
			
			var html = '';
			$.each(Accommodation.Map.geocoderResult.address_components, function(key, location)
			{
				var json_location = JSON.stringify(location);
				json_location = new String(json_location);
				json_location = json_location.replace(/"/g, '\'');
				html += '<input type="hidden" name="locations[]" class="locations" value="'+json_location+'" />' + "\n";
			});
			// save html to form
			$('#map_data').html(html);
			
			return true;
		}
		return false;
	}
	
};

Accommodation.Dialog = 
{
	error: null,
	standard: null
};

Accommodation.Get = 
{
	accommodationOverview: function()
	{
		var $tabs = $('#tabs');
		
		var i, count = 0;  
		for (i = 0; i < $tabs.tabs('length'); i++) 
		{  
			count++;  
		}
		count++;
		
		// Get Data out of current tab b4 new 1 is selected
		var accom_url = $('#accommodation_save_images').attr('href') + '/accommodation_id/' + $('.accommodation_id').first().val();

		// get currently selected tab
		var selected_tab = $tabs.tabs('option', 'selected');

		// create new tab 
		$tabs.tabs("add", "#ui-tabs-" + count, $('#Accommodation_accommodation_name').val(), (selected_tab));

		// select the newly created tab accommodation
		// $tabs.tabs('select', selected_tab);
		
		// reference the newly added tab for later use
		Accommodation.UI.newestTab = selected_tab;

		// get the tab content
		$.ajax(
		{
			url: accom_url,
			success: function(response)
			{
				$('#ui-tabs-' + count).html(response);
			}
		});
		
	},
	AddRoomTypeAssist: function(event)
	{
		console.log('room type');
		// select the newly added tab
		var $tabs = $('#tabs');
		$tabs.tabs('select', Accommodation.UI.newestTab);
		
		// trigger the add room event
		
	},
	AddRoom: function(event) 
	{
	  
	}
};

Accommodation.Op =
{
	saveDetails: function(event)
	{$(event.target).button('loading')
		// setup what happens if form is valid
		$.validator.setDefaults({
			submitHandler: function() 
			{
				$("#accommodation_details").fadeTo('fast', 0.5);
				// save form
				$.ajax({
					url: $("#accommodation_details").attr('action'),
					data: $("#accommodation_details").serialize(),
					type: 'POST',
					dataType: 'json',
					success: function(response)
					{
						// scroll to next step
						$('#accommodation_wizard_carousel').carousel('next');
						$("#accommodation_details").fadeTo('slow', 1);

						if ( response.accommodation_id )
						{
							if ( $('.accommodation_id') )
							{
								$('.accommodation_id').remove();
							}
							var accom_id = '<input type="hidden" name="Accommodation[accommodation_id]" value="'+response.accommodation_id+'" class="accommodation_id" />';
							$('#accommodation_details, #accommodation_location, #accommodation_features, #accommodation_images').append(accom_id);
						}
					}
				});
				
				 
			}
		});
	
		// setup validation rules
		$("#accommodation_details").validate({
			rules: {
				'Accommodation[accommodation_name]': 'required',
				'Accommodation[accommodation_description]': 'required',
			},
			messages: {
				'Accommodation[accommodation_name]': 'Please fill in the name of your accommodation',
				'Accommodation[accommodation_description]': 'Please fill in brief description',
			}
		});
		// validate the form
		$("#accommodation_details").submit();
		
	},
	saveLocation: function(event)
	{
		// check if there is a marker on the map, warn if not
		if ( Accommodation.Map.marker == null )
		{
			$('#map_canvas').addClass('error');
			$('#map_error').show();
		}
		else
		{
			$('#map_canvas').removeClass('error');
			$('#map_error').hide();
		}
		
		// setup what happens if form is valid
		$.validator.setDefaults({
			submitHandler: function() 
			{
				// check if there is a marker on the map, warn if not
				if ( Accommodation.Map.marker == null )
				{
					$('#map_canvas').addClass('error');
					$('#map_error').show();
					return;
				}
				else
				{
					$('#map_canvas').removeClass('error');
					$('#map_error').hide();
				}
			
				$("#accommodation_location").fadeTo('fast', 0.5);
				
				// save form
				var data = $("#accommodation_location").serialize();
				$.ajax({
					url: $("#accommodation_location").attr('action'),
					data: data,
					type: 'POST',
					dataType: 'json',
					success: function(response)
					{
						// scroll to next step
						$('#accommodation_wizard_carousel').carousel('next');
						$("#accommodation_location").fadeTo('slow', 1);
					}
				});
				
				 
			}
		});
		
		// setup validation rules
		$("#accommodation_location").validate({
			rules: {
				'Accommodation[accommodation_address]': 'required',
			},
			messages: {
				'Accommodation[accommodation_address]': 'Please fill your physicall address',
			}
		});
		// validate the form
		$("#accommodation_location").submit();
		
	},
	saveFeautures: function(event)
	{
		$("#accommodation_features").fadeTo('fast', 0.5);
		// save form
		$.ajax({
			url: $("#accommodation_features").attr('action'),
			data: $("#accommodation_features").serialize(),
			type: 'POST',
			dataType: 'json',
			success: function(response)
			{
				// scroll to next step
				$('#accommodation_wizard_carousel').carousel('next');
				$("#accommodation_features").fadeTo('slow', 1);
			}
		});
	},
	saveImages: function(event)
	{
		event.preventDefault();
		if ( Accommodation.Uploader.state === plupload.UPLOADING )
		{
			// show warning dialog
			Accommodation.Dialog.error.dialog('open').html('Please wait for images to finish uploading.');
			return;
		}
		
		$('#accommodation_wizard_carousel').carousel('next');
		
		if ( $('#add_edit_op').val() === 'add' )
		{
			Accommodation.Get.accommodationOverview();
		}
		
		// destroy the image uploader
		Accommodation.Uploader.instance.destroy();
		
	},
	deleteImage: function(event)
	{
		event.preventDefault();
		
		var $href = $(event.target).addClass('hide');
		$href.next('img').remove();
		$href.closest('div').addClass('empty').css({width:'150px', height:'150px'});
		
		// delete image
		$.ajax({
			url: $href.attr('href'),
			type: 'GET',
			dataType: 'json',
			success: function(response)
			{
				// nothing to do here
			}
		});
		
	}
};

Accommodation.UI = 
{
	tabPanes: new Array(),
	tabContainers: new Array(),
	newestTab: false,
	/**
	 * This method overides the tab caching by looking for tabs that have ids and ajax data accossiated with
	 * them. if a tab has these then ajax content is loaded into that tab.
	 */
	overRideTabCache: function(event, ui)
	{
		// if an overridden tab content was previously loaded then clear it
		$.each(Accommodation.UI.tabPanes, function(key, value)
		{
			// clear tab content
			$(value).html('');
			
			// remove that from tab panes to be emptied list
			Accommodation.UI.tabPanes.splice(key, (key + 1));
			
		});
		
		// if tab has id then get that tab
		if ( ui.tab.id != "" )
		{
			var $tab = $('#' + ui.tab.id);
			
			if ( $tab.data('url') == undefined || $tab.data('load_into') == undefined )
			{
				return;
			}
			// load content into tab
			$($tab.data('load_into')).load($tab.data('url'));
			Accommodation.UI.tabPanes.push(ui.panel);
			
		}
		
	},
	
	showAjaxTabContent: function(event, ui)
	{
		// if there is an ajax tab which is visible but has no content in it then load its content.		
		var $tab_panel = $(ui.panel).find('div.ui-tabs-panel:visible');
		if ( $tab_panel.length !== -1 && $tab_panel.id !== "" )
		{
			var $tab = $(ui.panel).find('ul li.ui-tabs-selected').find('a');
			
			if ( $tab.data('url') == undefined || $tab.data('load_into') == undefined )
			{
				return;
			}

			// load content into tab
			$($tab.data('load_into')).load($tab.data('url'));
			Accommodation.UI.tabPanes.push($tab_panel);
			
		}
	}
	
};

Accommodation.Delegate =
{
	initialize: function()
	{
		/**
		 * Room interactions start here
		 */
		$('.add_rooms_assist').live('click', $.proxy(Accommodation.Get.AddRoomTypeAssist, this));
		
		/**
		 * Accommodation establishment interactions start:
		 */
		// bind geocoder event
		$('#geocode_address').live('click', Accommodation.Map.geocode);
		
		// bind save saveDetails
		$('#accommodation_save_details').live('click', $.proxy(Accommodation.Op.saveDetails, this));
		
		// bind save location next button
		$('#accommodation_save_location').live('click', $.proxy(Accommodation.Op.saveLocation, this));
		
		// bind save location next button
		$('#accommodation_save_features').live('click', $.proxy(Accommodation.Op.saveFeautures, this));
		
		// bind the finish wizard btn
		$('#accommodation_save_images').live('click', $.proxy(Accommodation.Op.saveImages, this));
		
		// bind previous btn
		$('.previous-wizard').live('click', function()
		{
			// Dont allow user to navigate away if an upload is in progress
			if ( Accommodation.Uploader.state === plupload.UPLOADING )
			{
				return;
			}
			$('#accommodation_wizard_carousel').carousel('prev');
		});

		// bind image delete buttons
		$('.delete_image_btn').live('click', $.proxy(Accommodation.Op.deleteImage, this));
		
		// ignore return key on certian input elements
		$('.ignore-enter').live('keydown', function(e){
			var charCode = e.charCode || e.keyCode;
			if (charCode  == 13) { //Enter key's keycode
				return false;
			}
		});
		
	}
};

/**
 * Called when the dashboard is loaded.
 */
$(function() 
{
	// setup accordian menu
	$("#tabs").tabs({
		cache:true,
		select: Accommodation.UI.overRideTabCache,
		show: Accommodation.UI.showAjaxTabContent
	});
	
	// setup events listners
	Accommodation.Delegate.initialize();
	
	$("body").ajaxError(function(e) 
	{
		Accommodation.Dialog.error.dialog('open').html("The following error occurred: \n " + e.type);
		console.log('Error log: ' + e.type);
		console.log(e);
	});
	
	// initialize the re-usable dialog
	Accommodation.Dialog.error = $('<div></div>').dialog({
		autoOpen: false,
		title: 'Warning',
		modal: true,
		dialogClass: 'ui-state-error',
		buttons: { "Ok": function() { $(this).dialog("close"); }}
	});
	//.addClass("ui-state-error") // ui-state-highlight
	
	// async load google maps api
	var script = document.createElement("script");
	script.type = "text/javascript";
	script.src = "http://maps.googleapis.com/maps/api/js?key=AIzaSyDLfvkYf4sB06CFKzERvPZX32K3_FFqOFo&sensor=false&libraries=places&callback=Accommodation.Map.mapReady";
	document.body.appendChild(script);
	

});
