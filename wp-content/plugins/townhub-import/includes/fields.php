<?php

function townhub_import_addondo_fields($townhub_import_addon){
	
	$adon_fields = array(
		'location_opts_title'=> array(
			'is_title'			=> true,
			'field_type' 		=> 'text',
			'field_name'		=> __( 'Address/Contacts', 'townhub-import' ),
			'enum_values' 		=> array(), //null,
			'tooltip'			=> '',
			'is_html'			=> true,
			'default_text'		=> '',

			// do not auto update
			'ignore_update'		=> true,
		),
		'_cth_address' => array(
			'field_type' 		=> 'text',
			'field_name'		=> __( 'Listing Address (Google address is recommended)', 'townhub-import' ),
			'enum_values' 		=> array(), //null,
			'tooltip'			=> '',
			'is_html'			=> true,
			'default_text'		=> '',
		),
		'listing_location_lat_lng'=> array(
			'field_type' 		=> 'radio',
			'field_name'		=> __( 'Listing Coordinates', 'townhub-import' ),
			'enum_values' 		=> listing_coordinates($townhub_import_addon), //null,
			'tooltip'			=> '',
			'is_html'			=> true,
			'default_text'		=> '',

			// do not auto update
			'ignore_update'		=> true,
		),

		// 'esb_import_ado_cats' => array(
		// 	'field_type' 		=> 'text',
		// 	'field_name'		=> __( 'Categories ( separated by comma )', 'townhub-import' ),
		// 	'enum_values' 		=> array(), //null,
		// 	'tooltip'			=> '',
		// 	'is_html'			=> true,
		// 	'default_text'		=> '',

		// 	// do not auto update
		// 	'ignore_update'		=> true,
		// ),

		// 'esb_import_ado_features' => array(
		// 	'field_type' 		=> 'text',
		// 	'field_name'		=> __( 'Features ( separated by comma )', 'townhub-import' ),
		// 	'enum_values' 		=> array(), //null,
		// 	'tooltip'			=> '',
		// 	'is_html'			=> true,
		// 	'default_text'		=> '',

		// 	// do not auto update
		// 	'ignore_update'		=> true,
		// ),


		// 'esb_import_ado_tags' => array(
		// 	'field_type' 		=> 'text',
		// 	'field_name'		=> __( 'Tags ( separated by comma )', 'townhub-import' ),
		// 	'enum_values' 		=> array(), //null,
		// 	'tooltip'			=> '',
		// 	'is_html'			=> true,
		// 	'default_text'		=> '',

		// 	// do not auto update
		// 	'ignore_update'		=> true,
		// ),
		
		'_cth_phone' => array(
			'field_type' 		=> 'text',
			'field_name'		=> __( 'Phone', 'townhub-import' ),
			'enum_values' 		=> array(), //null,
			'tooltip'			=> '',
			'is_html'			=> true,
			'default_text'		=> '',
		),
		'_cth_whatsapp' => array(
			'field_type' 		=> 'text',
			'field_name'		=> __( 'Whatsapp number', 'townhub-import' ),
			'enum_values' 		=> array(), //null,
			'tooltip'			=> '',
			'is_html'			=> true,
			'default_text'		=> '',
		),
		
		'_cth_email' => array(
			'field_type' 		=> 'text',
			'field_name'		=> __( 'Email', 'townhub-import' ),
			'enum_values' 		=> array(), //null,
			'tooltip'			=> '',
			'is_html'			=> true,
			'default_text'		=> '',
		),
		'_cth_website' => array(
			'field_type' 		=> 'text',
			'field_name'		=> __( 'Website', 'townhub-import' ),
			'enum_values' 		=> array(), //null,
			'tooltip'			=> '',
			'is_html'			=> true,
			'default_text'		=> '',
		),

		'_cth_resmenus'=> array(
			'field_type' 		=> 'text',
			'field_name'		=> __( 'Serialized Restaurant menus', 'townhub-import' ),
			'enum_values' 		=> array(), //null,
			'tooltip'			=> '',
			'is_html'			=> true,
			'default_text'		=> '',
			// do not auto update
			'ignore_update'		=> true,
		),

		'_cth_socials' => array(
			'is_options'		=> true,
			'field_type' 		=> 'text',
			'field_name'		=> __( 'Socials', 'townhub-import' ),
			'enum_values' 		=> townhub_import_addon_socials('_cth_socials',$townhub_import_addon), //null,
			'tooltip'			=> '',
			'is_html'			=> true,
			'default_text'		=> '',

			// do not auto update
			'ignore_update'		=> true,
		),

		


		'price_opts_title'=> array(
			'is_title'			=> true,
			'field_type' 		=> 'text',
			'field_name'		=> __( 'Price Options', 'townhub-import' ),
			'enum_values' 		=> array(), //null,
			'tooltip'			=> '',
			'is_html'			=> true,
			'default_text'		=> '',

			// do not auto update
			'ignore_update'		=> true,
		),

		'_cth_price_range'=> array(
			'field_type' 		=> 'radio',
			'field_name'		=> __( 'Price Status', 'townhub-import' ),
			// 'enum_values' 		=> townhub_addons_get_listing_price_range(), //null,
			'enum_values' 		=> array(
							        'none' => 'Unset',
							        'inexpensive' => 'Inexpensive',
							        'moderate' => 'Moderate',
							        'pricey' => 'Pricey',
							        'ultrahigh' => 'Ultra High',
							    ),
			'tooltip'			=> '',
			'is_html'			=> true,
			'default_text'		=> '',
		),
		'_cth_price_from' => array(
			'field_type' 		=> 'text',
			'field_name'		=> __( 'Price From - Main Price', 'townhub-import' ),
			'enum_values' 		=> array(), //null,
			'tooltip'			=> '',
			'is_html'			=> true,
			'default_text'		=> '',
		),
		'_cth_price_to' => array(
			'field_type' 		=> 'text',
			'field_name'		=> __( 'Price To', 'townhub-import' ),
			'enum_values' 		=> array(), //null,
			'tooltip'			=> '',
			'is_html'			=> true,
			'default_text'		=> '',
		),
		'_cth_verified' => array(
			'field_type' 		=> 'text',
			'field_name'		=> __( 'Claim Status (Empty for Not claimed)', 'townhub-import' ),
			'enum_values' 		=> array(), //null,
			'tooltip'			=> '',
			'is_html'			=> true,
			'default_text'		=> '',

			// do not auto update
			'ignore_update'		=> true,
		),

		


		'media_opts_title'=> array(
			'is_title'			=> true,
			'field_type' 		=> 'text',
			'field_name'		=> __( 'Media Options', 'townhub-import' ),
			'enum_values' 		=> array(), //null,
			'tooltip'			=> '',
			'is_html'			=> true,
			'default_text'		=> '',

			// do not auto update
			'ignore_update'		=> true,
		),
		'_thumbnail' => array(
			'field_type' 		=> 'image',
			'field_name'		=> __( 'Listing Featured Image', 'townhub-import' ),
			'enum_values' 		=> array(), //null,
			'tooltip'			=> '',
			'is_html'			=> true,
			'default_text'		=> '',

			// do not auto update
			'ignore_update'		=> true,
		),

		'_llogo' => array(
			'field_type' 		=> 'image',
			'field_name'		=> __( 'Listing Logo Image', 'townhub-import' ),
			'enum_values' 		=> array(), //null,
			'tooltip'			=> '',
			'is_html'			=> true,
			'default_text'		=> '',

			// do not auto update
			'ignore_update'		=> true,
		),

		'esb_import_ado_images' => array(
			'field_type' 		=> 'images',
			'field_name'		=> __( 'Gallery/Slider Images', 'townhub-import' ),
			'enum_values' 		=> array(), //null,
			'tooltip'			=> '',
			'is_html'			=> true,
			'default_text'		=> '',

			// do not auto update
			'ignore_update'		=> true,
		),


		'esb_import_ado_header_imgs' => array(
			'field_type' 		=> 'images',
			'field_name'		=> __( 'Header Images', 'townhub-import' ),
			'enum_values' 		=> array(), //null,
			'tooltip'			=> '',
			'is_html'			=> true,
			'default_text'		=> '',

			// do not auto update
			'ignore_update'		=> true,
		),

		// '_cth_headermedia' => array(
		// 	'field_type' 		=> 'radio',
		// 	'field_name'		=> __( 'Listing Header Type. Images for header are from <strong>Listing Header Images</strong> section', 'townhub-import' ),
		// 	'enum_values' 		=> townhub_import_addon_header_types($townhub_import_addon), //null,
		// 	'tooltip'			=> '',
		// 	'is_html'			=> true,
		// 	'default_text'		=> '',

		// 	// do not auto update
		// 	'ignore_update'		=> true,
		// ),

		'_cth_header_type' => array(
			'field_type' 		=> 'text',
			'field_name'		=> __( 'Listing Header Type. Images for header are from <strong>Listing Header Images</strong> section', 'townhub-import' ),
			'enum_values' 		=> array(), //null,
			'tooltip'			=> '',
			'is_html'			=> true,
			'default_text'		=> '',

			// do not auto update
			'ignore_update'		=> true,
		),
		'headerbg_youtube' => array(
			'field_type' 		=> 'text',
			'field_name'		=> __( 'Youtube Background ( video ID. Ex: Hg5iNVSp2z8 )', 'townhub-import' ),
			'enum_values' 		=> array(), //null,
			'tooltip'			=> '',
			'is_html'			=> true,
			'default_text'		=> '',

			// do not auto update
			'ignore_update'		=> true,
		),
		'headerbg_vimeo' => array(
			'field_type' 		=> 'text',
			'field_name'		=> __( 'Vimeo Background ( video ID. Ex: 97871257 )', 'townhub-import' ),
			'enum_values' 		=> array(), //null,
			'tooltip'			=> '',
			'is_html'			=> true,
			'default_text'		=> '',

			// do not auto update
			'ignore_update'		=> true,
		),
		'headerbg_mp4' => array(
			'field_type' 		=> 'text',
			'field_name'		=> __( 'Vimeo URL (.MP4)', 'townhub-import' ),
			'enum_values' 		=> array(), //null,
			'tooltip'			=> '',
			'is_html'			=> true,
			'default_text'		=> '',

			// do not auto update
			'ignore_update'		=> true,
		),
		


		// 'content_widget_opts_title'=> array(
		// 	'is_title'			=> true,
		// 	'field_type' 		=> 'text',
		// 	'field_name'		=> __( 'Content Widgets', 'townhub-import' ),
		// 	'enum_values' 		=> array(), //null,
		// 	'tooltip'			=> '',
		// 	'is_html'			=> true,
		// 	'default_text'		=> '',

		// 	// do not auto update
		// 	'ignore_update'		=> true,
		// ),

		// 'content_widget_gallery_text'=> array(
		// 	'is_text'			=> true,
		// 	'field_type' 		=> 'text',
		// 	'field_name'		=> __( 'Gallery Widget - Images is imported from Listing Gallery Images section', 'townhub-import' ),
		// 	'enum_values' 		=> array(), //null,
		// 	'tooltip'			=> '',
		// 	'is_html'			=> true,
		// 	'default_text'		=> '',
		// ),
		// 'content_widget_slider_text'=> array(
		// 	'is_text'			=> true,
		// 	'field_type' 		=> 'text',
		// 	'field_name'		=> __( 'Slider Widget - Images is imported from Listing Slider Images section', 'townhub-import' ),
		// 	'enum_values' 		=> array(), //null,
		// 	'tooltip'			=> '',
		// 	'is_html'			=> true,
		// 	'default_text'		=> '',
		// ),

		'_cth_promo_video'=> array(
			'field_type' 		=> 'text',
			'field_name'		=> __( 'Listing Video ( YouTube, Vimeo or .mp4 video url )', 'townhub-import' ),
			'enum_values' 		=> array(), //null,
			'tooltip'			=> '',
			'is_html'			=> true,
			'default_text'		=> '',

			// do not auto update
			'ignore_update'		=> true,
		),
		// '_cth_promo_bg' => array(
		// 	'field_type' 		=> 'image',
		// 	'field_name'		=> __( 'Promo Video Background', 'townhub-import' ),
		// 	'enum_values' 		=> array(), //null,
		// 	'tooltip'			=> __( 'Set promo video a background image to display as popup', 'townhub-import' ),
		// 	'is_html'			=> true,
		// 	'default_text'		=> '',
		// ),


		

		// 'event_opts_title'=> array(
		// 	'is_title'			=> true,
		// 	'field_type' 		=> 'text',
		// 	'field_name'		=> __( 'Event Listing Options', 'townhub-import' ),
		// 	'enum_values' 		=> array(), //null,
		// 	'tooltip'			=> '',
		// 	'is_html'			=> true,
		// 	'default_text'		=> '',
		// ),

		'_event_date'=> array(
			'field_type' 		=> 'text',
			'field_name'		=> __( 'Event Start Date (Format: 2020-02-20 08:30:00)', 'townhub-import' ),
			'enum_values' 		=> array(), //null,
			'tooltip'			=> '',
			'is_html'			=> true,
			'default_text'		=> '',
			// do not auto update
			'ignore_update'		=> true,
		),

		'_event_date_end'=> array(
			'field_type' 		=> 'text',
			'field_name'		=> __( 'Event Date End (Format: 2020-02-20 16:30:00)', 'townhub-import' ),
			'enum_values' 		=> array(), //null,
			'tooltip'			=> '',
			'is_html'			=> true,
			'default_text'		=> '',
			// do not auto update
			'ignore_update'		=> true,
		),

		'_cth_working_hours_meta' => array(
			'is_options'		=> true,
			'field_type' 		=> 'text',
			'field_name'		=> __( 'Working Hours', 'townhub-import' ),
			'enum_values' 		=> townhub_import_addon_working_hours($townhub_import_addon), //null,
			'tooltip'			=> '',
			'is_html'			=> true,
			'default_text'		=> '',

			// do not auto update
			'ignore_update'		=> true,
		),

		'listing_dates'=> array(
			'field_type' 		=> 'text',
			'field_name'		=> __( 'Available Dates - separate with semicolon. Ex: 2020-03-02;2020-03-18;2020-03-31', 'townhub-import' ),
			'enum_values' 		=> array(), //null,
			'tooltip'			=> '',
			'is_html'			=> true,
			'default_text'		=> '',
			
			// do not auto update
			'ignore_update'		=> true,
		),


		'other_opts_title'=> array(
			'is_title'			=> true,
			'field_type' 		=> 'text',
			'field_name'		=> __( 'Other Options', 'townhub-import' ),
			'enum_values' 		=> array(), //null,
			'tooltip'			=> '',
			'is_html'			=> true,
			'default_text'		=> '',

			// do not auto update
			'ignore_update'		=> true,
		),

		'_cth_listing_type_id'=> array(
			'field_type' 		=> 'text',
			'field_name'		=> __( 'Listing Type ID (Optional) - <a href="https://www.youtube.com/watch?v=Tn7-F630lO4&list=PL8lnIVh9k3Ptfsumpmxf4CH4oIdMl9GLe" target="_blank">What\'s the Listing Type?</a>', 'townhub-import' ),
			'enum_values' 		=> array(), //null,
			'tooltip'			=> '',
			'is_html'			=> true,
			'default_text'		=> '',
		),

		'_cth_plan_id'=> array(
			'field_type' 		=> 'text',
			'field_name'		=> __( 'Listing Plan ID (Optional)', 'townhub-import' ),
			'enum_values' 		=> array(), //null,
			'tooltip'			=> __( 'Membership package ID ( Listing Plans post ), which this listing get field settings from.', 'townhub-import' ),
			'is_html'			=> true,
			'default_text'		=> '',
		),

		'_expire_date'=> array(
			'field_type' 		=> 'text',
			'field_name'		=> __( 'Listing Expire Date (Format: 2020-02-20 16:30:00)', 'townhub-import' ),
			'enum_values' 		=> array(), //null,
			'tooltip'			=> '',
			'is_html'			=> true,
			'default_text'		=> '',
			// do not auto update
			'ignore_update'		=> true,
		),

		
		

	);

	return apply_filters( 'esb_import_fields', $adon_fields, $townhub_import_addon );
}


