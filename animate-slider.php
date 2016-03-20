<?php
/**
 * Plugin Name: Animate Slider by Cybercraft Technologies
 * Plugin URI:
 * Description: A flexible and easy slideshow maker
 * Version: 1.0
 * Author: Mithu A Quayium
 * Author URI:
 * Requires at least: 4.1
 * Tested up to: 4.4
 *
 * Text Domain: wpas
 */

define( 'WPAS_VERSION', '1.0' );
define( 'WPAS_FILE', __FILE__ );
define( 'WPAS_ROOT', dirname( __FILE__ ) );
define( 'WPAS_ROOT_URI', plugins_url( '', __FILE__ ) );
define( 'WPAS_ASSET_URI', WPAS_ROOT_URI . '/assets' );

class WPAS_Init {

    function __construct() {
        add_action( 'wp_enqueue_scripts' , array( $this, 'wp_scripts_styles' ) );
        add_action( 'admin_enqueue_scripts' , array( $this, 'admin_scripts_styles' ) );

        $this->includes();
    }


    /**
     * Scripts and styles in frontend
     */
    function wp_scripts_styles() {
        wp_enqueue_script( 'wpas-vue-js', WPAS_ASSET_URI.'/js/vue.js' );
    }

    /**
     * Scripts and styles in admin panel
     */

    function admin_scripts_styles() {
        wp_enqueue_style( 'wpas-css', WPAS_ASSET_URI.'/css/style.css' );
        wp_enqueue_script( 'wpas-vue-js', WPAS_ASSET_URI.'/js/vue.js' );
    }

    /**
     * Includes necessary files
     */
    function includes() {
        require_once 'inc/slider.php';
        require_once 'inc/shortcode.php';

        if ( is_admin() ) {
            require_once 'inc/admin/slider-meta.php';
            require_once 'inc/admin/slide-meta.php';
        }
    }



    public static function init() {
        new WPAS_Init();
    }
}

WPAS_Init::init();