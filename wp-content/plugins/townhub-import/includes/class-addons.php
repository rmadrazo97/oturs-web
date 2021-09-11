<?php 


defined( 'ABSPATH' ) || exit; 

final class TownHub_Import_Addons { 
    private static $_instance;

    private $plugin_url;
    private $plugin_path;

    public $import = null;

    public $fields = array();

    private function __construct() {
        $this->define_constants();
        $this->includes();
        $this->init_hooks();
    }

    private function define_constants() {
        $this->plugin_url = plugin_dir_url(ESB_IMPORT_FILE);
        $this->plugin_path = plugin_dir_path(ESB_IMPORT_FILE);
    }

    private function includes() {

    	if($this->is_request('admin')){
    		require_once $this->plugin_path . 'wp-all-import-rapid-addon/rapid-addon.php';
    		require_once $this->plugin_path . 'includes/functions.php';
    		require_once $this->plugin_path . 'includes/fields.php';
    	}

    }

    private function init_hooks() {
        // register_activation_hook( ESB_PLUGIN_FILE, array( $this, 'install' ) );
        add_action('plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
        add_action('admin_init', array( $this, 'register_import' ) );
        add_action('pmxi_saved_post', array( $this, 'update_imported_post' ) );

        // register_activation_hook( ESB_PLUGIN_FILE, array( 'Esb_Class_Install', 'install') );
        // register_deactivation_hook( ESB_PLUGIN_FILE, array( 'Esb_Class_Install', 'uninstall') );
    }

    public function load_plugin_textdomain(){
        load_plugin_textdomain( 'townhub-import', false, plugin_basename(dirname(ESB_IMPORT_FILE)) . '/languages' );
    }

    public static function getInstance() {
        if ( ! ( self::$_instance instanceof self ) ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    private function __clone() {}

    private function __wakeup() {}

    public function is_request( $type ) {
        switch ( $type ) {
            case 'admin':
                return is_admin();
            case 'ajax':
                return defined( 'DOING_AJAX' );
            case 'frontend':
                return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
        }
    }

    // activation hook
    public function install(){

    }

    // start real plugin
    public function register_import(){
    	if( !is_plugin_active('townhub-add-ons/townhub-add-ons.php') ){
			return;
		}

		// if( !$this->is_request('admin') ){
		// 	return;
		// }

		$this->import = new RapidAddon('TownHub Import', 'townhub-import');

		// var_dump($this->import);

		$this->fields = townhub_import_addondo_fields( $this->import );

		foreach ( $this->fields as $slug => $foptions ) {
			if(isset($foptions['is_options']) && $foptions['is_options']){
				$this->import->add_options(
				        null,
				        $foptions['field_name'], 
				        $foptions['enum_values']
				);
			}elseif(isset($foptions['is_title']) && $foptions['is_title']){
				$this->import->add_title( $foptions['field_name'], $foptions['tooltip'] );
			}elseif(isset($foptions['is_text']) && $foptions['is_text']){
				$this->import->add_text( $foptions['field_name'] );
			}elseif($foptions['field_type'] == 'images'){
				$this->import->import_images( $slug, $foptions['field_name'] );
			}else{
				$this->import->add_field(
					$slug, 
					$foptions['field_name'], 
					$foptions['field_type'], 
					$foptions['enum_values'], 
					$foptions['tooltip'], 
					$foptions['is_html'], 
					$foptions['default_text']
				);
			}
				
		}

		$this->import->set_import_function( array( $this, 'import_function' ) );

		// http://www.wpallimport.com/documentation/addon-dev/best-practices/
		$this->import->run(
			array(
				"themes" => array("TownHub")
			)
			// ,
			// array(
			// 	"post_types" => array( "listing" ),
			// )
		);

    }

    public function import_function( $post_id, $data, $import_options, $article ){

    	foreach ( $this->fields as $fname => $foptions ) {
		    if ( empty( $article['ID'] ) || $this->import->can_update_meta( $fname, $import_options ) ) {
		    	
		    	// igmore fields
		    	$is_ignored = isset($foptions['ignore_update']) && true === $foptions['ignore_update'] ? true : false;
		    	if( false === $is_ignored ){
		    		update_post_meta( $post_id, $fname, $data[ $fname ] ); 
		    	}
		    	// don't update socials
		    	// if( strpos($fname, '_cth_socials_') === false || !in_array($fname, array('_cth_contact_infos_no_lat_lng','listing_location_lat_lng','_cth_listing_thumbnail','location_opts_title','price_opts_title','media_opts_title','hour_opts_title','social_opts_title','_cth_working_hours','other_opts_title','_cth_listing_header_imgs','content_widget_opts_title','_cth_listing_gallery_imgs','_cth_listing_slider_imgs','content_widget_slider_text','content_widget_gallery_text','event_opts_title','listing_event_date','listing_event_date_end'))){
		    	// 	update_post_meta( $post_id, $fname, $data[ $fname ] );  
		    	// }

		    	// listing_cat
		    	if($fname == 'esb_import_ado_cats'){
		      		if( !empty($data[ $fname ]) ) wp_set_post_terms( $post_id, $data[ $fname ], 'listing_cat' ); 
		      	}
		    	// listing_tag
		    	if($fname == 'esb_import_ado_tags'){
		      		if( !empty($data[ $fname ]) ) wp_set_post_terms( $post_id, $data[ $fname ], 'listing_tag' ); 
		      	}
		    	// listing_tag
		    	if($fname == 'esb_import_ado_features'){
		      		if( !empty($data[ $fname ]) ) wp_set_post_terms( $post_id, $data[ $fname ], 'listing_feature' ); 
		      	}


		      	

		    	
		    	// $this->import->log( 'Field name: ' . $fname );  
		    	// $this->import->log( 'Field value: ' . $data[ $fname ] );  

		      	// if($fname == '_cth_contact_infos_no_lat_lng' && $data[ $fname ] == 'yes' ){
	      		// 	$lat_lng = townhub_import_addondo_geocode($data[ '_cth_address' ]);
	      		// 	if($lat_lng){
	      		// 		update_post_meta( $post_id, '_cth_latitude', $lat_lng['lat'] ); 
	      		// 		update_post_meta( $post_id, '_cth_longitude', $lat_lng['lng'] ); 
	      		// 	}
		      	// }

		      	if($fname == 'listing_location_lat_lng'){
		      		if($data[$fname] == 'search_by_address' && null != $data['google_api_key'] ){
		      			$lat_lng = townhub_import_addondo_geocode($data[ '_cth_address' ], $data['google_api_key'] );
		      			if($lat_lng){
		      				update_post_meta( $post_id, '_cth_latitude', $lat_lng['lat'] ); 
		      				update_post_meta( $post_id, '_cth_longitude', $lat_lng['lng'] ); 
		      			}
		      		}

		      		if( $data[$fname] == 'enter_coordinates' ) {
		      			update_post_meta( $post_id, '_cth_latitude', $data['_cth_latitude'] ); 
	      				update_post_meta( $post_id, '_cth_longitude', $data['_cth_longitude'] ); 
		      		}
		      	}

		      	// price based on price from
		    	if( $fname == '_cth_price_from' ){
		    		update_post_meta( $post_id, '_price', $data[ $fname ] ); 
		    	}
		    	if( $fname == '_cth_verified' ){
		    		if( !empty($data[ $fname ]) ) update_post_meta( $post_id, $fname, 1 ); 
		    	}

		      	// update for _price
		      	if($fname == '_cth_price_from'){
		      		update_post_meta( $post_id, '_price', $data[ $fname ] ); 
		      	}
		      	// update listing thumbnail
		      	if($fname == '_thumbnail' && isset($data[ $fname ]['attachment_id']) && $data[ $fname ]['attachment_id'] != ''){
		      		set_post_thumbnail( $post_id, $data[ $fname ]['attachment_id'] );
		      	}
		      	if($fname == '_llogo' && isset($data[ $fname ]['attachment_id']) && $data[ $fname ]['attachment_id'] != ''){
		      		update_post_meta( $post_id, '_cth_llogo', $data[ $fname ]['attachment_id'] ); 
		      	}
		      	// update header video 
		      	// if($fname == '_cth_headermedia'){ // not exist for import images field
		      	// 	$header_value = array(
		      	// 		'type'		=> $data[ $fname ],
		      	// 		'photos'	=> '',
		      	// 	);

		      	// 	if( isset($data[ 'headerbg_youtube']) && $data[ 'headerbg_youtube'] != '' ){
		      	// 		$header_value['youtube'] = $data[ 'headerbg_youtube'];
		      	// 	}
		      	// 	if( isset($data[ 'headerbg_vimeo']) && $data[ 'headerbg_vimeo'] != '' ){
		      	// 		$header_value['vimeo'] = $data[ 'headerbg_vimeo'];
		      	// 	}
		      	// 	if( isset($data[ 'headerbg_mp4']) && $data[ 'headerbg_mp4'] != '' ){
		      	// 		$header_value['mp4'] = $data[ 'headerbg_mp4'];
		      	// 	}

		      	// 	$hdphotos = get_post_meta( $post_id, '_cth_headerimgs', true );
		      	// 	if( is_array($hdphotos) && !empty($hdphotos) ){
		      	// 		$header_value['photos'] = implode(",", $hdphotos);
		      	// 	}

		      	// 	update_post_meta( $post_id, $fname, $header_value ); 
		      	// }

		      	if($fname == '_cth_header_type'){ // not exist for import images field
		      		$header_value = array(
		      			'type'		=> $data[ $fname ],
		      			'photos'	=> '',
		      		);

		      		if( isset($data[ 'headerbg_youtube']) && $data[ 'headerbg_youtube'] != '' ){
		      			$header_value['youtube'] = $data[ 'headerbg_youtube'];
		      		}
		      		if( isset($data[ 'headerbg_vimeo']) && $data[ 'headerbg_vimeo'] != '' ){
		      			$header_value['vimeo'] = $data[ 'headerbg_vimeo'];
		      		}
		      		if( isset($data[ 'headerbg_mp4']) && $data[ 'headerbg_mp4'] != '' ){
		      			$header_value['mp4'] = $data[ 'headerbg_mp4'];
		      		}

		      		$hdphotos = get_post_meta( $post_id, '_cth_headerimgs', true );
		      		if( is_array($hdphotos) && !empty($hdphotos) ){
		      			$header_value['photos'] = implode(",", $hdphotos);
		      		}

		      		update_post_meta( $post_id, '_cth_headermedia', $header_value ); 
		      	}

		      	

		      	if($fname == '_cth_promo_video'){
		      		if( !empty($data[ $fname ]) ) update_post_meta( $post_id, $fname, array( 'url'=> $data[ $fname ] ) ); 
		      	}

		      	if($fname == '_event_date'){
		      		if( !empty($data[ $fname ]) ){
		      			$startDate = $endDate = Esb_Class_Date::format($data[ $fname ], 'Y-m-d');
		      			$startTime = $endTime = Esb_Class_Date::format($data[ $fname ], 'H:i:s');
		      			if( !empty($data[ '_event_date_end' ]) ){
		      				$endDate = Esb_Class_Date::format($data[ '_event_date_end' ], 'Y-m-d');
		      				$endTime = Esb_Class_Date::format($data[ '_event_date_end' ], 'H:i:s');
		      			}

		      			update_post_meta( $post_id, '_cth_eventdate', implode("|", array($startDate,$startTime,$endDate,$endTime) ) ); 
		      			update_post_meta( $post_id, '_cth_eventdate_start', $startDate ); 
		      			update_post_meta( $post_id, '_cth_eventdate_end', $endDate ); 
		      			
		      		}else{
		      			update_post_meta( $post_id, '_cth_eventdate_start', date_i18n( 'Y-m-d' ) );
		      			update_post_meta( $post_id, '_cth_eventdate_end', '' ); 
		      		}
		      	}

		      	if($fname == '_expire_date'){
		      		if( !empty($data[ $fname ]) ){
		      			update_post_meta( $post_id, '_cth_expire_date', Esb_Class_Date::format($data[ $fname ], 'Y-m-d H:i:s') );
		      		} 
		      	}
		      	

		      	

		      	// update working hours
		      	if($fname == '_cth_working_hours_meta'){
		      		$wkhours_value = array(
		      			'timezone'		=> $data[ 'timezone' ],
		      		);
		      		$dhours = trim( $data['wkhours'] );
		      		$dhours = explode('|', $dhours);
		      		$dhours = array_filter($dhours);
		      		if( !empty($dhours) ){
		      			foreach ($dhours as $dval) {
		      				// multiple open-close
		      				$dvhours = explode(";", $dval);
		      				// extract day
		      				if( !empty($dvhours[0]) ){
		      					$d_time_1 	= explode(",", $dvhours[0]);
		      					$dname 		= substr($d_time_1[0], 0, 3);
		      					$dvalues 	= array();
		      					// status
		      					if( !empty($d_time_1[1])  ){
		      						if( $d_time_1[1] == 'openAllDay' || $d_time_1[1] == 'closeAllDay' ){
		      							$wkhours_value[$dname] = array(
		      								'static' 	=> $d_time_1[1],
		      								'hours'		=> $dvalues,
		      							);
		      						}else{
		      							if(!empty($d_time_1[2])){
		      								$dvalues[] = array(
		      									'open'      => $d_time_1[1],
		                        				'close'     => $d_time_1[2],
		      								);
		      							}
		      							// for time two
		      							if( !empty($dvhours[1]) ){
		      								$d_time_2 	= explode(",", $dvhours[1]);
		      								$d_time_2 = array_filter($d_time_2);
		      								if( count($d_time_2) === 2 ){
		      									$dvalues[] = array(
			      									'open'      => $d_time_2[0],
			                        				'close'     => $d_time_2[1],
			      								);
		      								}
		      							}
		      							// for time three
		      							if( !empty($dvhours[2]) ){
		      								$d_time_3 	= explode(",", $dvhours[2]);
		      								$d_time_3 = array_filter($d_time_3);
		      								if( count($d_time_3) === 2 ){
		      									$dvalues[] = array(
			      									'open'      => $d_time_3[0],
			                        				'close'     => $d_time_3[1],
			      								);
		      								}
		      							}
		      							$wkhours_value[$dname] = array(
		      								'static' 	=> 'enterHours',
		      								'hours'		=> $dvalues,
		      							);

		      						}
		      					}
		      					// check has times
		      				}
		      				// end get first open-close
		      			}
		      			// end loop days
		      		}
		      		// end check have days
		      		
		      		update_post_meta( $post_id, $fname, $wkhours_value ); 

		      		Esb_Class_Listing_CPT::update_working_hours( $post_id, $wkhours_value );

		      	}

		      	// listing dates
		      	if($fname == 'listing_dates'){
		      		if( !empty($data[$fname]) ){
		      			$ldates_val = explode(";", $data[$fname]);
		      			$ldates_val = array_filter($ldates_val);
		      			if( !empty($ldates_val) ){
		      				$ldates_val = array_map('Esb_Class_Date::format', $ldates_val);
			      			update_post_meta( $post_id, '_cth_listing_dates', implode(";", $ldates_val) ); 
		      			}
		      		}
		      	}



		      	// update socials
		      	if($fname == '_cth_socials'){
		      		$socials = function_exists('townhub_addons_get_socials_list')? townhub_addons_get_socials_list() : array();		      		
		      		$socials_val = array();
					foreach ($socials as $val => $lbl) {
						if( isset($data[ '_cth_socials_'.$val ]) && $data[ '_cth_socials_'.$val ] != '' ) $socials_val[] = array('name'=>$val,'url'=>$data[ '_cth_socials_'.$val ]);
				    }
				    if(!empty($socials_val)) update_post_meta( $post_id, $fname, $socials_val ); 
		      	}
		      	// end update socials
		      	if($fname == '_cth_resmenus'){
		      		$fVal = maybe_unserialize( $data[$fname] );
		      		if( !empty($fVal) ) update_post_meta( $post_id, $fname, $fVal ); 
		      	}
		      	

		    }  
	  	}

	  	// end loop fields 

	  	// update featured data
	  	update_post_meta( $post_id, '_cth_featured', '0' );

    }
    public function update_imported_post($pid){

    	$headermedia = get_post_meta( $pid, '_cth_headermedia', true );
    	if( empty($headermedia) ){
    		$headermedia = array(
    			'type' => 'bgimage'
    		);
    	}
    	$hdphotos = get_post_meta( $pid, '_cth_headerimgs', true );
  		if( is_array($hdphotos) && !empty($hdphotos) ){
  			$headermedia['photos'] = implode(",", $hdphotos);
  		}
		update_post_meta( $pid, '_cth_headermedia', $headermedia ); 
    }
}

