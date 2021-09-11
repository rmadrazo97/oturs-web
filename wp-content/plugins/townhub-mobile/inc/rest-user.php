<?php 

class TownHub_User_Route extends TownHub_Custom_Route {
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
            '/' . $this->rest_base . '/register', 
            array(
                array(
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => array( $this, 'do_register' ),
                    'permission_callback' => array( $this, 'create_permissions_check' ),
                    'args'                => array(),
                ),
            ) 
        );
        register_rest_route( 
            $this->namespace, 
            '/' . $this->rest_base . '/login', 
            array(
                array(
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => array( $this, 'do_login' ),
                    'permission_callback' => array( $this, 'create_permissions_check' ),
                    'args'                => array(),
                ),
            ) 
        );
        register_rest_route( 
            $this->namespace, 
            '/' . $this->rest_base . '/resetpwd', 
            array(
                array(
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => array( $this, 'do_reset' ),
                    'permission_callback' => array( $this, 'create_permissions_check' ),
                    'args'                => array(),
                ),
            ) 
        );

        register_rest_route( 
            $this->namespace, 
            '/' . $this->rest_base . '/user/(?P<id>[\d]+)', 
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_item' ),
                    'permission_callback' => array( $this, 'get_permissions_check' ),
                    'args'                => array(),
                ),
            ) 
        );

        register_rest_route( 
            $this->namespace, 
            '/' . $this->rest_base . '/user/edit', 
            array(
                array(
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => array( $this, 'edit_profile' ),
                    'permission_callback' => array( $this, 'create_permissions_check' ),
                    'args'                => array(),
                ),
            ) 
        );

        register_rest_route( 
            $this->namespace, 
            '/' . $this->rest_base . '/user/notifications/(?P<id>[\d]+)', 
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_notifications' ),
                    'permission_callback' => array( $this, 'get_permissions_check' ),
                    'args'                => array(),
                ),
            ) 
        );

        register_rest_route( 
            $this->namespace, 
            '/' . $this->rest_base . '/user/contacts/(?P<id>[\d]+)', 
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_contacts' ),
                    'permission_callback' => array( $this, 'get_permissions_check' ),
                    'args'                => array(),
                ),
            ) 
        );
        
        register_rest_route( 
            $this->namespace, 
            // '/' . $this->rest_base . '/user/replies/(?P<id>[\d]+)', 
            '/' . $this->rest_base . '/user/replies', 
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_replies' ),
                    'permission_callback' => array( $this, 'get_permissions_check' ),
                    'args'                => array(),
                ),
            ) 
        );

        register_rest_route( 
            $this->namespace, 
            '/' . $this->rest_base . '/user/reply', 
            array(
                array(
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => array( $this, 'post_reply' ),
                    'permission_callback' => array( $this, 'create_permissions_check' ),
                    'args'                => array(),
                ),
            ) 
        );

        register_rest_route( 
            $this->namespace, 
            '/' . $this->rest_base . '/user/bookmark', 
            array(
                array(
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => array( $this, 'bookmark_listing' ),
                    'permission_callback' => array( $this, 'create_permissions_check' ),
                    'args'                => array(),
                ),
            ) 
        );

        register_rest_route( 
            $this->namespace, 
            '/' . $this->rest_base . '/user/bookings/(?P<id>[\d]+)', 
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_bookings' ),
                    'permission_callback' => array( $this, 'get_permissions_check' ),
                    'args'                => array(),
                ),
            ) 
        );
        

    }

    public function get_bookings($request){
        $user_id = $request->get_param( 'id' );
        $paged  = $request->get_param('paged', 1);

        $meta_queries = array(
            'relation' => 'AND',
            // array(
            //     'key'     => ESB_META_PREFIX.'lb_email',
            //     'value'   => $current_user->user_email,
            // ),
            array(
                'key'     => ESB_META_PREFIX.'user_id',
                'value'   => $user_id,
            ),

        );   
        $bk_show_status = townhub_addons_get_option('bk_show_status');
        $bk_show_status = array_filter($bk_show_status);
        if( !empty($bk_show_status) ){
            $meta_queries[] = array(
                        'key'     => ESB_META_PREFIX.'lb_status',
                        'value'   => $bk_show_status,
                        'compare' => 'IN'
                    );
        }

        $args = array(
            'post_type'     => 'lbooking', 
            // 'author'        =>  0, 
            'orderby'       => 'date',
            'order'         => 'DESC',
            'paged'         => $paged,
            'post_status'   => 'publish',
            // 'posts_per_page' => -1, // no limit
            // 'posts_per_page' => 1,
            'meta_query'    => $meta_queries
            
        );
        // The Query
        $posts_query = new WP_Query( $args );
        $bookings = array();
        if($posts_query->have_posts()): 
            while($posts_query->have_posts()) : $posts_query->the_post();
                $itemdata = $this->prepare_bookings_item( get_the_ID() );
                $bookings[] = $this->prepare_response_for_collection( $itemdata );
            endwhile;
        endif;
        $numPages = $posts_query->max_num_pages - $paged;
        return rest_ensure_response(
            array(
                'items'     => $bookings,
                'pages'     => $numPages,
            )
        );


    }

    public function prepare_bookings_item( $ID ){
        $listing_id = get_post_meta( $ID, ESB_META_PREFIX.'listing_id', true );
        $author_id = get_post_field( 'post_author', $ID, 'display' );

        $data = array(
            'ID'                                => $ID,
            'id'                                => $ID ,
            
            'url'                               => get_the_permalink( $ID ),
            'title'                             => get_the_title($ID),
            
            
            'ltitle'                            => get_the_title($listing_id),
            'thumbnail'                         => wp_get_attachment_image_url( townhub_addons_get_listing_thumbnail( $listing_id ) ),
            'address'                           => get_post_meta( $listing_id, ESB_META_PREFIX.'address', true ),
            // showing on chat
            'author_id'                         => $author_id,
            'author_name'                       => get_the_author_meta( 'display_name', $author_id ),

            'total'                             => get_post_meta( $ID, ESB_META_PREFIX.'price_total', true ),

            'checkin'                           => get_post_meta( $ID, ESB_META_PREFIX.'checkin', true ),
            'checkout'                          => get_post_meta( $ID, ESB_META_PREFIX.'checkout', true ),
            'status'                            => get_post_meta( $ID, ESB_META_PREFIX.'lb_status', true ),
        );
        return $data;
    }

    public function get_contacts($request){
        $uid     = $request->get_param('id');
        $datas = townhub_addons_get_chats($uid);
        return rest_ensure_response($datas);
    }
    public function get_replies($request){
        $params = $request->get_params();
        // return rest_ensure_response( $params );
        $return = townhub_addons_get_replies($params);

        if( $return['success'] ){
            return rest_ensure_response( $return['replies'] );
        }
        return rest_ensure_response( array() );
        
        // $cid     = $request->get_param('cid');
        // $replies = townhub_addons_get_chat_replies( (int)$cid );
        // if( !empty($replies) ){
        //     return rest_ensure_response( $replies );
        // }

        // return rest_ensure_response( array() );
    }
    public function post_reply($request){
        $params = $request->get_params();
        $result = townhub_addons_do_post_reply($params, false);
        
        return rest_ensure_response( $result );
    }
    public function get_notifications($request){
        $id     = $request->get_param('id');
        $paged  = $request->get_param('paged', 1);


        // $user = get_user_by( 'id', $id );
        $user = get_userdata( $id );
        $response = array(
            'success'   =>  false,
            'data'      => array(),
            'pages'     => 0,
        );
        if( $user ){
            $response['success'] = true;
            $notis = Esb_Class_Dashboard::get_notifications( $id, '', 20 , $paged);
            if( is_array($notis) && !empty($notis) ){
                $response['pages'] = array_pop($notis) - $paged;
                $response['data'] = $notis;
            }
        }else{
            $response['message'] = esc_html__( 'Invalid user to get notifications', 'townhub-mobile' ) ;
        }
        return rest_ensure_response($response);
    }
    public function bookmark_listing($request){
        $datas     = $request->get_params();

        $response = array(
            'success'   =>  false,
            'message'   => '',
            'data'      => $datas,
        );

        $return = $this->do_bookmark($datas);

        return rest_ensure_response( array_merge($response, $return) );
    }
    public function do_bookmark($DATAS){
        
        $response = array(
            'success'   =>  false,
            'message'   => '',
        );

        $user_id = isset($DATAS['user_id']) ? $DATAS['user_id'] : 0;
        $userObject = get_userdata($user_id);
        if( !$userObject ){
            $response['message'] = __( 'Invalid user id', 'townhub-mobile' );
            return $response;
        }
        $listing_id = isset($DATAS['listing']) ? $DATAS['listing'] : 0;
        $listing_post = get_post($listing_id);
        if(empty($listing_post)){
            $response['message'] = esc_html__( 'Invalid listing ID', 'townhub-mobile' ) ;
            return $response;
        }

        $listing_bookmarks = get_user_meta( $user_id, ESB_META_PREFIX . 'listing_bookmarks', true);
        if ( is_array($listing_bookmarks) ) {
            if ( !empty($listing_bookmarks) && array_search($listing_id, $listing_bookmarks) !== false) {
                $response['success'] = true;
                $response['message'] = esc_html__( 'This listing has been already bookmarked', 'townhub-mobile' ) ;
                return $response;
            }
            $listing_bookmarks[] = $listing_id;
        }else{
            $listing_bookmarks = array($listing_id);
        }
        $response['success'] = true;
        update_user_meta($user_id, ESB_META_PREFIX . 'listing_bookmarks', $listing_bookmarks);

        // increase listing bookmarks
        Esb_Class_Listing_CPT::update_bookmark_count($listing_id);


        // send notification to listing author
        if (townhub_addons_get_option('db_hide_bookmarks') != 'yes') {
            Esb_Class_Dashboard::add_notification($listing_post->post_author, array(
                'type'      => 'new_bookmark',
                'entity_id' => $listing_id,
                'actor_id'  => $user_id,
            ));

        }

        // send notification to current user
        if (townhub_addons_get_option('db_hide_bookmarks') != 'yes') {
            Esb_Class_Dashboard::add_notification($user_id, array(
                'type'      => 'bookmarked',
                'entity_id' => $listing_id,
                'actor_id'  => $user_id,
            ));
        }

        $response['message'] = esc_html__( 'Bookmark listing successful', 'townhub-mobile' ) ;

        return $response;
    }
    public function get_item($request){
        $id = $request->get_param('id');
        // $user = get_user_by( 'id', $id );
        $user = get_userdata( $id );
        $data = array();
        if( $user ){
            $data['ID'] = $user->ID;
            $data['first_name'] = $user->first_name;
            $data['last_name'] = $user->last_name;
            $data['display_name'] = $user->display_name;
            $data['registered_email'] = $user->user_email;
            $data['description'] = $user->description;
            $data['email'] = get_user_meta( $user->ID, ESB_META_PREFIX.'email', true);
            $data['phone'] = get_user_meta( $user->ID, ESB_META_PREFIX.'phone', true);
            $data['website'] = $user->user_url;
            $data['address'] = get_user_meta( $user->ID, ESB_META_PREFIX.'address', true);
            $data['company'] = get_user_meta( $user->ID, ESB_META_PREFIX.'company', true);

            $data['date_of_birth'] = get_user_meta( $user->ID, ESB_META_PREFIX.'date_of_birth', true);

            $data['avatar'] = get_avatar_url($user->ID, array('size'=>150,'default'=>'https://0.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536?s=150') );

            $data['roles'] = $user->roles;
            $data['role'] = current( $user->roles );
            $data['role_name'] = townhub_addons_get_user_role_name($data['role']);

            $data['auth_token'] = get_user_meta( $user->ID, '_cth_auth_token', true );

            $data['bookmarks'] = array();
            $bookmarks = get_user_meta( $user->ID, ESB_META_PREFIX . 'listing_bookmarks', true);
            if ( !empty($bookmarks) && is_array($bookmarks) ) {
                $data['bookmarks'] = array_values($bookmarks);
            }
            $data['notis_count'] = intval( get_user_meta($user->ID, ESB_META_PREFIX . 'notis_count', true) );
            
            $data['app_address'] = $this->get_addresses( $user->ID );
            $data['address_id'] = get_user_meta( $user->ID, ESB_META_PREFIX.'address_id', true);

            return rest_ensure_response($data);
        }
        return false;
    }

    public function get_addresses($user_id){
        $vals = get_user_meta( $user_id, ESB_META_PREFIX.'app_address', true);
        if( !empty($vals) ){
            if( is_array($vals) ){
                return $vals;
            }else{
                $vals = json_decode($vals, true);
                if(!empty($vals)) 
                    return $vals;
            }
        }
        return array();
    }

    public function edit_profile($request){
        $datas = $request->get_params();
        // $headers = $request->get_headers();
        // error_log(json_encode($datas));
        // error_log(json_encode($datas['customAvatar']));
        $response = array(
            'success'   =>  false,
        );

        $user_avatar = array();
        // https://stackoverflow.com/questions/30323004/generate-post-request-for-storing-images-in-wordpress
        if( !empty($datas['customAvatar']) ){
            // error_log($datas['customAvatar']);

            $upimage_id = $this->upload_base64_image( $datas['customAvatar'] );
            if ( is_wp_error( $upimage_id ) ) {
                error_log( $upimage_id->get_error_message() );
            }elseif( !empty($upimage_id) ){
                $user_avatar = array( $upimage_id );
            }
        }

        // $response['data'] = $datas;
        // https://firxworx.com/blog/wordpress/adding-an-endpoint-to-wordpress-rest-api-for-file-uploads/
        $files = $request->get_file_params();
        error_log(json_encode($files));
        if ( !empty( $files ) && !empty( $files['customAvatar'] ) ) {
            $file = $files['customAvatar'];
        }
        
        if( isset($file) ){
            $movefile = townhub_addons_handle_image_upload($file);
            if (is_array($movefile)) {
                // https://wordpress.stackexchange.com/questions/40301/how-do-i-set-a-featured-image-thumbnail-by-image-url-when-using-wp-insert-post
                // https://codex.wordpress.org/Function_Reference/wp_insert_attachment
                // Prepare an array of post data for the attachment.
                $attachment = array(
                    // 'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ),
                    'post_mime_type' => $movefile['type'],
                    'post_title'     => sanitize_file_name(basename($movefile['file'])),
                    'post_content'   => '',
                    'post_status'    => 'inherit',
                );

                // Insert the attachment.
                $attach_id = wp_insert_attachment($attachment, $movefile['file']);

                if ($attach_id != 0) {
                    // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
                    require_once ABSPATH . 'wp-admin/includes/image.php';

                    // Generate the metadata for the attachment, and update the database record.
                    $attach_data = wp_generate_attachment_metadata($attach_id, $movefile['file']);
                    // return value from update_post_meta -  https://codex.wordpress.org/Function_Reference/update_post_meta
                    // Returns meta_id if the meta doesn't exist, otherwise returns true on success and false on failure. NOTE: If the meta_value passed to this function is the same as the value that is already in the database, this function returns false.
                    wp_update_attachment_metadata($attach_id, $attach_data);

                    // $user_metas['custom_avatar'] = array( $attach_id => wp_get_attachment_url( $attach_id ) );
                    $user_avatar = array($attach_id);
                } else {
                    $response['avatar_upload_error'] = __("wp_insert_attachment error on custom avatar upload image", 'townhub-mobile');
                }
            } else {
                $response['avatar_upload_error'] = $movefile;
                
            }
        }
        // try {
        //     // smoke/sanity check
        //     if (! $file ) {
        //         // throw new PluginException( 'Error' );
        //         $response['file_upload'] = 'Error';
        //     }
        //     // confirm file uploaded via POST
        //     if (! is_uploaded_file( $file['tmp_name'] ) ) {
        //         $response['file_upload'] = 'File upload check failed';
        //         // throw new PluginException( 'File upload check failed ');
        //     }
        //     // confirm no file errors
        //     if (! $file['error'] === UPLOAD_ERR_OK ) {
        //         $response['file_upload'] = 'Upload error: ' . $file['error'];
        //         // throw new PluginException( 'Upload error: ' . $file['error'] );
        //     }
        //   // // confirm extension meets requirements
        //   // $ext = pathinfo( $file['name'], PATHINFO_EXTENSION );
        //   // if ( $ext !== $permittedExtension ) {
        //   //   throw new PluginException( 'Invalid extension. ');
        //   // }
        //   // // check type
        //   // $mimeType = mime_content_type($file['tmp_name']);
        //   // if ( !in_array( $file['type'], $permittedTypes )
        //   //     || !in_array( $mimeType, $permittedTypes ) ) {
        //   //       throw new PluginException( 'Invalid mime type' );
        //   // }
        // } catch ( $error ) {
        //     // return $pe->restApiErrorResponse( '...' );
        //     $response['file_upload'] = 'Error';
        // }
        

        $user_data = array(
            'ID'           => $datas['ID'],
            'first_name'   => $datas['first_name'],
            'last_name'    => $datas['last_name'],
            'display_name' => $datas['display_name'],
            'user_url'     => $datas['website'],
            'description'  => $datas['description'],
        );
        $user_id = wp_update_user($user_data);
        if (is_wp_error($user_id)) {
            // There was an error, probably that user doesn't exist.
            $response['error'] = $user_id->get_error_message();
        } else {
            $meta_fields = array(
                'email'                 => 'text',
                'phone'                 => 'text',
                'address'               => 'text',
                // 'socials'       => 'array',
                // for custom avatar upload
                // 'custom_avatar' => 'array',
                'company'       => 'text',
                'date_of_birth'       => 'text',
                // 'cover_bg'      => 'array',
            );
            
            $user_metas = array();
            foreach ($meta_fields as $fname => $ftype) {
                if($ftype == 'array'){
                    $user_metas[$fname] = isset($datas[$fname]) ? $datas[$fname]  : array();
                }else{
                    $user_metas[$fname] = isset($datas[$fname]) ? wp_kses_post($datas[$fname]) : '';
                }
            }

            if( !empty($user_avatar) ){
                $user_metas['custom_avatar'] = $user_avatar;
                
            }

            foreach ($user_metas as $key => $value) {
                update_user_meta($user_id, ESB_META_PREFIX . $key, $value);
            }
            $response['avatar'] = get_avatar_url($user_id, array('size'=>150,'default'=>'https://0.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536?s=150') );
            $response['success'] = true;
        }

        return rest_ensure_response( $response );
    }
    // https://carlofontanos.com/user-login-with-wordpress-using-react-native/
    // https://stackoverflow.com/questions/1846202/php-how-to-generate-a-random-unique-alphanumeric-string/13733588#13733588
    function crypto_rand_secure($min, $max)
    {
        $range = $max - $min;
        if ($range < 1) return $min; // not so random...
        $log = ceil(log($range, 2));
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd > $range);
        return $min + $rnd;
    }

    function getToken($length)
    {
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet.= "0123456789";
        $max = strlen($codeAlphabet); // edited

        for ($i=0; $i < $length; $i++) {
            $token .= $codeAlphabet[$this->crypto_rand_secure(0, $max-1)];
        }

        return $token;
    }

    public function do_login($request){
        $response = array(
            'data'      => array(),
            'title'       => __( 'Something went wrong', 'townhub-mobile' ),
            'message'       => __( 'Invalid email or password', 'townhub-mobile' ),
            'success'    => false,
            'debug'         => false
        );
        $log = $request->get_param('log');
        if( $log ){
            /* Get user data */
            if( is_email($log) ){
                $user = get_user_by( 'email', $log );
            }else{
                $user = get_user_by( 'login', $log );
            }
            
            if( $user ){
                $password = $request->get_param('password');
                $password_check = wp_check_password( $password, $user->user_pass, $user->ID );
                if ( $password_check ){
                    /* Generate a unique auth token */
                    $token = $this->getToken( 30 );
                    /* Store / Update auth token in the database */
                    if( update_user_meta( $user->ID, '_cth_auth_token', $token ) ){
                        
                        /* Return generated token and user ID*/
                        $response['title'] = esc_html__( "Login success", 'townhub-mobile' ) ;
                        $response['success'] = true;
                        $response['data'] = array(
                            'auth_token'    =>  $token,
                            'user_id'       =>  $user->ID,
                            'user_login'    =>  $user->user_login
                        );
                        $response['message'] = __( 'Successfully Authenticated', 'townhub-mobile' );
                    }
                }else{
                    $response['message'] = esc_html__( 'Invalid password', 'townhub-mobile' ) ;
                }
            }else{
                $response['message'] = esc_html__( 'Invalid username or email', 'townhub-mobile' ) ;
            }
        }else{
            $response['message'] = esc_html__( 'Username or email must not be empty', 'townhub-mobile' ) ;
        }
        
        return rest_ensure_response($response);

    }
    public function do_reset($request){
        $response = array(
            'data'      => array(),
            'title'       => __( 'Something went wrong', 'townhub-mobile' ),
            'message'       => __( 'Invalid email or password', 'townhub-mobile' ),
            'success'    => false,
            'debug'         => false,
        );

        $user_login = $request->get_param('user_login');
        if( empty($user_login) ){
            $response['message'] = esc_html__( 'Enter a username or email address.', 'townhub-mobile' ) ;
            return rest_ensure_response($response);
        }

        if( is_email($user_login) ){
            $user = get_user_by( 'email', $user_login );
        }else{
            $user = get_user_by( 'login', $user_login );
        }

        if( $user ){
            $key = get_password_reset_key( $user );
            if ( is_wp_error( $key ) ) {
                $response['message'] = esc_html__( 'There is something wrong. Please try again.', 'townhub-mobile' ) ;
            }else{
                if ( ! townhub_addons_send_resetpwd_email($user,$key) ){
                    $response['message'] = esc_html__( "Email could not be sent", 'townhub-mobile' ) ;
                }else{
                    $response['success'] = true;
                    $response['title'] = esc_html__( "Your password has been reset", 'townhub-mobile' ) ;
                    $response['message'] = esc_html__( "You will shortly receive an email with a link to setup a new password", 'townhub-mobile' ) ;
                }
            }
        }else{
            $response['message'] = esc_html__( 'Invalid username or email', 'townhub-mobile' ) ;
        }
        
        return rest_ensure_response($response);

    }
    public function do_register($request){
        $response = array(
            'data'      => array(),
            'title'       => __( 'Something went wrong', 'townhub-mobile' ),
            'message'       => __( 'Invalid email or password', 'townhub-mobile' ),
            'success'    => false,
            'debug'         => true
        );

        $username   = $request->get_param('username');
        $email      = $request->get_param('email');
        $password   = $request->get_param('password');
        if( empty($username) || empty($email) ){
            $response['message'] = esc_html__( 'Username or email must not be empty', 'townhub-mobile' ) ;
            return rest_ensure_response($response);
        }

        if( !is_email($email) ){
            $response['message'] = esc_html__( 'Email address is invalid', 'townhub-mobile' ) ;
            return rest_ensure_response($response);
        }

        $new_user_data = array(
            'user_login' => $username,
            'first_name' => $username,
            'user_pass'  => wp_generate_password( 12, false ),
            'user_email' => $email,
        );
        $reg_lauthor   = $request->get_param('reg_lauthor');
        $registered_as_author = false;
        if( townhub_addons_get_option('register_role') == 'yes' && $reg_lauthor ) {
            $new_user_data['role'] = 'listing_author'; 
            $registered_as_author = true;
        }
        if( !empty($password) ){
            $new_user_data['user_pass'] = $password;
        }

        $user_id = wp_insert_user( $new_user_data );

        //On success
        if ( ! is_wp_error( $user_id ) ) {
            $response['success'] = true;

            $response['title'] = esc_html__( "Your account has been registered", 'townhub-mobile' ) ;
            $response['message'] = esc_html__( "You can login with the account details", 'townhub-mobile' ) ;

            // send login
            $notify_user = townhub_addons_get_option('new_user_email');
            // end admin email
            if( $notify_user == 'admin' || $notify_user == 'both' ){
                wp_new_user_notification( $user_id, null, 'admin' );
            }
            if( ( $notify_user == 'user' || $notify_user == 'both' ) && empty($password) ){
                wp_new_user_notification( $user_id, null, 'user' );
                $response['message'] = esc_html__( "You will shortly receive an email with a link to setup a new password", 'townhub-mobile' ) ;
            } 
            
            do_action( 'townhub_addons_register_user', $user_id, false );

            // only active default author plan when registered user logged + has free_lplan value
            // $inserted_free_membership = false;
            // if( $registered_as_author && $free_lplan = townhub_addons_get_option('free_lplan') ){
            //     Esb_Class_Form_Handler::insert_free_subscription( $free_lplan );
            //     $inserted_free_membership = true;
            // }
        }else{
            $response['message'] = $user_id->get_error_message();
        }

        return rest_ensure_response($response);

    }
    

}


add_action( 'rest_api_init', function () {
    TownHub_User_Route::getInstance();
} );
