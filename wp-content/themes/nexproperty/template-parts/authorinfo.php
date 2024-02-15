
<div class="ath-info">
	<div class="abt-miller-img">
		<img src="<?php echo esc_url( get_avatar_url( get_the_author_meta( 'ID' ) ) ); ?>" alt="<?php esc_attr( the_author() ); ?>">
	</div><!--abt-miller-img end-->
	<div class="abt-miller-info">
		<h3><?php the_author(); ?></h3>
		<p class="phras"><?php the_author_meta( 'description' ); ?></p>
		<ul class="social_links">
			<li><a href="#"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
			<li><a href="#"><i class="fa fa-pinterest-p" aria-hidden="true"></i></a></li>
			<li><a href="#"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
		</ul>
	</div><!--abt-miller-info end-->
</div><!--author-info end-->