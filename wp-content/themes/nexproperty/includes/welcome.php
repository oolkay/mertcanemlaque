<?php


function nexproperty_notify_admin_welcome () {
    if(!current_user_can( 'activate_plugins' )) return false;
    add_action('admin_notices', function () {

        $screen = get_current_screen();
        if (get_user_meta(get_current_user_id(), 'theme_alert_dissmiss')) {
            return;
        }
        if ('appearance_page_one-click-demo-import' == $screen->id || 'appearance_page_nexos-dashboard' === $screen->id || 'appearance_page_tgmpa-install-plugins' === $screen->id) {
            return;
        }

        
        if (get_option('wdk_theme_nexproperty_installed')) {
            return true;
        }
        
        ?>
        <div class="updated notice nexproperty-welcome-notice">
            <div class="nexproperty-welcome-notice-wrap">
                <h2><?php esc_html_e('Congratulations!', 'nexproperty'); ?></h2>
                <p><?php echo sprintf(esc_html__('%1$s is now installed and ready to use. You can start either by importing the ready made demo or get started by customizing it your self.', 'nexproperty'),wp_get_theme()['Name']); ?></p>
    
                <div class="nexproperty-welcome-info">
                    <div class="nexproperty-welcome-thumb">
                        <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/screenshot.jpg'); ?>" alt="<?php echo sprintf(esc_attr__('%1$s Demo', 'nexproperty'),wp_get_theme()['Name']); ?>">
                    </div>
                        <div class="nexproperty-welcome-import">
                            <h3><?php esc_html_e('Import Demo', 'nexproperty'); ?></h3>
                            <p><?php esc_html_e('Click below to install and active Themes Demo Importer Plugin.', 'nexproperty'); ?></p>
                        
                            <?php if ( file_exists(ABSPATH . 'wp-content/plugins/one-click-demo-import/one-click-demo-import.php') && !in_array( 'one-click-demo-import/one-click-demo-import.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ):?>
                                <p><a data-slug="one-click-demo-import" data-filename="one-click-demo-import" href="<?php echo esc_url(get_admin_url() . "themes.php?page=tgmpa-install-plugins");?>" class="button button-primary nexproperty-activate-plugin"><?php esc_html_e('Activate Demo Importer Plugin', 'nexproperty'); ?></a></p>
                            <?php elseif ( file_exists(ABSPATH . 'wp-content/plugins/one-click-demo-import/one-click-demo-import.php') && in_array( 'one-click-demo-import/one-click-demo-import.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )):?>
                                <p><a href="<?php echo esc_url(get_admin_url() . "themes.php?page=one-click-demo-import");?>" class="button button-primary"><?php esc_html_e('Go to Demo Importer Page', 'nexproperty'); ?></a></p>
                            <?php else:?>
                                <p><a data-slug="one-click-demo-import" data-filename="one-click-demo-import" href="<?php echo esc_url(admin_url('themes.php?page=one-click-demo-import'));?>" class="button button-primary nexproperty-install-plugin"><?php esc_html_e('Install Demo Importer Plugin', 'nexproperty'); ?></a></p>
                            <?php endif;?>
                         
                        </div>
                    <div class="nexproperty-welcome-getting-started">
                        <h3><?php esc_html_e('Get Started', 'nexproperty'); ?></h3>
                        <p><?php echo sprintf(esc_html__('Here you will find all the necessary links and information on how to use %1$s.', 'nexproperty'),wp_get_theme()['Name']); ?></p>
                        <p><a href="<?php echo esc_url(admin_url('themes.php?page=nexos-dashboard')); ?>" class="button button-primary "><?php esc_html_e('Go to Setting Page', 'nexproperty'); ?></a></p>
                    </div>
                </div>
                <a href="?theme_alert_dissmiss" class="notice-close"><?php esc_html_e('Dismiss', 'nexproperty'); ?></a>
            </div>
        </div>
        <?php
    });

    add_action('admin_init', function () {
        $user_id = get_current_user_id();
        if (isset($_GET['theme_alert_dissmiss']))
            add_user_meta($user_id, 'theme_alert_dissmiss', 'true', true);
    });

    return true;
}

nexproperty_notify_admin_welcome();

function nexproperty_admin_scripts() {
    if(!current_user_can( 'activate_plugins' )) return false;

        $importer_params = array(
            'installing_text' => esc_html__('Installing Demo Importer Plugin', 'nexproperty'),
            'activating_text' => esc_html__('Activating Demo Importer Plugin', 'nexproperty'),
            'importer_page' => esc_html__('Go to Demo Importer Page', 'nexproperty'),
            'importer_url' => admin_url('themes.php?page=one-click-demo-import'),
            'error' => esc_html__('Error! Reload the page and try again.', 'nexproperty'),
            'success_redirect' => 1,
            'tgmpa_link' => esc_url(get_admin_url() . "themes.php?page=tgmpa-install-plugins"),
            'success_import' => esc_html__('For best experience please install and active all recommended plugin from theme before demo content import here.', 'nexproperty'),
            'wpnonce' => wp_create_nonce( 'activate_plugin' ),
        );

        wp_enqueue_style('real-estate-directory-welcome', get_stylesheet_directory_uri() . '/assets/css/welcome.css', array(), '1.0');
        wp_enqueue_script('real-estate-directory-welcome', get_stylesheet_directory_uri() . '/assets/js/welcome.js', ['jquery'], '1.0', true );
        wp_localize_script('real-estate-directory-welcome', 'importer_params', $importer_params);
}

add_action('admin_enqueue_scripts', 'nexproperty_admin_scripts');

if(!function_exists('nexproperty_activate_plugin')) {
    add_action('wp_ajax_nexproperty_activate_plugin', 'nexproperty_activate_plugin');
    function nexproperty_activate_plugin() {

        if(!current_user_can( 'activate_plugins' )) {
            echo esc_html__('Disable for current user', 'nexproperty');
            exit();
        }
        
        if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'activate_plugin' ) ) {
            echo esc_html__('Wrong _wpnonce', 'nexproperty');
            exit();
        }

        $slug = isset($_POST['slug']) ? $_POST['slug'] : '';
        $file = isset($_POST['file']) ? $_POST['file'] : '';
        $success = false;

        if (!empty($slug) && !empty($file)) {
            $result = activate_plugin($slug . '/' . $file . '.php');
            update_option('nexproperty_hide_notice', true);
            if (!is_wp_error($result)) {
                $success = true;
            }
        }
        echo wp_json_encode(array('success' => $success));
        die();
    }
}
