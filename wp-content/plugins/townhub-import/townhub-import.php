<?php
/*
Plugin Name: TownHub Import
Plugin URI: https://townhub.cththemes.com
Description: A custom plugin for TownHub - Directory & Listing WordPress Theme
Version: 1.5.6
Author: CTHthemes
Author URI: http://themeforest.net/user/cththemes
Text Domain: townhub-import
Domain Path: /languages/
Copyright: ( C ) 2014 - 2020 cththemes.com . All rights reserved.
License: GNU General Public License version 3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

if ( ! defined('ABSPATH') ) {
    die('Please do not load this file directly!');
}

if ( ! defined( 'ESB_IMPORT_FILE' ) ) {
    define( 'ESB_IMPORT_FILE', __FILE__ );
}

if ( ! class_exists( 'TownHub_Import_Addons' ) ) {
    include_once dirname( __FILE__ ) . '/includes/class-addons.php';
}

function ESB_IMPORT() {
    return TownHub_Import_Addons::getInstance();
}

ESB_IMPORT();

