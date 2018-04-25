<?php
	use Illuminate\Support\Str;

	use App\Models\Admin;
?>

@extends('back.template.master')

@section('title')
	Setting Edit
@endsection

@section('head_additional')
	{!!HTML::style('css/back/edit.css')!!}

	<style>
		/* Always set the map height explicitly to define the size of the div
		* element that contains the map. */
		#map {
			height: 100vh;
			max-height: 500px;
		}

		/* Optional: Makes the sample page fill the window. */
		html, body {
			height: 100%;
			margin: 0;
			padding: 0;
		}

		#description {
			font-family: Roboto;
			font-size: 15px;
			font-weight: 300;
		}

		#infowindow-content .title {
			font-weight: bold;
		}

		#infowindow-content {
			display: none;
		}

		#map #infowindow-content {
			display: inline;
		}

		.pac-card {
			margin: 10px 10px 0 0;
			border-radius: 2px 0 0 2px;
			box-sizing: border-box;
			-moz-box-sizing: border-box;
			outline: none;
			box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
			background-color: #fff;
			font-family: Roboto;
		}

		#pac-container {
			padding-bottom: 12px;
			margin-right: 12px;
		}

		.pac-controls {
			display: inline-block;
			padding: 5px 11px;
		}

		.pac-controls label {
			font-family: Roboto;
			font-size: 13px;
			font-weight: 300;
		}

		#pac-input {
			font-family: Roboto;
			font-size: 14px;
			margin-left: 12px;
			padding: 0 10px;
			text-overflow: ellipsis;
			width: 400px;
			height: 40px;
			top: 10px !important;
			border: 1px solid #d2d2d2;
			background-position: right 10px center !important;
			background-size: 20px auto !important;
		}

		#title {
			color: #fff;
			background-color: #4d90fe;
			font-size: 25px;
			font-weight: 500;
			padding: 6px 12px;
		}

		#target {
			width: 345px;
		}

		.edit-right-desc {
			position: relative;
			display: block;
			font-size: 12px;
			line-height: 20px;
		}
    </style>
@endsection

@section('js_additional')
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDRPDezG1FqdMpqMqJEPwYzBc70wpDpbOI&libraries=places&callback=initAutocomplete" async defer></script>

	<script>
		// This example adds a search box to a map, using the Google Place Autocomplete
		// feature. People can enter geographical searches. The search box will return a
		// pick list containing a mix of places and predicted search terms.

		// This example requires the Places library. Include the libraries=places
		// parameter when you first load the API. For example:
		// <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">

		function initAutocomplete() {
			var image = "{{URL::to('img/admin/pin.png')}}";

			/*
				SET CENTER OF MAPS
			*/
			var map = new google.maps.Map(document.getElementById('map'), {
				center: new google.maps.LatLng{{$setting->coor}},
				zoom: 15,
				mapTypeId: 'roadmap'
			});

			/*
				SET DEFAULT MARKER OF MAPS
			*/
			var marker = new google.maps.Marker({
				position: new google.maps.LatLng{{$setting->coor}},
				icon: image,
				map: map
			});

			/*
				CLICK FUNCTION FOR CHANGE COORDINATE
			*/
			google.maps.event.addListener(map, 'click', function(event) {
				// alert('Point.X.Y: ' + event.latLng);
				document.getElementById('langitude_input').value = event.latLng;
				document.getElementById('langitude_input1').value = event.latLng;
			});

			// Create the search box and link it to the UI element.
			var input = document.getElementById('pac-input');
			var searchBox = new google.maps.places.SearchBox(input);
			map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

			// Bias the SearchBox results towards current map's viewport.
			map.addListener('bounds_changed', function() {
				searchBox.setBounds(map.getBounds());
			});

			var markers = [];
			// Listen for the event fired when the user selects a prediction and retrieve
			// more details for that place.
			searchBox.addListener('places_changed', function() {
				var places = searchBox.getPlaces();

				if (places.length == 0) {
					return;
				}

				// Clear out the old markers.
				markers.forEach(function(marker) {
					marker.setMap(null);
				});
				markers = [];

				// For each place, get the icon, name and location.
				var bounds = new google.maps.LatLngBounds();
				places.forEach(function(place) {
					if (!place.geometry) {
						console.log("Returned place contains no geometry");
						return;
					}
					var icon = {
						url: place.icon,
						size: new google.maps.Size(71, 71),
						origin: new google.maps.Point(0, 0),
						anchor: new google.maps.Point(17, 34),
						scaledSize: new google.maps.Size(25, 25)
					};

					// Create a marker for each place.
					markers.push(new google.maps.Marker({
						map: map,
						icon: image,
						title: place.name,
						position: place.geometry.location
					}));

					if (place.geometry.viewport) {
						// Only geocodes have viewport.
						bounds.union(place.geometry.viewport);
					} 
					else 
					{
						bounds.extend(place.geometry.location);
					}

					/*
						CHANGE COORDINATE WHEN SEARCH PLACE USING SEARCH BOX
					*/
					document.getElementById('langitude_input').value = place.geometry.location;
					document.getElementById('langitude_input1').value = place.geometry.location;
				});

				map.fitBounds(bounds);
			});
		}

    </script>

    <script>
    	$(document).ready(function(){
    		$('.submit').click(function(){
    			$('.form-target').submit();
    		});

    		$('.reset').click(function(){
    			$('.reset-switch').click();
    		});
    	});
    </script>
