<?php 

class TownHub_Site_Route extends TownHub_Custom_Route {
    private static $_instance;
    public static function getInstance() {
        if ( ! ( self::$_instance instanceof self ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    public function register_routes() {
        register_rest_route( 
            $this->namespace, 
            '/' . $this->rest_base . '/currency', 
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_currency' ),
                    'permission_callback' => array( $this, 'get_permissions_check' ),
                    'args'                => array(),
                ),
            ) 
        );
        register_rest_route( 
            $this->namespace, 
            '/' . $this->rest_base . '/site/strings', 
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_strings' ),
                    'permission_callback' => array( $this, 'get_permissions_check' ),
                    'args'                => array(),
                ),
            ) 
        );
        // register_rest_route( 
        //     $this->namespace, 
        //     '/' . $this->rest_base . '/languages', 
        //     array(
        //         array(
        //             'methods'             => WP_REST_Server::READABLE,
        //             'callback'            => array( $this, 'get_app_languages' ),
        //             'permission_callback' => array( $this, 'get_permissions_check' ),
        //             'args'                => array(),
        //         ),
        //     ) 
        // );
        // register_rest_route( 
        //     $this->namespace, 
        //     '/' . $this->rest_base . '/currencies', 
        //     array(
        //         array(
        //             'methods'             => WP_REST_Server::READABLE,
        //             'callback'            => array( $this, 'get_app_currencies' ),
        //             'permission_callback' => array( $this, 'get_permissions_check' ),
        //             'args'                => array(),
        //         ),
        //     ) 
        // );
        
    }

    public function get_currency($request){
        $currency = $request->get_param('currency', 'null');
        if( $currency != 'null' ){
            $data = townhub_addons_get_currency_attrs($currency);
        }else{
            $data = townhub_addons_get_currency_attrs();
        }
        $data['crrsdsd'] = $currency;
        $data['headers'] = $request->get_headers();
        $data['Authorization'] = $request->get_header('Authorization');
        $data['lang'] = $request->get_param('lang');
        $data['_settingKey'] = $this->api_key;
        return rest_ensure_response( $data );
    }

    public function get_strings($request){
        $data = array(
            'home' =>           array(
                'search'            => esc_html_x( 'Search', 'Apps text', 'townhub-mobile' ),
            ),
            // button tabs
            'explore'                       => esc_html_x( 'Explore', 'Apps text', 'townhub-mobile' ),
            'myorder'                       => esc_html_x( 'My Order', 'Apps text', 'townhub-mobile' ),
            'favourite'                     => esc_html_x( 'Favourite', 'Apps text', 'townhub-mobile' ),
            'profile'                       => esc_html_x( 'Profile', 'Apps text', 'townhub-mobile' ),

            // price per
            'pernight'                      => esc_html_x( 'per night', 'Apps text', 'townhub-mobile' ),
            'perday'                        => esc_html_x( 'per day', 'Apps text', 'townhub-mobile' ),
            'perperson'                     => esc_html_x( 'per person', 'Apps text', 'townhub-mobile' ),

            // for booking price per
            'per_night' => array(
                // single listing bottom
                'single'                        => esc_html_x( '%s/NIGHT', 'Apps text - Per night price based', 'townhub-mobile' ),
                // for slots on available screen
                'slot_price'                    => esc_html_x( '%s per person.', 'Apps text - Per night price based', 'townhub-mobile' ),
                'slot_available'                => esc_html_x( ' %s slots available', 'Apps text - Per night price based', 'townhub-mobile' ),
                // for slot available on booking screen
                'bk_slot_avai'                  => esc_html_x( '%s slots available, grab one.', 'Apps text - Per night price based', 'townhub-mobile' ),
                // for listing detail on booking screen
                'bk_listing'                    => esc_html_x( '%s per night', 'Apps text - Per night price based', 'townhub-mobile' ),
                // for free booking on booking screen
                'bk_free'                       => esc_html_x( 'Free booking', 'Apps text - Per night price based', 'townhub-mobile' ),
                // for room adults on booking screen
                'bk_room_adults'                => esc_html_x( 'Adults: %s', 'Apps text - Per night price based', 'townhub-mobile' ),
                // for room children on booking screen
                'bk_room_children'              => esc_html_x( 'Children: %s', 'Apps text - Per night price based', 'townhub-mobile' ),
                // for room available on booking screen
                'bk_room_avai'                  => esc_html_x( '%s rooms available for booking.', 'Apps text - Per night price based', 'townhub-mobile' ),
                // for checkin on booking screen
                'bk_checkin'                    => esc_html_x( 'Checkin:', 'Apps text - Per night price based', 'townhub-mobile' ),
                // for checkout on booking screen
                'bk_checkout'                   => esc_html_x( 'Checkout:', 'Apps text - Per night price based', 'townhub-mobile' ),
                // for days/nights on booking screen
                'bk_days_nights'                => esc_html_x( 'x%s nights', 'Apps text - Per night price based', 'townhub-mobile' ),
                // for subtotal on booking screen
                'bk_subtotal'                   => esc_html_x( 'Subtotal', 'Apps text - Per night price based', 'townhub-mobile' ),
                'bk_tax'                        => esc_html_x( 'Tax', 'Apps text - Per night price based', 'townhub-mobile' ),
                'bk_fees'                       => esc_html_x( 'Additional Fees', 'Apps text - Per night price based', 'townhub-mobile' ),
                'bk_total'                      => esc_html_x( 'Total', 'Apps text - Per night price based', 'townhub-mobile' ),
                // for booking buttons
                'btn_book_now'                  => esc_html_x( 'Show dates', 'Apps text - Per night price based', 'townhub-mobile' ),
                'btn_continue'                  => esc_html_x( 'Continue', 'Apps text - Per night price based', 'townhub-mobile' ),
                'btn_bk_book_now'               => esc_html_x( 'Book now', 'Apps text - Per night price based', 'townhub-mobile' ),
            ),
            'per_person' => array(
                // single listing bottom
                'single'                        => esc_html_x( 'From %s/person', 'Apps text - Per person price based', 'townhub-mobile' ),
                // for slots on available screen
                'slot_price'                    => esc_html_x( '%s per person.', 'Apps text - Per person price based', 'townhub-mobile' ),
                'slot_available'                => esc_html_x( ' %s slots available', 'Apps text - Per person price based', 'townhub-mobile' ),
                // for slot available on booking screen
                'bk_slot_avai'                  => esc_html_x( '%s slots available, grab one.', 'Apps text - Per person price based', 'townhub-mobile' ),
                // for listing detail on booking screen
                'bk_listing'                    => esc_html_x( '%s per person', 'Apps text - Per person price based', 'townhub-mobile' ),
                // for free booking on booking screen
                'bk_free'                       => esc_html_x( 'Free booking', 'Apps text - Per person price based', 'townhub-mobile' ),
                // for room adults on booking screen
                'bk_room_adults'                => esc_html_x( 'Adults: %s', 'Apps text - Per person price based', 'townhub-mobile' ),
                // for room children on booking screen
                'bk_room_children'              => esc_html_x( 'Children: %s', 'Apps text - Per person price based', 'townhub-mobile' ),
                // for room available on booking screen
                'bk_room_avai'                  => esc_html_x( '%s rooms available for booking.', 'Apps text - Per person price based', 'townhub-mobile' ),
                // for checkin on booking screen
                'bk_checkin'                    => esc_html_x( 'Checkin:', 'Apps text - Per person price based', 'townhub-mobile' ),
                // for checkout on booking screen
                'bk_checkout'                   => esc_html_x( 'Checkout:', 'Apps text - Per person price based', 'townhub-mobile' ),
                // for days/nights on booking screen
                'bk_days_nights'                => esc_html_x( 'x%s nights', 'Apps text - Per person price based', 'townhub-mobile' ),
                // for subtotal on booking screen
                'bk_subtotal'                   => esc_html_x( 'Subtotal', 'Apps text - Per person price based', 'townhub-mobile' ),
                'bk_tax'                        => esc_html_x( 'Tax', 'Apps text - Per person price based', 'townhub-mobile' ),
                'bk_fees'                       => esc_html_x( 'Additional Fees', 'Apps text - Per person price based', 'townhub-mobile' ),
                'bk_total'                      => esc_html_x( 'Total', 'Apps text - Per person price based', 'townhub-mobile' ),
                // for booking buttons
                'btn_book_now'                  => esc_html_x( 'Check available', 'Apps text - Per person price based', 'townhub-mobile' ),
                'btn_continue'                  => esc_html_x( 'Continue', 'Apps text - Per person price based', 'townhub-mobile' ),
                'btn_bk_book_now'               => esc_html_x( 'Book now', 'Apps text - Per person price based', 'townhub-mobile' ),
            ),
        );

        $data['headers'] = $request->get_headers();
        // $data['Authorization'] = $request->get_header('Authorization');
        $data['lang'] = $request->get_param('lang');
        // $data['_settingKey'] = $this->api_key;

        return rest_ensure_response( $data );
    }

    // public function get_app_languages($request){
    //     $languages = $this->get_languages();
    //     if( !is_array($languages) || empty($languages) ) 
    //         $languages = array();
    //     return rest_ensure_response($languages);
    // }
    // public function get_app_currencies($request){
    //     $currencies = $this->get_currencies();
    //     if( !is_array($currencies) || empty($currencies) ) 
    //         $currencies = array();
    //     return rest_ensure_response($currencies);
    // }
}


add_action( 'rest_api_init', function () {
    TownHub_Site_Route::getInstance();
} );
