<?php
/*
Plugin Name: Tag Widget
Plugin URI: http://thinkclay.com/
Description: A tag cloud plugin for WordPress to give you more flexibility with the styling of your tag cloud.
Author: Clayton McIlrath
Version: 1.0.3
Author URI: http://thinkclay.com/

	Copyright (c) 2010 Clayton McIlrath (http://thinkclay.com)
	Tag Widget is released under the GNU General Public License (GPL)
	http://www.gnu.org/licenses/gpl.txt
*/

/* Load Helper Functions */
require(WP_PLUGIN_DIR.'/custom-tag-widget/base.php');

/* Load Template Tag Config Page */
include(WP_PLUGIN_DIR.'/custom-tag-widget/admin_page.php');

/* Load WP Sidebar Widget */
if (class_exists('WP_Widget')) {
	include(WP_PLUGIN_DIR.'/custom-tag-widget/widget_28.php');
} else {
	include(WP_PLUGIN_DIR.'/custom-tag-widget/widget.php');
}

register_activation_hook(__FILE__,'install_defs');
register_deactivation_hook(__FILE__,'uninstall_defs');
?>