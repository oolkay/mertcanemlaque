<?php
/**
 * The searchform.php template.
 *
 * Used any time that get_search_form() is called.
 */

/*
 * Generate a unique ID for each form and a string containing an aria-label
 * if one was passed to get_search_form() in the args array.
 */
$nexproperty_unique_id = wp_unique_id( 'search-form-' );

$nexproperty_aria_label = ! empty( $args['aria_label'] ) ? 'aria-label="' . esc_attr( $args['aria_label'] ) . '"' : '';
?>
<form role="search" <?php echo esc_attr($nexproperty_aria_label); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped above. ?> method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<input type="search" id="<?php echo esc_attr( $nexproperty_unique_id ); ?>" class="search-field" value="<?php echo esc_attr(get_search_query()); ?>" placeholder="<?php echo esc_attr__('Search...', 'nexproperty');?>" name="s" />
	<button type="submit" class="search-submit"><?php echo esc_html__( 'Search', 'nexproperty' ); ?></button>
</form>
