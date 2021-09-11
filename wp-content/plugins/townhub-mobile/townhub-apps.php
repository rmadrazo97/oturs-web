<?php
/*
Plugin Name: TownHub Mobile App
Plugin URI: https://townhub.cththemes.com
Description: A custom plugin for TownHub - Directory & Listing WordPress Theme
Version: 1.5.5
Author: CTHthemes
Author URI: http://themeforest.net/user/cththemes
Text Domain: townhub-mobile
Domain Path: /languages/
Copyright: ( C ) 2014 - 2020 cththemes.com . All rights reserved.
License: GNU General Public License version 3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

if ( ! defined('ABSPATH') ) {
    die('Please do not load this file directly!');
}

if ( ! defined( 'CTH_MOBILE_FILE' ) ) {
    define( 'CTH_MOBILE_FILE', __FILE__ );
}
if ( ! class_exists( 'CTHMobile_Addons' ) ) {
    include_once dirname( __FILE__ ) . '/includes/class-addons.php';
}

function CTHMB_ADO() {
    return CTHMobile_Addons::getInstance();
}

CTHMB_ADO();




/**
 * Log REST API errors
 *
 * @param WP_REST_Response $result  Result that will be sent to the client.
 * @param WP_REST_Server   $server  The API server instance.
 * @param WP_REST_Request  $request The request used to generate the response.
 */
// function log_rest_api_errors( $result, $server, $request ) {
// 	if ( $result->is_error() ) {
// 		error_log( sprintf(
// 			"REST request: %s: %s",
// 			$request->get_route(),
// 			print_r( $request->get_params(), true )
// 		) );

// 		error_log( sprintf(
// 			"REST request header: %s: %s",
// 			$request->get_route(),
// 			print_r( $request->get_headers(), true )
// 		) );

// 		error_log( sprintf(
// 			"REST result: %s: %s",
// 			$result->get_matched_route(),
// 			print_r( $result->get_data(), true )
// 		) );
// 	}

// 	return $result;
// }
// add_filter( 'rest_post_dispatch', 'log_rest_api_errors', 10, 3 );
