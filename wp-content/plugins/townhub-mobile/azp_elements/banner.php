<?php

azp_add_element(
    'app_banner',
    array(
        'name'                    => __('Image Banner', 'townhub-mobile'),
        'category'                => 'Mobile App',
        'icon'                    => ESB_DIR_URL . 'assets/azp-eles-icon/cththemes-logo.png',
        'open_settings_on_create' => true,
        'showStyleTab'            => false,
        'showTypographyTab'       => false,
        'showAnimationTab'        => false,
        'template_folder'         => 'apps/',
        'is_section'              => true,
        'attrs'                   => array(
            // array(
            //     'type'       => 'text',
            //     'param_name' => 'title',
            //     'label'      => __('Title', 'townhub-mobile'),
            //     // 'desc'                  => '',
            //     'default'    => '',
            // ),


            array(
                'type'          => 'image',
                'param_name'    => 'src',
                'label'         => __('Banner Image', 'townhub-mobile'),
                // 'show_in_admin' => true,
                'desc'          => '',
                'default'       => '',
            ),

            array(
                'type'          => 'text',
                'param_name'    => 'width',
                'label'         => __('Banner Width', 'townhub-mobile'),
                // 'desc'                  => '',
                'default'       => '',
            ),

            array(
                'type'          => 'text',
                'param_name'    => 'height',
                'label'         => __('Banner Height', 'townhub-mobile'),
                // 'desc'                  => '',
                'default'       => '150',
            ),

            array(
                'type'          => 'text',
                'param_name'    => 'url',
                'label'         => __('Banner Link URL', 'townhub-mobile'),
                // 'desc'                  => '',
                'default'       => '',
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
