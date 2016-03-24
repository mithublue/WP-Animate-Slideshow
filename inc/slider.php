<?php

class WPAS_slider_post_type {

    public function __construct() {
        add_action( 'init' , array( $this, 'register_post_type') );
        add_action( 'admin_menu', array( $this , 'add_submenu_page' ) );
    }


    /**
     * wpas_slider post type register
     */
    public function register_post_type() {
        $labels = array(
            'name'               => _x( 'Slideshow', 'post type general name', 'wpas' ),
            'singular_name'      => _x( 'Slideshow', 'post type singular name', 'wpas' ),
            'menu_name'          => _x( 'Slideshow', 'admin menu', 'wpas' ),
            'name_admin_bar'     => _x( 'Slideshow', 'add new on admin bar', 'wpas' ),
            'add_new'            => _x( 'Add New', 'Slideshow', 'wpas' ),
            'add_new_item'       => __( 'Add New Slideshow', 'wpas' ),
            'new_item'           => __( 'New Slideshow', 'wpas' ),
            'edit_item'          => __( 'Edit Slideshow', 'wpas' ),
            'view_item'          => __( 'View Slideshow', 'wpas' ),
            'all_items'          => __( 'All Slideshows', 'wpas' ),
            'search_items'       => __( 'Search Slideshows', 'wpas' ),
            'parent_item_colon'  => __( 'Parent Slideshows:', 'wpas' ),
            'not_found'          => __( 'No slideshow found.', 'wpas' ),
            'not_found_in_trash' => __( 'No slideshow found in Trash.', 'wpas' )
        );

        $args = array(
            'labels'             => $labels,
            'description'        => __( 'Description.', 'wpas' ),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'wpas-slider' ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array( 'title' )
        );

        register_post_type( 'wpas_slider', $args );

        /**
         * wpas_slide
         */
        $labels = array(
            'name'               => _x( 'Slide', 'post type general name', 'wpas' ),
            'singular_name'      => _x( 'Slide', 'post type singular name', 'wpas' ),
            'menu_name'          => _x( 'Slide', 'admin menu', 'wpas' ),
            'name_admin_bar'     => _x( 'Slide', 'add new on admin bar', 'wpas' ),
            'add_new'            => _x( 'Add New', 'Slide', 'wpas' ),
            'add_new_item'       => __( 'Add New Slide', 'wpas' ),
            'new_item'           => __( 'New Slide', 'wpas' ),
            'edit_item'          => __( 'Edit Slide', 'wpas' ),
            'view_item'          => __( 'View Slide', 'wpas' ),
            'all_items'          => __( 'All Slides', 'wpas' ),
            'search_items'       => __( 'Search Slides', 'wpas' ),
            'parent_item_colon'  => __( 'Parent Slides:', 'wpas' ),
            'not_found'          => __( 'No slides found.', 'wpas' ),
            'not_found_in_trash' => __( 'No slides found in Trash.', 'wpas' )
        );

        $args = array(
            'labels'             => $labels,
            'public'          => false,
            'show_ui'         => true,
            'show_in_menu'    => false,
            'hierarchical'    => false,
            'query_var'       => false,
            'supports'        => array('title'),
            'capability_type' => 'post',

        );

        register_post_type( 'wpas_slide', $args );
    }

    /**
     * Add submenu page
     */
    function add_submenu_page() {
        add_submenu_page( 'edit.php?post_type=wpas_slider', 'All Slides', 'All Slides', 'edit_posts', 'edit.php?post_type=wpas_slide' );
    }



    public static function init() {
        new WPAS_slider_post_type();
    }
}

WPAS_slider_post_type::init();