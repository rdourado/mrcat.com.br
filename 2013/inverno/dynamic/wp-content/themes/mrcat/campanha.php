<?php 
/*
Template name: Campanha
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
				<?php the_content(); ?>
			</div>
		</article>
<?php 	endwhile; ?>
	</div>
<?php is_ajax() ? json_content() : get_footer(); ?>