function listing_coordinates($townhub_import_addon){


	return 
        array(
                'search_by_address' => array(
                        __( 'Search by Address', 'townhub-import' ),
                        $townhub_import_addon->add_field(
                                'google_api_key',
                                __( 'Enter your Google Developers API key to fetch coordinates from google (Required services: Google Maps Geocoding API - And set your Google Application Restrictions to None: http://prntscr.com/kp1xhc ).', 'townhub-import' ),
                                'text',
                                array(),
                                ''
                        ) 
                ), // end Search by Address radio field
                'enter_coordinates' => array(
                        __( 'Or Coordinate values', 'townhub-import' ),
                        $townhub_import_addon->add_field( '_cth_latitude', __( 'Latitude', 'townhub-import' ), 'text' ),
                        $townhub_import_addon->add_field( '_cth_longitude', __( 'Longitude', 'townhub-import' ), 'text' )
                )
        );
}


function townhub_import_addon_header_types($townhub_import_addon){


	return array(
		'bgimage' => 	__( 'Image Background (from Header Images)', 'townhub-import' ),
		'bgvideo' => 	array(
	                        __( 'Video Background', 'townhub-import' ),
	                        $townhub_import_addon->add_field( 'headerbg_youtube', __( 'Youtube Video ID. Ex: Hg5iNVSp2z8', 'townhub-import' ), 'text' ),
	                        $townhub_import_addon->add_field( 'headerbg_vimeo', __( 'Or Vimeo Video ID. Ex: 97871257', 'townhub-import' ), 'text' ),
	                        $townhub_import_addon->add_field( 'headerbg_mp4', __( 'Or Vimeo URL (.MP4)', 'townhub-import' ), 'text' ),
		                ),
		'bgslider' => 	__( 'Slider (from Header Images)', 'townhub-import' ),
		'bgslideshow' => 	__( 'Slideshow (from Header Images)', 'townhub-import' ),
		

	);
}

