<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package NexProperty
 */

$footer_logo = get_theme_mod( 'footer_logo' );
if( get_theme_mod( 'footer_logo' )) {
	$footer_logo = get_theme_mod( 'footer_logo' );
}
$footer_content = get_theme_mod( 'footer_content' );
$footer_phone_number = get_theme_mod( 'footer_phone_number' );
$email_address = get_theme_mod( 'footer_email_address' );

?>
	<footer>
		<div class="container">
			<div class="top-footer">
				<div class="row">
					<?php if( is_active_sidebar( 'footer' ) ) : ?>
					<div class="col-lg-3 col-md-4 col-sm-6 col-12">
						<div class="my-property widget">
							<?php if( $footer_logo ) : ?>
								<img src="<?php echo esc_url( $footer_logo ) ?>" alt="<?php echo esc_attr( 'footer logo' ) ?>">
							<?php else: ?>
								<p><?php echo esc_html( get_bloginfo( 'name' ) ); ?></p>
							<?php endif; ?>
							<?php if( $footer_content ) : ?>
								<p><?php echo wp_kses_post( $footer_content ); ?></p>
							<?php else: ?>
								<p><?php echo esc_html( get_bloginfo( 'description' ) ); ?></p>
							<?php endif; ?>
							<?php if( $footer_phone_number || $email_address ) : ?>
								<ul>
									<?php if( $footer_phone_number ) : ?>
										<li><i class="fa fa-phone" aria-hidden="true"></i> <?php echo esc_html( $footer_phone_number ); ?></li>
									<?php endif; 
									if( $email_address ) : ?>
										<li><i class="fa fa-envelope-o" aria-hidden="true"></i>  <?php echo esc_html( $email_address ); ?></li>
									<?php endif; ?>
								</ul>
							<?php endif; ?>
						</div><!--my-property end-->
					</div>
					<?php endif;?>
					<?php
						if( is_active_sidebar( 'footer' ) ) : 
							dynamic_sidebar( 'footer' ); 
						endif;
					?>
				</div>
			</div><!--top-footer end-->
			<?php if( get_theme_mod( 'footer_copyright_text') ) : ?>
				<div class="bottom-footer">
					<div class="row">
						<div class="col-sm-6 text-sm-left">
							<h3><?php echo esc_html(get_theme_mod( 'footer_copyright_text' )); ?></h3>
						</div>
						<div class="col-sm-6 text-sm-right">
							<h3>
								<?php if(!empty(get_theme_mod( 'footer_powered_by_link' ))):?>
									<a href="<?php echo esc_url(get_theme_mod( 'footer_powered_by_link' ));?>" target="_blank"><?php echo esc_html(get_theme_mod( 'footer_powered_by' ));?></a>
								<?php else:?>
									<?php echo esc_html(get_theme_mod( 'footer_powered_by' ));?>
								<?php endif;?>
							</h3>
						</div>
					</div>
					
				</div><!--bottom-footer end-->
			<?php endif; ?>
		</div>
	</footer><!--footer end-->
</div><!--wrapper end-->
<?php wp_footer(); ?>
</body>
</html>
