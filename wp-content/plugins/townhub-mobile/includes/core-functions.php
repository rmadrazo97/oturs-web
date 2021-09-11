<?php

function cth_mobile_get_option( $setting, $default = null ) {
    global $cthmobile_options; 

    $default_options = array(
        'app_key'                       => '',
        'dis_auth'                      => 'no',
        'explore_page'                  => 0,
        'terms_page'                    => '',
        'policy_page'                   => '',
        'help_page'                     => '',
        'about_page'                    => '',
    );
    $value = false;
    if ( isset( $cthmobile_options[ $setting ] ) ) {
        $value = $cthmobile_options[ $setting ];
    }else {
        if(isset($default)){
            $value = $default;
        }else if( isset( $default_options[ $setting ] ) ){
            $value = $default_options[ $setting ];
        }
    }

    
    return apply_filters( 'cth_mobile_option_value', $value, $setting );
}

function cth_mobile_get_wpml_option($name='', $type='page', $default = null){

    $option_value    = cth_mobile_get_option($name, $default);
    if( is_numeric($option_value) ){
        return apply_filters( 'wpml_object_id', $option_value, $type, true );
    }
    return $option_value;
}

function cth_mobile_get_addons_option( $setting, $default = null ) {
    if( function_exists('townhub_addons_get_option') ){
        return townhub_addons_get_option( $setting, $default );
    }
    return cth_mobile_get_option( $setting, $default );
}

function cth_mobile_get_add_to_cart_url($postID = 0, $quantity = 1){
    $args = array(
        'add-to-cart' => $postID
    );
    if($quantity > 1) $args['quantity'] = $quantity;
    if(function_exists('wc_get_page_id')){
        $url = add_query_arg( $args, get_permalink( wc_get_page_id( 'checkout' ) ) );
    }else{
        $url = add_query_arg( $args, home_url( '/checkout/' ) );
    }

    return $url ; // do not esc_url because it's not working for quantity
}

add_filter( 'esb_payment_methods', function($payments){
    if( defined( 'REST_REQUEST' ) && REST_REQUEST && cth_mobile_get_option('woo_payment') == 'yes' ){
        $payments['woo'] = array(
                'title' => _x( 'WooCommerce Checkout', 'Mobile Method', 'townhub-mobile' ),
                
                'icon' => 'https://woocommerce.com/wp-content/themes/woo/images/logo-woocommerce.png',
                'desc' => _x( 'You will redirect to WooCommerce cart to complete booking', 'Mobile Method', 'townhub-mobile' ),

                'checkout_text' => _x( 'Go to WooCommerce', 'Mobile Method', 'townhub-mobile' ),
            );
    }
        
    return $payments;
} );

add_filter( 'cth_rest_booking_response', function($response, $booking_id , $cart_data){
    $payment_method = !empty($cart_data['payment_method']) ? esc_html($cart_data['payment_method']) : 'free';
    if( $payment_method == 'woo' && cth_mobile_get_option('woo_payment') == 'yes' ){
        $response['success'] = true;
        $response['url'] = cth_mobile_get_add_to_cart_url( $booking_id, 1 );
    }
    return $response;
}, 10, 3 );