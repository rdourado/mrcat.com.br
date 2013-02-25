<?php
$photopress_url = get_bloginfo('wpurl') . '/wp-content/plugins/photopress';

function photopress_mce_buttons($buttons) {
	array_push($buttons, "photopress"); // nothing special here, just add button names, using separators as needed
	return $buttons;
}

function photopress_mce_external_plugins($plugins) {
	global $photopress_url;
	$plugins['photopress'] = $photopress_url . '/tinymce/v3/editor_plugin.js';
	return $plugins;
}

// Add a button to the HTML editor (borrowed from Kimili Flash Embed)
function pp_add_quicktags() {
	$buttonshtml = '<input type="button" class="ed_button" onclick="edPhotopress(); return false;" title="' . __('Upload and insert images','photopress') . '" value="' . __('Photos','photopress') . '" />';
?>
<script type="text/javascript" charset="utf-8">
// <![CDATA[
	(function(){
		if (typeof jQuery === 'undefined') {
			return;
      }
      jQuery(document).ready(function(){
         jQuery("#ed_toolbar").append('<?php echo $buttonshtml; ?>');
      });
   }());
// ]]>
</script>
<?php
}

// Load the javascript for the popup tool. This runs when the button is clicked - it launches the Photopress popup tool.
function photopress_popup_javascript() {
	echo '
<script type="text/javascript">
//<![CDATA[
function edPhotopress() {
	tb_show("' . __('Upload photos','photopress') . '","' . get_bloginfo('wpurl') . '/wp-content/plugins/photopress/popup.php?action=upload&TB_iframe=true",false);
}
//]]>
</script>
';
}

// Actions and filters to connect the plugin with WP.
add_action( 'edit_form_advanced', 'pp_add_quicktags' );
add_action( 'edit_page_form', 'pp_add_quicktags' );
add_action('admin_print_scripts', 'photopress_popup_javascript');
add_filter( 'mce_external_plugins', 'photopress_mce_external_plugins');
add_filter( 'mce_buttons', 'photopress_mce_buttons');

?>
