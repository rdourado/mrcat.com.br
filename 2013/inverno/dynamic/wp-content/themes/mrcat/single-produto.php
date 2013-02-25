<?php get_header(); ?>
	<div id="body">
		<article id="content">
			<h1 class="entry-title"><?php 
			$current_post = $post;
			$term = end( wp_get_post_terms( get_the_ID(), 'categorias' ) );
			$parent = get_taxonomy_parent( $term );
			echo $parent->name;
			?></h1>
			<nav class="categorias-nav">
				<h2 class="current-name"><?php echo $term->name; ?></h2>
				<ul class="categorias-menu">
<?php 				wp_list_categories( array(
						'title_li' => '',
						'child_of' => $parent->term_id,
						'taxonomy' => 'categorias',
						'current_category' => $term->term_id,
					) ); ?>
				</ul>
			</nav>
			<div class="entry-content">
				<ul class="products-list">
<?php 				$loop = new WP_Query( "post_type=produto&categorias={$term->slug}&posts_per_page=-1&orderby=menu_order title&order=ASC&current_category={$term->term_id}" );
					while( $loop->have_posts() ) : $loop->the_post(); ?>
					<li class="product-item"><?php 
					$img = get_field( '_thumbnail_id' ); ?><a id="<?php 
					echo $post->post_name; ?>" href="<?php 
					echo $img['sizes']['product-large']; 
					?>" data-url="<?php the_permalink(); ?>" class="<?php 
					echo $current_post->ID == $post->ID ? 'active' : ''; ?>"><img src="<?php 
					echo $img['sizes']['product-small']; ?>" alt="<?php 
					echo $img['title']; ?>" width="238" height="172"><span></span></a></li>
<?php 				endwhile; ?>
				</ul>
			</div>
			<a href="<?php echo get_term_link( $parent, 'categorias' ); ?>" id="back">« Voltar</a>
		</article>
	</div>
<?php get_footer(); ?>