<?php 

class TownHub_Booking_Route extends TownHub_Custom_Route {
    private static $_instance;
    public static function getInstance() {
        if ( ! ( self::$_instance instanceof self ) ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
    public function register_routes() {
        // register_rest_route( 
        //     $this->namespace, 
        //     '/' . $this->rest_base . '/calendar/(?P<id>[\d]+)/(?P<start>\d{4}-\d{2}-\d{2})', 
        //     array(
        //         array(
        //             'methods'             => WP_REST_Server::READABLE,
        //             'callback'            => array( $this, 'get_calendar' ),
        //             // 'permission_callback' => array( $this, 'get_permissions_check' ),
        //             'args'                => array(),
        //         ),
        //     ) 
        // );

        

        register_rest_route( 
            $this->namespace, 
            '/' . $this->rest_base . '/booking/rooms', 
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_rooms' ),
                    'permission_callback' => array( $this, 'get_permissions_check' ),
                    'args'                => array(),
                ),
            ) 
        );

        register_rest_route( 
            $this->namespace, 
            '/' . $this->rest_base . '/booking/checkout', 
            array(
                array(
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => array( $this, 'booking_checkout' ),
                    'permission_callback' => array( $this, 'create_permissions_check' ),
                    'args'                => array(),
                ),
            ) 
        );

        register_rest_route( 
            $this->namespace, 
            '/' . $this->rest_base . '/booking/(?P<id>[\d]+)', 
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_booking' ),
                    'permission_callback' => array( $this, 'get_permissions_check' ),
                    'args'                => array(),
                ),
            ) 
        );
        register_rest_route( 
            $this->namespace, 
            '/' . $this->rest_base . '/booking/cancel', 
            array(
                array(
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => array( $this, 'cancel_booking' ),
                    'permission_callback' => array( $this, 'create_permissions_check' ),
                    'args'                => array(),
                ),
            ) 
        );
        register_rest_route( 
            $this->namespace, 
            '/' . $this->rest_base . '/booking/status', 
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'check_status' ),
                    'permission_callback' => array( $this, 'get_permissions_check' ),
                    'args'                => array(),
                ),
            ) 
        );
        
    }

    public function get_rooms($request){
        
        $args = array(
            'checkin'       => $request->get_param( 'checkin' ),
            'checkout'      => $request->get_param( 'checkout' ),
            'listing_id'    => $request->get_param( 'id' ),
        );
        $return = Esb_Class_Booking_CPT::get_available_rooms_datas($args);
        // add room photo
        if( !empty($return['rooms']) ){
            $newRooms = array();
            foreach ($return['rooms'] as $room) {
                
                $photos = get_post_meta( $room['id'], ESB_META_PREFIX.'images', true );
                if( !is_array($photos) ){
                    $photos = explode(',', $photos);
                }
                $photos = array_filter($photos);
                $photosData = array();
                if( !empty($photos) ){
                    foreach ($photos as $ptid) {
                        $attachment = get_post($ptid);
                        if( $attachment){
                            array_push($photosData, array(
                                'id'            => $attachment->ID,
                                'alt'           => get_post_meta($attachment->ID, '_wp_attachment_image_alt', true),
                                'caption'       => $attachment->post_excerpt,
                                'description'   => $attachment->post_content,
                                'href'          => get_permalink($attachment->ID),
                                'src'           => $attachment->guid,
                                'title'         => $attachment->post_title
                            ));
                        }
                            
                    }
                }

                $room['images'] = $photosData;
                $newRooms[] = $room;
            }
            $return['rooms'] = $newRooms;
        }
        return rest_ensure_response( $return );
    }

    public function booking_checkout($request){
        $response = array(
            'success'   =>  false,
        );
        $listing_id = $request->get_param('listing_id');
        $user_id = $request->get_param('user_id', 0);
        $cart_data = $request->get_params();
        $response['data'] = $cart_data;
        if(is_numeric($listing_id) && (int)$listing_id > 0){
            $booking_title = __( '%1$s booking by %2$s', 'townhub-mobile' ); 
            $booking_datas = array();
            
            $booking_datas['post_title'] = sprintf( $booking_title, get_the_title( $listing_id ), $cart_data['lb_name'] );

            $booking_datas['post_content'] = '';
            $booking_datas['post_author'] = $user_id;

            //$booking_datas['post_author'] = '0';// default 0 for no author assigned
            $booking_datas['post_status'] = 'publish';
            $booking_datas['post_type'] = 'lbooking';

            do_action( 'townhub_addons_insert_booking_before', $booking_datas );

            $booking_id = wp_insert_post($booking_datas ,true );

            if (!is_wp_error($booking_id)) {
                $listing_author_id = get_post_field( 'post_author', $listing_id );
                Esb_Class_Dashboard::add_notification($listing_author_id, array(
                    'type' => 'new_booking',
                    'entity_id'     => $listing_id,
                    'actor_id'      => $user_id
                ));

                // insert cth_booking post 
                $cth_booking_datas = array(
                    'booking_id'                => $booking_id,
                    'listing_id'                => $listing_id,
                    'guest_id'                  => $user_id,
                    'status'                    => 0,
                );
                if(isset($cart_data['checkin'])) $cth_booking_datas['date_from'] = Esb_Class_Date::format($cart_data['checkin']);
                if(isset($cart_data['checkout'])) $cth_booking_datas['date_to'] = Esb_Class_Date::format($cart_data['checkout']);

                // if(isset($cart_data['rooms']) && !empty($cart_data['rooms'])){
                //     foreach ((array)$cart_data['rooms'] as $cart_room) {
                //         if(isset($cart_room['ID']) && isset($cart_room['quantity']) && (int)$cart_room['quantity'] > 0){
                //             $cth_booking_datas['room_id'] = $cart_room['ID'];
                //             $cth_booking_datas['quantity'] = (int)$cart_room['quantity'];
                //             Esb_Class_Ajax_Handler::insert_cth_booking($cth_booking_datas);
                //         }
                        
                //     }
                // }

                if(isset($cart_data['rooms_old_data']) && !empty($cart_data['rooms_old_data'])){
                    foreach ((array)$cart_data['rooms_old_data'] as $cart_room) {
                        if( isset($cart_room['ID']) && isset($cart_room['quantity']) && (int)$cart_room['quantity'] > 0 ){
                            $cth_booking_datas['room_id'] = $cart_room['ID'];
                            $cth_booking_datas['quantity'] = $cart_room['quantity'];
                            Esb_Class_Ajax_Handler::insert_cth_booking($cth_booking_datas);
                        }
                        
                    }
                }
                

                $meta_fields = array(
                    'user_id'                       => 'text',
                    'lb_name'                       => 'text',
                    'lb_email'                      => 'text',
                    'lb_phone'                      => 'text',
                    


                    'rooms_old_data'                => 'array',
                    'tickets'                       => 'array',
                    'tour_slots'                    => 'array',
                    'bk_menus'                      => 'array',
                    // hour/slot person
                    'person_slots'                  => 'array',
                    // hour/slot
                    'time_slots'                    => 'array',
                    'bk_qtts'                       => 'text',
                    'checkin'                       => 'text',
                    'checkout'                      => 'text',
                    'nights'                        => 'text',
                    'days'                          => 'text',
                    'adults'                        => 'text',
                    'children'                      => 'text',
                    'infants'                       => 'text',
                    'subtotal'                      => 'text',
                    'subtotal_fee'                  => 'text',
                    'subtotal_vat'                  => 'text',
                    'price_total'                   => 'text',
                    'qty'                           => 'text',
                    'date_event'                    => 'text',

                    'booking_type'                  => 'text',
                    'price_based'                   => 'text',
                    // 'book_services'              => 'array',
                    'price'                         => 'text',
                    'adult_price'                   => 'text',
                    'children_price'                => 'text',
                    'infant_price'                  => 'text',

                    'day_prices'                    => 'array',
                    'adult_prices'                  => 'array',
                    'children_prices'               => 'array',
                    'infant_prices'                 => 'array',

                    'payment_method'                => 'text',


                    
                );

                $meta_fields = apply_filters( 'esb_booking_meta_fields', $meta_fields );
                $booking_metas = array();
                foreach($meta_fields as $field => $ftype){
                    if(isset($cart_data[$field])) 
                        $booking_metas[$field] = $cart_data[$field] ;
                    else{
                        if($ftype == 'array'){
                            $booking_metas[$field] = array();
                        }else{
                            $booking_metas[$field] = '';
                        }
                    } 
                }
                $booking_metas['listing_id'] = $listing_id;
                $booking_metas['lb_status'] = 'pending'; // pending - completed - failed - refunded - canceled

                $booking_metas['platform'] = 'native_app';

                $booking_metas['notes'] = '';
                if(isset($cart_data['notes'])) $booking_metas['notes'] = wp_kses_post($cart_data['notes']);
                if(isset($cart_data['taxes'])) $booking_metas['subtotal_vat'] = $cart_data['taxes'];
                if(isset($cart_data['fees'])) $booking_metas['subtotal_fee'] = $cart_data['fees'];
                if(isset($cart_data['total'])) $booking_metas['price_total'] = floatval($cart_data['total']);

                // check for discount
                $applied_coupon_code = '';
                $cart_discount_percent = 0;
                $cart_discount_amount = 0;
                if( isset($cart_data['coupon_code']) && $cart_data['coupon_code'] != '' ){
                    $coupon_post = get_posts(
                        array(
                            'post_type'         => 'cthcoupons',
                            'posts_per_page'    => 1,
                            'post_status'       => 'publish',
                            'fields'            => 'ids',
                            'meta_query'        => array(
                                'relation' => 'AND',
                                array(
                                    'key'           => ESB_META_PREFIX.'coupon_code',
                                    'value'         => $cart_data['coupon_code'],
                                ),
                                array(
                                    'relation' => 'OR',
                                    array(
                                        'key'           => ESB_META_PREFIX.'for_coupon_listing_id',
                                        'value'         => $listing_id,
                                    ),
                                    array(
                                        'key'           => ESB_META_PREFIX.'for_all_listing',
                                        'value'         => 'yes',
                                    ),
                                ),
                                    
                            )
                        )
                    );


                    if(!empty($coupon_post)){
                        $coupon_id = reset($coupon_post);
                        // double check for listing coupon
                        // $listing_coupon_ids = get_post_meta($listing_id, ESB_META_PREFIX.'coupon_ids', true);
                        // if(is_array($listing_coupon_ids) && in_array($coupon_id, $listing_coupon_ids)){

                            $expire_date = get_post_meta($coupon_id, ESB_META_PREFIX.'coupon_expiry_date', true);
                            $not_expired_yet = townhub_addons_compare_dates($expire_date ,'now','>=');
                            $coupon_qty = get_post_meta($coupon_id, ESB_META_PREFIX.'coupon_qty', true);
                            
                            if( $coupon_id != '' && (int)$coupon_qty > 0 && $not_expired_yet ){
                                $discount_type = get_post_meta($coupon_id, ESB_META_PREFIX.'discount_type', true);
                                $discount_amount = get_post_meta($coupon_id, ESB_META_PREFIX.'dis_amount', true);
                                if($discount_type == 'percent'){
                                    $cart_discount_percent = (float)$discount_amount;
                                    $cart_discount_amount = 0;
                                    
                                }else if($discount_type == 'fixed_cart'){
                                    $cart_discount_percent = 0;
                                    $cart_discount_amount = (float)$discount_amount;
                                }
                                $applied_coupon_code = $cart_data['coupon_code'];
                            }

                        // }
                        // // end check coupon in listing meta
                    }
                    // is coupon post exists
                }
                // if cart coupon code exists

                if($cart_discount_percent > 0){
                
                    // $data['amount_of_discount'] = floatval( $data['price_total'] ) * ($cart_discount_percent/100);

                    $booking_metas['price_total'] = $booking_metas['price_total'] * (100 - $cart_discount_percent)/100;
                }elseif($cart_discount_amount > 0){
                    // $data['amount_of_discount'] = $cart_discount_amount;

                    $booking_metas['price_total'] = $booking_metas['price_total'] - $cart_discount_amount ;
                }

                // woo payment
                // $booking_metas['payment_method'] = 'woo'; // banktransfer - paypal - stripe - woo 

                // $cmb_prefix = '_cth_';
                foreach ($booking_metas as $key => $value) {
                    update_post_meta( $booking_id, ESB_META_PREFIX.$key,  $value  );
                }

                update_post_meta( $booking_id, '_price',  $booking_metas['price_total']  );

                if ( isset($cart_data['book_services']) && is_array($cart_data['book_services']) && !empty($cart_data['book_services']) ){
                    update_post_meta( $booking_id, ESB_META_PREFIX.'book_services', $cart_data['book_services']);     
                }
                // slot booking
                if ( isset($cart_data['slots']) && is_array($cart_data['slots']) && !empty($cart_data['slots']) ){
                    update_post_meta( $booking_id, ESB_META_PREFIX.'slots', $cart_data['slots']);     
                    update_post_meta( $booking_id, ESB_META_PREFIX.'slots_text', implode("|", $cart_data['slots'] ) );     
                }
                // tpicker booking
                if ( isset($cart_data['times']) && is_array($cart_data['times']) && !empty($cart_data['times']) ){
                    update_post_meta( $booking_id, ESB_META_PREFIX.'times', $cart_data['times']);     
                    update_post_meta( $booking_id, ESB_META_PREFIX.'times_text', implode("|", $cart_data['times'] ) );     
                }
                if ( !empty($applied_coupon_code) ) {
                    update_post_meta( $booking_id, ESB_META_PREFIX.'bkcoupon',  $applied_coupon_code );
                    self::update_quantity_coupon($applied_coupon_code);
                }
                // if(!empty($quantity) && $quantity != '' && $quantity > 0){
                //     update_post_meta( $booking_id, ESB_META_PREFIX.'quantity',  $quantity );

                //     $rid = '';
                //     $rprice = '';
                //     if(isset($cart_data['rooms']) && !empty($cart_data['rooms'])){
                //         foreach ((array)$cart_data['rooms'] as $cart_room) {
                //             if(isset($cart_room['ID']) && isset($cart_room['quantity']) && (int)$cart_room['quantity'] > 0){
                //                 $rid = $cart_room['ID'];
                //             }  
                //         }
                //         $rprice = get_post_meta($rid,ESB_META_PREFIX.'_price',true);
                //     }
                //     $rooms_price = 0;
                //     $rooms_price += $quantity * $rprice;
                //     $price_total_room = $rooms_price + $cart_data['subtotal_fee'] + $cart_data['subtotal_vat'];
                //     update_post_meta( $booking_id, ESB_META_PREFIX.'price_total_room',  $price_total_room);
                // } 

                $process_results = array(
                    'success'   => false,
                    'url'       => ''
                );
                $payment_method = !empty($cart_data['payment_method']) ? esc_html($cart_data['payment_method']) : 'free';
                // where payment method
                if( !empty( $booking_metas['price_total'] ) ){
                    $data_checkout = array(
                        'inserted_post_first'       => $booking_id,
                        // 'stripeEmail'               => $stripeEmail,
                        'inserted_posts_text'       => $booking_id,

                        // for rest only
                        'total'                     => $booking_metas['price_total'],
                        'user_id'                   => $user_id,
                    );
                    $esb_payments = ESB_ADO()->payment_methods;
                    if(isset($esb_payments[$payment_method])) $process_results = $esb_payments[$payment_method]->process_payment_checkout($data_checkout);
                    
                }
                // end payment methods
                if ( $payment_method == 'free' || $payment_method == 'banktransfer' || $payment_method ==  'submitform' || $payment_method ==  'cod' ) {
                    $process_results = array(
                        'success'   => true,
                        'url'       => '',
                    );
                }
                $response = array_merge($response, $process_results);
                
                // $response['success'] = $process_results['success'];
                // $response['url'] = $process_results['url'];

                Esb_Class_Booking_CPT::update_bookings_count($listing_author_id);
                
                do_action( 'esb_insert_booking_after', $booking_id , $cart_data);
                $response['booking_id'] = $booking_id;

                $response = apply_filters( 'cth_rest_booking_response', $response, $booking_id , $cart_data );

                return rest_ensure_response( $response );
            }
            // end check insert booking error
            $response['error'] = __('Can not insert booking post','townhub-mobile');
        }
        // end check listing id
        $response['error'] = __('Invalid listing id','townhub-mobile');
        
        return rest_ensure_response( $response );

    }


    // public function get_calendar($request){
    //     $lid =  $request->get_param( 'id' );
    //     $dayStart =  $request->get_param( 'start' );
    //     $ltype_id = get_post_meta( $lid, ESB_META_PREFIX.'listing_type_id', true );
    //     if( empty($ltype_id) ) $ltype_id = esb_addons_get_wpml_option('default_listing_type', 'listing_type');

    //     $calendar_type = get_post_meta( $ltype_id, '_apps_calendar_type', true );
    //     $months_available = get_post_meta( $ltype_id, '_apps_months_available', true );
    //     if( empty($calendar_type) ) $calendar_type = 'simple';
    //     if( empty($months_available) ) $months_available = 2;

    //     // get calendar dates
    //     $startDateObj = new DateTime($dayStart);
    //     if( !$startDateObj ){
    //         $startDateObj = new DateTime('now');
    //     }
    //     $dayStart = $startDateObj->format('Y-m-d');
    //     $yStart = $startDateObj->format('Y');
    //     $mStart = $startDateObj->format('m');
    //     // $dStart = $startDateObj->format('d');
    //     // get last day of last month
    //     $dayEnd = (new DateTime( $yStart . '-' . ($mStart + $months_available - 1) . '-01' ) )->format('Y-m-t');
    //     $dayEndObj = new DateTime($dayEnd);
    //     $dates = array();
    //     for ($i=0; $i < 1000 ; $i++) { 
    //         $temp = Esb_Class_Date::modify( $dayStart, $i, 'Y-m-d' );
    //         $tempObj = new DateTime($temp);
    //         if( $tempObj > $dayEndObj ) break;
    //         $dates[] = $temp;
    //     }
        
    //     $data = array(
    //         'calendar_type' => $calendar_type,
    //         'months_available' => $months_available,
    //         // 'dates' => $dates,
    //         'available' => Esb_Class_Booking_CPT::get_availability($dates, $lid),
    //         'check_available' => get_post_meta( $lid, ESB_META_PREFIX.'listing_dates', true ) == '' ? false : true,

    //         // 'dayStart' => $dayStart,
    //         // 'dayEnd' => $dayEnd,
    //     );

        
    //     return rest_ensure_response($data);

    // }
    
    public function get_booking($request){
        $ID = $request->get_param( 'id' );
        $listing_id = get_post_meta( $ID, ESB_META_PREFIX.'listing_id', true );
        $author_id = get_post_field( 'post_author', $listing_id, 'display' );

        $payment_method = get_post_meta( $ID, ESB_META_PREFIX.'payment_method', true );
        $data = array(
            'ID'                                => $ID,
            'id'                                => $ID ,
            
            'url'                               => get_the_permalink( $ID ),
            'title'                             => get_the_title($ID),
            'status'                            => get_post_meta( $ID, ESB_META_PREFIX.'lb_status', true ),
            
            
            'ltitle'                            => get_the_title($listing_id),
            'thumbnail'                         => wp_get_attachment_image_url( townhub_addons_get_listing_thumbnail( $listing_id ) ),
            'address'                           => get_post_meta( $listing_id, ESB_META_PREFIX.'address', true ),
            // show author page
            'author_id'                         => $author_id,
            'author_name'                       => get_the_author_meta( 'display_name', $author_id ),

            'subtotal'                          => get_post_meta( $ID, ESB_META_PREFIX.'subtotal', true ),
            'taxes'                             => get_post_meta( $ID, ESB_META_PREFIX.'subtotal_vat', true ),
            'fees'                              => get_post_meta( $ID, ESB_META_PREFIX.'subtotal_fee', true ),
            'total'                             => get_post_meta( $ID, ESB_META_PREFIX.'price_total', true ),

            'pmmethod'                          => townhub_addons_payment_names($payment_method),

            'checkin'                           => get_post_meta( $ID, ESB_META_PREFIX.'checkin', true ),
            'checkout'                          => get_post_meta( $ID, ESB_META_PREFIX.'checkout', true ),

            

            'price_based'                       => get_post_meta( $ID, ESB_META_PREFIX.'price_based', true ),
            'price'                             => get_post_meta( $ID, ESB_META_PREFIX.'price', true ),
            'children_price'                    => get_post_meta( $ID, ESB_META_PREFIX.'children_price', true ),
            'infant_price'                      => get_post_meta( $ID, ESB_META_PREFIX.'infant_price', true ),

            'bk_qtts'                           => (int)get_post_meta( $ID, ESB_META_PREFIX.'bk_qtts', true ),
            // per person
            'adults'                            => (int)get_post_meta( $ID, ESB_META_PREFIX.'adults', true ),
            'children'                          => (int)get_post_meta( $ID, ESB_META_PREFIX.'children', true ),
            'infants'                           => (int)get_post_meta( $ID, ESB_META_PREFIX.'infants', true ),
            // hour/slot person
            'person_slots'                      => get_post_meta( $ID, ESB_META_PREFIX.'person_slots', true ),
            // hour/slot
            'time_slots'                        => get_post_meta( $ID, ESB_META_PREFIX.'time_slots', true ),
            'rooms_old'                         => get_post_meta( $ID, ESB_META_PREFIX.'rooms_old_data', true ),
            'tickets'                           => get_post_meta( $ID, ESB_META_PREFIX.'tickets', true ),
            'tour_slots'                        => get_post_meta( $ID, ESB_META_PREFIX.'tour_slots', true ),
            'bk_menus'                          => get_post_meta( $ID, ESB_META_PREFIX.'bk_menus', true ),
            

            'book_services'                     => get_post_meta( $ID, ESB_META_PREFIX.'book_services', true ),
            'notes'                             => get_post_meta( $ID, ESB_META_PREFIX.'notes', true ),
        );

        return rest_ensure_response($data);

    }
    public function cancel_booking($request){
        $ID = $request->get_param( 'id' );
        $user_id = $request->get_param( 'user_id' );
        $response = array(
            'success' => false,
            'debug' => false
        );
        if(is_numeric($ID) && (int)$ID > 0){
            $booking_status = get_post_meta( $ID, ESB_META_PREFIX.'lb_status', true );
            if( $booking_status === 'canceled' ){
                $response['error'] = esc_html__( 'The booking has already canceled.', 'townhub-mobile' ) ;
            }else{
                $listing_id = get_post_meta( $ID, ESB_META_PREFIX.'listing_id', true );
                $listing_author_id = get_post_field('post_author', $listing_id);
                $buser_id = get_post_meta( $ID, ESB_META_PREFIX.'user_id', true );

                if( $user_id == $buser_id || $user_id == $listing_author_id ){
                    update_post_meta( $ID, ESB_META_PREFIX.'lb_status',  'canceled'  );
                    update_post_meta( $ID, ESB_META_PREFIX.'canceled_user',  $user_id  );
                    $response['success'] = true;
                }else{
                    $response['error'] = esc_html__( "You don't have permission to cancel this booking", 'townhub-mobile' ) ;
                }
            }
        }else{
            $response['error'] = esc_html__( 'The post id is incorrect.', 'townhub-mobile' ) ;
        }

        return rest_ensure_response($response);
    }
    public function check_status($request){
        $ID = $request->get_param( 'id' );
        $response = array(
            'success'       => true,
            'debug'         => false,
            'status'        => get_post_meta( $ID, ESB_META_PREFIX.'lb_status', true ),
        );
        return rest_ensure_response($response);
    }

    protected static function update_quantity_coupon($coupon_code){
        $coupon_post = get_posts(
            array(
                'post_type'         => 'cthcoupons',
                'posts_per_page'    => 1,
                'post_status'       => 'publish',
                'fields'            => 'ids',
                'meta_query'        => array(
                    array(
                        'key'           => ESB_META_PREFIX.'coupon_code',
                        'value'         => $coupon_code,
                    ),
                    // array(
                    //     'key'           => ESB_META_PREFIX.'for_coupon_listing_id',
                    //     'value'         => $listing_id,
                    // )
                )
            )
        );
        if(!empty($coupon_post)){
            $coupon_id = reset($coupon_post);
            $coupon_qty = get_post_meta($coupon_id, ESB_META_PREFIX.'coupon_qty', true);
            if ( is_numeric($coupon_qty) && $coupon_qty > 0) {
                update_post_meta( $coupon_id, ESB_META_PREFIX.'coupon_qty',  $coupon_qty - 1 );
            } 
        }
    }
}


add_action( 'rest_api_init', function () {
    TownHub_Booking_Route::getInstance();
} );
