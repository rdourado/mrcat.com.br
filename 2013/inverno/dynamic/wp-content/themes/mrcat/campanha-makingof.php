<?php 
/*
Template name: Fotos Making of
*/
?>
<?php is_ajax() ? ob_start() : get_header(); ?>
	<div id="body">
<?php 	while( have_posts() ) : the_post(); ?>
		<article id="content" <?php post_class(); ?>>
			<h1 class="entry-title"><?php the_title(); ?></h1>
			<?php 
			wp_nav_menu( array(
				'theme_location'  => 'campanha',
				'container' 	  => 'nav', 
				'container_class' => 'entry-nav',
				'menu_class' 	  => 'entry-menu',
				'fallback_cb' 	  => false,
				'depth' 		  => 1,
			) );
			?>

			<div class="entry-content">
<?php 			$images = get_field( 'gallery' );
				if ( $images ) : ?>
				<ul class="makingof-list">
<?php 				foreach( $images as $img ) : ?>
					<li class="makingof-item"><a href="<?php 
					echo $img['sizes']['large']; ?>" class="fancybox"><?php 
					echo wp_get_attachment_image( $img['id'], 'product-small' ); ?><span></span></a></li>
<?php 				endforeach; ?>
				</ul>
<?php 			endif; ?>
			</div>
		</article>
<?php 	endwhile; ?>
	</div>
<?php is_ajax() ? json_content() : get_footer(); ?>