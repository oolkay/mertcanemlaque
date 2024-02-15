<?php

/**
 * Fired during plugin activation
 *
 * @link       listing-themes.com
 * @since      1.0.0
 *
 * @package    Wpdirectorykit
 * @subpackage Wpdirectorykit/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Wpdirectorykit
 * @subpackage Wpdirectorykit/includes
 * @author     listing-themes.com <dev@listing-themes.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
class Wpdirectorykit_Activator {

    public static $db_version = 4.5;

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
      
        $prefix = 'wdk_';

        // Default options
        add_option($prefix.'is_location_enabled', '1');
        add_option($prefix.'is_category_enabled', '1');
        add_option($prefix.'is_results_page_require', '1');
        add_option($prefix.'is_address_enabled', '1');

        // By default set London position
        add_option($prefix.'default_lat', 51.505);
        add_option($prefix.'default_lng', -0.09);

        /* disable elmentor experement feature */
        update_option( 'elementor_experiment-landing-pages', 'inactive' );
        update_option( 'elementor_experiment-e_dom_optimization', 'inactive');
	}

    public static function plugins_loaded(){
		if ( get_site_option( 'wdk_db_version' ) === false ||
		     get_site_option( 'wdk_db_version' ) < self::$db_version ) {
			self::install();
		}
    }

    // https://codex.wordpress.org/Creating_Tables_with_Plugins
    public static function install() {
        global $wpdb;

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        $charset_collate = $wpdb->get_charset_collate();
        // For init version 1.0
        if(get_site_option( 'wdk_db_version' ) === false)
        {
            // Main table for visited pages

            $table_name = $wpdb->prefix . 'wdk_fields';

            $sql = "CREATE TABLE IF NOT EXISTS $table_name (
                    `idfield` int(11) NOT NULL AUTO_INCREMENT,
                    `date` datetime DEFAULT NULL,
                    `lang_code` varchar(8) DEFAULT NULL,
                    `order_index` int(11) DEFAULT NULL,
                    `field_type` varchar(45) DEFAULT NULL,
                    `is_locked` tinyint(1) DEFAULT NULL,
                    `is_table_visible` tinyint(1) DEFAULT NULL,
                    `is_visible_frontend` tinyint(1) DEFAULT NULL,
                    `is_visible_dashboard` tinyint(1) DEFAULT NULL,
                    `is_hardlocked` tinyint(1) DEFAULT NULL,
                    `is_required` tinyint(1) DEFAULT NULL,
                    `is_translatable` tinyint(1) DEFAULT NULL,
                    `is_quickvisible` tinyint(1) DEFAULT NULL,
                    `max_length` int(11) DEFAULT NULL,
                    `repository_id` int(11) DEFAULT NULL,
                    `files` int(11) DEFAULT NULL,
                    `icon_id` int(11) DEFAULT NULL,
                    `columns_number` int(11) DEFAULT NULL,
                    `hubspot_id` varchar(60) DEFAULT NULL,
                    `field_label` varchar(50) DEFAULT NULL,
                    `prefix` varchar(30) DEFAULT NULL,
                    `suffix` varchar(30) DEFAULT NULL,
                    `values_list` varchar(160) DEFAULT NULL,
                    `hint` varchar(160) DEFAULT NULL,
                PRIMARY KEY  (idfield)
            ) $charset_collate COMMENT='Custom Fields Data for WP Directory Kit';";
        
            dbDelta( $sql );

            $table_name = $wpdb->prefix . 'wdk_listings';

            $sql = "CREATE TABLE IF NOT EXISTS $table_name (
                    `idlisting` int(11) NOT NULL AUTO_INCREMENT,
                    `post_id` int(11) NOT NULL,
                    `transition_id` varchar(100) DEFAULT NULL,
                    `category_id` int(11) DEFAULT NULL,
                    `location_id` int(11) DEFAULT NULL,
                    `is_primary` tinyint(1) DEFAULT NULL,
                    `last_edit_user_id` int(11) DEFAULT NULL,
                    `related_id` int(11) DEFAULT NULL,
                    `is_featured` tinyint(1) DEFAULT NULL,
                    `is_activated` tinyint(1) DEFAULT NULL,
                    `lat` decimal(9,6) DEFAULT NULL,
                    `lng` decimal(9,6) DEFAULT NULL,
                    `address` varchar(200) DEFAULT NULL,
                    `rank` int(11) DEFAULT NULL,
                    `date` datetime DEFAULT NULL,
                    `date_modified` datetime DEFAULT NULL,
                    `date_activated` datetime DEFAULT NULL,
                    `date_rank_expire` datetime DEFAULT NULL,
                    `date_alert` datetime DEFAULT NULL,
                    `date_notify` datetime DEFAULT NULL,
                    `date_repost` datetime DEFAULT NULL,
                    `date_renew` datetime DEFAULT NULL,
                    `date_activation_paid` datetime DEFAULT NULL,
                    `date_featured_paid` datetime DEFAULT NULL,
                    `date_status` datetime DEFAULT NULL,
                    `date_expire` datetime DEFAULT NULL,
                    `status` varchar(45) DEFAULT NULL,
                    `last_edit_ip` varchar(45) DEFAULT NULL,
                    `counter_views` int(11) DEFAULT NULL,
                    `reviews_stars` int(11) DEFAULT NULL,
                    `listing_images` text,
                    `affilate_id` int(11) DEFAULT NULL,
                    `not_notified_user` tinyint(1) DEFAULT NULL,
                    `hubspot_id` varchar(60) DEFAULT NULL,
                    PRIMARY KEY  (idlisting),
                    UNIQUE KEY (post_id)
                ) $charset_collate;";
        
            dbDelta( $sql );

            $table_name = $wpdb->prefix . 'wdk_listings_fields';

            $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
                    `idlistings_fields` int(11) NOT NULL AUTO_INCREMENT,
                    `post_id` int(11) NOT NULL,
                    `lang_code` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
                PRIMARY KEY  (idlistings_fields),
                UNIQUE KEY (post_id)
                ) $charset_collate;";
        
            dbDelta( $sql );

            $table_name = $wpdb->prefix . 'wdk_categories';

            $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
                    `idcategory` int(11) NOT NULL AUTO_INCREMENT,
                    `parent_id` int(11) DEFAULT NULL,
                    `page_id` int(11) DEFAULT NULL,
                    `lang_code` varchar(8) DEFAULT NULL,
                    `date` datetime DEFAULT NULL,
                    `parent_path` text,
                    `order_index` int(11) DEFAULT NULL,
                    `level` int(11) DEFAULT NULL,
                    `icon_id` int(11) DEFAULT NULL,
                    `image_id` int(11) DEFAULT NULL,
                    `font_icon_code` varchar(64) DEFAULT NULL,
                    `code` varchar(6) DEFAULT NULL,
                    `hubspot_id` varchar(60) DEFAULT NULL,
                    `category_title` varchar(160) DEFAULT NULL,
                PRIMARY KEY  (idcategory)
                ) $charset_collate;";
        
            dbDelta( $sql );

            $table_name = $wpdb->prefix . 'wdk_locations';

            $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
                    `idlocation` int(11) NOT NULL AUTO_INCREMENT,
                    `parent_id` int(11) DEFAULT NULL,
                    `page_id` int(11) DEFAULT NULL,
                    `lang_code` varchar(8) DEFAULT NULL,
                    `date` datetime DEFAULT NULL,
                    `parent_path` text,
                    `order_index` int(11) DEFAULT NULL,
                    `level` int(11) DEFAULT NULL,
                    `icon_id` int(11) DEFAULT NULL,
                    `image_id` int(11) DEFAULT NULL,
                    `font_icon_code` varchar(64) DEFAULT NULL,
                    `code` varchar(6) DEFAULT NULL,
                    `hubspot_id` varchar(60) DEFAULT NULL,
                    `location_title` varchar(160) DEFAULT NULL,
                PRIMARY KEY  (idlocation)
                ) $charset_collate;";
        
            dbDelta( $sql );

            $table_name = $wpdb->prefix . 'wdk_searchform';

            $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
                    `idsearchform` int(11) NOT NULL AUTO_INCREMENT,
                    `date` datetime DEFAULT NULL,
                    `searchform_name` varchar(160) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `searchform_json` text COLLATE utf8_unicode_ci,
                PRIMARY KEY  (idsearchform)
                ) $charset_collate;";
        
            dbDelta( $sql );

            $table_name = $wpdb->prefix . 'wdk_resultitem';

            $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
                    `idresultitem` int(11) NOT NULL AUTO_INCREMENT,
                    `date` datetime DEFAULT NULL,
                    `resultitem_name` varchar(160) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `resultitem_json` text COLLATE utf8_unicode_ci,
                PRIMARY KEY  (idresultitem)
                ) $charset_collate;";
        
            dbDelta( $sql );

            update_option( 'wdk_db_version', "1" );

        }
        
        /* version 1.1.0 db install */
        if ( get_site_option( 'wdk_db_version' ) < '1.2' ) {
            $table_name = $wpdb->prefix . 'wdk_messages';
            $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
                        `idmessage` int(11) NOT NULL AUTO_INCREMENT,
                        `post_id` int DEFAULT NULL,
                        `date` datetime DEFAULT NULL,
                        `user_id_sender` int DEFAULT NULL,
                        `user_id_receiver` int DEFAULT NULL,
                        `json_object` text,
                        `email_receiver` varchar(100) DEFAULT NULL,
                        `email_sender` varchar(100) DEFAULT NULL,
                        `message` text,
                        `date_from` datetime DEFAULT NULL,
                        `date_to` datetime DEFAULT NULL,
                        `is_readed` tinyint(1) DEFAULT NULL,
                PRIMARY KEY  (idmessage)
                ) $charset_collate;";
       
            dbDelta( $sql );

            $table_name = $wpdb->prefix . 'wdk_listings_users';
            $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
                        `idlistings_users` int(11) NOT NULL AUTO_INCREMENT,
                        `post_id` int DEFAULT NULL,
                        `user_id` int DEFAULT NULL,
                PRIMARY KEY  (idlistings_users)
                ) $charset_collate;";
       
            dbDelta( $sql );
            /* udpate option with db version */
        }
        
        /* version 1.2.0 db install */
        if ( get_site_option( 'wdk_db_version' ) < '1.3' ) {

            $table_name = $wpdb->prefix . 'wdk_categories';
            $sql = "ALTER TABLE `$table_name` CHANGE `parent_id` `parent_id` INT(11) NULL DEFAULT '0'; ";
            $wpdb->query($sql);

            self::$db_version = 1.3;
            /* udpate option with db version */
        }

        /* version 1.2.0 db install */
        if ( get_site_option( 'wdk_db_version' ) < '1.4' ) {

            $table_name = $wpdb->prefix . 'wdk_listings';
            $sql = "ALTER TABLE `$table_name`  ADD `is_approved` INT(1) NULL DEFAULT NULL AFTER `is_activated`; ";
            $wpdb->query($sql);

            $sql = "UPDATE `$table_name` SET `is_approved` = '1'";
            $wpdb->query($sql);

            self::$db_version = 1.4;
            /* udpate option with db version */
        }

        if ( get_site_option( 'wdk_db_version' ) < '1.5' ) {

            $table_name = $wpdb->prefix . 'wdk_listings';
            $sql = "ALTER TABLE `$table_name`  ADD `date_package_expire` DATETIME NULL AFTER `hubspot_id`, ADD `package_id` INT NULL AFTER `date_package_expire`; ";
            $wpdb->query($sql);

            self::$db_version = 1.5;
            /* udpate option with db version */
        }

        if ( get_site_option( 'wdk_db_version' ) < '1.6' ) {

            $table_name = $wpdb->prefix . 'wdk_categories';
            $sql = "ALTER TABLE `$table_name`  ADD `marker_image_id` int(11) DEFAULT NULL";
            $wpdb->query($sql);

            self::$db_version = 1.6;
            /* udpate option with db version */
        }
        
        if ( get_site_option( 'wdk_db_version' ) < '1.7' ) {

            $table_name = $wpdb->prefix . 'wdk_listings';
            $sql = "ALTER TABLE `$table_name`  ADD `listing_images_path` VARCHAR(200) DEFAULT ''";
            $wpdb->query($sql);

            // TODO: this column content should be generated based on listing_images or old client may have issues after update

            self::$db_version = 1.7;
            /* udpate option with db version */
        }

        if ( get_site_option( 'wdk_db_version' ) < '1.8' ) {

            $table_name = $wpdb->prefix . 'wdk_listings';
            $sql = "ALTER TABLE `$table_name` ADD `subscription_id` INT NULL; ";
            $wpdb->query($sql);

            self::$db_version = 1.8;
            /* udpate option with db version */
        }

        if ( get_site_option( 'wdk_db_version' ) < '1.9' ) {

            $table_name = $wpdb->prefix . 'wdk_fields';
            $sql = "ALTER TABLE `$table_name` ADD `is_price_format` tinyint(1) DEFAULT NULL; ";
            $wpdb->query($sql);

            self::$db_version = 1.9;
            /* udpate option with db version */
        }

        if ( get_site_option( 'wdk_db_version' ) < '2.0' ) {

            $table_name = $wpdb->prefix . 'wdk_resultitem';
            $sql = "ALTER TABLE `$table_name` ADD `is_multiline_enabled` tinyint(1) DEFAULT NULL; ";
            $wpdb->query($sql);

            self::$db_version = 2.0;
            /* udpate option with db version */
        }

        if ( get_site_option( 'wdk_db_version' ) < '2.1' ) {

            $table_name = $wpdb->prefix . 'wdk_listings';
            $sql = "ALTER TABLE `$table_name` ADD `user_id_editor` INT DEFAULT NULL;";
            $wpdb->query($sql);

            $table_user_name = $wpdb->prefix . 'wdk_listings_users';

            /* copy agents to new column */
            $sql = "UPDATE 
                        `$table_name` table_listings , 
                        `$table_user_name` table_users
                    SET 
                        table_listings.user_id_editor = table_users.user_id
                    WHERE
                        table_listings.post_id = table_users.post_id;";
            $wpdb->query($sql);

            $sql = "TRUNCATE TABLE `$table_user_name`";
            $wpdb->query($sql);

            self::$db_version = 2.1;
            /* udpate option with db version */
        }

        if ( get_site_option( 'wdk_db_version' ) < '2.2' ) {

            $table_name = $wpdb->prefix . 'wdk_locations';
            $sql = "ALTER TABLE `$table_name` ADD `level_0_id` INT DEFAULT NULL;";
            $wpdb->query($sql);

            $table_name = $wpdb->prefix . 'wdk_categories';
            $sql = "ALTER TABLE `$table_name` ADD `level_0_id` INT DEFAULT NULL;";
            $wpdb->query($sql);


            $table_name = $wpdb->prefix . 'wdk_listings_locations';
            $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
                        `idlistings_locations` int(11) NOT NULL AUTO_INCREMENT,
                        `location_id` int DEFAULT NULL,
                        `post_id` int DEFAULT NULL,
                PRIMARY KEY  (idlistings_locations)
            ) $charset_collate;";
            dbDelta( $sql );

            $table_name = $wpdb->prefix . 'wdk_listings_categories';
            $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
                        `idlistings_categories` int(11) NOT NULL AUTO_INCREMENT,
                        `category_id` int DEFAULT NULL,
                        `post_id` int DEFAULT NULL,
                PRIMARY KEY  (idlistings_categories)
            ) $charset_collate;";
            dbDelta( $sql );


            self::$db_version = 2.2;
            /* udpate option with db version */
        }

        if ( get_site_option( 'wdk_db_version' ) < '2.3' ) {

            $table_name = $wpdb->prefix . 'wdk_listings';
            $sql = "ALTER TABLE `$table_name` ADD `listing_plans_documents` text DEFAULT '';";
            $wpdb->query($sql);

            self::$db_version = 2.3;
            /* udpate option with db version */
        }

        if ( get_site_option( 'wdk_db_version' ) < '2.4' ) {

            $table_name = $wpdb->prefix . 'wdk_resultitem';
            $sql = "ALTER TABLE `$table_name`  ADD `is_label_disable` INT(1) NULL DEFAULT NULL";
            $wpdb->query($sql);

            self::$db_version = 2.4;
            /* udpate option with db version */
        }

        if ( get_site_option( 'wdk_db_version' ) < '2.5' ) {

            $table_name = $wpdb->prefix . 'wdk_listings';
            $sql = "ALTER TABLE `$table_name`  ADD `categories_list` VARCHAR(128) DEFAULT ''";
            $wpdb->query($sql);

            $table_name = $wpdb->prefix . 'wdk_listings';
            $sql = "ALTER TABLE `$table_name`  ADD `locations_list` VARCHAR(128) DEFAULT ''";
            $wpdb->query($sql);


            self::$db_version = 2.5;
            /* udpate option with db version */ 
        }

        if ( get_site_option( 'wdk_db_version' ) < '2.6' ) {

            $table_name = $wpdb->prefix . 'wdk_fields';

            $sql = "UPDATE `$table_name` SET `is_visible_frontend` = '1'";
            $wpdb->query($sql);

            self::$db_version = 2.6;
            /* udpate option with db version */ 
        }

        if ( get_site_option( 'wdk_db_version' ) < '2.7' ) {
            $table_name = $wpdb->prefix . 'wdk_listings';
            $sql = "ALTER TABLE `$table_name`  ADD `listing_images_path_medium` VARCHAR(250) DEFAULT ''";
            $wpdb->query($sql);

            self::$db_version = 2.7;
            /* udpate option with db version */ 
        }

        if ( get_site_option( 'wdk_db_version' ) < '2.8' ) {
            // Main table for visited pages
                    
            $table_name = $wpdb->prefix . 'wdk_dependfields';

            $sql = "CREATE TABLE IF NOT EXISTS $table_name (
                    `iddependfields` int(11) NOT NULL AUTO_INCREMENT,
                    `main_field` varchar(60) DEFAULT '',
                    `field_id` int(11) DEFAULT NULL,
                    `hidden_fields_list` varchar(256) DEFAULT '',
                    `date` datetime DEFAULT NULL,
                PRIMARY KEY  (iddependfields)
            ) $charset_collate";

            dbDelta( $sql );

            self::$db_version = 2.8;
            /* udpate option with db version */ 
        }

        if ( get_site_option( 'wdk_db_version' ) < '2.9' ) {
            // Main table for visited pages
            global $wpdb;
            $wpdb->query('UPDATE '.$wpdb->prefix . 'wdk_fields SET is_price_format = 1 WHERE (idfield=6 OR idfield=7) AND field_type="NUMBER"');
            self::$db_version = 2.9;
            /* udpate option with db version */ 
        }
        
        if ( get_site_option( 'wdk_db_version' ) < '3.0' ) {

            $table_name = $wpdb->prefix . 'wdk_fields';

            $sql = "UPDATE `$table_name` SET `is_visible_dashboard` = '1'";
            $wpdb->query($sql);

            self::$db_version = 3.0;
            /* udpate option with db version */ 
        }
        
        if ( get_site_option( 'wdk_db_version' ) < '3.1' ) {

            $table_name = $wpdb->prefix . 'wdk_locations';
            $sql = "ALTER TABLE `$table_name`  ADD `icon_path` VARCHAR(100) DEFAULT ''";
            $wpdb->query($sql);
            $sql = "ALTER TABLE `$table_name`  ADD `image_path` VARCHAR(100) DEFAULT ''";
            $wpdb->query($sql);

            $table_name = $wpdb->prefix . 'wdk_categories';
            $sql = "ALTER TABLE `$table_name`  ADD `icon_path` VARCHAR(100) DEFAULT ''";
            $wpdb->query($sql);
            $sql = "ALTER TABLE `$table_name`  ADD `image_path` VARCHAR(100) DEFAULT ''";
            $wpdb->query($sql);
            $sql = "ALTER TABLE `$table_name`  ADD `marker_image_path` VARCHAR(100) DEFAULT ''";
            $wpdb->query($sql);

            // TODO: this column content should be generated based on listing_images or old client may have issues after update

            self::$db_version = 3.1;
            /* udpate option with db version */ 
        }

        if ( get_site_option( 'wdk_db_version' ) < '3.2' ) {
            // Main table for visited pages
                    
            $table_name = $wpdb->prefix . 'wdk_token';

            $sql = "CREATE TABLE IF NOT EXISTS $table_name (
                    `idtoken` int(11) NOT NULL AUTO_INCREMENT,
                    `token` varchar(60) UNIQUE DEFAULT '',
                    `user_id` int(11) DEFAULT NULL,
                    `user_email` varchar(256) DEFAULT '',
                    `expire_date` datetime DEFAULT NULL,
                    `date` datetime DEFAULT NULL,
                PRIMARY KEY  (idtoken)
            ) $charset_collate";

            dbDelta( $sql );

            self::$db_version = 3.2;
            /* udpate option with db version */ 
        }

        if ( get_site_option( 'wdk_db_version' ) < '3.3' ) {
            // Main table for visited pages


            self::$db_version = 3.3;
            /* udpate option with db version */ 
        }

        if ( get_site_option( 'wdk_db_version' ) < '3.4' ) {
            // Main table for visited pages
            
            $table_name = $wpdb->prefix . 'wdk_locations';
            $sql = "ALTER TABLE `$table_name` ADD `related_svg_map` varchar(64) DEFAULT NULL;";
            $wpdb->query($sql);

            $table_name = $wpdb->prefix . 'wdk_locations';
            $sql = "ALTER TABLE `$table_name` ADD `related_svg_map_location` varchar(128) DEFAULT NULL;";
            $wpdb->query($sql);

            $table_name = $wpdb->prefix . 'wdk_fields';
            $sql = "ALTER TABLE `$table_name` CHANGE `values_list` `values_list` TEXT NULL DEFAULT NULL;";
            $wpdb->query($sql);

            self::$db_version = 3.4;
            /* udpate option with db version */ 
        }

        if ( get_site_option( 'wdk_db_version' ) < '3.5' ) {
            // Main table for visited pages

            $table_name = $wpdb->prefix . 'wdk_categories';
            $sql = "ALTER TABLE `$table_name` ADD `category_color` varchar(32) DEFAULT NULL;";
            $wpdb->query($sql);

            $table_name = $wpdb->prefix . 'wdk_fields';
            $sql = "ALTER TABLE `$table_name` ADD `validation` varchar(64) DEFAULT NULL;";
            $wpdb->query($sql);

            $table_name = $wpdb->prefix . 'wdk_fields';
            $sql = "ALTER TABLE `$table_name` ADD `min_length` INT(3) DEFAULT NULL;";
            $wpdb->query($sql);

            $table_name = $wpdb->prefix . 'wdk_listings';
            $sql = "ALTER TABLE `$table_name` CHANGE `listing_images_path` `listing_images_path` TEXT DEFAULT '';";
            $wpdb->query($sql);

            $table_name = $wpdb->prefix . 'wdk_listings';
            $sql = "ALTER TABLE `$table_name` CHANGE `listing_images_path_medium` `listing_images_path_medium` TEXT DEFAULT '';";
            $wpdb->query($sql);

            self::$db_version = 3.5;
            /* udpate option with db version */ 
        }

        if ( get_site_option( 'wdk_db_version' ) < '3.6' ) {

            $table_name = $wpdb->prefix . 'wdk_fields';

            $sql = "UPDATE `$table_name` SET `is_visible_dashboard` = '1'";
            $wpdb->query($sql);

            $table_name = $wpdb->prefix . 'wdk_listings';
            $sql = "ALTER TABLE `$table_name` CHANGE `locations_list` `locations_list` TEXT DEFAULT '';";
            $wpdb->query($sql);

            $table_name = $wpdb->prefix . 'wdk_listings';
            $sql = "ALTER TABLE `$table_name` CHANGE `categories_list` `categories_list` TEXT DEFAULT '';";
            $wpdb->query($sql);

            self::$db_version = 3.6;
            /* udpate option with db version */ 
        }

        if ( get_site_option( 'wdk_db_version' ) < '3.7' ) {

            $table_name = $wpdb->prefix . 'wdk_fields';

            $sql = "ALTER TABLE `$table_name` ADD `placeholder` varchar(128) DEFAULT NULL;";
            $wpdb->query($sql);

            self::$db_version = 3.7;
            /* udpate option with db version */ 
        }

        if ( get_site_option( 'wdk_db_version' ) < '3.8' ) {

            $table_name = $wpdb->prefix . 'wdk_messages';
            $sql = "ALTER TABLE `$table_name` ADD `is_notified` tinyint(1) DEFAULT NULL; ";
            $wpdb->query($sql);

            self::$db_version = 3.8;
            /* udpate option with db version */ 
        }

        if ( get_site_option( 'wdk_db_version' ) < '3.9' ) {

            $table_name = $wpdb->prefix . 'wdk_users';

            $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
                    `idusers` int(11) NOT NULL AUTO_INCREMENT,
                    `cacheduser_user_id` int(11) DEFAULT NULL,
                    `cacheduser_profile_url` text DEFAULT NULL,
                    `cacheduser_display_name` text DEFAULT NULL,
                    `cacheduser_email` text DEFAULT NULL,
                    `cacheduser_avatar_url` text DEFAULT NULL,
                    `cacheduser_wdk_address` text DEFAULT NULL,
                    `cacheduser_wdk_phone` text DEFAULT NULL,
                    `cacheduser_wdk_city` text DEFAULT NULL,
                    `cacheduser_wdk_company_name` text DEFAULT NULL,
                    `cacheduser_wdk_facebook` text DEFAULT NULL,
                    `cacheduser_wdk_youtube` text DEFAULT NULL,
                    `cacheduser_wdk_linkedin` text DEFAULT NULL,
                    `cacheduser_wdk_twitter` text DEFAULT NULL,
                    `cacheduser_wdk_instagram` text DEFAULT NULL,
                    `cacheduser_wdk_whatsapp` text DEFAULT NULL,
                    `cacheduser_wdk_viber` text DEFAULT NULL,
                    `cacheduser_wdk_iban` text DEFAULT NULL,
                    `cacheduser_wdk_position_title` text DEFAULT NULL,
                    `cacheduser_wdk_telegram` text DEFAULT NULL,
                    `cacheduser_roles` text DEFAULT NULL,
                    `cacheduser_description` text DEFAULT NULL,
                    `cacheduser_user_url` text DEFAULT NULL,
                    `cacheduser_json_data` text DEFAULT NULL,
                    `cacheduser_date_updated` datetime DEFAULT NULL,
                PRIMARY KEY  (idusers)
                ) $charset_collate;";
        
            dbDelta( $sql );

            self::$db_version = 3.9;
            /* udpate option with db version */ 
        }

        if ( get_site_option( 'wdk_db_version' ) < '4.0' ) {

            $table_name = $wpdb->prefix . 'wdk_listings';
            $sql = "ALTER TABLE `$table_name`  ADD `listing_related_ids` VARCHAR(512) DEFAULT ''";
            $wpdb->query($sql);

            $table_name = $wpdb->prefix . 'wdk_listings';
            $sql = "ALTER TABLE `$table_name`  ADD `sublisting_order` INT(11) DEFAULT NULL";
            $wpdb->query($sql);

            $table_name = $wpdb->prefix . 'wdk_listings';
            $sql = "ALTER TABLE `$table_name`  ADD `listing_parent_post_id` INT(11) DEFAULT NULL";
            $wpdb->query($sql);
                
            /* disable elmentor experement feature */
            update_option( 'elementor_experiment-landing-pages', 'inactive' );
            update_option( 'elementor_experiment-e_dom_optimization', 'inactive');

            self::$db_version = 4.0;
            /* udpate option with db version */ 
        }

        if ( get_site_option( 'wdk_db_version' ) < '4.1' ) {

            $table_name = $wpdb->prefix . 'wdk_dependfields';
            $sql = "ALTER TABLE `$table_name` CHANGE `hidden_fields_list` `hidden_fields_list` TEXT NULL DEFAULT NULL;";
            $wpdb->query($sql);

            self::$db_version = 4.1;
            /* udpate option with db version */ 
        }

        if ( get_site_option( 'wdk_db_version' ) < '4.2' ) {

            self::$db_version = 4.2;
            /* udpate option with db version */ 
        }

        if ( get_site_option( 'wdk_db_version' ) < '4.3' ) {
            $table_name = $wpdb->prefix . 'wdk_editlog';

            $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
                    `ideditlog` int(11) NOT NULL AUTO_INCREMENT,
                    `post_id` int(11) DEFAULT NULL,
                    `user_id` int(11) DEFAULT NULL,
                    `date` datetime DEFAULT NULL,
                    `ip` text DEFAULT NULL,
                    `comment` text DEFAULT NULL,
                PRIMARY KEY  (ideditlog)
                ) $charset_collate;";
        
            dbDelta( $sql );
            self::$db_version = 4.3;
            /* udpate option with db version */ 
        }

        if ( get_site_option( 'wdk_db_version' ) < '4.4' ) {
                
            /* disable elmentor experement feature */
            update_option( 'elementor_experiment-e_font_icon_svg', 'inactive');

            self::$db_version = 4.4;
            /* udpate option with db version */ 
        }

        if ( get_site_option( 'wdk_db_version' ) < '4.5' ) {
                
            /* disable elmentor experement feature */
            update_option( 'wdk_is_featured_enabled', '1');
            update_option( 'wdk_is_rank_enabled', '1');
            update_option( 'wdk_is_user_editor_enabled', '1');
            update_option( 'wdk_is_post_content_enable', '1');
            update_option( 'wdk_is_alt_agent_enabled', '1');

            $table_name = $wpdb->prefix . 'wdk_users';
            $sql = "ALTER TABLE `$table_name`  ADD `cacheduser_agency_name` text DEFAULT ''";
            $wpdb->query($sql);

            self::$db_version = 4.5;
            /* udpate option with db version */ 
        }
       
        update_option( 'wdk_db_version', self::$db_version );
    }
}   