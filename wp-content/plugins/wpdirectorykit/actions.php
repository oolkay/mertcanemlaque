<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once WPDIRECTORYKIT_PATH . 'extensions/wdk-dependfields.php';
new \Wdk\Extensions\WdkDependfields();


if(file_exists(WPDIRECTORYKIT_PATH . 'extensions/wdk-cached-users.php')) {
	require_once WPDIRECTORYKIT_PATH . 'extensions/wdk-cached-users.php';
	new \Wdk\Extensions\WdkCachedUsers();
}

/* remove wdk data on remove user */
add_action('delete_user', function($user_id ){
	if(!empty($user_id)) {
		global $Winter_MVC_WDK;
		$Winter_MVC_WDK->model('user_m');
		$Winter_MVC_WDK->user_m->remove_user_data(intval($user_id));
	}
});

/* home, results page, dash/login, menu for hamburger */
add_action('wp_footer', function(){
	if(wdk_get_option('wdk_mobile_bottom_navbar_enable')) {
		wp_enqueue_style( 'dashicons' );
		?>
			<div class="wdk_mobile_footer_menu">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr__('Home','nexproperty' ); ?>"><span class="dashicons dashicons-admin-home"></span></a>
				<?php if(wdk_get_option('wdk_results_page')):?>
					<a href="<?php echo esc_url(get_permalink(wdk_get_option('wdk_results_page'))); ?>" title="<?php echo esc_attr__('Search','nexproperty' ); ?>"><span class="dashicons dashicons-search"></span></a>
				<?php endif;?>
				<?php if (!is_user_logged_in()): ?>
					<a href="<?php echo (get_option('wdk_membership_login_page')) ? esc_url(get_permalink(wdk_get_option('wdk_membership_login_page'))) : esc_url(wp_login_url());?>" class="sign_in sign-btn"><i class="fas fa-user" aria-hidden="true"></i></a>
				<?php else:?>
					<?php
						$dash_url = get_admin_url() . "admin.php?page=wdk";
						if(function_exists('wdk_dash_url') && get_option('wdk_membership_dash_page') &&  wdk_dash_url()){
							$dash_url = wdk_dash_url();
						} 
					?>
					<a href="<?php echo esc_url($dash_url);?>" class="sign_in dash-btn" title="<?php echo esc_attr__('Dash','nexproperty' ); ?>" class="wdk-element-button logout">
						<i aria-hidden="true" class="fas fa-tachometer-alt"></i>                           
					</a>
					<a href="<?php echo esc_url(wp_logout_url( get_permalink() )); ?>" class="sign_in sign-btn" title="<?php echo esc_attr__('Log Out','nexproperty' ); ?>"><i class="fas fa-user-times" aria-hidden="true"></i></a>
				<?php endif;?>

				<div class="wdk-footer-menu">
					<?php
						$menus = wp_get_nav_menus();

						$available_menus = [];
						foreach ($menus as $menu) {
							$available_menus[$menu->slug] = $menu->name;
						}

					?>

					<input id="wdk_mobile_footer_menu_gumb" type="checkbox" class="wdk_mobile_footer_menu_gumb">

					<?php if(!empty($available_menus)):?>
						<label for="wdk_mobile_footer_menu_gumb" class="wdk_mobile_footer_menu_gumb-open">
							<span class="dashicons dashicons-menu"></span>
						</label>
					<?php endif;?>

					<div class="menu__box">
						<?php echo wp_nav_menu( array(
							'menu' => array_keys($available_menus)[0],
							'menu_class' => 'wl-nav-menu wl-nav-menu',
							'menu_id' => 'menu-' . array_keys($available_menus)[0] . '-wdk-bottom-menu',
							'fallback_cb' => '__return_empty_string',
						));?>
					</div>
				</div>
			</div>
		<?php
	}
});

add_filter( 'intermediate_image_sizes_advanced', function( $sizes ){
	if(get_option('wdk_install_images_sizes_disable') == 1) {
		return array();
	}
	return $sizes;
} );

/* update edit log */
add_action('wdk-membership/listing/saved', function($post_id = NULL, $old_listing_date = NULL){
	global $Winter_MVC_WDK;
	$Winter_MVC_WDK->model('editlog_m');
	$Winter_MVC_WDK->editlog_m->insert(array(
				'user_id' => get_current_user_id(),
				'post_id' => $post_id,
				'date' => date('Y-m-d H:i:s'),
				'ip' => $_SERVER['REMOTE_ADDR']
			));
});

?>