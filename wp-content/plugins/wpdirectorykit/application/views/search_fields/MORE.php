<?php
/**
 * The template for Search field MORE.
 *
 * This is the template that field layout for search form
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<?php
global $wdk_activate_more;
global $wdk_enable_search_fields_toggle;

$wdk_enable_search_fields_toggle = true;

$wdk_activate_more = true;
wdk_search_fields_toggle();
?>

<div class="wdk-field wdk-col <?php echo esc_attr(wmvc_show_data('field_type', $field_data)); ?> <?php echo esc_attr(wmvc_show_data('class', $field_data)); ?>">
</div>