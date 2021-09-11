<?php 
/* import_addon_php */


// header images action
// this action callback is automatically call by WP ALL IMPORT for pmxi__cth_listing_header_imgs action with pmxi_ prefix
function esb_import_ado_header_imgs($post_id,$attachment_id,$attachment_path,$action_type){
	
	// $action_type = 'update_images'
	$meta_field = '_cth_headerimgs';

	$meta_val = get_post_meta( $post_id, $meta_field, true );
	if(is_array($meta_val))
		$meta_val[] = $attachment_id;
	else
		$meta_val = array($attachment_id);

	update_post_meta( $post_id, $meta_field, $meta_val );

}
// listing slider images
// callback for pmxi_esb_import_ado_images
function esb_import_ado_images($post_id,$attachment_id,$attachment_path,$action_type){
	
	// $action_type = 'update_images'
	$meta_field = '_cth_images';

	$meta_val = get_post_meta( $post_id, $meta_field, true );
	if( is_array($meta_val) ){
		$meta_val[] = $attachment_id;
		$meta_val = implode(",", $meta_val);
	}else{
		$meta_val .= ','.$attachment_id;
	}

	$meta_val = trim($meta_val, " ,");

	update_post_meta( $post_id, $meta_field, $meta_val );
}
// listing gallery images




function townhub_import_addondo_geocode($address = '', $api_key = ''){
	

	$address   = urlencode($address);
	if(townhub_addons_get_option('map_provider') == 'googlemap'){
		$url       = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key={$api_key}";
		
	}else{
		$url       = "https://nominatim.openstreetmap.org/?format=json&addressdetails=1&q={$address}&format=json&limit=1";
	}
	
	$response = wp_remote_get( $url );
	if ( is_array( $response ) ) {
	    $body = json_decode($response['body'],true); // use the content

	    if(townhub_addons_get_option('map_provider') == 'googlemap'){

	    	if ($body['status'] == 'OK') {
		        // get the important data
		        $lati  = $body['results'][0]['geometry']['location']['lat'];
		        $longi = $body['results'][0]['geometry']['location']['lng'];
		        return array(
		        	'lat' => $lati,
		        	'lng' => $longi,
		        );
		    }

		    return false;

	    	
	    	
	    }else{
	    	
	    	if(!empty($body) && isset($body[0])){
	    		return array(
		        	'lat' => $body[0]['lat'],
		        	'lng' => $body[0]['lon'],
		        );
	    	}

	    	return false;
	    	
	    }
	}

	return false;

}

// function esb_import_ado_logo_img($post_id,$attachment_id,$attachment_path,$action_type){
// 	$meta_field = '_cth_llogo';
// 	update_post_meta( $post_id, $meta_field, $attachment_id );
// }
