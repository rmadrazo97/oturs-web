<?php 
/* add_ons_php */

class TownHub_Custom_Route extends WP_REST_Controller {

    private static $_instance;

    protected $ltype_id = false;
    protected $lmenus_cats = array();

    protected $api_key = '';

    // Here initialize our namespace and resource name.
    public function __construct() {
        $this->namespace     = 'cththemes/v1';
        $this->rest_base = 'listings';

        $this->api_key = cth_mobile_get_option('app_key');

        $this->register_routes();
    }

    public static function getInstance() {
        if ( ! ( self::$_instance instanceof self ) ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function register_routes() {
        register_rest_route( 
            $this->namespace, 
            '/' . $this->rest_base, 
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE, // ALLMETHODS,//READABLE,, - should be readable for filter query
                    'callback'            => array( $this, 'get_archive' ),
                    'permission_callback' => array( $this, 'get_permissions_check' ),
                    'args'                => array(
                        'posts_per_page'    => array(
                            'default'       => 6,
                            'required'      => false,
                            'validate_callback' => function($param, $request, $key) {
                              return is_numeric( $param );
                            }
                        ),
                        'order'    => array(
                            'default'       => 'DESC',
                        ),
                        'orderby'    => array(
                            'default'       => 'date',
                        ),
                    ),
                )
            ) 
        );
        register_rest_route( 
            $this->namespace, 
            '/' . $this->rest_base . '/latest', 
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_items' ),
                    'permission_callback' => array( $this, 'get_permissions_check' ),
                    'args'                => array(
                        'posts_per_page'    => array(
                            'default'       => 16,
                            'required'      => false,
                            'validate_callback' => function($param, $request, $key) {
                              return is_numeric( $param );
                            }
                        ),
                        'order'    => array(
                            'default'       => 'DESC',
                        ),
                        'orderby'    => array(
                            'default'       => 'date',
                        ),
                    ),
                )
            ) 
        );
        register_rest_route( 
            $this->namespace, 
            '/' . $this->rest_base . '/(?P<id>[\d]+)/(?P<start>\d{4}-\d{2}-\d{2})', 
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_item' ),
                    'permission_callback' => array( $this, 'get_permissions_check' ),
                    'args'                => array(
                        'context' => array(
                            'default' => 'view',
                        ),
                    ),
                ),
            ) 
        );

        register_rest_route( 
            $this->namespace, 
            '/' . $this->rest_base . '/reviews/add', 
            array(
                array(
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => array( $this, 'add_review' ),
                    'permission_callback' => array( $this, 'create_permissions_check' ),
                    'args'                => array(),
                ),
            ) 
        );

        register_rest_route( 
            $this->namespace, 
            '/' . $this->rest_base . '/claim/add', 
            array(
                array(
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => array( $this, 'add_claim' ),
                    'permission_callback' => array( $this, 'create_permissions_check' ),
                    'args'                => array(),
                ),
            ) 
        );

        register_rest_route( 
            $this->namespace, 
            '/' . $this->rest_base . '/report/add', 
            array(
                array(
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => array( $this, 'add_report' ),
                    'permission_callback' => array( $this, 'create_permissions_check' ),
                    'args'                => array(),
                ),
            ) 
        );

        register_rest_route( 
            $this->namespace, 
            '/' . $this->rest_base . '/site', 
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_site_datas' ),
                    'permission_callback' => array( $this, 'get_permissions_check' ),
                    // 'args'                => array(
                    //     'context' => array(
                    //         'default' => 'view',
                    //     ),
                    // ),
                ),
            ) 
        );
    }

    public function add_review($request){
        $datas = $request->get_params();
        $criteria = $request->get_param('reviewCriteria');
        $lID = $request->get_param('comment_post_ID');
        $response = array(
            'success'   =>  false,
            'ID'        => 0,
        );
        // $response['data'] = $datas;

        $addedID = wp_insert_comment( $datas );
        if( $addedID ){

            $rating_fields = townhub_addons_get_rating_fields( get_post_meta( $lID, ESB_META_PREFIX.'listing_type_id', true ) );
            if (!empty($rating_fields)) {
                foreach ((array)$rating_fields as $key => $field) {
                    $rate_name = $field['fieldName'];
                    if(isset($criteria[$rate_name])) add_comment_meta($addedID, ESB_META_PREFIX.$rate_name, $criteria[$rate_name]);
                }
            }

            // add_comment_meta($addedID, '_cth_from_apps', 'yes' );

            do_action( 'cth_mobile_add_review_after', $addedID, get_comment($addedID) );

            $response['success'] = true;
            $response['ID'] = $addedID;
        }
        return rest_ensure_response($response);
    }
    public function add_claim($request){
        $datas = $request->get_params();
        // return rest_ensure_response( array(
        //     'success' => false,
        //     'data' => array(
        //         'message' => 'this is custom error'
        //     )
        // ) );
        $response = Esb_Class_Claim_CPT::do_add_claim_post($datas);
        return rest_ensure_response($response);
    }
    public function add_report($request){
        $datas = $request->get_params();
        // return rest_ensure_response( array(
        //     'success' => false,
        //     'data' => array(
        //         'datas'     => $datas,
        //         'message' => 'this is custom error'
        //     )
        // ) );
        $response = Esb_Class_LReport_CPT::do_add_report_post($datas);
        return rest_ensure_response($response);
    }
    public function get_site_datas( $request ){
        // error_log(json_encode($request->get_headers()));
        $cats = get_terms( array(
                'taxonomy'      => 'listing_cat',
                'hide_empty'    => false,
                'orderby'       => 'count',
                'order'         => 'DESC',
        ) );
        $locs = get_terms( array(
                'taxonomy'      => 'listing_location',
                'hide_empty'    => false,
                'orderby'       => 'count',
                'order'         => 'DESC',
        ) );
        $feas = get_terms( array(
                'taxonomy'      => 'listing_feature',
                'hide_empty'    => true,
                'orderby'       => 'count',
                'order'         => 'DESC',
        ) );
        $tags = get_terms( array(
                'taxonomy'      => 'listing_tag',
                'hide_empty'    => true,
                'orderby'       => 'count',
                'order'         => 'DESC',
        ) );
        $lIDs = get_posts( array(
                'fields'        => 'ids',
                'post_type' => 'listing',
                'posts_per_page'=> -1,
                'post_status' => 'publish',
                'suppress_filters'  => false,
        ) );
        $listings = array();
        if( !empty($lIDs) ){
            foreach ($lIDs as $lid) {
                $itemdata = $this->prepare_listings_item( $lid );
                $listings[] = $this->prepare_response_for_collection( $itemdata );
            }
        }
        $currencies = array_filter((array)townhub_addons_get_option('currencies'), function($cur){
            return is_array($cur) && isset($cur['currency']);
        });
        $datas = array(
            'explore'                       => $this->get_home_datas( $request ),
            'cats'                          => $this->prepare_taxes( $cats, $request ),
            'locs'                          => $this->prepare_taxes( $locs, $request ),
            'feas'                          => $this->prepare_taxes( $feas, $request ),
            'tags'                          => $this->prepare_taxes( $tags, $request ),
            'listings'                      => $listings,
            
            'layout'                        => array(
                'list'                          => __( 'List view', 'townhub-mobile' ),
                'grid'                          => __( 'Grid view', 'townhub-mobile' ),
            ),
            'languages'                     => $this->get_languages(),
            'currencies'                    => $currencies,
            'base_currency'                 => townhub_addons_get_base_currency(),
            'date_format'                   => get_option( 'date_format', 'Y-m-d' ),
            'time_format'                   => get_option( 'time_format', 'H:i' ),

            'terms_page'                    => get_the_permalink( cth_mobile_get_option('terms_page') ),
            'policy_page'                   => get_the_permalink( cth_mobile_get_option('policy_page') ),
            'help_page'                     => get_the_permalink( cth_mobile_get_option('help_page') ),
            'about_page'                    => get_the_permalink( cth_mobile_get_option('about_page') ),
        );
        // error_log(print_r($datas,true));
        return rest_ensure_response($datas);
    }
    public function get_home_datas(){
        $datas = array();
        $layout = get_post_meta( cth_mobile_get_wpml_option('explore_page'), '_cth_cthazp_layout', true );
        // error_log($home);
        $eles = (new AZPParser())->getContentShortcodeEles($layout);
        if( !empty($eles) && is_array($eles) ){
            foreach ($eles as $ele) {
                $ele = (array)$ele;
                
                if( $ele && isset($ele['type']) ){
                    $parseEle = array(
                        'type'  => $ele['type']
                    );
                    if( isset($ele['attrs']) ){
                        $attrs = (array)$ele['attrs'];
                        unset($attrs['el_id']);
                        unset($attrs['el_class']);
                        unset($attrs['el_disable']);
                        unset($attrs['azp_bwid']);
                        unset($attrs['azp_mID']);
                        if( isset($attrs['title']) ){
                            $parseEle['title'] = $attrs['title'];
                        }
                        if( isset($attrs['show_view_all']) ){
                            $parseEle['show_view_all'] = $attrs['show_view_all'] == 'yes' ? true : false ;
                        }
                        if( isset($attrs['viewall_text']) ){
                            $parseEle['viewall_text'] = $attrs['viewall_text'];
                        }
                        if( isset($attrs['ele_layout']) ){
                            $parseEle['layout'] = $attrs['ele_layout'];
                        }
                        // image banner
                        if( isset($attrs['src']) ){
                            $parseEle['src'] = $attrs['src'];
                            if( is_numeric($attrs['src']) ){
                                $bnsrc = wp_get_attachment_image_url( $attrs['src'], 'full' );
                                if( false == $bnsrc ) $bnsrc = '';
                                $parseEle['src'] = $bnsrc;
                            }
                        }
                        if( isset($attrs['height']) ){
                            $parseEle['height'] = (float)$attrs['height'];
                        }
                        if( isset($attrs['width']) ){
                            $parseEle['width'] = (float)$attrs['width'];
                        }

                        if( isset($attrs['url']) ){
                            $parseEle['url'] = $attrs['url'];
                        }
                        switch ($ele['type']) {
                            case 'app_listings':
                                $parseEle['data'] = $this->do_query_listings($attrs);
                                break;
                            case 'app_cats':
                                $parseEle['data'] = $this->do_query_cats($attrs);
                                break;
                            case 'app_locs':
                                $parseEle['data'] = $this->do_query_locs($attrs);
                                break;
                        }
                        
                    }
                    $datas[] = $parseEle;
                }
            }
        }
        
        return $datas;
    }
    public function do_query_listings($request_args = array() ){
        extract($request_args);
        $post_args = array(
            'fields'            => 'ids',
            'post_type'         => 'listing',
            // 'posts_per_page'    => 10,
            // 'orderby'           => 'date',
            // 'order'             => 'DESC',
            'post_status'       => 'publish',
        );
        if( isset($posts_per_page) ){
            $post_args['posts_per_page'] = $posts_per_page;
        }
        if( isset($orderby) ){
            $post_args['orderby'] = $orderby;
            if( $orderby == 'listing_featured' ){
                $post_args['meta_key'] = ESB_META_PREFIX . 'featured';
                $post_args['orderby'] = 'meta_value_num';
            }
        }

        if( isset($order) ){
            $post_args['order'] = $order;
        }
        if( isset($ids) && !empty($ids) ){
            $ids = explode(",", $ids);
            $post_args['post__in'] = $ids;
        }
        if( isset($ids_not) && !empty($ids_not) ){
            $ids_not = explode(",", $ids_not);
            $post_args['post__not_in'] = $ids_not;
        }

        $tax_queries = array();

        if( isset($cat_ids) && !empty($cat_ids) ){
            $tax_queries[] =    array(
                                    'taxonomy' => 'listing_cat',
                                    'field'    => 'term_id',
                                    'terms'    => explode( ",", $cat_ids ),
                                );

        }
        if( isset($loc_ids) && !empty($loc_ids) ){
            $tax_queries[] =    array(
                                    'taxonomy' => 'listing_location',
                                    'field'    => 'term_id',
                                    'terms'    => explode( ",", $loc_ids ),
                                );

        }
        
        if( isset($tag_ids) && !empty($tag_ids) ){
            $tax_queries[] =    array(
                                    'taxonomy' => 'listing_tag',
                                    'field'    => 'term_id',
                                    'terms'    => explode( ",", $tag_ids ),
                                );
        }

        if(!empty($tax_queries)){
            $post_args['tax_query'] = $tax_queries;
        } 

        $meta_queries = array();
        if( isset($featured_only) && $featured_only == 'yes'){
            $meta_queries[] =   array(
                                    'key'     => ESB_META_PREFIX .'featured',
                                    'value'   => '1',
                                    'type'    => 'NUMERIC'
                                );
        }

        if(!empty($meta_queries)) $post_args['meta_query'] = $meta_queries;

        


        
        $items = get_posts($post_args);
        $listings = array();
        foreach( $items as $lid ) {
            $itemdata = $this->prepare_listings_item( $lid );
            $listings[] = $this->prepare_response_for_collection( $itemdata );
        }
        return $listings;
    }
    public function do_query_cats($request_args = array() ){
        extract($request_args);
        

        $term_args = array(
            'taxonomy'      => 'listing_cat',
            'count'         => true,
            'pad_counts'    => true,
        );

        if( isset($hide_empty) && $hide_empty != 'yes' ){
            $term_args['hide_empty'] = false;
        }
        if( isset($number) ){
            $term_args['number'] = $number;
        }
        if( isset($orderby) ){
            $term_args['orderby'] = $orderby;
            
        }
        if( isset($order) ){
            $term_args['order'] = $order;
        }


        if( isset($cat_ids) && !empty($cat_ids) ){
            $term_args['include'] = $cat_ids;
        }
        if( isset($cat_ids_not) && !empty($cat_ids_not) ){
            $term_args['exclude'] = $cat_ids_not;
        }
        $listing_terms = get_terms( $term_args );

        return $this->prepare_taxes( $listing_terms, null );

    }
    public function do_query_locs($request_args = array() ){
        extract($request_args);
        

        $term_args = array(
            'taxonomy'      => 'listing_location',
            'count'         => true,
            'pad_counts'    => true,
        );

        if( isset($hide_empty) && $hide_empty != 'yes' ){
            $term_args['hide_empty'] = false;
        }
        if( isset($number) ){
            $term_args['number'] = $number;
        }
        if( isset($orderby) ){
            $term_args['orderby'] = $orderby;
            
        }
        if( isset($order) ){
            $term_args['order'] = $order;
        }


        if( isset($cat_ids) && !empty($cat_ids) ){
            $term_args['include'] = $cat_ids;
        }
        if( isset($cat_ids_not) && !empty($cat_ids_not) ){
            $term_args['exclude'] = $cat_ids_not;
        }
        $listing_terms = get_terms( $term_args );

        return $this->prepare_taxes( $listing_terms, null );

    }
    public function get_languages(){
        $languages = array(
            array(
                'code'      => 'en',
                'name'      => __( 'English', 'townhub-mobile' ),
                'rtl'       => false,
            ),
            array(
                'code'      => 'fr',
                'name'      => __( 'French (France)', 'townhub-mobile' ),
                'rtl'       => false,
            ),
            array(
                'code'      => 'tr',
                'name'      => __( 'Turkish', 'townhub-mobile' ),
                'rtl'       => false,
            ),
            array(
                'code'      => 'cn',
                'name'      => __( 'Chinese (China)', 'townhub-mobile' ),
                'rtl'       => false,
            ),
            array(
                'code'      => 'my',// my
                'name'      => __( 'Myanmar (Burmese)', 'townhub-mobile' ),
                'rtl'       => false,
            ),
            array(
                'code'      => 'pt',
                'name'      => __( 'Portuguese (Portugal)', 'townhub-mobile' ),
                'rtl'       => false,
            ),
            array(
                'code'      => 'es',
                'name'      => __( 'Spanish (Spain)', 'townhub-mobile' ),
                'rtl'       => false,
            ),
            array(
                'code'      => 'jp',
                'name'      => __( 'Japanese', 'townhub-mobile' ),
                'rtl'       => false,
            ),
            array(
                'code'      => 'pl',
                'name'      => __( 'Polish', 'townhub-mobile' ),
                'rtl'       => false,
            ),
            array(
                'code'      => 'ru',
                'name'      => __( 'Russian', 'townhub-mobile' ),
                'rtl'       => false,
            ),
            array(
                'code'      => 'it',
                'name'      => __( 'Italian', 'townhub-mobile' ),
                'rtl'       => false,
            ),
            array(
                'code'      => 'de',
                'name'      => __( 'German', 'townhub-mobile' ),
                'rtl'       => false,
            ),
            array(
                'code'      => 'vi',
                'name'      => __( 'Vietnamese (Viet Nam)', 'townhub-mobile' ),
                'rtl'       => false,
            ),
        );

        return (array)apply_filters( 'cth_mobile_languages', $languages );
    }
    public function get_currencies($with_base = false){
        $currencies = array_filter((array)townhub_addons_get_option('currencies'), function($cur){
            return is_array($cur) && isset($cur['currency']);
        });
        if( $with_base ){
            $base_currency = townhub_addons_get_base_currency();
            if( !empty($base_currency) ){
                array_unshift($currencies, $base_currency);
            }
        }
        if( is_array($currencies) && !empty($currencies) ){
            return array_map(function($crr){
                $crr['decimal'] = intval($crr['decimal']);
                $crr['rate'] = floatval($crr['rate']);
                return $crr;
            }, $currencies);
        }

        return array();
    }
    public function prepare_taxes($posts, $request, $tax = 'listing_cat' ){
        $data = array();
        if ( ! empty( $posts ) && ! is_wp_error( $posts ) ){
            $dfthumb = '';
            $default_thumbnail = townhub_addons_get_option('default_thumbnail');
            if( $default_thumbnail && !empty($default_thumbnail['id']) ){
                $dfthumb = wp_get_attachment_url( $default_thumbnail['id'] );
            }
            foreach ( $posts as $post ) {
                $response = $this->prepare_tax( $post, $dfthumb, $request, $tax );
                $data[] = $this->prepare_response_for_collection( $response );
            }
        }
        return $data;
        // return rest_ensure_response($data);
    }
    public function prepare_tax($post, $dfthumb = '', $request, $tax ){
        $term_metas = townhub_addons_custom_tax_metas($post->term_id, $tax); 
        if( !empty($term_metas['featured_url']) ) $dfthumb = $term_metas['featured_url'];
        $data = array(
            'id'            => $post->term_id,
            'title'         => $post->name,
            'count'         => $post->count,
            'thumbnail'     => $dfthumb,
            'icon'          => $term_metas['icon'],
            'color'         => $term_metas['color'],
            'imgicon'       => $term_metas['imgicon_url'],

            'description'   => $post->description,
            'parent'        => $post->parent,
            'taxonomy'      => $post->taxonomy,

            // ["term_id"]=>  //int
            // ["name"]=>   //string 
            // ["slug"]=>  //string 
            // ["term_group"]=>  //int
            // ["term_taxonomy_id"]=> //int
            // ["taxonomy"]=>   //string
            // ["description"]=>    //string
            // ["parent"]=> //int
            // ["count"]=>  // int
            // ["filter"]= //string

        );

        return rest_ensure_response($data);
    }
    /**
    * Get a collection of items
    *
    * @param WP_REST_Request $request Full data about the request.
    * @return WP_Error|WP_REST_Response
    */
    public function get_items( $request ) {
        $ids = $request->get_param( 'ids' );
        $ids_not = $request->get_param( 'ids_not' );
        $posts_per_page = $request->get_param( 'posts_per_page' );
        $orderby = $request->get_param( 'orderby' );
        $order = $request->get_param( 'order' );

        if(!empty($ids)){
            $ids = explode(",", $ids);
            $post_args = array(
                'post_type' => 'listing',
                'posts_per_page'=> $posts_per_page,
                'post__in' => $ids,
                'orderby'=> $orderby,
                'order'=> $order,
                'post_status' => 'publish'
            );
        }elseif(!empty($ids_not)){
            $ids_not = explode(",", $ids_not);
            $post_args = array(
                'post_type' => 'listing',
                'posts_per_page'=> $posts_per_page,
                'post__not_in' => $ids_not,
                'orderby'=> $orderby,
                'order'=> $order,
                'post_status' => 'publish'
            );
        }else{
            $post_args = array(
                'post_type' => 'listing',
                'posts_per_page'=> $posts_per_page,
                'orderby'=> $orderby,
                'order'=> $order,
                'post_status' => 'publish'
            );
        }
        $items = get_posts($post_args);
        $listings = array();
        foreach( $items as $item ) {
            $itemdata = $this->prepare_listings_item( $item->ID );
            $listings[] = $this->prepare_response_for_collection( $itemdata );
        }
        return rest_ensure_response($listings);
        
    }

    public function get_archive( $request ) {
        // return rest_ensure_response( $request->get_params() );

        $ids = $request->get_param( 'ids' );
        $ids_not = $request->get_param( 'ids_not' );
        $posts_per_page = $request->get_param( 'posts_per_page' );
        $orderby = $request->get_param( 'orderby' );
        $order = $request->get_param( 'order' );
        $paged  = $request->get_param('paged', 1);

        if(!empty($ids)){
            $ids = explode(",", $ids);
            $post_args = array(
                'post_type' => 'listing',
                'posts_per_page'=> $posts_per_page,
                'post__in' => $ids,
                'orderby'=> $orderby,
                'order'=> $order,
                'paged'=> $paged,
                'post_status' => 'publish'
            );
        }elseif(!empty($ids_not)){
            $ids_not = explode(",", $ids_not);
            $post_args = array(
                'post_type' => 'listing',
                'posts_per_page'=> $posts_per_page,
                'post__not_in' => $ids_not,
                'orderby'=> $orderby,
                'order'=> $order,
                'paged'=> $paged,
                'post_status' => 'publish'
            );
        }else{
            $post_args = array(
                'post_type' => 'listing',
                'posts_per_page'=> $posts_per_page,
                'orderby'=> $orderby,
                'order'=> $order,
                'paged'=> $paged,
                'post_status' => 'publish'
            );
        }
        
        $tax_queries = array();
        $cats = $request->get_param( 'cats' );
        // if( !is_array($cats) && strpos($cats, '[') !== false ){
        //     $cats = json_decode($cats);
        // }
        if( !empty($cats) && is_array($cats) ){
            $tax_queries[] =    array(
                                    'taxonomy' => 'listing_cat',
                                    'field'    => 'term_id',
                                    'terms'    => $cats,
                                );
        }
        $locs = $request->get_param( 'locs' );
        if( !empty($locs) && is_array($locs) ){
            $tax_queries[] =    array(
                                    'taxonomy' => 'listing_location',
                                    'field'    => 'term_id',
                                    'terms'    => $locs,
                                );
        }
        $feas = $request->get_param( 'feas' );
        if( !empty($feas) && is_array($feas) ){
            $tax_queries[] =    array(
                                    'taxonomy' => 'listing_feature',
                                    'field'    => 'term_id',
                                    'terms'    => $feas,
                                    'operator' => 'AND',
                                );
        }
        $tags = $request->get_param( 'tags' );
        if( !empty($tags) && is_array($tags) ){
            $tax_queries[] =    array(
                                    'taxonomy' => 'listing_tag',
                                    'field'    => 'term_id',
                                    'terms'    => $tags,
                                    'operator' => 'AND',
                                );
        }
        if(!empty($tax_queries)){
            if( count($tax_queries) > 1 ) $tax_queries['relation'] = townhub_addons_get_option('search_tax_relation');
            $post_args['tax_query'] = $tax_queries;
        } 

        // meta queries
        $meta_queries = array();
        $ltype = $request->get_param( 'ltype' );
        if( !empty($ltype) && (int)$ltype > 0 ){
            $meta_queries[] =   array(
                                    'key'           => ESB_META_PREFIX.'listing_type_id',
                                    'value'         => intval($ltype),
                                    'type'          => 'NUMERIC'
                                );
                
        }

        // price_range filter
        $prices = $request->get_param( 'prices' );
        if( !empty($prices) && is_array($prices) ){
            if( count($prices) == 2 ){
                $meta_queries[] = array(
                    'key'     => '_price',
                    'value'   => $prices,
                    'type'    => 'numeric',
                    'compare' => 'BETWEEN',
                );
            }     
        }

        // if( isset($_POST['price_range']) && !empty($_POST['price_range'] ) ){
        //     $meta_queries[] =    array(
        //                                     'key' => ESB_META_PREFIX.'price_range',
        //                                     'value'    => $_POST['price_range'],
        //                                 );
                
        // }

        // query by date
        
        // if( isset($_POST['event_date']) && !empty($_POST['event_date'] ) ){
        //     // for changing event date
        //     $event_date_mysql = date('Y-m-d', strtotime($_POST['event_date']));
        //     $meta_queries[] =    array(
        //                                     'key'       => ESB_META_PREFIX.'levent_date',
        //                                     'value'     => $event_date_mysql,
        //                                     'compare'   => '>=',
        //                                     'type'      => 'DATE'
        //                                 );
        // }
        // if( isset($_POST['event_time']) && !empty($_POST['event_time'] ) ){
        //     $meta_queries[] =    array(
        //                                     'key'       => ESB_META_PREFIX.'levent_time',
        //                                     'value'     => $_POST['event_time'],
        //                                     'compare'   => '>=',
        //                                     'type'      => 'TIME'
        //                                 );
        // }

            
        $meta_queries = (array)apply_filters( 'townhub_mobile_add_meta_queries', $meta_queries );

        if(!empty($meta_queries)){
            if(count($meta_queries)> 1) $meta_queries['relation'] = 'AND';
            $post_args['meta_query'] = $meta_queries;
        } 
        

        // add filter for custom filter field
        $post_args = apply_filters( 'townhub_mobile_search_args', $post_args );

        $post_args['suppress_filters'] = false; // for additional wpdb query
        $post_args['cthqueryid'] = 'ajax-search';
        $posts_query = new WP_Query($post_args);
        $listings = array();
        if($posts_query->have_posts()): 
            while($posts_query->have_posts()) : $posts_query->the_post();
                
                $distance = '';
                if( isset($posts_query->post) && isset($posts_query->post->listing_distance) && !empty($posts_query->post->listing_distance) ) $distance = $posts_query->post->listing_distance;
                
                $itemdata = $this->prepare_listings_item( get_the_ID(), $distance );
                $listings[] = $this->prepare_response_for_collection( $itemdata );
            endwhile;
        endif;
        $numPages = $posts_query->max_num_pages - $paged;
        return rest_ensure_response(
            array(
                'items'     => $listings,
                'pages'     => $numPages,
            )
        );
        
    }
    public function get_item( $request ) {
        //get listing post 
        $id =  $request->get_param( 'id' );
        if(!$id){
            return array(
                'success'   =>  false,
                'error'     =>  __('Invalid listing id','townhub-mobile')
            );
        }
        $item = get_post($id);
        $this->set_ltype_id($id);
        $data = $this->prepare_listing_for_response($item, $request);
        // set view count
        Esb_Class_LStats::set_stats($id);
        return $data;
    }

    protected function set_ltype_id($id){
        $ltype_id = get_post_meta( $id, ESB_META_PREFIX.'listing_type_id', true );
        if( empty($ltype_id) ) $ltype_id = esb_addons_get_wpml_option('default_listing_type', 'listing_type');

        $this->ltype_id = $ltype_id;
    }

    public function prepare_listings_item( $ID, $distance = '' ){
        $this->set_ltype_id($ID);
        $rating = townhub_addons_get_average_ratings($ID);
        
        // rating.rating
        // rating.count
        if( $rating ){
            $rcount = (int)$rating['count'];
            $rateData = array(
                'base'              => (int)townhub_addons_get_option('rating_base'),
                'rating'            => (float)$rating['rating'],
                'count'             => $rcount,
                'count_text'        => sprintf( _nx( '%d review', '%d reviews', $rcount, 'reviews', 'townhub-mobile' ), $rcount ),
            );
        }else{
            $rateData = array(
                'base'              => (int)townhub_addons_get_option('rating_base'),
                'rating'            => 0,
                'count'             => 0,
                'count_text'        => ''
            );
        }

        $author_id = get_post_field( 'post_author', $ID, 'display' );

        $distanceVal = floatval($distance);
        $distanceUnit = _x( '%s km', 'Search distance', 'townhub-mobile' );
        if( cth_mobile_get_addons_option('distance_miles') == 'yes' ){
            $distanceUnit = _x( '%s mile', 'Search distance', 'townhub-mobile' );
            $distanceVal *= 0.621371;
        }


        $data = array(
            'ID'                            => $ID,
            'id'                            => $ID ,
            'ltype_id'                      => (int)$this->ltype_id,
            'rating'                        => $rateData,
            // 'author_avatar'             =>  $this->get_author_avatar($author_id),
            // // 'author_avatar'             =>  get_avatar($author_id,'40','https://0.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536?s=40'),
            'address'                       =>  get_post_meta( $ID, ESB_META_PREFIX.'address', true ),
            'latitude'                      =>  (float)get_post_meta( $ID, ESB_META_PREFIX.'latitude', true ),
            'longitude'                     =>  (float)get_post_meta( $ID, ESB_META_PREFIX.'longitude', true ),
            // '_cth_working_hours'        =>  $working_hours,
            'url'                           =>  get_the_permalink( $ID ),
            'title'                         =>  get_the_title($ID),
            
            'content'                       =>  get_the_content(null, false , $ID), // $item->post_content,
            'excerpt'                       => townhub_addons_the_excerpt_max_charlength(townhub_addons_get_option('excerpt_length','55'),false, $ID),
            'price'                         =>  (float)get_post_meta( $ID, '_price', true ),
            'thumbnail'                     =>  wp_get_attachment_url( townhub_addons_get_listing_thumbnail( $ID ) ), // get_the_post_thumbnail_url( $ID ),
            'distance'                      =>  $distance,
            'distance_with_unit'            =>  $distance != '' ? sprintf($distanceUnit, number_format($distanceVal,1)) : '',

            
            'isFeatured'                    => get_post_meta( $ID, ESB_META_PREFIX.'featured', true ) == '1' ? true : false,

            // showing on chat
            'author_id'                     => $author_id,
            'author_name'                   => get_the_author_meta( 'display_name', $author_id ),
            'viewCount'                     => (int)Esb_Class_LStats::get_stats($ID),

        );
        return $data;
    }

    public function prepare_listing_for_response($item, $request){
        $dayStart =  $request->get_param( 'start' );
        $id = $item->ID;
        // $header_type = get_post_meta($id,'_cth_headertype',false);
        // $address = get_post_meta( $id, ESB_META_PREFIX.'address', true );
        // $working_hours = get_post_meta( $id, ESB_META_PREFIX.'working_hours', true );
        // $thumbnail = get_the_post_thumbnail_url( $id );
        // $author_id = (int)$item->post_author;
        // $wkhour = townhub_addons_get_working_hours( $item->ID );

        

        $photos = get_post_meta( $id, ESB_META_PREFIX.'images', true );
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
        
        // rating.rating
        // rating.count
        $rating = townhub_addons_get_average_ratings($id);
        $rating_base = (int)townhub_addons_get_option('rating_base'); 
        if(empty($rating_base)) $rating_base = 5;
        if( $rating ){
            $rcount = (int)$rating['count'];
            $rateData = array(
                'base'              => $rating_base,
                'rating'            => (float)$rating['rating'],
                'count'             => $rcount,
                'count_text'        => sprintf( _nx( '%d review', '%d reviews', $rcount, 'reviews', 'townhub-mobile' ), $rcount ),
            );
        }else{
            $rateData = array(
                'base'              => $rating_base,
                'rating'            => 0,
                'count'             => 0,
                'count_text'        => ''
            );
        }


        // rating details
        $rateFields = array();
        $rating_fields = townhub_addons_get_rating_fields( $this->ltype_id );
        if( !empty($rating) && townhub_addons_get_option('show_score_rating') == '1' && townhub_addons_get_option('single_show_rating') == '1' ){
            if(!empty($rating_fields)) {
                foreach ((array)$rating_fields as $key => $field) {
                    $val = floatval( $rating['values'][$field['fieldName']] );
                    $rateFields[] = array(
                        'title'         => $field['title'],
                        'value'         => number_format($val, 1),
                        'percent'       => ($val/$rating_base)*100,
                    );
                }
            }
        }
        // add review
        $reviewFields = array();
        foreach ((array)$rating_fields as $rfield) {
            $r_base = isset($rfield['rating_base']) && $rfield['rating_base'] != '' ? intval( $rfield['rating_base'] ) : $rating_base;
            $r_default = isset($rfield['default']) && $rfield['default'] != '' ? intval( $rfield['default'] ) : $rating_base;
            $reviewFields[] = array(
                'title'         => $rfield['title'],
                'name'          => $rfield['fieldName'],
                'base'          => $r_base,
                'default'       => $r_default,
            );
        }

        $author_id = (int)get_post_field( 'post_author', $id, 'display' );

        $listingAvailability = $this->get_calendar( $id, $dayStart );
        $leventdate = get_post_meta( $id, ESB_META_PREFIX.'eventdate', true );
        $event_single = $leventdate != '' && $listingAvailability['checkAvailable'] === false;
        $data = array(
            'ID'                            => $id,
            'rating'                        => $rateData,
            'ratingFields'                  => $rateFields,
            'reviewFields'                  => $reviewFields,
            // first comments
            'comments'                      => $this->get_first_comments( $id ),
            // showing on chat
            'author_id'                     => $author_id,
            'author_name'                   => get_the_author_meta( 'display_name', $author_id ),
            'author_avatar'                 =>  $this->get_author_avatar($author_id),
            'author_lcouts'                 => sprintf(_x( '%d Places Hosted','Author listing count', 'townhub-mobile' ), count_user_posts( $author_id , "listing" , true ) ),
            'author_desc'                   => strip_tags( get_the_author_meta('description',$author_id) ),
            'author_phone'                  => get_user_meta( $author_id, '_cth_phone', true ),
            'author_address'                => get_user_meta( $author_id, '_cth_address', true ),
            'author_email'                  => get_user_meta( $author_id, '_cth_email', true ),

            // // 'author_avatar'             =>  get_avatar($author_id,'40','https://0.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536?s=40'),
            'address'                       => get_post_meta( $id, ESB_META_PREFIX.'address', true ),
            'latitude'                      => get_post_meta( $id, ESB_META_PREFIX.'latitude', true ),
            'longitude'                     => get_post_meta( $id, ESB_META_PREFIX.'longitude', true ),
            // '_cth_working_hours'        =>  $working_hours,
            'url'                           => get_the_permalink( $id ),
            'title'                         => get_the_title( $id ),
            'id'                            => $id ,
            'content'                       => get_the_content(null, false , $id), // $item->post_content,
            'excerpt'                       => townhub_addons_the_excerpt_max_charlength(townhub_addons_get_option('excerpt_length','55'),false, $id),
            'thumbnail'                     => wp_get_attachment_url( townhub_addons_get_listing_thumbnail( $id ) ), // get_the_post_thumbnail_url( $id ),
            'photos'                        => $photosData,
            // 'post_title'                =>  $item->post_title,
            // 'post_author'               =>  $item->post_author,
            // 'post_date'                 =>  $item->post_date,
            // 'headertype'                =>  $header_type,
            // 'headerimgs'                =>  $this->get_header_type($item->ID),
            // 'headerbg_youtube'          =>  get_post_meta($id,'_cth_headerbg_youtube',false),
            // 'headerbg_vimeo'            =>  get_post_meta($id,'_cth_headerbg_vimeo',false),
            // 'headerbg_mp4'              =>  get_post_meta($id,'_cth_headerbg_mp4',false),
            'cats'                          =>  $this->get_listing_cats( $id ),
            'features'                      =>  $this->get_listing_features( $id ),
            'tags'                          =>  $this->get_listing_tags( $id ),
            'facts'                         =>  $this->get_listing_facts( $id ),
            // for calendar
            'availability'                  =>  $listingAvailability,
            'quantities'                    => (int)get_post_meta( $id, ESB_META_PREFIX.'quantities', true ),
            // event single date
            'eventdate'                     => $leventdate,
            'event_single'                  => $event_single,
            // for booking
            'rooms'                         => $this->get_rooms( $id ),
            'price'                         => floatval( get_post_meta( $id, '_price', true ) ),
            'children_price'                => floatval( get_post_meta( $id, ESB_META_PREFIX.'children_price', true ) ),
            'infant_price'                  => floatval( get_post_meta( $id, ESB_META_PREFIX.'infant_price', true ) ),
            // price base - strings
            'price_based'                   => $event_single === true ? 'event_single' : get_post_meta( $this->ltype_id, ESB_META_PREFIX.'price_based', true ),
            // for calculate price
            'vat_tax'                       => apply_filters( 'esb_listing_vat', $this->vat_default(), $id ),
            'add_fees'                      => apply_filters( 'esb_listing_fees', townhub_addons_get_option('service_fee'), $id ),
            'tax_with_fees'                 => townhub_addons_get_option('booking_vat_include_fee') == 'yes' ? true : false,
            
            'evt_tickets'                   => Esb_Class_Booking_CPT::get_tickets($id),
            'lfaqs'                         => get_post_meta( $id, ESB_META_PREFIX.'lfaqs', true ),
            'lmembers'                      => $this->get_members( $id ),
            'lmenus'                        => $this->get_menus( $id ),
            'lmenus_cats'                   => $this->lmenus_cats,
            // per person
            'guests'                        => townhub_addons_listing_max_guests( $id ),

            // add services
            'lservices'                     => get_post_meta( $id, ESB_META_PREFIX.'lservices', true ),
            'payments'                      => townhub_addons_get_payments(),

            'allow_free_booking'            => get_post_meta( $this->ltype_id, '_apps_allow_free_booking', true ) == 'no' ? false : true,
            'free_hide_services'            => get_post_meta( $this->ltype_id, '_apps_free_hide_services', true ) == 'yes' ? true : false,

            // '_cth_gallery_imgs'         =>  $this->get_gallery_imgs($item->ID),
            // 'latitude'                  =>  get_post_meta( $id, ESB_META_PREFIX.'latitude', true ),
            // 'longitude'                 =>  get_post_meta( $id, ESB_META_PREFIX.'longitude', true ),
            // 'thumbnail'                 =>  $thumbnail,
            // 'phone'                     =>  get_post_meta( $id, ESB_META_PREFIX.'phone', true ),
            // // 'post_working_hours'        =>  townhub_addons_get_working_hours($item->ID),
            // 'icon'                      =>  $this->get_term_icon($item->ID),
            // 'cats'                      =>  $this->get_listings_cats($item->ID),
            // 'price'                     =>  get_post_meta( $item->ID , '_price', true ),
            // 'vat_percent'               =>  townhub_addons_get_option('vat_tax'),
            // 'price_from'                =>  get_post_meta(  $item->ID , ESB_META_PREFIX.'price_from', true ),
            // 'price_to'                  =>  get_post_meta(  $item->ID , ESB_META_PREFIX.'price_to', true ),
            // 'likeCount'                 =>  get_post_meta( $item->ID, "_cth_post_like_count", true ),
            // 'post_author'               =>  $item->post_author,
            // // 'status'                    =>  $wkhour['status'],
            // // 'statusText'                =>  $wkhour['statusText'],
            // 'views'                     =>  Esb_Class_LStats::get_stats($item->ID),
            // '_cth_slider_imgs'          =>  $this->get_slider_imgs($item->ID),
            // '_cth_feature'              =>  get_post_meta( $item->ID, ESB_META_PREFIX.'featured', true ),
            // 'verified'                  =>  get_post_meta( $item->ID , ESB_META_PREFIX.'verified', true ),
            // 'is_ad'                     =>  get_post_meta( $item->ID,ESB_META_PREFIX.'is_ad',true),
            // 'featured'                  =>  get_post_meta( $item->ID, ESB_META_PREFIX.'featured', true ),
            // 'locations'                 =>  get_the_terms( $item->ID, 'listing_location'),
            'isFeatured'                    => get_post_meta( $id, ESB_META_PREFIX.'featured', true ) == '1' ? true : false,
            'phone'                         => get_post_meta( $id, ESB_META_PREFIX.'phone', true ),
            'email'                         => get_post_meta( $id, ESB_META_PREFIX.'email', true ),
            'website'                       => get_post_meta( $id, ESB_META_PREFIX.'website', true ),

            'comment_reglogin'              => get_option('comment_registration') == 1 ? true : false,

            'disable_booking'               => get_post_meta( $this->ltype_id, '_apps_disable_booking', true ) == 'yes' ? true : false,
            'viewCount'                     => (int)Esb_Class_LStats::get_stats($id),
            'ltype_id'                      => (int)$this->ltype_id,
        );
        // $data['rating'] =  get_post_meta($item->ID, '_cth_'.'rating_average', true);
        // $data['rating_count'] = get_post_meta($item->ID, ESB_META_PREFIX.'rating_count', true);
        // $data['latitude'] = get_post_meta(  $item->ID , '_cth_latitude', true );
        // $data['longitude'] = get_post_meta(  $item->ID , '_cth_longitude', true );


        $data = (array)apply_filters( 'cth_mobile_slisting_datas', $data, $id, $request );

        return $data;
    }

    public function get_calendar($lid, $dayStart){
        // $lid =  $request->get_param( 'id' );
        // $dayStart =  $request->get_param( 'start' );
        // $ltype_id = get_post_meta( $lid, ESB_META_PREFIX.'listing_type_id', true );
        // if( empty($ltype_id) ) $ltype_id = esb_addons_get_wpml_option('default_listing_type', 'listing_type');

        $booking_type = get_post_meta( $this->ltype_id, '_apps_booking_type', true );
        $months_available = (int)get_post_meta( $this->ltype_id, '_apps_months_available', true );
        if( empty($booking_type) ) $booking_type = 'simple';
        if( empty($months_available) ) $months_available = 2;
        $mdMStr = "last day of +$months_available months";
        if( $months_available == 1 ) $mdMStr = "last day of +1 month";
        // get calendar dates
        $startDateObj = new DateTime($dayStart);
        if( !$startDateObj ){
            $startDateObj = new DateTime('now');
        }
        $dayStart = $startDateObj->format('Y-m-d');
        // $yStart = $startDateObj->format('Y');
        // $mStart = $startDateObj->format('m');
        // // $dStart = $startDateObj->format('d');
        // // get last day of last month
        // $dayEnd = (new DateTime( $yStart . '-' . ($mStart + $months_available - 1) . '-01' ) )->format('Y-m-t');
        // $dayEndObj = new DateTime($dayEnd);

        $dayEndObj = (new DateTime($dayStart))->modify($mdMStr);
        // error_log($dayEndObj->format('Y-m-d'));
        $dates = array();
        for ($i=0; $i < 1000 ; $i++) { 
            $temp = Esb_Class_Date::modify( $dayStart, $i, 'Y-m-d' );
            $tempObj = new DateTime($temp);
            if( $tempObj > $dayEndObj ) break;
            $dates[] = $temp;
        }
        if( $booking_type == 'hotel_rooms' ){
            $available = Esb_Class_Booking_CPT::get_hotel_availability($dates, $lid);
            $rooms_ids = get_post_meta( $lid, ESB_META_PREFIX.'rooms_ids', true );
            $checkAvailable =  empty($rooms_ids) ? false : true;
        }else{
            $available = Esb_Class_Booking_CPT::get_availability($dates, $lid);
            $checkAvailable = get_post_meta( $lid, ESB_META_PREFIX.'listing_dates', true ) == '' ? false : true;
        }
        $data = array(
            'bookingType' => $booking_type,
            'months_available' => $months_available,
            // 'dates' => $dates,
            'available' => $available,
            'checkAvailable' => $checkAvailable,

            // 'dayStart' => $dayStart,
            // 'dayEnd' => $dayEnd,
        );

        return $data;
        // return rest_ensure_response($data);

    }

    private function get_rooms($listing_id){
        $rooms = array();
        // get rooms data
        $rooms_ids = get_post_meta( $listing_id, ESB_META_PREFIX.'rooms_ids', true );
        if(!empty($rooms_ids) && is_array($rooms_ids)){
            foreach ($rooms_ids as $rid) {
                $rooms[] = array( 
                                'id'=> $rid, 
                                'title' => get_post_field( 'post_title', $rid), 
                                'adults' => get_post_meta($rid, ESB_META_PREFIX.'adults', true), 
                                'children' => get_post_meta($rid, ESB_META_PREFIX.'children', true), 
                                'price' => get_post_meta($rid, '_price', true) 
                            );
            }
        }

        return $rooms;
    }
    private function get_members($listing_id){
        $datas = array();
        // get rooms data
        $lmembers = get_post_meta( $listing_id, ESB_META_PREFIX.'lmember', true );
        if(!empty($lmembers) && is_array($lmembers)){
            foreach ($lmembers as $lmember) {

                $mem_img = '';
                if(!empty($lmember['id_image']) && !is_array( $lmember['id_image'] ) ){
                    $mem_img = $lmember['id_image'];
                }
                if( !empty($mem_img) ) $mem_img = wp_get_attachment_image_url( $mem_img, 'full' );
                if( false == $mem_img ) $mem_img = '';

                $datas[] = array( 
                                'avatar' => $mem_img, 
                                
                                'name' => !empty($lmember['name']) ? $lmember['name'] : '', 
                                'url' => !empty($lmember['url']) ? $lmember['url'] : '', 
                                'job' => !empty($lmember['job']) ? $lmember['job'] : '', 
                                'desc' => !empty($lmember['desc']) ? $lmember['desc'] : '', 
                                'socials' => !empty($lmember['socials']) ? $lmember['socials'] : array(), 
                            );
            }
        }

        return $datas;
    }

    private function get_menus($listing_id){
        $allCats = array();
        $datas = array();
        // get rooms data
        $resmenus = get_post_meta( $listing_id, ESB_META_PREFIX.'resmenus', true );
        if(!empty($resmenus) && is_array($resmenus)){
            foreach ($resmenus as $resmenu) {

                $ccats = array();
                if( isset($resmenu['cats']) && !empty($resmenu['cats']) ){
                    $ccats = explode(",", $resmenu['cats']);
                    $ccats = array_unique($ccats);
                    $ccats = array_values($ccats);
                }

                $allCats = array_merge($allCats, $ccats);


                $photos = isset($resmenu['photos']) ? $resmenu['photos'] : '';
                $thumbnail = '';
                $photos_gal = array();
                if( !empty($photos) && !is_array($photos) ){
                    $photos = explode(',', $photos);
                    foreach ($photos as $ik => $iid) {
                        if( $thumbnail == '' ) $thumbnail = wp_get_attachment_image_url($iid, 'thumbnail');
                        $photos_gal[] = wp_get_attachment_url( $iid );
                    }
                }
                $datas[] = array( 
                                'thumbnail'     => $thumbnail, 
                                'photos'        => $photos_gal, 
                                'cats'          => $ccats, 
                                
                                '_id'           => isset($resmenu['_id']) ? $resmenu['_id'] : 'lmenu', 
                                'name'          => !empty($resmenu['name']) ? $resmenu['name'] : '', 
                                'url'           => !empty($resmenu['url']) ? $resmenu['url'] : '', 
                                'price'         => !empty($resmenu['price']) ? (float)$resmenu['price'] : 0, 
                                'desc'          => !empty($resmenu['desc']) ? $resmenu['desc'] : '', 
                                'available'     => isset($resmenu['available']) ? (int)$resmenu['available'] : 10, 
                                'socials'       => !empty($resmenu['socials']) ? $resmenu['socials'] : array(), 
                            );
            }
        }

        $this->lmenus_cats = array();
        $allCats = array_unique($allCats);
        $this->lmenus_cats = array_values($allCats);
        return $datas;
    }

    private function vat_default(){
        return townhub_addons_get_option('vat_tax', 10);
    }

    private function get_listing_cats($id){
        $data = array();
        $terms = get_the_terms($id, 'listing_cat');
        if ( $terms && ! is_wp_error( $terms ) ){ 

            foreach( $terms as $key => $term){
                
                $data[] = array(
                    'id'            => $term->term_id,
                    'name'          => $term->name,
                    
                );
            }

        }
        return $data;
    }

    private function get_listing_features($id){
        $data = array();
        $terms = get_the_terms($id, 'listing_feature');
        if ( $terms && ! is_wp_error( $terms ) ){ 

            foreach( $terms as $key => $term){
                $term_metas = townhub_addons_custom_tax_metas($term->term_id, 'listing_feature'); 
                $data[] = array(
                    'id'            => $term->term_id,
                    'name'          => $term->name,
                    'icon'          => $term_metas['icon'],
                );
                // if(townhub_addons_get_option('feature_parent_group') == 'yes'){
                //     if($term->parent){
                //         if( !isset($feature_group[$term->parent]) || !is_array($feature_group[$term->parent]) ) $feature_group[$term->parent] = array();
                //         $feature_group[$term->parent][$term->term_id] = $term->name;
                //     }else{
                //         if(!isset($feature_group[$term->term_id])) $feature_group[$term->term_id] = $term->name;
                //     }
                // }else{
                //     if(!isset($feature_group[$term->term_id])) $feature_group[$term->term_id] = $term->name;
                // }
                    
            }

        }


        return $data;

    }

    private function get_listing_tags($id){
        $data = array();
        $terms = get_the_terms($id, 'listing_tag');
        if ( $terms && ! is_wp_error( $terms ) ){ 

            foreach( $terms as $key => $term){
                
                $data[] = array(
                    'id'            => $term->term_id,
                    'name'          => $term->name,
                    
                );
            }

        }
        return $data;
    }

    private function get_listing_facts($id){
        $data = array();
        $facts = get_post_meta( $id, ESB_META_PREFIX.'facts', true );
        if ( is_array( $facts) && !empty($facts)) {
            return $facts;
            // foreach( $facts as $fact){
            //     $data[] = array();
            // }
            
        }
        return $data;
    }
    

    private function get_first_comments($id){
        
        $comments = get_comments(
            array(
                'post_id'       => $id,
                'status'        => 'approve',
                'parent'        => 0,
                'number'        => '', // Maximum number of comments to retrieve. Default empty (no limit).
            )
        );
        // return $comments;
        
        $data = array();
        foreach ($comments as $comment) {
            // $comment = (object)$comment;
            // $comment->author = get_comment_author( $comment->comment_ID );
            $comment->avatar_url = get_avatar_url( $comment, array('size'=>50,'default'=>'https://0.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536?s=50') );
            $data[] = $comment;
        }
        return $data;
    }
    
    public function prepare_item_for_response( $item, $request ) {
        $rating = townhub_addons_get_average_ratings( $item->ID );
        // $wkhour = townhub_addons_get_working_hours( $item->ID );
        $price_range = get_post_meta(  $item->ID , ESB_META_PREFIX.'price_range', true );
        $levent_date = get_post_meta(  $item->ID , ESB_META_PREFIX.'levent_date', true );
        $levent_time = get_post_meta(  $item->ID , ESB_META_PREFIX.'levent_time', true );
        $like_count = get_comment_meta( $item->ID, "_cth_comment_like_count", true );
        $post_slider_images = get_post_meta($item->ID, '_cth_post_slider_images',true);

        $author_id = (int)$item->post_author;
        $data = array(
            'url'                       =>  get_the_permalink( $item->ID ),
            'title'                     =>  get_the_title(  $item->ID  ),
            'excerpt'                   =>  townhub_addons_the_excerpt_max_charlength(townhub_addons_get_option('excerpt_length','55'),false, $item->ID),
            'thumbnail'                 =>  '',
            'author_name'               =>  get_the_author_meta('display_name',(int)$author_id),
            'like_stats'                => townhub_addons_get_likes_stats( $item->ID ),
            'author_avatar'             => $this->get_author_avatar($author_id),
            'author_url'                => get_author_posts_url( get_the_author_meta( 'ID' )),
            'post_author'               =>  $item->post_author,
            'id'                        =>  $item->ID ,
            // 'status'                    => $wkhour['status'],
            // 'statusText'                => $wkhour['statusText'],
            'view'                      => Esb_Class_LStats::get_stats($item->ID),
            // 'excerpt'                   =>  get_the_excerpt($item->ID),
            'content'                   =>  get_post_field('post_content', $item->ID),
            'time'                      =>  get_the_time(get_option('date_format'),$item->ID),
            // 'tags'                      =>  $this->get_tags_post($item->ID),
            'page_header_bg'            =>  get_post_meta($item->ID, '_cth_page_header_bg',true),
            'show_page_header'          =>  get_post_meta( $item->ID, '_cth_show_page_header', true ),
            'post_slider_images'        =>  $post_slider_images ,
            'comments'                  =>  $this->get_comments_posts($item->ID)
        );

        $data['thumbnail'] = townhub_addons_get_attachment_thumb_link( townhub_addons_get_listing_thumbnail( $item->ID ) ,'townhub-listing-grid');

        // has_post_thumbnail(  $item->ID ) ? $data['thumbnail'] = get_the_post_thumbnail_url(  $item->ID , 'townhub-listing-grid' ):'';
        return $data;
    }
    /**
    * Check if a given request has access to get items
    *
    * @param WP_REST_Request $request Full data about the request.
    * @return WP_Error|bool
    */
    public function get_permissions_check( $request ) {
        if( cth_mobile_get_option('dis_auth') == 'yes' ) return true;
        $key = $request->get_header('Authorization');
        if($key == $this->api_key){
            return true;
        }
        return false;
    }

    /**
    * Check if a given request has access to get a specific item
    *
    * @param WP_REST_Request $request Full data about the request.
    * @return WP_Error|bool
    */
    // public function get_item_permissions_check( $request ) {
    //     return $this->get_items_permissions_check( $request );
    // }

    /**
    * Check if a given request has access to create items
    *
    * @param WP_REST_Request $request Full data about the request.
    * @return WP_Error|bool
    */
    public function create_permissions_check( $request ) {
        return $this->get_permissions_check( $request );
    }

    /**
    * Check if a given request has access to update a specific item
    *
    * @param WP_REST_Request $request Full data about the request.
    * @return WP_Error|bool
    */
    public function update_item_permissions_check( $request ) {
        return $this->create_item_permissions_check( $request );
    }

    /**
    * Check if a given request has access to delete a specific item
    *
    * @param WP_REST_Request $request Full data about the request.
    * @return WP_Error|bool
    */
    public function delete_item_permissions_check( $request ) {
        return $this->create_item_permissions_check( $request );
    }

    /**
    * Prepare the item for create or update operation
    *
    * @param WP_REST_Request $request Request object
    * @return WP_Error|object $prepared_item
    */
    protected function prepare_item_for_database( $request ) {
        return array();
    }

    /**
    * Prepare the item for the REST response
    *
    * @param mixed $item WordPress representation of the item.
    * @param WP_REST_Request $request Request object.
    * @return mixed
    */
    public function get_author_avatar($author_id){
        return get_avatar_url($author_id, array('size'=>150,'default'=>'https://0.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536?s=150') );
    }
    public function get_comments_posts($id){
        $comment = get_comments(array('post_id'=>$id,'parent'=>0));
        $comments = array();
        for($i=0;$i<count($comment);$i++){
            $comment_child = get_comments(array('post_id'=>$id,'parent'=>$comment[$i]->comment_ID));
            $comments_child = array();
            for($j=0;$j<count($comment_child);$j++){
                if($comment_child[$j]->comment_approved!=0){
                    array_push($comments_child,array(
                        'comment_author'    =>  $comment_child[$j]->comment_author,
                        'comment_content'   =>  $comment_child[$j]->comment_content,
                        'comment_date'      =>  get_comment_date(get_option('date_format'),$comment_child[$j]->comment_ID),
                        'comment_rating'    =>  get_comment_meta($comment_child[$j]->comment_ID,'rating',true),
                        'comment_author_avata' => $this->get_author_avatar($comment_child[$j]->user_id),
                        'rating_imgs'       =>  get_comment_meta($comment_child[$j]->comment_ID,'images',true),
                        'comment_parent'   =>   $comment_child[$j]->comment_parent,
                        'comment_ID'        =>  $comment_child[$j]->comment_ID,
                    ));
                }
            }
            if($comment[$i]->comment_approved!=0){
                array_push($comments,array(
                    'comment_author'    =>  $comment[$i]->comment_author,
                    'comment_content'   =>  $comment[$i]->comment_content,
                    'comment_date'      =>  get_comment_date(get_option('date_format'),$comment[$i]->comment_ID),
                    'comment_rating'    =>  get_comment_meta($comment[$i]->comment_ID,'rating',true),
                    'comment_author_avata' => $this->get_author_avatar($comment[$i]->user_id),
                    'rating_imgs'       =>  get_comment_meta($comment[$i]->comment_ID,'images',true),
                    'comment_parent'   =>   $comment[$i]->comment_parent,
                    'comment_ID'        =>  $comment[$i]->comment_ID,
                    'comment_child'     =>  $comments_child
                ));
            }
        };
        return $comments ;
    }
    
    
    /**
    * Get the query params for collections
    *
    * @return array
    */
    public function get_collection_params() {
        return array(
            'page'     => array(
                'description'       => 'Current page of the collection.',
                'type'              => 'integer',
                'default'           => 1,
                'sanitize_callback' => 'absint',
            ),
            'per_page' => array(
                'description'       => 'Maximum number of items to be returned in result set.',
                'type'              => 'integer',
                'default'           => 10,
                'sanitize_callback' => 'absint',
            ),
            'search'   => array(
                'description'       => 'Limit results to those matching a string.',
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            ),
        );
    }

    protected function upload_base64_image( $data, $post_id = 0, $headers = array() ){
        if ( empty( $data ) ) {
            return new WP_Error(
                'rest_upload_no_data',
                __( 'No data supplied.', 'townhub-mobile'),
                array( 'status' => 400 )
            );
        }

        $data = json_decode($data, true);

        if ( empty( $data['data'] ) ) {
            return new WP_Error(
                'rest_upload_no_data',
                __( 'No file data supplied.', 'townhub-mobile'),
                array( 'status' => 400 )
            );
        }

        if ( empty( $data['fname'] ) ) {
            return new WP_Error(
                'rest_upload_no_data',
                __( 'No file name supplied.', 'townhub-mobile'),
                array( 'status' => 400 )
            );
        }

        $filename = $data['fname'];


        $directory = "/".date('Y')."/".date('m')."/";
        $wp_upload_dir = wp_upload_dir();

        $data = base64_decode($data['data']);
        $fileurl = ABSPATH . "wp-content/uploads".$directory.$filename;
        $filetype = wp_check_filetype( basename( $fileurl), null );
        file_put_contents($fileurl, $data);

        $attachment = array(
            // 'guid' => $wp_upload_dir['url'] . '/' . basename( $fileurl ),
            'post_mime_type' => $filetype['type'],
            'post_title'     => sanitize_file_name(basename($fileurl)),
            'post_content'   => '',
            'post_status'    => 'inherit',
        );


        // Insert the attachment.
        $attach_id = wp_insert_attachment($attachment, $fileurl, $post_id);

        if ($attach_id != 0) {
            // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
            require_once ABSPATH . 'wp-admin/includes/image.php';

            // Generate the metadata for the attachment, and update the database record.
            $attach_data = wp_generate_attachment_metadata($attach_id, $fileurl);
            // return value from update_post_meta -  https://codex.wordpress.org/Function_Reference/update_post_meta
            // Returns meta_id if the meta doesn't exist, otherwise returns true on success and false on failure. NOTE: If the meta_value passed to this function is the same as the value that is already in the database, this function returns false.
            wp_update_attachment_metadata($attach_id, $attach_data);

            // $user_metas['custom_avatar'] = array( $attach_id => wp_get_attachment_url( $attach_id ) );
            return $attach_id;
        } else {
            return new WP_Error(
                'rest_upload_sideload_error',
                __("wp_insert_attachment error on custom avatar upload image", 'townhub-mobile'),
                array( 'status' => 500 )
            );

        }

    }
}



add_action( 'rest_api_init', function () {
    TownHub_Custom_Route::getInstance();
    // $ctb_rest_api_route = new TownHub_Custom_Route();

    // $ctb_rest_api_route->register_routes();
} );



