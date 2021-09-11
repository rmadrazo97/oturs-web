<?php 

class TownHub_Categories_Route extends TownHub_Custom_Route {
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
            '/' . $this->rest_base . '/categories', 
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_items' ),
                    'permission_callback' => array( $this, 'get_permissions_check' ),
                    'args'                => array(),
                ),
            ) 
        );
    }

    public function get_items($request){
        $number = $request->get_param('number', 0);
        $posts = get_terms( array(
            'taxonomy'      => 'listing_cat',
            'hide_empty'    => true,
            'number'        => $number,
        ) );
        
        $data = $this->prepare_taxes( $posts, $request );
        return rest_ensure_response($data);
    }
    // public function prepare_item_for_response($post, $dfthumb = '', $request ){
    //     $term_metas = townhub_addons_custom_tax_metas($post->term_id, 'listing_cat'); 
    //     if( !empty($term_metas['featured_url']) ) $dfthumb = $term_metas['featured_url'];
    //     $data = array(
    //         'id'            => $post->term_id,
    //         'title'         => $post->name,
    //         'count'         => $post->count,
    //         'thumbnail'     => $dfthumb,
    //         'icon'          => $term_metas['icon'],
    //         'color'         => $term_metas['color'],
            
    //     );

    //     return rest_ensure_response($data);
    // }


}


add_action( 'rest_api_init', function () {
    TownHub_Categories_Route::getInstance();
} );
