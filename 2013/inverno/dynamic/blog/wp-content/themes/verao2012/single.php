<?php get_header(); ?>
<?php get_sidebar(); ?>
	<div id="contentarea">

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	
		<div class="navigation">
			<div class="alignleft"><?php previous_post('&laquo; %','','yes') ?></div>
			<div class="alignright"><?php next_post(' % &raquo;','','yes') ?></div>
		</div>
	
		<div class="post">
		
				<h2 id="post-<?php the_ID(); ?>"><div class="data"><?php the_time('j/m') ?></div><div id="titulocom"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></div></h2>
				
				
	
			<div class="entrytext">
				<?php the_content(''); ?>
	
				<?php link_pages('<p><strong>Pages:</strong> ', '</p>', 'number'); ?>
	
	
			</div>
			
			
		</div>
		
	<?php comments_template(); ?>
	
	<?php endwhile; else: ?>
	
		<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
	
<?php endif; ?>
	</div>



<?php get_footer(); ?>