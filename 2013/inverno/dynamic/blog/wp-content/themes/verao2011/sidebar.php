	<div id="leftsidebar"><ul><?php if ( !function_exists('dynamic_sidebar')
        || !dynamic_sidebar() ) : ?>
                          
      <div id="arquivos"><li><?php _e('Archives'); ?>
				<ul><?php wp_get_archives('type=monthly'); ?>
				</ul></li></div><li><h2><?php _e('Categories'); ?></h2>
				<ul>
				<?php list_cats(0, '', 'name', 'asc', '', 1, 0, 1, 1, 1, 1, 0,'','','','','') ?>
				</ul></li>

			<?php /* If this is the frontpage */ if ( is_home() || is_page() ) { ?>			
				<?php get_links_list(); ?>
				
				<li><h2><?php _e('Meta'); ?></h2>
				<ul>
					<?php wp_register(); ?>
					<li><?php wp_loginout(); ?></li>
					<li><a href="http://validator.w3.org/check/referer" title="<?php _e('This page validates as XHTML 1.0 Transitional'); ?>"><?php _e('Valid <abbr title="eXtensible HyperText Markup Language">XHTML</abbr>'); ?></a></li>
					<li><a href="http://gmpg.org/xfn/"><abbr title="XHTML Friends Network">XFN</abbr></a></li>
					<li><a href="http://wordpress.org/" title="<?php _e('Powered by WordPress, state-of-the-art semantic personal publishing platform.'); ?>">WordPress</a></li>
					<?php wp_meta(); ?>
				</ul>
				</li>
			<?php } ?>
			<?php endif; ?>
		</ul>
	</div>