function townhub_import_addon_socials($prefix = '',$townhub_import_addon){


	$socials = function_exists('townhub_addons_get_socials_list')? townhub_addons_get_socials_list() : array();

	$fields = array();
	foreach ($socials as $val => $lbl) {
		$fields[] = $townhub_import_addon->add_field( $prefix.'_'.$val, $lbl, 'text', null, '' );
    }

	return $fields;
}


function townhub_import_addon_working_hours($townhub_import_addon){


	$fields = array(
		$townhub_import_addon->add_field(
			'timezone', 
			__( 'Timezone String. Default is your site timezone. Ex: America/New_York.<br><a href="http://php.net/manual/en/timezones.php" target="_blank">Available values</a>', 'townhub-import' ), 
			'text', 
			array(), 
			'', 
			true, 
			get_option( 'timezone_string', 'Europe/London' )
		),
		$townhub_import_addon->add_field(
			'wkhours', 
			__( 'Days and hours. Ex: Mon,09:00:00,19:00:00|Tue,09:00:00,19:00:00|Wed,09:00:00,19:00:00|Thu,09:00:00,19:00:00|Fri,09:00:00,19:00:00|Sat,09:00:00,19:00:00|Sun,closeAllDay', 'townhub-import' ), 
			'text', 
			array(), 
			__( 'Format: Mon,OpenTime,CloseTime|Tue,OpenTime1,CloseTime1;OpenTime2,CloseTime2 and so on', 'townhub-import' ), 
			true, 
			''
		)
	);
	// foreach (Esb_Class_Date::week_days() as $dayname => $lbl) {
	// 	$fields[] = $townhub_import_addon->add_field(
	// 			$prefix.'status_'.$dayname, 
	// 			sprintf(__( '%s - Status', 'townhub-import' ), $lbl ), 
	// 			'radio', 
	// 			array(
	// 				'enterHours' => __( 'Enter Hours', 'townhub-import' ),
	// 				'openAllDay' => __( 'Open all day', 'townhub-import' ),
	// 				'closeAllDay' => __( 'Close all day', 'townhub-import' ),
	// 			), 
	// 			'', 
	// 			true, 
	// 			'enterHours'
	// 		);
	// 	$fields[] = $townhub_import_addon->add_field(
	// 			$prefix.'hours_'.$dayname, 
	// 			sprintf(__( '%s - Hours ( Format: open_hour-close_hour and multiple hours are separated by a space.<br>Ex: 8:00-18:00 or 8:00-12:00 13:30-18:00)', 'townhub-import' ), $lbl ), 
	// 			'text', 
	// 			array(), 
	// 			'', 
	// 			true, 
	// 			'8:00-12:00 13:30-18:00'
	// 		);

 //    }

	return $fields;

}