@endsection

@section('page_title')
	Setting Edit
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <span>Setting Edit</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		<li>
			Back End Session Lifetime adalah waktu yang digunakan untuk Sign Out secara otomatis jika halaman back end yang Anda buka tidak ada kegiatan sama sekali dalam waktu yang sudah ditentukan
		</li>
		<li>
			Front End Session Lifetime adalah waktu yang digunakan untuk Sign Out secara otomatis jika halaman front end yang Anda buka tidak ada kegiatan sama sekali dalam waktu yang sudah ditentukan
		</li>
		<li>
			Admin URL adalah URL yang digunakan untuk masuk ke halaman Back End
		</li>
		<li>
			Pastikan anda tidak lupa dengan Admin URL yang Anda Buat
		</li>
		<li>
			Maintenance digunakan untuk merubah status Front End menjadi Maintenance Mode atau sebaliknya
		</li>
		<li>
			Contact Email akan ditampilkan di halaman Contact Us front end
		</li>
		<li>
			Receiver Email akan digunakan untuk menerima email dari user lewat sistem di front end
		</li>
		<li>
			Sender Email akan digunakan sebagai alamat email pengirim yang di kirim melalui sistem
		</li>
		<li>
			Facebook, Twitter, Instagram akan digunakan sebagai link menuju Social Media masing-masing yang diletakkan di Footer Front End
		</li>
		<li>
			Untuk merubah Koordinat Anda hanya perlu meng-klik Koordinat yang ada di Google Maps, dan Koordinat Anda otomatis berubah
		</li>
	</ul>
@endsection

