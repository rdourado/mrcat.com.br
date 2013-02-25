<?php get_header(); ?>
<?php get_sidebar(); ?>
	<div id="contentarea">

	<?php if (have_posts()) : ?>
		
		<?php while (have_posts()) : the_post(); ?>
				
			<div class="post">
				<h2 id="post-<?php the_ID(); ?>"><div class="data"><?php the_time('j/m') ?></div><div class="titulo"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></div></h2>
				
				
				<div class="entry">
					<?php the_content('Read the rest of this entry &raquo;'); ?>
				</div>
		<div id="footercom"></div>
				<p class="postmetadata"><?php edit_post_link('Edit','','<strong> |</strong>'); ?>  <?php comments_popup_link('Sem comentários &#187;', '1 Comentário &#187;', '% Comentários &#187;'); ?></p>


				
				<!--
				<?php trackback_rdf(); ?>
				-->
			</div>
	
		<?php endwhile; ?>

		<div class="navigation">
			<div class="alignleft"><?php posts_nav_link('','','&laquo; Previous Entries') ?></div>
			<div class="alignright"><?php posts_nav_link('','Next Entries &raquo;','') ?></div>
		</div>
		
	<?php else : ?>

		<h2 class="center">Not Found</h2>
		<p class="center"><?php _e("Sorry, but you are looking for something that isn't here."); ?></p>
		<?php include (TEMPLATEPATH . "/searchform.php"); ?>

	<?php endif; ?>
	</div>



<?php get_footer(); ?>