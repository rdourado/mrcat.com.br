<?php 
/*
Template name: Home
*/
$homeID = get_option( 'page_on_front' );
?>
<?php get_header(); ?>
	<div id="body">
		<div id="highlight">
			<a href="<?php echo get_permalink( 3163 ); ?>"><?php 
			$img = get_field( 'highlight', $homeID ); ?><img src="<?php 
			echo $img['sizes']['home-large']; ?>" alt="<?php 
			echo $img['title']; ?>" width="1440" height="647"></a>
		</div>
		<ul id="categories">
<?php 		while( has_sub_field( 'categories' ) ) : ?>
			<li class="cat-item"><a href="<?php 
			the_sub_field( 'link' ); ?>"><?php 
			$img = get_sub_field( 'image' ); ?><img src="<?php 
			echo $img['sizes']['home-small']; ?>" alt="<?php 
			echo $img['title']; ?>" width="310" height="277"></a></li>
<?php 		endwhile; ?>
		</ul>
		<div id="instagram">
<?php 		$result = fetch_data( "https://api.instagram.com/v1/users/20714375/media/recent/?access_token=MY_TOKEN&count=6" );
			$result = json_decode( $result );
			if ( $result ) : ?>
			<h2 class="heading"><img src="<?php t_url(); ?>/img/instagram.png" alt="Instagram" width="168" height="57"></h2>
			<ul class="instagram-list">
<?php 			foreach( $result->data as $row ) : ?>
				<li class="instagram-item"><a href="<?php echo $row->link; ?>" target="_blank"><img src="<?php echo $row->images->thumbnail->url; ?>" alt="" width="100" height="100"></a></li>
<?php 			endforeach; ?>
			</ul>
<?php 		endif; ?>
		</div>
		<div id="blog">
<?php 		global $wpdb;
			$sql = "SELECT `ID`,`post_title`,`post_content`,`post_date`
					FROM `wp_posts` 
					WHERE `post_type`='post' AND `post_status`='publish' 
					ORDER BY `ID` DESC";
			$item = $wpdb->get_row( $sql );
			if ( $item ) : ?>
			<h2 class="heading">Blog</h2>
			<a href="http://www.mrcat.com.br/blog/?p=<?php echo $item->ID; ?>" class="blog-link" target="_blank">
				<?php 
				global $t_url;
				$output = preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $item->post_content, $matches );
				$first_img = $matches[1][0];
				if ( $first_img )
					echo '<img src="' . $t_url . '/timthumb.php?src=' . $first_img . '&amp;h=225&amp;w=536&amp;a=t" alt="" class="blog-thumb" width="536" height="225" />' . "\n";
				?>
				<div class="blog-info">
					<time datetime="<?php 
					echo mysql2date( 'Y-m-d', $item->post_date ); ?>" class="blog-date"><?php 
					echo mysql2date( 'j \d\e F \d\e Y', $item->post_date ); ?></time>
					<h3 class="blog-title"><?php echo $item->post_title; ?></h3>
				</div>
			</a>
<?php 		endif; ?>
		</div>
	</div>
<?php get_footer(); ?>