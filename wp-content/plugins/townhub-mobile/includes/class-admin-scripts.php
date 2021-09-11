<?php

defined('ABSPATH') || exit;

class CTHMB_Class_Admin_Scripts
{

    private static $plugin_url;

    public static function init()
    {
        self::$plugin_url = plugin_dir_url(CTH_MOBILE_FILE);
        add_action('admin_enqueue_scripts', array(get_called_class(), 'enqueue_scripts'));
    }
    
    private static function enqueue_react_libraries()
    {
        wp_enqueue_script('react', self::$plugin_url . "assets/js/react.production.min.js", array(), null, true);
        wp_enqueue_script('react-dom', self::$plugin_url . "assets/js/react-dom.production.min.js", array(), null, true);
        wp_enqueue_script('react-router-dom', self::$plugin_url . "assets/js/react-router-dom.min.js", array(), null, true);
        wp_enqueue_script('redux', self::$plugin_url . "assets/js/redux.min.js", array(), null, true);
        wp_enqueue_script('react-redux', self::$plugin_url . "assets/js/react-redux.min.js", array(), null, true);
        wp_enqueue_script('redux-thunk', self::$plugin_url . "assets/js/redux-thunk.min.js", array(), null, true);
        wp_enqueue_script('qs', self::$plugin_url . "assets/js/qs.js", array(), null, true);
        wp_enqueue_script('axios', self::$plugin_url . "assets/js/axios.min.js", array(), null, true);
        wp_enqueue_script('Sortable', self::$plugin_url . "assets/js/Sortable.min.js", array(), null, true);
        wp_enqueue_script('react-sortable', self::$plugin_url . "assets/js/react-sortable.min.js", array(), null, true);
        wp_enqueue_script('jquery-scrolltofixed', self::$plugin_url . "assets/js/jquery-scrolltofixed-min.js", array(), null, true);

    }

    public static function enqueue_scripts($hook)
    {
        wp_enqueue_style('cth-mobileapp', self::$plugin_url . 'assets/css/style.css');
        $screen = get_current_screen();
        if ($screen->base == 'post' && $screen->id == 'page' ) {
            global $post;
            if( isset($post->ID) && cth_mobile_get_wpml_option('explore_page') == $post->ID ){
                wp_enqueue_script('react', self::$plugin_url . "assets/js/react.production.min.js", array(), null, true);
                wp_enqueue_script('react-dom', self::$plugin_url . "assets/js/react-dom.production.min.js", array(), null, true);
                wp_enqueue_script('react-router-dom', self::$plugin_url . "assets/js/react-router-dom.min.js", array(), null, true);
                wp_enqueue_script('redux', self::$plugin_url . "assets/js/redux.min.js", array(), null, true);
                wp_enqueue_script('react-redux', self::$plugin_url . "assets/js/react-redux.min.js", array(), null, true);
                wp_enqueue_script('redux-thunk', self::$plugin_url . "assets/js/redux-thunk.min.js", array(), null, true);
                wp_enqueue_script('qs', self::$plugin_url . "assets/js/qs.js", array(), null, true);
                wp_enqueue_script('axios', self::$plugin_url . "assets/js/axios.min.js", array(), null, true);
                wp_enqueue_script('Sortable', self::$plugin_url . "assets/js/Sortable.min.js", array(), null, true);
                wp_enqueue_script('react-sortable', self::$plugin_url . "assets/js/react-sortable.min.js", array(), null, true);
                    
                wp_enqueue_script('cth-mobileapp', self::$plugin_url . "assets/js/townhub-mobile-app.min.js", array('underscore'), null, true);

                $_townhub_add_ons_adminapp = array(
                    'i18n'         => array(

                    ),
                    // azp elements
                    'azp_elements' => AZPElements::getEles(),
                );
              
                wp_localize_script('cth-mobileapp', '_townhub_add_ons_adminapp', $_townhub_add_ons_adminapp);
            }
        }
        
    }
}

CTHMB_Class_Admin_Scripts::init();
