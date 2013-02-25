<?php get_header(); ?>
	<div id="body">
		<article id="content" <?php post_class(); ?>>
			<h1 class="entry-title"><?php 
			$term = get_queried_object();
			$img = get_field( 'taxonomy_large', "categorias_{$term->term_id}" );
			?><img src="<?php 
			echo $img['sizes']['taxonomy-large']; ?>" alt="<?php 
			echo $img['title']; ?>" width="960" height="472"></h1>
			<div class="entry-content">
<?php 			$menu = get_nav_menu_locations();
				$menu = $menu[$term->slug];
				$menu = wp_get_nav_menu_object( $menu );
				$menu_items = wp_get_nav_menu_items( $menu->term_id ); ?>
				<ul class="taxonomy-list">
<?php 			foreach( (array) $menu_items as $key => $item ) : 
					$img = get_field( 'taxonomy_small', "{$item->object}_{$item->object_id}" );
					if ( $img ) : ?>
					<li class="tax-item"><a href="<?php 
					$term = get_term_by( 'id', $item->object_id, $item->object );
					echo get_term_link( $term, 'categorias' ); ?>"><img src="<?php 
					echo $img['sizes']['taxonomy-small']; ?>" alt="<?php 
					echo $img['title']; ?>" width="300" height="260"></a></li>
<?php 				endif;
				endforeach; ?>
				</ul>
			</div>
		</article>
	</div>
<?php get_footer(); ?>