<?php 
/*
Template name: Produtos (Parceria)
*/
?>
<?php get_header(); ?>
	<div id="body">
		<article id="content" <?php post_class(); ?>>
			<h1 class="entry-title">Parcerias</h1>
			<div class="entry-content">
				<?php 
				global $post;
				$query = new WP_Query( 'page_id=' . $post->post_parent );
				while( $query->have_posts() ) :
					$query->the_post();
					the_content();
				endwhile;
				wp_reset_postdata();
				?>
			</div>
			<div class="entry-aside">
				<?php 
				while( have_posts() ) :
					the_post();
					the_content();
				endwhile;
				?>
				<ul class="products-list">
<?php 				$loop = new WP_Query( "post_type=produto&categorias=auslander&posts_per_page=-1&orderby=menu_order title&order=ASC" );
					while( $loop->have_posts() ) : $loop->the_post(); ?>
					<li class="product-item"><?php 
					$img = get_field( '_thumbnail_id' ); ?><a id="<?php 
					echo $post->post_name; ?>" href="<?php 
					echo $img['sizes']['product-large']; ?>" data-url="<?php the_permalink(); 
					?>"><img src="<?php echo $img['sizes']['product-small']; ?>" alt="<?php 
					echo $img['title']; ?>" width="238" height="172"><span></span></a></li>
<?php 				endwhile; ?>
				</ul>
			</div>
		</article>
	</div>
<?php get_footer(); ?>