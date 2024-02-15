<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly;

class Wdk_addons extends Winter_MVC_Controller {

	public function __construct(){
		parent::__construct();
	}
    
	public function index()
	{

        $this->data['addons'] = array();

        $this->data['addons'] []  = array(
            'title' => __('Energy efficiency', 'wpdirectorykit'),
            'description' =>__('Show nicely designed energy efficiency on Listing preview page based on Field selected', 'wpdirectorykit'),
            'thumbnail' => 'https://wpdirectorykit.com/img/plugins/wp-sweet-energy-efficiency.jpg',
            'link' => 'https://wpdirectorykit.com/plugins/sweet-energy-efficiency.html',
            'link_info' => 'https://wpdirectorykit.com/plugins/sweet-energy-efficiency.html',
            'slug' => 'sweet-energy-efficiency',
            'is_exists_slug' => 'sweet-energy-efficiency/sweet-energy-efficiency.php',
            'is_activated_slug' => 'run_sweet_energy_efficiency',
        );

        $this->data['addons'] []  = array(
            'title' => __('Multy Currency', 'wpdirectorykit'),
            'description' =>__('Automatically import and sync wanted currencies available in API and auto conver on Homepage Based on Switcher selection', 'wpdirectorykit'),
            'thumbnail' => 'https://wpdirectorykit.com/img/plugins/wp-multi-currency.jpg',
            'link' => 'https://wpdirectorykit.com/plugins/wp-directory-multy-currency.html',
            'link_info' => 'https://wpdirectorykit.com/plugins/wp-directory-multy-currency.html',
            'slug' => 'wpdirectorykit',
            'is_activated_slug' => 'run_wdk_currency_conversion',
            'is_exists_slug' => 'wdk-currency-conversion/wdk-currency-conversion.php',
        );

        $this->data['addons'] []  = array(
            'title' => __('Booking & Calendar', 'wpdirectorykit'),
            'description' =>__('Calendar and Availability Rates/Prices per Hour/Day/Week... Reservations from Listing Page based on Availability.', 'wpdirectorykit'),
            'thumbnail' => 'https://wpdirectorykit.com/img/plugins/wp-booking-calendar.jpg',
            'link' => 'https://wpdirectorykit.com/plugins/wp-directory-booking-calendar.html',
            'link_info' => 'https://wpdirectorykit.com/plugins/wp-directory-booking-calendar.html',
            'slug' => 'wpdirectorykit',
            'is_activated_slug' => 'run_wdk_bookings',
            'is_exists_slug' => 'wdk-bookings/wdk-bookings.php',
        );

        $this->data['addons'] []  = array(
            'title' => __('Review system', 'wpdirectorykit'),
            'description' =>__('Users will be able to rate listing or profiles based on configurable multiple criterias/options individually.', 'wpdirectorykit'),
            'thumbnail' => 'https://wpdirectorykit.com/img/plugins/wp-review-system.jpg',
            'link' => 'https://wpdirectorykit.com/plugins/wp-directory-review-system.html',
            'link_info' => 'https://wpdirectorykit.com/plugins/wp-directory-review-system.html',
            'slug' => 'wpdirectorykit',
            'is_activated_slug' => 'run_wdk_reviews',
            'is_exists_slug' => 'wdk-reviews/wdk-reviews.php',
        );

        $this->data['addons'] []  = array(
            'title' => __('Membership Features', 'wpdirectorykit'),
            'description' =>__('Login, Registration, Frontend Dashboard, Search for Agent, and every Agent have own page with their Listings and Details.', 'wpdirectorykit'),
            'thumbnail' => 'https://wpdirectorykit.com/img/plugins/wp-membership-features.jpg',
            'link' => 'https://wpdirectorykit.com/plugins/wp-directory-membership.html',
            'link_info' => 'https://wpdirectorykit.com/plugins/wp-directory-membership.html',
            'slug' => 'wpdirectorykit',
            'is_activated_slug' => 'run_wdk_membership',
            'is_exists_slug' => 'wdk-membership/wdk-membership.php',
        );

        $this->data['addons'] []  = array(
            'title' => __('Import/Export', 'wpdirectorykit'),
            'description' =>__('XML Export, CSV/XML Import via popular Plugin Wp All Import and VIsual Fields Mapping.', 'wpdirectorykit'),
            'thumbnail' => 'https://wpdirectorykit.com/img/plugins/wp-import-export.jpg',
            'link' => 'https://wpdirectorykit.com/plugins/wp-directory-import-export.html',
            'link_info' => 'https://wpdirectorykit.com/plugins/wp-directory-import-export.html',
            'slug' => 'wdk-wp-all-import',
            'is_activated_slug' => 'wdk_export_xml',
            'is_exists_slug' => 'wdk-wp-all-import/rapid-addon.php',
        );

        $this->data['addons'] []  = array(
            'title' => __('WP Directory Kit - Favorites', 'wpdirectorykit'),
            'description' =>__('Visitors will be able to Save any listing as Favorite for later use, Manage from User Frontend Dashboard.', 'wpdirectorykit'),
            'thumbnail' => 'https://wpdirectorykit.com/img/plugins/wp-favorites.jpg',
            'link' => 'https://wpdirectorykit.com/plugins/wp-directory-favorites.html',
            'link_info' => 'https://wpdirectorykit.com/plugins/wp-directory-favorites.html',
            'slug' => 'wpdirectorykit',
            'is_activated_slug' => 'run_wdk_favorites',
            'is_exists_slug' => 'wdk-favorites/wdk-favorites.php',
        );

        $this->data['addons'] []  = array(
            'title' => __('WP Directory Kit - Mortgage Calculator', 'wpdirectorykit'),
            'description' =>__('Allow Visitor to calculate Mortgage / Loan Prices based on related listing price or enter values manually.', 'wpdirectorykit'),
            'thumbnail' => 'https://wpdirectorykit.com/img/plugins/wp-mortgage-loan.jpg',
            'link' => 'https://wpdirectorykit.com/plugins/wp-directory-mortgage-loan-calculator.html',
            'link_info' => 'https://wpdirectorykit.com/plugins/wp-directory-mortgage-loan-calculator.html',
            'slug' => 'wpdirectorykit',
            'is_activated_slug' => 'run_wdk_mortgage',
            'is_exists_slug' => 'wdk-mortgage/wdk-mortgage.php',
        );

        $this->data['addons'] []  = array(
            'title' => __('Profile picture uploader', 'wpdirectorykit'),
            'description' =>__('WordPress support only Gravatar, with this plugin visitors will be able to upload own image directly from Dashboard.', 'wpdirectorykit'),
            'thumbnail' => 'https://wpdirectorykit.com/img/plugins/wp-profile-picture-uploader.jpg',
            'link' => 'https://wpdirectorykit.com/plugins/wp-directory-profile-picture-uploader.html',
            'link_info' => 'https://wpdirectorykit.com/plugins/wp-directory-profile-picture-uploader.html',
            'slug' => 'profile-picture-uploader',
            'is_activated_slug' => 'ppu_custom_user_profile_fields',
            'is_exists_slug' => 'profile-picture-uploader/profile-picture-uploader.php',
        );

        $this->data['addons'] []  = array(
            'title' => __('MailChimp Newsletter', 'wpdirectorykit'),
            'description' =>__('Generate email list ready to use on MailChimp Newsletter service.', 'wpdirectorykit'),
            'thumbnail' => 'https://wpdirectorykit.com/img/plugins/wp-mailchimp.jpg',
            'link' => 'https://wpdirectorykit.com/plugins/wp-directory-mailchimp.html',
            'link_info' => 'https://wpdirectorykit.com/plugins/wp-directory-mailchimp.html',
            'slug' => 'wdk-mailchimp',
            'is_activated_slug' => 'run_wdk_mailchimp',
            'is_exists_slug' => 'wdk-mailchimp/wdk-mailchimp.php',
        );

        $this->data['addons'] []  = array(
            'title' => __('Facebook Comments', 'wpdirectorykit'),
            'description' =>__('Connect with social network, show Facebook comments directly on your listing preview pages.', 'wpdirectorykit'),
            'thumbnail' => 'https://wpdirectorykit.com/img/plugins/wp-facebook.jpg',
            'link' => 'https://wpdirectorykit.com/plugins/wp-directory-facebook.html',
            'link_info' => 'https://wpdirectorykit.com/plugins/wp-directory-facebook.html',
            'slug' => 'wdk-facebook-comments',
            'is_activated_slug' => 'run_wdk_facebook_comments',
            'is_exists_slug' => 'wdk-facebook-comments/wdk-facebook-comments.php',
        );

        $this->data['addons'] []  = array(
            'title' => __('Report Abuse', 'wpdirectorykit'),
            'description' =>__('Report Abuse button will allow your visitors to report abused listings directly to admin.', 'wpdirectorykit'),
            'thumbnail' => 'https://wpdirectorykit.com/img/plugins/wp-report-abuse.jpg',
            'link' => 'https://wpdirectorykit.com/plugins/wp-directory-report-abuse.html',
            'link_info' => 'https://wpdirectorykit.com/plugins/wp-directory-report-abuse.html',
            'slug' => 'wdk-report-abuse',
            'is_activated_slug' => 'run_wdk_report_abuse',
            'is_exists_slug' => 'wdk-report-abuse/wdk-report-abuse.php',
        );

        $this->data['addons'] []  = array(
            'title' => __('Payments Listing Packages', 'wpdirectorykit'),
            'description' =>__('Monetize your website with payments packages and all WooCommerce payment gateways supported.', 'wpdirectorykit'),
            'thumbnail' => 'https://wpdirectorykit.com/img/plugins/wp-payments.jpg',
            'link' => 'https://wpdirectorykit.com/plugins/wp-directory-payments.html',
            'link_info' => 'https://wpdirectorykit.com/plugins/wp-directory-payments.html',
            'slug' => 'wdk-payments',
            'is_activated_slug' => 'run_wdk_payments',
            'is_exists_slug' => 'wdk-payments/wdk-payments.php',
        );

        $this->data['addons'] []  = array(
            'title' => __('Compare Listings', 'wpdirectorykit'),
            'description' =>__('Your visitors will be able to compare listings, so will see up to 4 listings at once in table.', 'wpdirectorykit'),
            'thumbnail' => 'https://wpdirectorykit.com/img/plugins/wp-compare-listings.jpg',
            'link' => 'https://wpdirectorykit.com/plugins/wp-compare-listings.html',
            'link_info' => 'https://wpdirectorykit.com/plugins/wp-compare-listings.html',
            'slug' => 'wdk-compare-listing',
            'is_activated_slug' => 'run_wdk_compare_listing',
            'is_exists_slug' => 'wdk-compare-listing/wdk-compare-listing.php',
        );

        $this->data['addons'] []  = array(
            'title' => __('Save Search', 'wpdirectorykit'),
            'description' =>__('Your visitors will be able to Save Search, run it from Frontend Dash and receive Email Alerts when new/changed listing become available.', 'wpdirectorykit'),
            'thumbnail' => 'https://wpdirectorykit.com/img/plugins/wp-save-search.jpg',
            'link' => 'https://wpdirectorykit.com/plugins/wp-directory-save-search.html',
            'link_info' => 'https://wpdirectorykit.com/plugins/wp-directory-save-search.html',
            'slug' => 'wdk-save-search',
            'is_activated_slug' => 'run_wdk_save_search',
            'is_exists_slug' => 'wdk-save-search/wdk-save-search.php',
        );

        $this->data['addons'] []  = array(
            'title' => __('WDK Duplicate Listing', 'wpdirectorykit'),
            'description' =>__('One Click to Duplicate any listing from backend Dashboard.', 'wpdirectorykit'),
            'thumbnail' => 'https://wpdirectorykit.com/img/plugins/wp-duplicate-listing.jpg',
            'link' => 'https://wpdirectorykit.com/plugins/wp-directory-duplicate-listing.html',
            'link_info' => 'https://wpdirectorykit.com/plugins/wp-directory-duplicate-listing.html',
            'slug' => 'wdk-duplicate-listing',
            'is_activated_slug' => 'run_wdk_duplicate_listing',
            'is_exists_slug' => 'wdk-duplicate-listing/wdk-duplicate-listing.php',
        );

        $this->data['addons'] []  = array(
            'title' => __('WDK PDF Download', 'wpdirectorykit'),
            'description' =>__('Download PDF Button Elementor Element for Listing preview page.', 'wpdirectorykit'),
            'thumbnail' => 'https://wpdirectorykit.com/img/plugins/wp-pdf-download.jpg',
            'link' => 'https://wpdirectorykit.com/plugins/wp-directory-pdf-download.html',
            'link_info' => 'https://wpdirectorykit.com/plugins/wp-directory-pdf-download.html',
            'slug' => 'wdk-pdf-export',
            'is_activated_slug' => 'run_wdk_pdf_export',
            'is_exists_slug' => 'wdk-pdf-export/wdk-pdf-export.php',
        );

        $this->data['addons'] []  = array(
            'title' => __('WDK Claim / Take Ownership', 'wpdirectorykit'),
            'description' =>__('Your visitors will be able to Claim/Take Ownership on their Listing.', 'wpdirectorykit'),
            'thumbnail' => 'https://wpdirectorykit.com/img/plugins/wp-listing-claim.jpg',
            'link' => 'https://wpdirectorykit.com/plugins/wp-directory-listing-claim.html',
            'link_info' => 'https://wpdirectorykit.com/plugins/wp-directory-listing-claim.html',
            'slug' => 'wdk-listing-claim',
            'is_activated_slug' => 'run_wdk_listing_claim',
            'is_exists_slug' => 'wdk-listing-claim/wdk-listing-claim.php',
        );

        $this->data['addons'] []  = array(
            'title' => __('WDK Geo Coding', 'wpdirectorykit'),
            'description' =>__('Location Autodetection based on IP or Google API, supporting also auto search after visitor confirmation', 'wpdirectorykit'),
            'thumbnail' => 'https://wpdirectorykit.com/img/plugins/wp-geo.jpg',
            'link' => 'https://wpdirectorykit.com/plugins/wp-directory-geo-coding.html',
            'link_info' => 'https://wpdirectorykit.com/plugins/wp-directory-geo-coding.html',
            'slug' => 'wdk-geo',
            'is_activated_slug' => 'run_wdk_geo',
            'is_exists_slug' => 'wdk-geo/wdk-geo.php',
        );

        $this->data['addons'] []  = array(
            'title' => __('WDK SVG Map', 'wpdirectorykit'),
            'description' =>__('SVG Map with elementor element for search based on map', 'wpdirectorykit'),
            'thumbnail' => 'https://wpdirectorykit.com/img/plugins/wp-svg-map.jpg',
            'link' => 'https://wpdirectorykit.com/plugins/wp-directory-svg-map.html',
            'link_info' => 'https://wpdirectorykit.com/plugins/wp-directory-svg-map.html',
            'slug' => 'wdk-svg-map',
            'is_activated_slug' => 'run_wdk_svg_map',
            'is_exists_slug' => 'wdk-svg-map/wdk-svg-map.php',
        );

        $this->data['addons'] []  = array(
            'title' => __('WDK Live Messages Chat', 'wpdirectorykit'),
            'description' =>__('Live chat option between users will simplify and provide faster communication', 'wpdirectorykit'),
            'thumbnail' => 'https://wpdirectorykit.com/img/plugins/wp-messages-chat.jpg',
            'link' => 'https://wpdirectorykit.com/plugins/wp-directory-messages-chat.html',
            'link_info' => 'https://wpdirectorykit.com/plugins/wp-directory-messages-chat.html',
            'slug' => 'wdk-messages-chat',
            'is_activated_slug' => 'run_wdk_messages_chat',
            'is_exists_slug' => 'wdk-messages-chat/wdk-messages-chat.php',
        );

        // Load view
        $this->load->view('wdk_addons/index', $this->data);
    }


}
