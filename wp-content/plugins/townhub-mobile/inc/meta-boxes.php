<?php 

// add_filter( 'cth_cpt_listing_type_meta_boxes', 'townhub_mobile_app_listing_type_meta_boxes', 10, 1 );

function townhub_mobile_app_listing_type_meta_boxes($meta_boxes){
    
    $meta_boxes['mobile_app'] = array(
        'title'         => __('Mobile App', 'townhub-mobile'),
        'context'       => 'normal', // normal - side - advanced
        'priority'      => 'high', // default - high - low
        'callback_args' => array(),
        'callback_func' => 'townhub_mobile_app_listing_type_meta_boxes_mobile_app',

    );

    return $meta_boxes;
}

class CTH_Mobile_Class_Meta_Boxes {
    private static $_instance;
    private function __construct() {
        $this->init_metas();
    }
    public static function getInstance() {
        if ( ! ( self::$_instance instanceof self ) ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
    protected function init_metas(){
        add_action( 'init', array($this, 'init_cb') );
        add_action( 'add_meta_boxes_page', array($this, 'page_metas') );
        add_action( 'save_post_page', array($this, 'save_page_metas') );
        add_action( 'add_meta_boxes_listing_type', array($this, 'cpt_listing_type_metas') );
        add_action( 'cth_cpt_listing_type_save_meta_boxes', array($this, 'cpt_listing_type_save_metas') );


    }
    public function init_cb(){
        register_post_meta( 'page', 'cthazp_layout', array(
            'show_in_rest' => true,
            'single' => true,
            'type' => 'string',
        ) );
    }
    public function page_metas(){
        global $post;
        if( isset($post->ID) && cth_mobile_get_wpml_option('explore_page') == $post->ID ){
            add_meta_box(
                'page_apps',
                __('Mobile App', 'townhub-mobile'),
                array($this, 'page_metas_callback'),
                'page',
                'normal',
                'low',
                array()
            );
        }
    }
    public function cpt_listing_type_metas(){
        add_meta_box(
            'cpt_listing_type_apps',
            __('Mobile App', 'townhub-mobile'),
            array($this, 'cpt_listing_type_metas_callback'),
            'listing_type',
            'normal',
            'low',
            array()
        );
    }
    public function page_metas_callback($post, $args){
        wp_nonce_field( 'cth-azp-page', '_azpnonce' );
        ?>
        <h3><?php esc_html_e( 'Mobile Apps', 'townhub-mobile' ); ?></h3>
        <div id="cthMobileApp"></div>
        <textarea id="listing_type_azp_layout" name="cthazp_layout" style="display: none;"><?php echo get_post_meta($post->ID, '_cth_cthazp_layout', true); ?></textarea>
        <?php
    }

    public function save_page_metas($post_ID){
        // error_log(json_encode($_POST));
        if( isset($_POST["_azpnonce"]) && wp_verify_nonce( $_POST['_azpnonce'], 'cth-azp-page' ) ) {
            if( isset($_POST["cthazp_layout"]) ) update_post_meta( $post_ID, '_cth_cthazp_layout', $_POST["cthazp_layout"]);
        }
    }
    public function cpt_listing_type_metas_callback($post, $args){
        $booking_type = get_post_meta( $post->ID, '_apps_booking_type', true );
        $months_available = get_post_meta( $post->ID, '_apps_months_available', true );
        $allow_free_booking = get_post_meta( $post->ID, '_apps_allow_free_booking', true );
        $free_hide_services = get_post_meta( $post->ID, '_apps_free_hide_services', true );
        $disable_booking = get_post_meta( $post->ID, '_apps_disable_booking', true );

        ?>
        <h3><?php esc_html_e( 'Mobile Apps', 'townhub-mobile' ); ?></h3>
        <table class="form-table booking_type">
            <tbody>

                <tr class="hoz">
                    <th class="w20 align-left"><?php _e( 'Booking Type', 'townhub-mobile' ); ?></th>
                    <td>
                        <select name="_apps_booking_type">
                            <option value="simple" <?php selected( $booking_type, 'simple', true ); ?>><?php _e('Simple - Checkin', 'townhub-mobile') ?></option>
                            <option value="hotel_rooms" <?php selected( $booking_type, 'hotel_rooms', true ); ?>><?php _e('Hotel Rooms - Check In-Out', 'townhub-mobile') ?></option>
                            <option value="event" <?php selected( $booking_type, 'event', true ); ?>><?php _e('Event - Checkin/Tickets', 'townhub-mobile') ?></option>
                            <option value="tour" <?php selected( $booking_type, 'tour', true ); ?>><?php _e('Tour - Checkin/Tickets', 'townhub-mobile') ?></option>
                        </select>
                        
                        <p><?php _e( 'Allow user select checkin/checkout dates on checkin date only on <strong>SELECT DATES</strong> screen.', 'townhub-mobile' ); ?> <a href="https://prnt.sc/rxlw91" target="_blank">https://prnt.sc/rxlw91</a></p>

                    </td>
                </tr>

                <tr class="hoz">
                    <th class="w20 align-left"><?php _e( 'Disable booking', 'townhub-mobile' ); ?></th>
                    <td>
                        <input type="checkbox" class="input-text" name="_apps_disable_booking" value="yes" <?php checked( $disable_booking, 'yes', true ); ?>>
                        <p><?php _e( 'Check if you don\'t want users booking listings of this type', 'townhub-mobile' ); ?></p>
                    </td>
                </tr>
                
            </tbody>
        </table>
        <table class="form-table months_available">
            <tbody>

                <tr class="hoz">
                    <th class="w20 align-left"><?php _e( 'Max available months for booking', 'townhub-mobile' ); ?></th>
                    <td>
                        <input type="number" class="input-text" name="_apps_months_available" value="<?php echo esc_attr( $months_available ); ?>" min="1" max="12">

                        <p><?php _e( 'Number of months showing on <strong>SELECT DATES</strong> screen.', 'townhub-mobile' ); ?> <a href="https://prnt.sc/rxlvm9" target="_blank">https://prnt.sc/rxlvm9</a></p>
                    </td>
                </tr>
                
            </tbody>
        </table>
        <table class="form-table allow_free_booking">
            <tbody>

                <tr class="hoz">
                    <th class="w20 align-left"><?php _e( 'Allow free booking', 'townhub-mobile' ); ?></th>
                    <td>
                        <select name="_apps_allow_free_booking">
                            <option value="yes" <?php selected( $allow_free_booking, 'yes', true ); ?>><?php _e('Yes', 'townhub-mobile') ?></option>
                            <option value="no" <?php selected( $allow_free_booking, 'no', true ); ?>><?php _e('No', 'townhub-mobile') ?></option>
            
                        </select>
                        <p><?php _e( 'If <strong>No</strong>, <strong>Booking now</strong> button is disabled when subtotal is zero.', 'townhub-mobile' ); ?> <a href="https://prnt.sc/rxlufy" target="_blank">https://prnt.sc/rxlufy</a></p>

                    </td>
                </tr>
                
            </tbody>
        </table>
        <table class="form-table free_hide_services">
            <tbody>

                <tr class="hoz">
                    <th class="w20 align-left"><?php _e( 'Hide Additional Services on free', 'townhub-mobile' ); ?></th>
                    <td>
                        <select name="_apps_free_hide_services">
                            <option value="no" <?php selected( $free_hide_services, 'no', true ); ?>><?php _e('No', 'townhub-mobile') ?></option>
                            <option value="yes" <?php selected( $free_hide_services, 'yes', true ); ?>><?php _e('Yes', 'townhub-mobile') ?></option>
                            
            
                        </select>
                        <p><?php _e( 'If <strong>Yes</strong>, <strong>Additional Services</strong> are hidden when subtotal is zero.', 'townhub-mobile' ); ?> <a href="https://prnt.sc/rxlxz6" target="_blank">https://prnt.sc/rxlxz6</a></p>

                    </td>
                </tr>
                
            </tbody>
        </table>
        <?php
    }
    public function cpt_listing_type_save_metas($post_id){

        if (isset($_POST['_apps_booking_type'])) {
            update_post_meta($post_id, '_apps_booking_type', $_POST['_apps_booking_type'] );
        }

        if (isset($_POST['_apps_months_available'])) {
            update_post_meta($post_id, '_apps_months_available', $_POST['_apps_months_available'] );
        }
        if (isset($_POST['_apps_allow_free_booking'])) {
            update_post_meta($post_id, '_apps_allow_free_booking', $_POST['_apps_allow_free_booking'] );
        }
        if (isset($_POST['_apps_free_hide_services'])) {
            update_post_meta($post_id, '_apps_free_hide_services', $_POST['_apps_free_hide_services'] );
        }
        if (isset($_POST['_apps_disable_booking'])) {
            update_post_meta($post_id, '_apps_disable_booking', $_POST['_apps_disable_booking'] );
        }else{
            update_post_meta($post_id, '_apps_disable_booking', 'no' );
        }
    }
}
CTH_Mobile_Class_Meta_Boxes::getInstance();
