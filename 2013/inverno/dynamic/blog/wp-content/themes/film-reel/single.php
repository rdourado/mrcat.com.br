
<?php get_header(); ?>
<?php get_sidebar(); ?>
	<div id="contentarea">

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	
		<div class="navigation">
			<div class="alignleft"><?php previous_post('&laquo; %','','yes') ?></div>
			<div class="alignright"><?php next_post(' % &raquo;','','yes') ?></div>
		</div>
	
		<div class="post">
		
				<h2 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
				
				<div class="postdetails"><small>Posted on <?php the_date('') ?> by <?php the_author() ?> | <?php comments_number('No Comments','1 Comment','% Comments'); ?></small></div>
	
			<div class="entrytext">
				<?php the_content('<p class="serif">Read the rest of this entry &raquo;</p>'); ?>
	
				<?php link_pages('<p><strong>Pages:</strong> ', '</p>', 'number'); ?>
	
	
			
			
				<p class="postdetailsbottom">
					<small>
						This entry was posted on <?php the_time('l, F jS, Y') ?> at <?php the_time() ?>
						and is filed under <?php the_category(', ') ?>.
						You can follow any comments to this post through the <?php comments_rss_link('RSS 2.0'); ?> feed. 
						
						<?php if (('open' == $post-> comment_status) && ('open' == $post->ping_status)) {
							// Both Comments and Pings are open ?>
							You can <a href="#respond">leave a response</a>, or <a href="<?php trackback_url(display); ?>">trackback</a> from your own site.
						
						<?php } elseif (!('open' == $post-> comment_status) && ('open' == $post->ping_status)) {
							// Only Pings are Open ?>
							Responses are currently closed, but you can <a href="<?php trackback_url(display); ?> ">trackback</a> from your own site.
						
						<?php } elseif (('open' == $post-> comment_status) && !('open' == $post->ping_status)) {
							// Comments are open, Pings are not ?>
							You can skip to the end and leave a response. Pinging is currently not allowed.
			
						<?php } elseif (!('open' == $post-> comment_status) && !('open' == $post->ping_status)) {
							// Neither Comments, nor Pings are open ?>
							Both comments and pings are currently closed.			
						
						<?php } edit_post_link('Edit this entry.','',''); ?>
						
					</small>
				</p>
				
				<br>
			<div class="socialbookmarking">
			Bookmark this post: <br />
			
			<a href="http://digg.com/submit?phase=2&amp;url=<?php the_permalink() ?>&amp;title=<?php the_title(); ?>">
			<img src="<?php bloginfo('stylesheet_directory'); ?>/images/socialbookmarkingicons/digg.png" alt="Digg" />
			</a>
			<a href="http://del.icio.us/post?url=<?php the_permalink() ?>&amp;title=<?php the_title(); ?>">
			<img src="<?php bloginfo('stylesheet_directory'); ?>/images/socialbookmarkingicons/delicious.png" alt="Del.icio.us" />
			</a>
			<a href="http://reddit.com/submit?url=<?php the_permalink() ?>&amp;title=<?php the_title(); ?>">
			<img src="<?php bloginfo('stylesheet_directory'); ?>/images/socialbookmarkingicons/reddit.gif" alt="Reddit" />
			</a>
			<a href="http://furl.net/storeIt.jsp?u=<?php the_permalink() ?>&amp;t=<?php the_title(); ?>">
			<img src="<?php bloginfo('stylesheet_directory'); ?>/images/socialbookmarkingicons/furl.gif" alt="Furl" />
			</a>
			<a href="http://google.com/bookmarks/mark?op=edit&amp;bkmk=<?php the_permalink() ?>&amp;title=<?php the_title(); ?>">
			<img src="<?php bloginfo('stylesheet_directory'); ?>/images/socialbookmarkingicons/google.png" alt="Google Bookmarks" />
			</a>
			<a href="http://stumbleupon.com/submit?url=<?php the_permalink() ?>&amp;title=<?php the_title(); ?>">
			<img src="<?php bloginfo('stylesheet_directory'); ?>/images/socialbookmarkingicons/stumbleupon.gif" alt="StumbleUpon" />
			</a>
			<a href="http://favorites.live.com/quickadd.aspx?marklet=1&amp;mkrt=en-us&amp;url=<?php the_permalink() ?>&amp;title=<?php the_title(); ?>">
			<img src="<?php bloginfo('stylesheet_directory'); ?>/images/socialbookmarkingicons/live.gif" alt="Windows Live" />
			</a>
			<a href="http://www.technorati.com/faves?add=<?php the_permalink() ?>">
			<img src="<?php bloginfo('stylesheet_directory'); ?>/images/socialbookmarkingicons/technorati.png" alt="Technorati" />
			</a>
			<a href="http://myweb2.search.yahoo.com/myresults/bookmarklet?u=<?php the_permalink() ?>&amp;t=<?php the_title(); ?>">
			<img src="<?php bloginfo('stylesheet_directory'); ?>/images/socialbookmarkingicons/yahoo-myweb.png" alt="Yahoo MyWeb" />
			</a>
				
			</div> <!-- End div id of social bookmarking --><br /><br />
	
			</div>
		</div>
		
	<?php comments_template(); ?>
	
	<?php endwhile; else: ?>
	
		<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
	
<?php endif; ?>
	</div>



<?php get_footer(); ?>