@section('content')
	<div class="page-group">
		<div class="page-item col-1">
			<div class="page-item-content">				
				<a class="edit-button-item edit-button-back" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">
					Back
				</a>

				<div class="page-item-error-container">
					@foreach ($errors->all() as $error)
						<div class='page-item-error-item'>
							{{$error}}
						</div>
					@endforeach
				</div>
				{!!Form::model($setting, ['url' => URL::to(Crypt::decrypt($setting->admin_url) . '/setting/edit'), 'method' => 'POST', 'files' => true, 'class'=>'form-target'])!!}
					<div class="page-group">
						<div class="page-item col-2-4">
							<div class="page-item-title">
								Session
							</div>
							<div class="page-item-content edit-item-content">
								<div class="edit-form-group">
									{!!Form::label('back_session_lifetime', 'Back End Session Lifetime', ['class'=>'edit-form-label'])!!}
									{!!Form::input('number', 'back_session_lifetime', null, ['class'=>'edit-form-text large', 'required'])!!}
									<span class="edit-form-note">
										*Required
									</span>
								</div>
								<div class="edit-form-group">
									{!!Form::label('front_session_lifetime', 'Front End Session Lifetime', ['class'=>'edit-form-label'])!!}
									{!!Form::input('number', 'front_session_lifetime', null, ['class'=>'edit-form-text large', 'required'])!!}
									<span class="edit-form-note">
										*Required
									</span>
								</div>
								<div class="edit-form-group">
									{!!Form::label('visitor_lifetime', 'Visitor Lifetime', ['class'=>'edit-form-label'])!!}
									{!!Form::input('number', 'visitor_lifetime', null, ['class'=>'edit-form-text large', 'required'])!!}
									<span class="edit-form-note">
										*Required
									</span>
								</div>
							</div>
						</div>
						<div class="page-item col-2-4">
							<div class="page-item-title">
								Admin Management
							</div>
							<div class="page-item-content edit-item-content">
								<div class="edit-form-group">
									{!!Form::label('admin_url', 'Admin URL', ['class'=>'edit-form-label'])!!}
									{!!Form::text('admin_url', Crypt::decrypt($setting->admin_url), ['class'=>'edit-form-text large ckeditor'])!!}
									<span class="edit-form-note">
										*Required
									</span>
								</div>
								<div class="edit-form-group">
									{!!Form::label('maintenance', 'Maintenance Status', ['class'=>'edit-form-label'])!!}
									<div class="edit-form-radio-group">
										<div class="edit-form-radio-item">
											{!!Form::radio('maintenance', 1, true, ['class'=>'edit-form-radio', 'id'=>'true'])!!} 
											{!!Form::label('true', 'Maintenance Mode', ['class'=>'edit-form-radio-label'])!!}
										</div>
										<div class="edit-form-radio-item">
											{!!Form::radio('maintenance', 0, false, ['class'=>'edit-form-radio', 'id'=>'false'])!!} 
											{!!Form::label('false', 'Not Maintenance', ['class'=>'edit-form-radio-label'])!!}
										</div>
									</div>
								</div>
								<div class="edit-form-group">
									{!!Form::label('google_analytics', 'Google Analytic', ['class'=>'edit-form-label'])!!}
									{!!Form::textarea('google_analytics', $setting->google_analytics, ['class'=>'edit-form-text large area'])!!}
								</div>
							</div>
						</div>
					</div>
					<div class="page-group">
						<div class="page-item col-2-4">
							<div class="page-item-title">
								Contact Information
							</div>
							<div class="page-item-content edit-item-content">
								<div class="edit-form-group">
									{!!Form::label('name', 'Name', ['class'=>'edit-form-label'])!!}
									{!!Form::text('name', null, ['class'=>'edit-form-text large'])!!}
								</div>
								<div class="edit-form-group">
									{!!Form::label('address', 'Address', ['class'=>'edit-form-label'])!!}
									{!!Form::textarea('address', null, ['class'=>'edit-form-text large area'])!!}
								</div>
								<div class="edit-form-group">
									{!!Form::label('phone', 'Phone', ['class'=>'edit-form-label'])!!}
									{!!Form::text('phone', null, ['class'=>'edit-form-text large'])!!}
								</div>
								<div class="edit-form-group">
									{!!Form::label('fax', 'Fax', ['class'=>'edit-form-label'])!!}
									{!!Form::text('fax', null, ['class'=>'edit-form-text large'])!!}
								</div>
								<div class="edit-form-group">
									{!!Form::label('bbm', 'BBM', ['class'=>'edit-form-label'])!!}
									{!!Form::text('bbm', null, ['class'=>'edit-form-text large'])!!}
								</div>
								<div class="edit-form-group">
									{!!Form::label('whatsapp', 'Whatsapp', ['class'=>'edit-form-label'])!!}
									{!!Form::text('whatsapp', null, ['class'=>'edit-form-text large'])!!}
								</div>
								<div class="edit-form-group">
									{!!Form::label('line', 'Line', ['class'=>'edit-form-label'])!!}
									{!!Form::text('line', null, ['class'=>'edit-form-text large'])!!}
								</div>
							</div>
						</div>
						<div class="page-item col-2-4">
							<div class="page-item-title">
								Email
							</div>
							<div class="page-item-content edit-item-content">
								<div class="edit-form-group">
									{!!Form::label('contact_email', 'Contact Email', ['class'=>'edit-form-label'])!!}
									{!!Form::email('contact_email', null, ['class'=>'edit-form-text large'])!!}
									<span class="edit-form-note">
										*Required
									</span>
								</div>
								<div class="edit-form-group">
									{!!Form::label('sender_email', 'Sender Email', ['class'=>'edit-form-label'])!!}
									{!!Form::email('sender_email', null, ['class'=>'edit-form-text large'])!!}
									<span class="edit-form-note">
										*Required
									</span>
								</div>
								<div class="edit-form-group">
									{!!Form::label('sender_email_name', 'Sender Email Name', ['class'=>'edit-form-label'])!!}
									{!!Form::text('sender_email_name', null, ['class'=>'edit-form-text large'])!!}
									<span class="edit-form-note">
										*Required
									</span>
								</div>
								<div class="edit-form-group">
									{!!Form::label('receiver_email', 'Receiver Email', ['class'=>'edit-form-label'])!!}
									{!!Form::email('receiver_email', null, ['class'=>'edit-form-text large'])!!}
									<span class="edit-form-note">
										*Required
									</span>
								</div>
								<div class="edit-form-group">
									{!!Form::label('receiver_email_name', 'Receiver Email Name', ['class'=>'edit-form-label'])!!}
									{!!Form::text('receiver_email_name', null, ['class'=>'edit-form-text large'])!!}
									<span class="edit-form-note">
										*Required
									</span>
								</div>
							</div>
						</div>
					</div>
					<div class="page-group">
						<div class="page-item col-2-4">
							<div class="page-item-title">
								Social Media
							</div>
							<div class="page-item-content edit-item-content">
								<div class="edit-form-group">
									{!!Form::label('facebook', 'Facebook', ['class'=>'edit-form-label'])!!}
									{!!Form::text('facebook', null, ['class'=>'edit-form-text large'])!!}
								</div>
								<div class="edit-form-group">
									{!!Form::label('twitter', 'Twitter', ['class'=>'edit-form-label'])!!}
									{!!Form::text('twitter', null, ['class'=>'edit-form-text large'])!!}
								</div>
								<div class="edit-form-group">
									{!!Form::label('instagram', 'Instagram', ['class'=>'edit-form-label'])!!}
									{!!Form::text('instagram', null, ['class'=>'edit-form-text large'])!!}
								</div>
							</div>
						</div>
						<div class="page-item col-2-4">
							<div class="page-item-title">
								Order System
							</div>
							<div class="page-item-content edit-item-content">
								<div class="edit-form-group">
									{!!Form::label('weight_tolerance', 'Weight Tolerance', ['class'=>'edit-form-label'])!!}
									{!!Form::input('number', 'weight_tolerance', null, ['class'=>'edit-form-text large'])!!}
								</div>
								<div class="edit-form-group">
									{!!Form::label('is_free', 'Free Delivery', ['class'=>'edit-form-label'])!!}
									<div class="edit-form-radio-group">
										<div class="edit-form-radio-item">
											{!!Form::radio('is_free', 1, true, ['class'=>'edit-form-radio', 'id'=>'true'])!!} 
											{!!Form::label('true', 'Yes', ['class'=>'edit-form-radio-label'])!!}
										</div>
										<div class="edit-form-radio-item">
											{!!Form::radio('is_free', 0, false, ['class'=>'edit-form-radio', 'id'=>'false'])!!} 
											{!!Form::label('false', 'No', ['class'=>'edit-form-radio-label'])!!}
										</div>
									</div>
								</div>
								<div class="edit-form-group">
									{!!Form::label('free_delivery', 'Free Delivery Amount', ['class'=>'edit-form-label'])!!}
									{!!Form::input('number', 'free_delivery', null, ['class'=>'edit-form-text large'])!!}
								</div>
							</div>
						</div>
					</div>

					{!!Form::hidden('coordinat', $setting->coor, ['class'=>'large-text', 'id'=>'langitude_input1', 'readonly', 'placeholder'=>'Click on the maps to get coordinat'])!!}
					{!!Form::submit('submit', ['style'=>'display: none;'])!!}
					{!!Form::reset('reset', ['style'=>'display: none;', 'class'=>'reset-switch'])!!}

				{!!Form::close()!!}

				<div class="page-group">
					<div class="page-item col-1">
						<div class="page-item-title">
							Location
						</div>
						<div class="page-item-content edit-item-content">
							<div class="edit-form-group">
								{!!Form::label('coordinat', 'Coordinate', ['class'=>'edit-form-label'])!!}
								{!!Form::text('coordinat', $setting->coor, ['class'=>'edit-form-text large', 'id'=>'langitude_input', 'readonly', 'placeholder'=>'Click on the maps to get coordinat'])!!}
								<span class="edit-form-note">
									*Required
								</span>
								<div class="edit-form-note-custom">
									*Click on the map to change the coordinate instantly or<br>
									*Type your address on the <i>Search Box</i> to change the coordinate
								</div>
							</div>
							<div class="edit-form-group">
								<input id="pac-input" class="controls" type="text" placeholder="Type address here" style="background: #fff URL('<?php echo URL::to('img/admin/icon_search_box.png') ?>') no-repeat;">
							    <div id="map"></div>
							</div>
						</div>
					</div>
				</div>

				<div class="page-group">
					<div class="edit-button-group">
						{{Form::submit('Save', ['class'=>'edit-button-item submit'])}}
						{{Form::reset('Reset', ['class'=>'edit-button-item reset'])}}
					</div>
				</div>

				<div class="edit-last-edit">
					<?php
						$updateuser = Admin::find($setting->settingupdate_id);
					?>

					<div class="page-item-title" style="margin-bottom: 20px;">
						Basic Information
					</div>

					<div class="edit-last-edit-group">
						<div class="edit-last-edit-title">
							Update
						</div>
						
						<div class="edit-last-edit-item">
							<span>
								Last Updated by
							</span>
							<span>
								:
							</span>
							<span>
								{{$updateuser->name}}
							</span>
						</div>
					</div>
				</div>
				
			</div>
		</div>
	</div>
@endsection