<!doctype html>
<html lang="pt-br">
<head>
	<meta charset="UTF-8">
	<title><?php wp_title(); ?></title>
	<link rel="stylesheet" href="/min/g=mrcat-css" media="screen">
	<!--[if lt IE 9]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
	<!-- WP/ --><?php wp_head(); ?><!-- /WP -->
</head>
<body <?php body_class( "{$post->post_name} no-js" ); ?>>
	<header id="head">
<?php 	if ( is_front_page() ) : ?>
		<h1 id="logo"><img src="<?php t_url(); ?>/img/mr-cat.png" alt="Mr.Cat" width="191" height="59"></h1>
<?php 	else : ?>
		<div id="logo"><a href="<?php echo home_url( '/' ); ?>"><img src="<?php t_url(); ?>/img/mr-cat.png" alt="Mr.Cat" width="191" height="59"></a></div>
<?php 	endif; ?>
		<?php 
		wp_nav_menu( array(
			'theme_location' => 'menu',
			'container' 	 => 'nav', 
			'container_id' 	 => 'nav',
			'menu_id' 		 => 'menu-head',
			'fallback_cb' 	 => false,
			'depth' 		 => 2,
		) );
		?>

		<ul id="social-head">
			<li class="social-item"><a href="<?php the_field( 'instagram', 'options' ); ?>" target="_blank"><img src="<?php t_url(); ?>/img/icon-ig.png" alt="Instagram" width="24" height="24"></a></li>
			<li class="social-item item-yt"><a href="<?php the_field( 'youtube', 'options' ); ?>" target="_blank"><img src="<?php t_url(); ?>/img/icon-yt.png" alt="Youtube" width="24" height="24"></a></li>
			<li class="social-item item-tw"><a href="<?php the_field( 'twitter', 'options' ); ?>" target="_blank"><img src="<?php t_url(); ?>/img/icon-tw.png" alt="Twitter" width="24" height="24"></a></li>
			<li class="social-item item-fb"><a href="<?php the_field( 'facebook', 'options' ); ?>" target="_blank"><img src="<?php t_url(); ?>/img/icon-fb.png" alt="Facebook" width="24" height="24"></a></li>
		</ul>
	</header>
	<hr>
