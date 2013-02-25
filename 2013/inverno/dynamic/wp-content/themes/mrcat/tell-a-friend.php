<?php 
/*
Template name: Tell a friend
*/
?>
<?php get_header(); ?>
	<div id="body">
<?php 	while( have_posts() ) : the_post(); ?>
		<article id="content" <?php post_class(); ?>>
			<h1 class="entry-title"><?php the_title(); ?></h1>
			<div class="entry-content">
				<?php if ( is_tellafriend( $_GET['pid'] ) ) insert_cform( 5 ); ?>
			</div>
		</article>
<?php 	endwhile; ?>
	</div>
<?php get_footer(); ?>