<?php 
defined( 'ABSPATH' ) || exit; 

// set global options value
if(!isset($cthmobile_options)) 
    $cthmobile_options = get_option( 'cthmobile-options', array() ); 

final class CTHMobile_Addons { 
    private static $_instance;
    private function __construct() {
        $this->define_constants();
        $this->includes();
        $this->init_hooks();
    }

    private function init_hooks() {
        add_action('plugins_loaded', array( $this, 'load_plugin_textdomain' ));
        
        add_action( 'init', array( $this, 'init' ), 0 );

        // add_action( 'rest_api_init', array( $this, 'init_rest' ) );
        // add_action( 'parse_request', array( $this, 'init_rest' ), 15 );

        add_action( 'cth_mobile_add_review_after', array( $this, 'insert_comment' ), 10, 2 );

        add_action( 'azp_elements_init', array( $this, 'registerAZPElements' ) );
    }

    public function load_plugin_textdomain(){
        load_plugin_textdomain( 'townhub-mobile', false, plugin_basename(dirname(CTH_MOBILE_FILE)) . '/languages' );
    }

    public static function getInstance() {
        if ( ! ( self::$_instance instanceof self ) ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    private function __clone() {
    }

    private function __wakeup() {
    }

    private function define_constants() {
        $this->define( 'CTHMB_ABSPATH', plugin_dir_path( CTH_MOBILE_FILE ) );
    }

    private function define( $name, $value ) {
        if ( ! defined( $name ) ) {
            define( $name, $value );
        }
    }

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

    private function includes() {
        require_once CTHMB_ABSPATH . 'includes/core-functions.php';
        if($this->is_request('admin')){
            /* plugin options */
            require_once CTHMB_ABSPATH . 'includes/class-options.php';
            require_once CTHMB_ABSPATH . 'includes/class-admin-scripts.php';
            require_once CTHMB_ABSPATH . 'inc/meta-boxes.php';
        }

        require_once CTHMB_ABSPATH . 'inc/rest-api.php';
        require_once CTHMB_ABSPATH . 'inc/rest-site.php';


        require_once CTHMB_ABSPATH . 'inc/rest-user.php';
        require_once CTHMB_ABSPATH . 'inc/rest-categories.php';
        require_once CTHMB_ABSPATH . 'inc/rest-locations.php';
        require_once CTHMB_ABSPATH . 'inc/rest-booking.php';
        
    }

    public function init() {
        do_action( 'cth_mobile_init_after' );
    }
    // public function init_rest(){
    //     if ( defined('REST_REQUEST') && REST_REQUEST ){
    //         require_once CTHMB_ABSPATH . 'inc/rest-api.php';
    //         require_once CTHMB_ABSPATH . 'inc/rest-site.php';
    //         require_once CTHMB_ABSPATH . 'inc/rest-user.php';
    //         require_once CTHMB_ABSPATH . 'inc/rest-categories.php';
    //         require_once CTHMB_ABSPATH . 'inc/rest-locations.php';
    //         require_once CTHMB_ABSPATH . 'inc/rest-booking.php';
    //     }
    // }
    public function registerAZPElements(){
        require_once CTHMB_ABSPATH.'azp_elements/listings.php';
        require_once CTHMB_ABSPATH.'azp_elements/cats.php';
        require_once CTHMB_ABSPATH.'azp_elements/locs.php';
        require_once CTHMB_ABSPATH.'azp_elements/banner.php';
    }
    public function insert_comment($id, $comment){
        if( $comment && $comment->comment_approved == '1' ){
            townhub_addons_comment_unapproved_to_approved($comment);
        }
    }
}