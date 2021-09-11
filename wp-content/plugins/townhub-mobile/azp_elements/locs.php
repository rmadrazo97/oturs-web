<?php

azp_add_element(
    'app_locs',
    array(
        'name'                    => __('Locations', 'townhub-mobile'),
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
                'default'    => 'Explore best cities',
                'show_in_admin' => true,
            ),

            array(
                'type'          => 'switch',
                'param_name'    => 'show_view_all',
                'label'         => __('Show View all?', 'townhub-mobile'),
                'desc'          => '',
                'default'       => 'yes',
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
                'default'       => 'grid',
                'value'         => array(
                    'carousel'  => __('Carousel', 'townhub-mobile'),
                    'grid'      => __('Grid', 'townhub-mobile'),
                ),
            ),

            
            array(
                'type'       => 'text',
                'param_name' => 'cat_ids',
                'label'      => __('Locations IDs', 'townhub-mobile'),
                'desc'       => __('Enter Locations ids to include, separated by a comma (,). Leave empty to show all.', 'townhub-mobile' ),
                'default'    => '',
            ),
            array(
                'type'       => 'text',
                'param_name' => 'cat_ids_not',
                'label'      => __('Or Locations IDs to Exclude', 'townhub-mobile'),
                'desc'       => __( 'Enter Locations ids to include, separated by a comma (,).', 'townhub-mobile' ),
                'default'    => '',
            ),

            array(
                'type'          => 'switch',
                'param_name'    => 'hide_empty',
                'show_in_admin' => true,
                'label'         => __('Hide Empty', 'townhub-mobile'),
                'desc'          => esc_html__('Hide categories  has no listings assigned to.', 'townhub-mobile'),
                'default'       => 'yes',
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
                'default'       => 'count',
                'value'         => array(

                    'name' => esc_html__('Name', 'townhub-mobile'), 
                    'slug' => esc_html__('Slug', 'townhub-mobile'), 
                    'term_group' => esc_html__('Term Group', 'townhub-mobile'), 
                    'term_id' => esc_html__('Term ID', 'townhub-mobile'), 
                    'description' => esc_html__('Description', 'townhub-mobile'),
                    'parent' => esc_html__('Parent', 'townhub-mobile'),
                    'count' => esc_html__('Term Count', 'townhub-mobile'),
                    'include' => esc_html__('For Include above', 'townhub-mobile'),
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
                'param_name' => 'number',
                'label'      => __('Numbers show', 'townhub-mobile'),
                'desc'       => __( 'Number of Categories to show (0 for all).', 'townhub-mobile' ),
                'default'    => '6',
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
