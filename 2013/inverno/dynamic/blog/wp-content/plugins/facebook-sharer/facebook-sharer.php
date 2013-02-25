<?php
/*
Plugin Name: Facebook Sharer
Plugin URI: http://www.saevar.is/blog/facebook-sharer-plugin/
Description: Adds a small link to share blog post on FaceBook
Author: Sævar Öfjörð Magnússon
Version: 0.1.1
Author URI: http://www.saevar.is/blog/
*/

function fb_init() {
	load_plugin_textdomain('facebook-sharer', PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)));
}

function fb_header() {
	echo "<script type=\"text/javascript\">\nfunction fbs_click(u,t) {window.open('http://www.facebook.com/sharer.php?u='+encodeURIComponent(u)+'&t='+encodeURIComponent(t),'sharer','toolbar=0,status=0,width=626,height=436');return false;}\n</script>\n";
	echo "<style>\n.fb_link{\n\tpadding-left:20px;\n\tbackground: url('".get_bloginfo('url')."/wp-content/plugins/facebook-sharer/facebook_share_icon.gif') no-repeat left center;\n}\n.fb_wrap {\n\tdisplay: block;\n}\n</style>\n";
}

function fb_filter($data) {
	global $wp_query, $post;
	$my_post = get_post($post->ID); 
	if($my_post->post_type=="post") {
		return $data. '<div class="fb_wrap"><a class="fb_link" onclick="fbs_click(\''.get_bloginfo('url').'/'.$my_post->post_name.'\',\''.$my_post->title.'\');return false;" href="#">'.__('Send to Facebook','facebook-sharer').'</a></div>';
	} else {
		return $data;
	}
}

add_action('init', 'fb_init');
add_action('wp_head', 'fb_header');
add_filter('the_content', 'fb_filter');

?>
