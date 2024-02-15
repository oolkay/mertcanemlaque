<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_action( 'init', 'wdk_custom_user_roles' );
function wdk_custom_user_roles()
{
    add_role('wdk_agent'  , __('Agent', 'wpdirectorykit'), array( 'read' => true, 'level_0' => true ) );
    $role = get_role('wdk_agent');
    $role->add_cap('edit_own_listings'); 
    $role->add_cap('edit_own_profile');
    $role->add_cap('upload_files');

    add_role('wdk_listing_admin'  , __('Listing Admin', 'wpdirectorykit'), array( 'read' => true, 'level_0' => true ) );
    $role = get_role('wdk_listing_admin');
    $role->add_cap('edit_own_listings'); 
    $role->add_cap('edit_own_profile');
    $role->add_cap('upload_files');
    $role->add_cap('wdk_listings_manage');

    $role = get_role('administrator');
    $role->add_cap('wdk_listings_manage'); 
}

?>