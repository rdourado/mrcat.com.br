<?php
/*
Plugin Name: Photopress widget
Plugin URI: http://familypress.net/photopress/
Description: Adds a sidebar widget to dispay random Photopress images.
Author: Isaac Wedin
Version: 1.8
Author URI: http://familypress.net/
*/

// Put functions into one big function we'll call at the plugins_loaded
// action. This ensures that all required plugin functions are defined.
function widget_ppwidg_init() {

	// Check for the required plugin functions. This will prevent fatal
	// errors occurring when you deactivate the dynamic-sidebar plugin.
	if ( !function_exists('register_sidebar_widget') || !function_exists('pp_random_image_bare'))
		return;

	// This is the function that outputs our little Google search form.
	function widget_ppwidg($args) {
		
		// $args is an array of strings that help widgets to conform to
		// the active theme: before_widget, before_title, after_widget,
		// and after_title are the array keys. Default tags: li and h2.
		extract($args);

		// Each widget can store its own options. We keep strings here.
		$options = get_option('widget_ppwidg');
		$title = $options['title'];
		$style = $options['style'];
		$ppcategory = $options['ppcategory'];
		$number = (int)$options['number'];

		// These lines generate our output. Widgets can be very complex
		// but as you can see here, they can also be very, very simple.
		echo $before_widget . $before_title . $title . $after_title;
		pp_random_image_bare($number,$before='<p>',$after='</p>',$style,pp_slugify($ppcategory),$type=1);
		echo $after_widget;
	}

	// This is the function that outputs the form to let the users edit
	// the widget's title and number of images displayed.
	function widget_ppwidg_control() {

		// Get our options and see if we're handling a form submission.
		$options = get_option('widget_ppwidg');
		if ( !is_array($options) )
			$options = array('title'=>'', 'style'=>'', 'ppcategory'=>'', 'number'=>'1', 'widgets');
		if ( $_POST['ppwidg-submit'] ) {
			$options['title'] = strip_tags(stripslashes($_POST['ppwidg-title']));
			$options['style'] = strip_tags(stripslashes($_POST['ppwidg-style']));
			$options['ppcategory'] = strip_tags(stripslashes($_POST['ppwidg-ppcategory']));
			$options['number'] = strip_tags(stripslashes($_POST['ppwidg-number']));
			update_option('widget_ppwidg', $options);
		}
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		$style = htmlspecialchars($options['style'], ENT_QUOTES);
		$ppcategory = htmlspecialchars($options['ppcategory'], ENT_QUOTES);
		$number = (int)$options['number'];
		// Here is our little form segment. Notice that we don't need a
		// complete form. This will be embedded into the existing form.
		echo '<p style="text-align:right;"><label for="ppwidg-title">' . __('Title:') . ' <input style="width: 100px;" id="ppwidg-title" name="ppwidg-title" type="text" value="'.$title.'" /></label></p>';
		echo '<p style="text-align:right;"><label for="ppwidg-style">' . __('Style class:') . ' <input style="width: 100px;" id="ppwidg-style" name="ppwidg-style" type="text" value="'.$style.'" /></label></p>';
		echo '<p style="text-align:right;"><label for="ppwidg-ppcategory">' . __('Category:') . ' <select name="ppwidg-ppcategory" id="ppwidg-ppcategory"' . ">\n" . '<option value=""';
		if (empty($ppcategory)) {
			echo ' selected="selected"';
		}
		echo '>(all)' . "</option>\n";
		$pp_cats = pp_list_cats();
		echo pp_cat_dropdown($pp_cats,$ppcategory);
		echo '</select></label></p>';
		echo '<p style="text-align:right;"><label for="ppwidg-number">' . __('Number of images:') . '<select id="ppwidg-number" name="ppwidg-number"' . ">\n";
		for ($i=1;$i<=10;$i++) {
			echo '<option value="' . $i . '"';
				if ($number == $i) {
					echo ' selected="selected"';
				}
			echo '>' . $i . "</option>\n";
      }
		echo "</select>\n" . '</label></p>';
		echo '<input type="hidden" id="ppwidg-submit" name="ppwidg-submit" value="1" />';
	}
	
	// This registers our widget so it appears with the other available
	// widgets and can be dragged and dropped into any active sidebars.
	register_sidebar_widget(array('Photopress', 'widgets'), 'widget_ppwidg');

	// This registers our optional widget control form. Because of this
	// our widget will have a button that reveals a 300x100 pixel form.
	register_widget_control(array('Photopress', 'widgets'), 'widget_ppwidg_control');
}

// Run our code later in case this loads prior to any required plugins.
add_action('widgets_init', 'widget_ppwidg_init');

?>
