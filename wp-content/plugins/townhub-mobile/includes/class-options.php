<?php

if( class_exists('Esb_Class_Options') ) {
	class CTHMobile_Options extends Esb_Class_Options {

        private $slug; // settings page slug 
        private $page; // settings page slug
        protected $options; // global options

        private $permalinks = array();

        public $plugin_url;
        public $plugin_path;

        protected $menu_slug = 'cthmobile';

        protected $option_options = array();

        protected $current_section = 'default-section-id';

        function __construct() {
            
            $this->option_name     = $this->menu_slug.'-options';

            // If the theme options don't exist, create them.
            if( false == get_option( $this->option_name ) ) {  
                add_option( $this->option_name , array());
            }else{
                $this->options = get_option( $this->option_name );
            } 

            // var_dump($this->options);
            // end if
            $this->plugin_info();
            $this->init();
        }
        function init() {
            add_action( 'admin_menu', array($this, 'add_pagemenu') );
            add_action( 'admin_init', array($this, 'register_settings') );
        }
        function add_pagemenu(){
        	$this->slug = add_menu_page(
                __( 'TownHub Mobile Apps', 'townhub-mobile' ), 
                __( 'TownHub Mobile Apps', 'townhub-mobile' ), 
                'manage_options', 
                $this->menu_slug, 
                array($this, 'view_admin_settings'), 
                'dashicons-admin-generic'
            );
        }
        function register_settings() {
            
            
            $this->options  = get_option($this->option_name, array());

            // https://developer.wordpress.org/reference/functions/register_setting/
            // string $option_group, string $option_name, array $args = array()
            register_setting($this->option_name, $this->option_name, array($this, 'sanitize_settings') );

            if(!empty($this->option_options)){
                foreach ($this->option_options as $field) {
                    $this->register_setting_field_new($field);
                }
            }

        }

        function register_setting_field_new($field = array()){
            $default = array(
                'type' => 'section', // section or field
                'field_type' => 'text', // field type for register field only
                'id' => 'default-section-id', // section or field id
                'title' => __( 'Default title', 'townhub-mobile' ), // section or field title
                // 'callback' => '__return_false', // section or field callback for echo its output
                // 'section_id' => 'default-section-id', // for setting field only // use current_section instead
                // 'args' => array(
                //     'id' => 'default-section-id', // id of the field
                //     'desc' => __( 'Default description', 'townhub-add-ons' ), // id of the field
                // ), // for setting field only

                'desc' => '', // desc of the field

                'args' => array(
                    // 'options' => array(key=>value) for radio
                )
            );

            $field = array_merge($default, $field);

            if($field['type'] == 'section'){
                if(!isset($field['callback'])) $field['callback'] = '__return_false';
                // $page_tab = $this->menu_slug.'_widgets-options';
                // $id, $title, $callback, $page
                // $page - The menu page on which to display this section. Should match $menu_slug from Function Reference/add theme page if you are adding a section to an 'Appearance' page, or Function Reference/add options page if you are adding a section to a 'Settings' page.
                add_settings_section( $field['id'], $field['title'], $field['callback'], $this->menu_slug );

                $this->current_section = $field['id'] ;
            }
            if($field['type'] == 'field'){
                if(!isset($field['callback'])){
                    switch ($field['field_type']) {
                        case 'text':
                            $field['callback'] = array($this, 'settings_field_input');
                            break;
                        case 'number':
                            $field['callback'] = array($this, 'settings_field_number');
                            break;
                        case 'textarea':
                            $field['callback'] = array($this, 'settings_field_textarea');
                            break;
                        case 'editor':
                            $field['callback'] = array($this, 'settings_field_editor');
                            break;
                        case 'checkbox':
                            $field['callback'] = array($this, 'settings_field_checkbox');
                            break;
                        case 'radio':
                            $field['callback'] = array($this, 'settings_field_radio');
                            break;
                        case 'select':
                            $field['callback'] = array($this, 'settings_field_select');
                            break;
                        case 'info':
                            $field['callback'] = array($this, 'settings_field_info');
                            break;
                        case 'page_select':
                            $field['callback'] = array($this, 'settings_field_page_select');
                            break;
                        case 'user_select':
                            $field['callback'] = array($this, 'settings_field_user_select');
                            break;
                        case 'image':
                            $field['callback'] = array($this, 'settings_field_image');
                            break;
                        case 'repeat_content':
                            $field['callback'] = array($this, 'settings_field_repeat_content');
                            break;
                        case 'repeat_widget':
                            $field['callback'] = array($this, 'settings_field_repeat_widget');
                            break;
                        case 'currencies':
                            $field['callback'] = array($this, 'settings_field_currencies');
                            break;
                        case 'color':
                            $field['callback'] = array($this, 'settings_field_color');
                            break;
                        case 'lfeatures':
                            $field['callback'] = array($this, 'settings_field_lfeatures');
                            break;
                        case 'cth_tags':
                            $field['callback'] = array($this, 'settings_field_cth_tags');
                            break;
                        case 'repeat_key_mobileapp':
                            $field['callback'] = array($this, 'settings_field_repeat_key_mobile_app');
                            break;
                        case 'toggle_chat':
                            $field['callback'] = array($this, 'settings_field_toggle_chat');
                            break;
                        default:
                            $field['callback'] = array($this, 'settings_field_input');
                            break;
                    }
                }
                // $id, $title, $callback, $page, $section, $args
                add_settings_field(
                    $field['id'], 
                    $field['title'], 
                    $field['callback'], 
                    $this->menu_slug, 
                    $this->current_section, 
                    array_merge(array(
                        'id'    => $field['id'], 
                        'desc'  => $field['desc']
                    ), $field['args'])
                );
            }
        }

        // function settings_field_input($args) {
        //     if(!isset($args['id'])) return;
        //     $id = $args['id'];
        //     $desc = isset($args['desc'])? $args['desc'] : '';
        //     $value = isset($this->options[$id]) ? $this->options[$id] : (isset($args['default']) ? $args['default'] : '');
        //     var_dump($this->options);
        //     var_dump($id);
        //     echo "<input id='$id' name='{$this->option_name}[{$id}]' size='40' type='text' value='{$value}' />";
        //     echo "<p class='description'>$desc</div>";

        // }

        function view_admin_settings() {
            ?>
            <div class="wrap">
    
                <div id="icon-options-general" class="icon32"></div>
                <h2><?php _e( 'TownHub Mobile Apps Settings', 'townhub-mobile' ); ?></h2>
                <form action="options.php" method="post" id="cth-mobile-options-form">
                <?php
                // $slug = $this->option_name;
                // A settings group name. This should match the group name used in register_setting().
                settings_fields($this->option_name); // Output nonce, action, and option_page fields for a settings page
                // $page - The slug name of the page whose settings sections you want to output. This should match the page name used in add_settings_section().
                do_settings_sections( $this->menu_slug );

                submit_button();
                ?>
                </form>

            </div>
            <?php
        }
        function plugin_info() {
            // get option array from includes/option_values.php function
            $this->option_options = array(
            	array(
	                "type" => "section",
	                'id' => 'gen_sec_1',
	                "title" => __( 'General', 'townhub-mobile' ),     
	            ),
	            array(
	                "type" 			=> "field",
	                "field_type" 	=> "text",
	                'id' 			=> 'app_key',
	                'args' 			=> array(
	                    'default'      => 'MTVkemkxcG9jOGptM3ZpZDJiMnlmaQXXX',
	                ),
	                "title" 		=> __('App Key', 'townhub-mobile'),
	                'desc'  		=> __('For REST API request authentication', 'townhub-mobile'),
	            ),
                array(
                    "type"          => "field",
                    "field_type"    => "checkbox", 
                    'id'            => 'dis_auth',
                    'args'          => array(
                        'default'   => 'no',
                        'value'     => 'yes',
                    ),
                    "title"         => __('Disable request authentication', 'townhub-mobile'),  
                    'desc'          => __( 'Use for testing only.', 'townhub-mobile' ),
                ),
	            array(
	                "type" 			=> "field",
	                "field_type" 	=> "page_select",
	                'id' 			=> 'explore_page',
	                "title" 		=> __('Explore Page', 'townhub-mobile'),
	                'desc'  		=> __('The page will be used for explore page on app', 'townhub-mobile'),
	                'args' 			=> array(
	                    'default_title' => "Mobile App",
	                )
	            ),

                array(
                    "type"          => "field",
                    "field_type"    => "page_select",
                    'id'            => 'terms_page',
                    "title"         => __('Terms and conditions page', 'townhub-mobile'),
                    'desc'          => __('The page will be used for Terms and conditions menu on profile screen', 'townhub-mobile'),
                    'args'          => array()
                ),

                array(
                    "type"          => "field",
                    "field_type"    => "page_select",
                    'id'            => 'policy_page',
                    "title"         => __('Privacy policy page', 'townhub-mobile'),
                    'desc'          => __('The page will be used for Privacy policy menu on profile screen', 'townhub-mobile'),
                    'args'          => array()
                ),
                array(
                    "type"          => "field",
                    "field_type"    => "page_select",
                    'id'            => 'help_page',
                    "title"         => __('Help center page', 'townhub-mobile'),
                    'desc'          => __('The page will be used for Help center menu on profile screen', 'townhub-mobile'),
                    'args'          => array()
                ),
                array(
                    "type"          => "field",
                    "field_type"    => "page_select",
                    'id'            => 'about_page',
                    "title"         => __('About us page', 'townhub-mobile'),
                    'desc'          => __('The page will be used for About us menu on profile screen', 'townhub-mobile'),
                    'args'          => array()
                ),
                array(
                    "type"          => "field",
                    "field_type"    => "checkbox", 
                    'id'            => 'woo_payment',
                    'args'          => array(
                        'default'   => 'no',
                        'value'     => 'yes',
                    ),
                    "title"         => __('Enable WooCommerce Payment', 'townhub-mobile'),  
                    'desc'          => __( 'Users will be redirect to website WooCommerce checkout page to complete payment.', 'townhub-mobile' ),
                ),
	        );
        }

    }

    new CTHMobile_Options();
}
