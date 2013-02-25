<?php get_header(); ?>
	<div id="body">
		<article id="content">
			<h1 class="entry-title">Clipping</h1>
			<div class="entry-content">
				<ul class="clipping-list">
<?php 				while( have_posts() ) : the_post(); ?>
					<li class="clip-item item-<?php echo $i++%2; ?>">
						<?php echo wp_get_attachment_image( get_field( '_thumbnail_id' ), 'clipping', false, array( 'alt' => '' ) ); ?>
						<h2 class="clip-name"><?php the_title(); ?></h2>
					</li>
<?php 				endwhile; ?>
				</ul>
				<?php if ( function_exists( 'wp_pagenavi' ) ) wp_pagenavi(); ?>
			</div>
		</article>
	</div>
<?php get_footer(); ?>