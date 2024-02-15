<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/*
* Creating a function to create our CPT
*/
 
function wdk_custom_post_type() {
 
    // Set UI labels for Custom Post Type
        $labels = array(
            'name'                => _x( 'WDK-Listings', 'Post Type General Name', 'wpdirectorykit' ),
            'singular_name'       => _x( 'Listing', 'Post Type Singular Name', 'wpdirectorykit' ),
            'menu_name'           => __( 'WDK-Listings', 'wpdirectorykit' ),
            'parent_item_colon'   => __( 'Parent Listing', 'wpdirectorykit' ),
            'all_items'           => __( 'All Listings', 'wpdirectorykit' ),
            'view_item'           => __( 'View Listing', 'wpdirectorykit' ),
            'add_new_item'        => __( 'Add New Listing', 'wpdirectorykit' ),
            'add_new'             => __( 'Add New', 'wpdirectorykit' ),
            'edit_item'           => __( 'Edit Listing', 'wpdirectorykit' ),
            'update_item'         => __( 'Update Listing', 'wpdirectorykit' ),
            'search_items'        => __( 'Search Listing', 'wpdirectorykit' ),
            'not_found'           => __( 'Not Found', 'wpdirectorykit' ),
            'not_found_in_trash'  => __( 'Not found in Trash', 'wpdirectorykit' ),
        );
         
    // Set other options for Custom Post Type
         
        $args = array(
            'label'               => __( 'Listings', 'wpdirectorykit' ),
            'description'         => __( 'Listing', 'wpdirectorykit' ),
            'labels'              => $labels,
            // Features this CPT supports in Post Editor
            'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'custom-fields', 'elementor' ),
            // You can associate this CPT with a taxonomy or custom taxonomy. 
            'taxonomies'          => array( ),
            /* A hierarchical CPT is like Pages and can have
            * Parent and child items. A non-hierarchical CPT
            * is like Posts.
            */ 
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => false,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_position'       => 30,
            'can_export'          => true,
            'has_archive'         => false,
            'exclude_from_search' => true,
            'publicly_queryable'  => true,
            'capability_type'     => 'page',
            'show_in_rest'        => true,
            'menu_icon'           => 'dashicons-category',
        );
         
        // Registering your Custom Post Type
        register_post_type( 'wdk-listing', $args );
     
    }
     
    /* Hook into the 'init' action so that the function
    * Containing our post type registration is not 
    * unnecessarily executed. 
    */
     
    add_action( 'init', 'wdk_custom_post_type', 0 );

