<?php 
/*
Template name: Coleção
*/
?>
<?php get_header(); ?>
	<div id="body">
<?php 	while( have_posts() ) : the_post(); ?>
		<div id="content" <?php post_class(); ?>>
			<h1 class="entry-title"><?php the_title(); ?></h1>
<?php 		$terms = get_categories( array( 
				'taxonomy' => 'categorias', 
				'parent' => 0, 
				'number' => 2,
				'hide_empty' => 0 
			) ); ?>
			<ul class="colecoes-menu">
<?php 			foreach( $terms as $term ) : ?>
				<li class="colecao-item"><a href="<?php 
				echo get_term_link( $term, 'categorias' ); ?>"><?php 
				$img = get_field( 'taxonomy_medium', "categorias_{$term->term_id}" );
				?><img src="<?php 
				echo $img['sizes']['taxonomy-medium']; ?>" alt="<?php 
				echo $img['title']; ?>" width="475" height="410"></a></li>
<?php 			endforeach; ?>
			</ul>
		</div>
<?php 	endwhile; ?>
	</div>
<?php get_footer(); ?>