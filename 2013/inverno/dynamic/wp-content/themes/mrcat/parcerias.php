<?php 
/*
Template name: Parcerias
*/
?>
<?php get_header(); ?>
	<div id="body">
<?php 	while( have_posts() ) : the_post(); ?>
		<article id="content" <?php post_class(); ?>>
			<h1 class="entry-title"><?php the_title(); ?></h1>
			<div class="entry-content">
				<?php the_content(); ?>
			</div>
			<aside class="entry-aside">
				<?php the_field( 'sidebar' ); ?>
			</aside>
		</article>
<?php 	endwhile; ?>
	</div>
<?php get_footer(); ?>