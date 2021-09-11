<?php

azp_add_element(
    'app_listings',
    array(
        'name'                    => __('Listings', 'townhub-mobile'),
        'category'                => 'Mobile App',
        'icon'                    => ESB_DIR_URL . 'assets/azp-eles-icon/cththemes-logo.png',
        'open_settings_on_create' => true,
        'showStyleTab'            => false,
        'showTypographyTab'       => false,
        'showAnimationTab'        => false,
        'template_folder'         => 'apps/',
        'is_section'              => true,
        'attrs'                   => array(
            array(
                'type'       => 'text',
                'param_name' => 'title',
                'label'      => __('Title', 'townhub-mobile'),
                // 'desc'                  => '',
                'default'    => 'Discover featured listings',
                'show_in_admin' => true,
            ),

            array(
                'type'          => 'switch',
                'param_name'    => 'show_view_all',
                'label'         => __('Show View all?', 'townhub-mobile'),
                'desc'          => '',
                'default'       => 'no',
                'value'         => array(
                    'yes' => __('Yes', 'townhub-mobile'),
                    'no'  => __('No', 'townhub-mobile'),
                ),
            ),

            array(
                'type'       => 'text',
                'param_name' => 'viewall_text',
                'label'      => __('View all text', 'townhub-mobile'),
                // 'desc'                  => '',
                'default'    => 'View all',
            ),

            array(
                'type'          => 'select',
                'param_name'    => 'ele_layout',
                'show_in_admin' => true,
                'label'         => __('Layout', 'townhub-mobile'),
                'default'       => 'slider',
                'value'         => array(
                    'slider'    => __('Slider', 'townhub-mobile'),
                    'carousel'  => __('Carousel', 'townhub-mobile'),
                    'list'      => __('List', 'townhub-mobile'),
                    'grid'      => __('Grid', 'townhub-mobile'),
                ),
            ),

            

            array(
                'type'       => 'text',
                'param_name' => 'cat_ids',
                'label'      => __('Listing Categories', 'townhub-mobile'),
                'desc'       => __( "Enter listing category's ids to get listings from, separated by a comma (,).", 'townhub-mobile' ),
                'default'    => '',
            ),
            array(
                'type'       => 'text',
                'param_name' => 'loc_ids',
                'label'      => __('Listing Locations', 'townhub-mobile'),
                'desc'       => __( "Enter listing location's ids to get listings from, separated by a comma (,).", 'townhub-mobile' ),
                'default'    => '',
            ),
            array(
                'type'       => 'text',
                'param_name' => 'tag_ids',
                'label'      => __('Listing Tags', 'townhub-mobile'),
                'desc'       => __( "Enter listing tag's ids to get listings from, separated by a comma (,).", 'townhub-mobile' ),
                'default'    => '',
            ),
            array(
                'type'       => 'text',
                'param_name' => 'ids',
                'label'      => __('Enter Listing IDs', 'townhub-mobile'),
                'desc'       => __( 'Enter Post ids to show, separated by a comma (,). Leave empty to show all.', 'townhub-mobile' ),
                'default'    => '',
            ),
            array(
                'type'       => 'text',
                'param_name' => 'ids_not',
                'label'      => __('Or Post IDs to Exclude', 'townhub-mobile'),
                'desc'       => __( 'Enter post ids to exclude, separated by a comma (,). Use if the field above is empty.', 'townhub-mobile' ),
                'default'    => '',
            ),

            array(
                'type'          => 'switch',
                'param_name'    => 'featured_only',
                'show_in_admin' => true,
                'label'         => __('Show featured listings only?', 'townhub-mobile'),
                'desc'          => '',
                'default'       => 'no',
                'value'         => array(
                    'yes' => __('Yes', 'townhub-mobile'),
                    'no'  => __('No', 'townhub-mobile'),
                ),
            ),

            

            array(
                'type'          => 'select',
                'param_name'    => 'orderby',
                'show_in_admin' => true,
                'label'         => __('Order by', 'townhub-mobile'),
                'default'       => 'date',
                'value'         => array(

                    'date' => esc_html__('Date', 'townhub-mobile'), 
                    'ID' => esc_html__('ID', 'townhub-mobile'), 
                    'author' => esc_html__('Author', 'townhub-mobile'), 
                    'title' => esc_html__('Title', 'townhub-mobile'), 
                    'modified' => esc_html__('Modified', 'townhub-mobile'),
                    'rand' => esc_html__('Random', 'townhub-mobile'),
                    'comment_count' => esc_html__('Comment Count', 'townhub-mobile'),
                    'menu_order' => esc_html__('Menu Order', 'townhub-mobile'),
                    'post__in' => esc_html__('ID order given (post__in)', 'townhub-mobile'),
                    'listing_featured' => esc_html__('Listing Featured', 'townhub-mobile'),
                ),
            ),
            array(
                'type'          => 'select',
                'param_name'    => 'order',
                'show_in_admin' => true,
                'label'         => __('Sort Order', 'townhub-mobile'),
                'default'       => 'DESC',
                'value'         => array(
                    'ASC'  => __('Ascending', 'townhub-mobile'),
                    'DESC' => __('Descending', 'townhub-mobile'),
                ),
            ),

            array(
                'type'       => 'text',
                'param_name' => 'posts_per_page',
                'label'      => __('Posts to show', 'townhub-mobile'),
                'desc'       => __( 'Number of posts to show (-1 for all).', 'townhub-mobile' ),
                'default'    => '12',
            ),
            

            // array(
            //     'type'       => 'text',
            //     'param_name' => 'el_id',
            //     'label'      => __('Element ID', 'townhub-mobile'),
            //     // 'desc'                  => '',
            //     'default'    => '',
            // ),

            // array(
            //     'type'       => 'text',
            //     'param_name' => 'el_class',
            //     'label'      => __('Extra Class', 'townhub-mobile'),
            //     'desc'       => __("Use this field to add a class name and then refer to it in your CSS.", 'townhub-mobile'),
            //     'default'    => '',
            // ),

        ),
    )
);


        