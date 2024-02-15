<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly;

class Wdk_demo_import extends Winter_MVC_Controller {

	public function __construct(){
		parent::__construct();
	}
    
	public function index()
	{
        $current_theme = wp_get_theme();

        $this->data['current_theme'] = $current_theme;

        $this->load->view('wdk_demo_import/index', $this->data);
    }

    public function step_2()
	{
        $current_theme = wp_get_theme();

        $this->data['current_theme'] = $current_theme;

        $this->load->view('wdk_demo_import/step_2', $this->data);
    }

    public function step_1()
	{
        $current_theme = wp_get_theme();

        $plugins = array(

            array(
                'name'               => 'Elementor', // The plugin name.
                'slug'               => 'elementor', // The plugin slug (typically the folder name).
                'required'           => false, // If false, the plugin is only 'recommended' instead of required.
                'version'            => '3.12.2', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
                'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
                'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
                'external_url'       => '', // If set, overrides default API URL and points to an external URL.
                'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
            ),
            
            array(
                'name'               => 'Element Invader', // The plugin name.
                'slug'               => 'elementinvader', // The plugin slug (typically the folder name).
                'required'           => false, // If false, the plugin is only 'recommended' instead of required.
                'version'            => '1.2.1', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
                'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
                'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
                'external_url'       => '', // If set, overrides default API URL and points to an external URL.
                'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
            ),
        );
        
        
        if(file_exists( get_stylesheet_directory() .'/addons/elementinvader-addons-for-elementor.zip')) {
            $plugins [] = array(
                'name'               => 'ElementInvader Add-ons for Elementor', // The plugin name.
                'slug'               => 'elementinvader-addons-for-elementor', // The plugin slug (typically the folder name).
                'source'             => get_stylesheet_directory() . '/addons/elementinvader-addons-for-elementor.zip', // The plugin source.
                'required'           => false, // If false, the plugin is only 'recommended' instead of required.
                'version'            => '1.1.4', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
                'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
                'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
                'external_url'       => '', // If set, overrides default API URL and points to an external URL.
                'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
            );
        }
        else
        {
            $plugins [] = array(
                'name'               => 'ElementInvader Addons for Elementor', // The plugin name.
                'slug'               => 'elementinvader-addons-for-elementor', // The plugin slug (typically the folder name).
                'required'           => false, // If false, the plugin is only 'recommended' instead of required.
                'version'            => '1.1.4', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
                'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
                'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
                'external_url'       => '', // If set, overrides default API URL and points to an external URL.
                'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
            );
        }
        
        if(file_exists( get_stylesheet_directory() .'/addons/wdk-bookings.zip')) {
            $plugins [] = array(
                'name'               => 'WDK Bookings Addon', // The plugin name.
                'slug'               => 'wdk-bookings', // The plugin slug (typically the folder name).
                'source'             => get_stylesheet_directory() . '/addons/wdk-bookings.zip', // The plugin source.
                'required'           => false, // If false, the plugin is only 'recommended' instead of required.
                'version'            => '1.0.1', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
                'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
                'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
                'external_url'       => '', // If set, overrides default API URL and points to an external URL.
                'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
            );
        }
        
        if(file_exists( get_stylesheet_directory() .'/addons/wdk-currency-conversion.zip')) {
            $plugins [] = array(
                'name'               => 'WDK Currency Conversion Addon', // The plugin name.
                'slug'               => 'wdk-currency-conversion', // The plugin slug (typically the folder name).
                'source'             => get_stylesheet_directory() . '/addons/wdk-currency-conversion.zip', // The plugin source.
                'required'           => false, // If false, the plugin is only 'recommended' instead of required.
                'version'            => '1.0.1', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
                'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
                'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
                'external_url'       => '', // If set, overrides default API URL and points to an external URL.
                'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
            );
        }
        
        if(file_exists( get_stylesheet_directory() .'/addons/wdk-favorites.zip')) {
            $plugins [] = array(
                'name'               => 'WDK Favorites Addon', // The plugin name.
                'slug'               => 'wdk-favorites', // The plugin slug (typically the folder name).
                'source'             => get_stylesheet_directory() . '/addons/wdk-favorites.zip', // The plugin source.
                'required'           => false, // If false, the plugin is only 'recommended' instead of required.
                'version'            => '1.0.1', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
                'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
                'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
                'external_url'       => '', // If set, overrides default API URL and points to an external URL.
                'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
            );
        }
        if(file_exists( get_stylesheet_directory() .'/addons/wdk-membership.zip')) {
            $plugins [] = array(
                'name'               => 'WDK Membership Addon', // The plugin name.
                'slug'               => 'wdk-membership', // The plugin slug (typically the folder name).
                'source'             => get_stylesheet_directory() . '/addons/wdk-membership.zip', // The plugin source.
                'required'           => false, // If false, the plugin is only 'recommended' instead of required.
                'version'            => '1.0.1', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
                'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
                'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
                'external_url'       => '', // If set, overrides default API URL and points to an external URL.
                'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
            );
        }
        if(file_exists( get_stylesheet_directory() .'/addons/wdk-mortgage.zip')) {
            $plugins [] = array(
                
                'name'               => 'WDK Mortgage Addon', // The plugin name.
                'slug'               => 'wdk-mortgage', // The plugin slug (typically the folder name).
                'source'             => get_stylesheet_directory() . '/addons/wdk-mortgage.zip', // The plugin source.
                'required'           => false, // If false, the plugin is only 'recommended' instead of required.
                'version'            => '1.0.1', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
                'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
                'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
                'external_url'       => '', // If set, overrides default API URL and points to an external URL.
                'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
            );
        }
        
        if(file_exists( get_stylesheet_directory() .'/addons/wdk-wp-all-import.zip')) {
            $plugins [] = array(
                
                'name'               => 'WDK Wp All Import', // The plugin name.
                'slug'               => 'wdk-wp-all-import', // The plugin slug (typically the folder name).
                'source'             => get_stylesheet_directory() . '/addons/wdk-wp-all-import.zip', // The plugin source.
                'required'           => false, // If false, the plugin is only 'recommended' instead of required.
                'version'            => '1.0.1', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
                'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
                'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
                'external_url'       => '', // If set, overrides default API URL and points to an external URL.
                'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
                'has_notices'  => false,  
            );
        }
        
        if(file_exists( get_stylesheet_directory() .'/addons/profile-picture-uploader.zip')) {
            $plugins [] = array(
                
                'name'               => 'WDK Profile picture uploader', // The plugin name.
                'slug'               => 'profile-picture-uploader', // The plugin slug (typically the folder name).
                'source'             => get_stylesheet_directory() . '/addons/profile-picture-uploader.zip', // The plugin source.
                'required'           => false, // If false, the plugin is only 'recommended' instead of required.
                'version'            => '1.0.1', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
                'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
                'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
                'external_url'       => '', // If set, overrides default API URL and points to an external URL.
                'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
            );
        }
        
        if(file_exists( get_stylesheet_directory() .'/addons/wdk-reviews.zip')) {
            $plugins [] = array(
                
                'name'               => 'WDK Reviews', // The plugin name.
                'slug'               => 'wdk-reviews', // The plugin slug (typically the folder name).
                'source'             => get_stylesheet_directory() . '/addons/wdk-reviews.zip', // The plugin source.
                'required'           => false, // If false, the plugin is only 'recommended' instead of required.
                'version'            => '1.0.1', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
                'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
                'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
                'external_url'       => '', // If set, overrides default API URL and points to an external URL.
                'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
            );
        }
        
        if(file_exists( get_stylesheet_directory() .'/addons/sweet-energy-efficiency.zip')) {
            $plugins [] = array(
                'name'               => 'Sweet Energy Efficiency', // The plugin name.
                'slug'               => 'sweet-energy-efficiency', // The plugin slug (typically the folder name).
                'source'             => get_stylesheet_directory() . '/addons/sweet-energy-efficiency.zip', // The plugin source.
                'required'           => false, // If false, the plugin is only 'recommended' instead of required.
                'version'            => '1.0.5', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
                'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
                'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
                'external_url'       => '', // If set, overrides default API URL and points to an external URL.
                'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
            );
        }
        
        if(file_exists( get_stylesheet_directory() .'/addons/wdk-report-abuse.zip')) {
            $plugins [] = array(
                'name'               => 'WDK Report Abuse', // The plugin name.
                'slug'               => 'wdk-report-abuse', // The plugin slug (typically the folder name).
                'source'             => get_stylesheet_directory() . '/addons/wdk-report-abuse.zip', // The plugin source.
                'required'           => false, // If false, the plugin is only 'recommended' instead of required.
                'version'            => '1.0.1', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
                'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
                'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
                'external_url'       => '', // If set, overrides default API URL and points to an external URL.
                'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
            );
        }
        
        if(file_exists( get_stylesheet_directory() .'/addons/wdk-facebook-comments.zip')) {
            $plugins [] = array(
                'name'               => 'WDK Facebook Comments', // The plugin name.
                'slug'               => 'wdk-facebook-comments', // The plugin slug (typically the folder name).
                'source'             => get_stylesheet_directory() . '/addons/wdk-facebook-comments.zip', // The plugin source.
                'required'           => false, // If false, the plugin is only 'recommended' instead of required.
                'version'            => '1.0.1', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
                'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
                'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
                'external_url'       => '', // If set, overrides default API URL and points to an external URL.
                'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
            );
        }
        
        if(file_exists( get_stylesheet_directory() .'/addons/wdk-mailchimp.zip')) {
            $plugins [] = array(
                'name'               => 'WDK Mailchimp', // The plugin name.
                'slug'               => 'wdk-mailchimp', // The plugin slug (typically the folder name).
                'source'             => get_stylesheet_directory() . '/addons/wdk-mailchimp.zip', // The plugin source.
                'required'           => false, // If false, the plugin is only 'recommended' instead of required.
                'version'            => '1.0.1', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
                'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
                'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
                'external_url'       => '', // If set, overrides default API URL and points to an external URL.
                'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
            );
        }
        
        if(file_exists( get_stylesheet_directory() .'/addons/wdk-compare-listing.zip')) {
            $plugins [] = array(
                'name'               => 'WDK Compare Listings', // The plugin name.
                'slug'               => 'wdk-compare-listing', // The plugin slug (typically the folder name).
                'source'             => get_stylesheet_directory() . '/addons/wdk-compare-listing.zip', // The plugin source.
                'required'           => false, // If false, the plugin is only 'recommended' instead of required.
                'version'            => '1.0.1', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
                'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
                'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
                'external_url'       => '', // If set, overrides default API URL and points to an external URL.
                'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
            );
        }
        
        if(file_exists( get_stylesheet_directory() .'/addons/wdk-save-search.zip')) {
            $plugins [] = array(
                'name'               => 'WDK Save Search', // The plugin name.
                'slug'               => 'wdk-save-search', // The plugin slug (typically the folder name).
                'source'             => get_stylesheet_directory() . '/addons/wdk-save-search.zip', // The plugin source.
                'required'           => false, // If false, the plugin is only 'recommended' instead of required.
                'version'            => '1.0.1', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
                'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
                'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
                'external_url'       => '', // If set, overrides default API URL and points to an external URL.
                'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
            );
        }

        $this->data['current_theme'] = $current_theme;

        $this->data['theme_plugins'] = $plugins;

        $this->load->view('wdk_demo_import/step_1', $this->data);
    }
}

