	<hr>
	<footer id="foot">
		<form action="<?php echo home_url( '/wp-content/plugins/newsletter/do/subscribe.php' ); ?>" method="post" id="newsform" onsubmit="return newsletter_check(this)">
			<fieldset>
				<legend>Receba novidades</legend>
				<p class="field field-nn">
					<label for="nn">Nome</label>
					<input type="text" name="nn" id="nn" class="input-text" placeholder="nome" required aria-required="true">
				</p>
				<p class="field field-ne">
					<label for="ne">Email</label>
					<input type="email" name="ne" id="ne" class="input-text" placeholder="email" required aria-required="true">
				</p>
				<p class="field field-ng">
					<input type="radio" name="nx" id="ngf" class="input-radio" value="f">
					<label for="ngf">Feminino</label>
					<input type="radio" name="nx" id="ngm" class="input-radio" value="m">
					<label for="ngm">Masculino</label>
				</p>
				<p class="field field-submit">
					<button type="submit">Ok</button>
				</p>
			</fieldset>
			<p class="delivery">
				<a href="<?php echo get_permalink( 2636 ); ?>">Clique aqui e receba<br>nosso catálogo em casa</a>
			</p>
		</form>
		<?php 
		echo str_replace( '<a ', '<a tabindex="-1" ', wp_nav_menu( array(
			'theme_location' => 'menu',
			'container' 	 => '', 
			'menu_id' 		 => 'menu-foot',
			'echo' 			 => false,
			'fallback_cb' 	 => false,
			'depth' 		 => 1,
		) ) );
		?>

		<ul id="social-foot">
			<li class="social-item"><a href="<?php the_field( 'instagram', 'options' ); ?>" target="_blank" tabindex="-1"><img src="<?php t_url(); ?>/img/icon-ig.png" alt="Instagram" width="24" height="24"></a></li>
			<li class="social-item item-yt"><a href="<?php the_field( 'youtube', 'options' ); ?>" target="_blank" tabindex="-1"><img src="<?php t_url(); ?>/img/icon-yt.png" alt="Youtube" width="24" height="24"></a></li>
			<li class="social-item item-tw"><a href="<?php the_field( 'twitter', 'options' ); ?>" target="_blank" tabindex="-1"><img src="<?php t_url(); ?>/img/icon-tw.png" alt="Twitter" width="24" height="24"></a></li>
			<li class="social-item item-fb"><a href="<?php the_field( 'facebook', 'options' ); ?>" target="_blank" tabindex="-1"><img src="<?php t_url(); ?>/img/icon-fb.png" alt="Facebook" width="24" height="24"></a></li>
		</ul>
		<a href="http://mgstudio.com.br/" id="mg" target="_blank">by MG Studio</a>
	</footer>
	<script src="http://maps.googleapis.com/maps/api/js?key=MY_KEY&amp;sensor=false"></script>
	<!-- WP/ --><?php wp_footer(); ?><!-- /WP -->
</body>
</html>