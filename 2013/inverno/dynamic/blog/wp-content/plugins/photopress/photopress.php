<?php
/*
Plugin Name: Photopress
Plugin URI: http://familypress.net/photopress/
Description: Photopress adds some image handling tools to Wordpress, including a popup upload and browse tool, a random image template function, and a simple album. Installs and uses new database tables. Tested and working with WP 2.9.1.
Version: 1.8
Author: Isaac Wedin
Author URI: http://familypress.net/

----------------------------------------------------------

    Copyright 2005-2010  Isaac Wedin (email : isaac@familypress.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// Include shared functions and defaults
require_once(ABSPATH . 'wp-content/plugins/photopress/include.php');
require_once(ABSPATH . 'wp-content/plugins/photopress/buttons.php');
require_once(ABSPATH . 'wp-content/plugins/photopress/pp_album.php');

// Insert CSS and Javascript for the Manage pages.
function pp_popup_js() {
	if ($_GET['page'] == 'pp_album_manager') {
		echo '
<style type="text/css" media="screen">
.wrap {
	overflow: auto;
}
.pp_thumb img {
	padding: 5px;
	text-align: center;
	margin: 2px;
	border: 2px solid #ccc;
}
.pp_thumb a:hover img {
	border: 2px solid #06c;
	margin: 2px;
}
.pp_photo {
}
ul.pp_slides {
	display: block;
	width: 100%;
	margin-left: auto;
	margin-right: auto;
	margin-top: 15px;
	padding:0;
	list-style:none;
	line-height:1.4em;
}
.pp_slides li {
	display:block;
	float:left;
	margin:0 10px 10px 0;
	padding:0;
	width: auto;
	height: auto;
}
.pp_tag {
	text-align: center;
	font-size: 0.9em;
}
#pp_block {
   width: 100%;
	clear: both;
}
.pp_lgphoto {
	display: block;
	margin-left: auto;
	margin-right: auto;
}
#pp_block a img {
	padding: 5px;
	border: 2px solid #ccc;
}
#pp_block a:hover img {
	padding: 5px;
	border: 2px solid #06c;
}
#pp_page_links {
	text-align: center;
}
.pp_prev, .pp_next {
	margin: 10px;
	display: block;
	padding: 5px;
}
a.pp_prev, a.pp_next {
	background: #ccc;
	border: solid 1px #9ac;
	color: #000;
	text-decoration: none;
	font-weight: bold;
}
a.pp_prev:hover, a.pp_next:hover {
	background: #acf;
	border: solid 1px #036;
	color: #036;
	text-decoration: none;
}
.pp_prev {
	float: left;
}
.pp_next {
	float: right;
}
</style>
<script type="text/javascript">
//<![CDATA[
function setbrowsesort(sort) {
	window.top.location.href = "' . get_settings('siteurl') . '/wp-admin/tools.php?page=pp_album_manager&" + sort.options[sort.selectedIndex].value;
	return false;
}
//]]>
</script>
';
	}
}

add_action('admin_head','pp_popup_js');

// Random image function. Given $category_slug and $type it returns an array of random images, formatted by $type: 1 = linked thumb, 2 = unlinked thumb, 3 = file name; CSS class can be set by passing a third argument (did this to get proper class on the main album page) The final option sets the number of random images to return (defaults to 1).
function pp_random_image($category_slug='', $type = 1, $style = 'random', $number = 1) {
	global $pp_options, $table_prefix, $wpdb;
	$table_name = $table_prefix . 'photopress';
	if (empty($category_slug) && $count = pp_count()) {
		if ($count <= $number) { // if too many are requested just return all of them
			$number = $count;
			$rand = 0;
		} else {
			$rand = rand(0,$count - $number); // spot in list to start getting images
		}
		$random_image_array = array();
		for ($i=0;$i<$number;$i++) { // do this $number times
			$random_image_array[] = $wpdb->get_row("SELECT * FROM $table_name",ARRAY_A, $rand+$i);
		}
	} elseif (!empty($category_slug)) {
		$catcount = pp_count($category_slug);
		if ($catcount != 0 && FALSE != $catcount) {
			if ($catcount <= $number) {
				$number = $catcount;
				$rand = 0;
			} else {
				$rand = rand(0,($catcount - $number));
			}
			$random_image_array = array();
			$category_slug = $wpdb->escape($category_slug);
			for ($i=0;$i<$number;$i++) { // do this $number times
				$random_image_array[] = $wpdb->get_row("SELECT * FROM $table_name WHERE binary catslug='$category_slug'",ARRAY_A, $rand+$i);
			}
		} else {
			return FAlSE;
		}
	} else {
		return FALSE;
	}
	$random_thumb = array();
	$random_alt_text = array();
	$j = 0;
	foreach ((array)$random_image_array as $random_image) {
		if (!empty($random_image['imgname'])) {
			$random_alt_text[] = $random_image['imgname'];
		} else {
			$random_alt_text[] = $random_image['imgfile'];
		}
		$random_image_thumb_size = @getimagesize( $pp_options['photospath'] . '/' . $pp_options['thumbprefix'] . $random_image['imgfile']);
		$random_thumb_temp = '<img class="';
		if ($style == 'album') {
			$random_thumb_temp .= 'pp_photo';
		} elseif ($style == 'random') {
			$random_thumb_temp .= $pp_options['rand_class'];
		} else {
			$random_thumb_temp .= $style;
		}
		$random_thumb_temp .= '" src="' . $pp_options['photosaddress'] . '/' . $pp_options['thumbprefix'] . $random_image['imgfile'] . '" ' . $random_image_thumb_size[3] . ' alt="' . attribute_escape(stripslashes($random_alt_text[$j])) . '" title="' . attribute_escape(stripslashes($random_alt_text[$j])) . '" />';
		$random_thumb[] = $random_thumb_temp;
		$j++;
	}
	$random_linked_thumb = array();
	if ($pp_options['album'] == '1') {
		$k = 0;
		foreach((array)$random_image_array as $random_image) {
			$random_linked_thumb[] =  '<a href="' . $pp_options['albumaddress'] . $pp_options['cat_token'] . $random_image['catslug'] . $pp_options['images_token'] . $random_image['imgfile'] . '" title="' . attribute_escape(stripslashes($random_alt_text[$k])) . '">' . $random_thumb[$k] . '</a>';
			$k++;
		}
	} else { // not using the album, so lightbox or popup
		$k = 0;
		foreach((array)$random_image_array as $random_image) {
			if (is_file($pp_options['photospath'] . '/' . $pp_options['origprefix'] . $random_image['imgfile'])) {
				$random_image_address = $pp_options['photosaddress'] . '/' . $pp_options['origprefix'] . $random_image['imgfile'];
				$random_image_size = @getimagesize($pp_options['photospath'] . '/' . $pp_options['origprefix'] . $random_image['imgfile']);
			} else {
				$random_image_address = $pp_options['photosaddress'] . '/' . $random_image['imgfile'];
				$random_image_size = @getimagesize($pp_options['photospath'] . '/' . $random_image['imgfile']);
			}
			if ($pp_options['album'] == 'lightbox') {
				$action = 'rel="lightbox"';
			} else {
				$action = 'onclick="pp_image_popup(\'' . $random_image_address . '\',' . $random_image_size[0] . ',' . $random_image_size[1] . ',\'' . js_escape(stripslashes($random_image['imgname'])) . '\'); return false;"';
			}
			$random_linked_thumb[] =  '<a href="' . $random_image_address . '" ' . $action . ' title="' . attribute_escape(stripslashes($random_alt_text[$k])) . '">' . $random_thumb[$k] . '</a>';
			$k++;
		}
	}
	if ($type == 3) {
		return $random_image;
	} elseif ($type == 2) {
		return $random_thumb;
	} else {
		return $random_linked_thumb;
	}
}

// Template function to make random linked thumbnails, adding tags wrapped around each if requested. Default is one linked thumbnail from all categories with just a line break tag after.
function pp_random_image_bare($number=1,$before='',$after='<br />',$style='random',$category='',$type=1) {
	$randimages = array();
	$randimages = pp_random_image($category,$type,$style,$number);
	foreach ((array) $randimages as $randimage) {
		echo $before . $randimage . $after . "\n";
	}
}

// Puts a link to the photo album and/or a random linked thumbnail in the meta section of the sidebar, depending on the options. Won't try to get a random image if there aren't any images yet. Like many template-related things, this may only work well with the default template.
function pp_stuff_in_meta() {
	global $pp_options;
	$stuff_for_meta = '';
	if ($pp_options['meta_link'] == 1) {
		$stuff_for_meta .= '<li><a href="' . $pp_options['albumaddress'] . '/">' . __('Photos','photopress') . '</a></li>' . "\n";
	}
	if ($pp_options['meta_rand'] == 1 && pp_count()) {
		$rand_image = pp_random_image();
		$stuff_for_meta .= '<li>' . $rand_image[0] . "</li>\n";
	}
	echo $stuff_for_meta;
}

add_action('wp_meta', 'pp_stuff_in_meta');

// Insert the album CSS and/or the image popup Javascript into the admin and blog headers.
function pp_album_css() {
	global $pp_options;
	if (isset($_GET['pp_album']) || get_query_var('pp_album') != '' || $_GET['page_id'] == $pp_options['album_token'] || get_query_var('page_id') == $pp_options['album_token'] || $_GET['pagename'] == $pp_options['album_token'] || get_query_var('pagename') == $pp_options['album_token']) { // only add the album CSS and javascript to the album page
		echo '
<script type="text/javascript">
//<![CDATA[
jQuery(document).ready(function() {
	jQuery("div.ppshowhideedit").hide();
	jQuery("a.pptoggleedit").click(function(){
		jQuery("div.ppshowhideedit").toggle();
	});
});
//]]>
</script>
';
		$stylesheet_dir = ABSPATH . 'wp-content/themes/' . get_settings('stylesheet');
		$template_dir = ABSPATH . 'wp-content/themes/' . get_settings('template');
		if (file_exists($template_dir . '/pp_album_css.php')) { // check for a custom style sheet
			include($template_dir . '/pp_album_css.php');
		} elseif (file_exists($stylesheet_dir . '/pp_album_css.php')) { // need to check both places
			include($stylesheet_dir . '/pp_album_css.php');
		} else {
			include(ABSPATH . 'wp-content/plugins/photopress/pp_album_css.php');
		}
	}
	if (($_GET['page'] == 'pp_album_manager') || (get_query_var('page') == 'pp_album_manager') || (isset($_GET['pp_album'])) || (get_query_var('pp_album') != '') || ($pp_options['album'] == '0')) { // only insert the popup Javascript on the album page and the management pages
		echo '
<style type="text/css" media="screen">
.pp_image img {
	margin: 2px;
	border: 2px solid #ccc;
}
.pp_image a:hover img {
	border: 2px solid #06c;
	margin: 2px;
}
</style>
<script type="text/javascript">
//<![CDATA[
function pp_image_popup(image,width,height,title) {
	image_popup = window.open("","","height=" + height + ",width=" + width + ",toolbar=no,menubar=no,scrollbars=no,resizable=yes");
	var tmp = image_popup.document;
	tmp.write("<html><head><title>" + title + "</title>");
	tmp.write("<style type=\'text/css\' media=\'screen\'>");
	tmp.write("body{ margin: 0; padding: 0; }");
	tmp.write("p.centered { text-align: center; font-size: small; margin: 0; padding: 0; }");
	tmp.write("a img { border: 0; }");
	tmp.write("img.centered { display: block; margin-left: auto; margin-right: auto; padding: 0;}");
	tmp.write("</style>");
	tmp.write("</head><body>");
	tmp.write("<a href=\'javascript:self.close()\'>");
	tmp.write("<img class=\'centered\' src=\'" + image + "\' width=\'" + width + "\' height=\'" + height + "\' />");
	tmp.write("</a>");
	tmp.write("</body></html>");
	tmp.close();
	image_popup.focus();
	return false;
}
//]]>
</script>';
	}
}

add_action('wp_head','pp_album_css');
add_action('admin_head','pp_album_css');

function pp_enqueue() {
   global $pp_options;
   if (isset($_GET['pp_album']) || get_query_var('pp_album') != '' || $_GET['page_id'] == $pp_options['album_token'] || get_query_var('page_id') == $pp_options['album_token'] || $_GET['pagename'] == $pp_options['album_token'] || get_query_var('pagename') == $pp_options['album_token']) { // only add jquery to the album page
      wp_enqueue_script('jquery');
	}
}

add_action('template_redirect', 'pp_enqueue');

function pp_add_options_page() {
	add_options_page('Photopress', 'Photopress', 'manage_options', 'pp_options', 'pp_options_subpanel');
}

function pp_options_subpanel() {
	global $pp_options;
	if (isset($_POST['pp_options_update'])) {
	   $pp_updated_options = array();
		$pp_updated_options = $_POST;
		add_option('pp_options');
		update_option('pp_options', $pp_updated_options);
		$pp_options = get_option('pp_options');
		echo '<div class="updated">' . __('Photopress options updated.','photopress') . '</div>';
	}
	echo '
	<div class="wrap">
	<h2>' . __('Photopress Options','photopress') . '</h2>
	<form name="pp_options" method="post" action="">
	<input type="hidden" name="pp_options_update" value="update" />
	<fieldset class="options">
	<p class="submit"><input type="submit" name="Submit" value="' . __('Update Options &raquo;','photopress') . '" /></p>
	<table class="form-table">
		<tr>
			<th scope="row">' . __('Path to your photos folder','photopress') . '</th>
			<td><input name="photospath" type="text" id="photospath" value="' . $pp_options['photospath'] . '" size="40" /><br />' . __('Best guess:','photopress') . ' "' . ABSPATH . 'wp-content/photos" ' . __('Make sure this path is correct and that the folder at the end exists and is writable.','photopress') . '</td>
		</tr>
		<tr>
			<th scope = "row">' . __('Wordpress address','photopress') . '</th>
			<td><input name="wpaddress" type="text" id="wpaddress" value="' . $pp_options['wpaddress'] . '" size="40" /><br />' . __('Best guess:','photopress') . ' "' . get_settings('siteurl') . '"</td>
		</tr>
		<tr>
			<th scope = "row">' . __('Address of your photos folder','photopress') . '</th>
			<td><input name="photosaddress" type="text" id="photosaddress" value="' . $pp_options['photosaddress'] . '" size="40" /><br />' . __('Best guess:','photopress') . ' "' . get_settings('siteurl') . '/wp-content/photos"</td>
		</tr>
		<tr>
			<th scope="row">' . __('Use permalinks','photopress') . '</th>
			<td>
			<label><input name="use_permalinks" type="radio" value="1" ';
			if ($pp_options['use_permalinks'] == '1') {echo 'checked="checked" ';}
			echo '/> ' . __('Yes','photopress') . '</label><br />
			<label><input name="use_permalinks" type="radio" value="0" ';
			if ($pp_options['use_permalinks'] == '0') {echo 'checked="checked" ';}
			echo ' /> ' . __('No','photopress') . '</label><br />
			' . __('This will add some rules for Photopress to your Wordpress permalink setup. Be sure to refresh your WP permalink rules after turning this on.','photopress') . '</td>
		</tr>
		<tr>
			<th scope = "row">' . __('Album page name','photopress') . '</th>
			<td><input name="album_token" type="text" id="album_token" value="' . $pp_options['album_token'] . '" size="10" /><br />' . __('This is the slug name (or page ID number) of the page you created for the album, which should contain <code>&lt;!--photopress_album--&gt;</code> in it somewhere.','photopress') . '</td>
		</tr>
      <tr>
         <th scope="row">' . __('Using page ID','photopress') . '</th>
         <td>
         <label><input name="usepgid" type="radio" value="1" ';
         if ($pp_options['usepgid'] == '1') {echo 'checked="checked" ';}
         echo '/> ' . __('Yes','photopress') . '</label><br />
         <label><input name="usepgid" type="radio" value="0" ';
         if ($pp_options['usepgid'] == '0') {echo 'checked="checked" ';}
         echo ' /> ' . __('No','photopress') . '</label><br />
         ' . __('If you cannot figure out the slug name for your album page, enter the page ID above and choose "Yes" here to use the page ID number instead.','photopress') . '</td>
      </tr>
      <tr>
         <th scope="row">' . __('Album category sort','photopress') . '</th>
         <td>
         <label><input name="album_sort" type="radio" value="asc_cat" ';
         if ($pp_options['album_sort'] == 'asc_cat') {echo 'checked="checked" ';}
         echo '/> ' . __('Ascending by category','photopress') . '</label><br />
         <label><input name="album_sort" type="radio" value="desc_cat" ';
         if ($pp_options['album_sort'] == 'desc_cat') {echo 'checked="checked" ';}
         echo '/> ' . __('Descending by category','photopress') . '</label><br />
         <label><input name="album_sort" type="radio" value="asc_id" ';
         if ($pp_options['album_sort'] == 'asc_id') {echo 'checked="checked" ';}
         echo '/> ' . __('Ascending by id','photopress') . '</label><br />
         <label><input name="album_sort" type="radio" value="desc_id" ';
         if ($pp_options['album_sort'] == 'desc_id') {echo 'checked="checked" ';}
         echo ' /> ' . __('Descending by id','photopress') . '</label><br />
         ' . __('Set the sort order for the album categories here.','photopress') . '</td>
      </tr>
		<tr>
			<th scope="row">' . __('Keep original images','photopress') . '</th>
			<td>
			<label><input name="originals" type="radio" value="1" ';
			if ($pp_options['originals'] == '1') {echo 'checked="checked" ';}
			echo '/> ' . __('Yes','photopress') . '</label><br />
			<label><input name="originals" type="radio" value="0" ';
			if ($pp_options['originals'] == '0') {echo 'checked="checked" ';}
			echo ' /> ' . __('No','photopress') . '</label><br />
			' . __('Originals are normally deleted to save disk space, but if that is not an issue you may wish to keep them. They will be displayed when viewers click through to the popup and will be used during batch resizing.','photopress') . '</td>
		</tr>
		<tr>
			<th scope="row">' . __('Originals prefix','photopress') . '</th>
			<td><input name="origprefix" type="text" id="origprefix" value="' . $pp_options['origprefix'] . '" size="10" /><br />' . __('String to add to the original image name, if they are being retained.','photopress') . '</td>
		</tr>
		<tr>
			<th scope="row">' . __('Capability required to upload','photopress') . '</th>
			<td><input name="upload_cap" type="text" id="upload_cap" value="' . $pp_options['upload_cap'] . '" size="20"><br />
			' . __('You probably want to use <code>publish_posts</code>, but you could also use <code>upload_files</code>, <code>edit_posts</code> or any other capability you want.','photopress') . '</td>
		</tr>
		<tr>
			<th scope="row">' . __('Capability required to maintain','photopress') . '</th>
			<td><input name="maintain_cap" type="text" id="maintain_cap" value="' . $pp_options['maintain_cap'] . '" size="20"><br />
			' . __('You may want to make this more restrictive than the uploading requirement if your users are error-prone or untrustworthy.','photopress') . '</td>
		</tr>
		<tr>
			<th scope="row">' . __('Maximum allowed size for uploads','photopress') . '</th>
			<td><input name="maxk" type="text" id="maxk" value="' . $pp_options['maxk'] . '" size="4" /> 
			' . __('Kilobytes (KB)','photopress') . '<br />
			' . __('This setting does not always work because PHP also has its own maximum allowed upload size.','photopress') . '</td>
		</tr>
		<tr>
			<th scope="row">' . __('Maximum resized image dimensions (width or height)','photopress') . '</th>
			<td><input name="maxsize" type="text" id="maxsize" value="' . $pp_options['maxsize'] . '" size="4" />
			' . __('pixels','photopress') . '</td>
		</tr>
		<tr>
			<th scope="row">' . __('Maximum thumbnail dimensions','photopress') . '</th>
			<td><input name="thumbsize" type="text" id="thumbsize" value="' . $pp_options['thumbsize'] . '" size="4" />' . __('pixels','photopress') . '</td>
		</tr>
		<tr>
			<th scope="row">' . __('Make square thumbnails','photopress') . '</th>
			<td>
			<label><input name="square" type="radio" value="1" ';
			if ($pp_options['square'] == '1') {echo 'checked="checked" ';}
			echo '/> ' . __('Yes','photopress') . '</label><br />
			<label><input name="square" type="radio" value="0" ';
			if ($pp_options['square'] == '0') {echo 'checked="checked" ';}
			echo ' /> ' . __('No','photopress') . '</label><br />
			' . __('This crops your thumbnails to make them square, which can make the album look better.','photopress') . '</td>
		</tr>
		<tr>
			<th scope="row">' . __('Quality for resized JPEGs','photopress') . '</th>
			<td><input name="quality" type="text" id="quality" value="' . $pp_options['quality'] . '" size="3" /><br />' . __('Recommended: between 70 and 95. This only matters if you care about how much disk space your resized images take up.','photopress') . '</td>
		</tr>
		<tr>
			<th valign="top" scope="row">' . __('Allowed file extensions (and MIME types)','photopress') . '</th>
			<td><input name="allowedtypes" type="text" id="allowedtypes" value="' . $pp_options['allowedtypes'] . '" size="40" /><br />' . __('Recommended: <code>jpg jpeg png gif</code> The resizing function only supports these types, but you can remove any you do not want your users to be able to upload.','photopress') . '</td>
		</tr>
		<tr>
			<th scope="row">' . __('Insert thumbnail or full image by default','photopress') . '</th>
			<td><label><input name="insert_thumb" type="radio" value="1" ';
			if ($pp_options['insert_thumb'] == '1') {echo 'checked="checked" ';}
			echo '/> ' . __('Thumb','photopress') . '</label> 
			<label><input name="insert_thumb" type="radio" value="0" ';
			if ($pp_options['insert_thumb'] == '0') {echo 'checked="checked" ';}
			echo ' /> ' . __('Full','photopress') . '</label><br />' . __('Both options will be in a dropdown menu - this selects the default choice.','photopress') . '</td>
		</tr>
		<tr>
			<th scope="row">' . __('Thumbnails prefix','photopress') . '</th>
			<td><input name="thumbprefix" type="text" id="thumbprefix" value="' . $pp_options['thumbprefix'] . '" size="10" /><br />' . __('String to add to the thumbnails Photopress creates.','photopress') . '</td>
		</tr>
		<tr>
			<th scope="row">' . __('Link to album','photopress') . '</th>
			<td>
			<label><input name="album" type="radio" value="1" ';
			if ($pp_options['album'] == '1') {echo 'checked="checked" ';}
			echo '/> ' . __('Album','photopress') . '</label> 
			<label><input name="album" type="radio" value="0" ';
			if ($pp_options['album'] == '0') {echo 'checked="checked" ';}
			echo ' /> ' . __('Popup','photopress') . '</label>
			<label><input name="album" type="radio" value="lightbox" ';
			if ($pp_options['album'] == 'lightbox') {echo 'checked="checked" ';}
			echo ' /> ' . __('Lightbox','photopress') . '</label><br />' . __('The code for inserted images can point to the image in the album, to a popup containing the image, or it can use Lightbox. This also causes thumbnails on category pages in the album to point to popups, instead of the image pages.','photopress') . '
        </td>
		</tr>
		<tr>
			<th scope="row">' . __('Insert tags or HTML','photopress') . '</th>
			<td>
			<label><input name="insert_tags" type="radio" value="1" ';
			if ($pp_options['insert_tags'] == '1') {echo 'checked="checked" ';}
			echo '/> ' . __('Tags','photopress') . '</label>
			<label><input name="insert_tags" type="radio" value="0" ';
			if ($pp_options['insert_tags'] == '0') {echo 'checked="checked" ';}
			echo ' /> ' . __('HTML','photopress') . '</label><br />' . __('The popup tool can insert tags (like [photopress:...]) or regular HTML. HTML gives you inline images in the rich text editor, which is pretty. However, since tags are rendered into HTML when posts are displayed they can respond to changes to Options and image sizes, so you are less likely to end up with broken or strange images in old posts.','photopress') . '
			</td>
		</tr>
		<tr>
			<th scope="row">' . __('Insert link in Meta','photopress') . '</th>
			<td>
			<label><input name="meta_link" type="radio" value="1" ';
			if ($pp_options['meta_link'] == '1') {echo 'checked="checked" ';}
			echo '/> ' . __('Yes','photopress') . '</label> 
			<label><input name="meta_link" type="radio" value="0" ';
			if ($pp_options['meta_link'] == '0') {echo 'checked="checked" ';}
			echo ' /> ' . __('No','photopress') . '</label><br />' . __('Adds a link to the photo album in the Meta sidebar list.','photopress') . '
			</td>
		</tr>
		<tr>
			<th scope="row">' . __('Insert random image in Meta','photopress') . '</th>
			<td>
			<label><input name="meta_rand" type="radio" value="1" ';
			if ($pp_options['meta_rand'] == '1') {echo 'checked="checked" ';}
			echo '/> ' . __('Yes','photopress') . '</label> 
			<label><input name="meta_rand" type="radio" value="0" ';
			if ($pp_options['meta_rand'] == '0') {echo 'checked="checked" ';}
			echo ' /> ' . __('No','photopress') . '</label><br />' . __('Adds a random linked thumbail to the Meta sidebar list.','photopress') . '
			</td>
		</tr>
		<tr>
			<th scope="row">' . __('Show posts with image in album','photopress') . '</th>
			<td>
			<label><input name="show_posts" type="radio" value="1" ';
			if ($pp_options['show_posts'] == '1') {echo 'checked="checked" ';}
			echo '/> ' . __('Yes','photopress') . '</label> 
			<label><input name="show_posts" type="radio" value="0" ';
			if ($pp_options['show_posts'] == '0') {echo 'checked="checked" ';}
			echo ' /> ' . __('No','photopress') . '</label><br />' . __('Shows a list of posts containing the image in the album.','photopress') . '
			</td>
		</tr>
		<tr>
			<th scope="row">' . __('Show thumbs in mass edit in the album manager','photopress') . '</th>
			<td>
			<label><input name="thumbs_in_mass_edit" type="radio" value="1" ';
			if ($pp_options['thumbs_in_mass_edit'] == '1') {echo 'checked="checked" ';}
			echo '/> ' . __('Yes','photopress') . '</label> 
			<label><input name="thumbs_in_mass_edit" type="radio" value="0" ';
			if ($pp_options['thumbs_in_mass_edit'] == '0') {echo 'checked="checked" ';}
			echo ' /> ' . __('No','photopress') . '</label><br />' . __('Toggles thumbnail display in the Mass Editor part of the album manager. Turning them off speeds up display a lot.','photopress') . '
			</td>
		</tr>
		<tr>
			<th scope="row">' . __('Allow images used in posts to be deleted','photopress') . '</th>
			<td>
			<label><input name="allow_post_image_delete" type="radio" value="1" ';
			if ($pp_options['allow_post_image_delete'] == '1') {echo 'checked="checked" ';}
			echo '/> ' . __('Yes','photopress') . '</label>
			<label><input name="allow_post_image_delete" type="radio" value="0" ';
			if ($pp_options['allow_post_image_delete'] == '0') {echo 'checked="checked" ';}
			echo ' /> ' . __('No','photopress') . '</label><br />' . __('Makes the delete button appear in the album manager for images that are used in posts. The mass editor always has check boxes to delete.','photopress') . '
			</td>
		</tr>
      <tr>
         <th scope="row">' . __('Images per migrate','photopress') . '</th>
         <td><input name="per_migrate" type="text" id="per_migrate" value="' . $pp_options['per_migrate'] . '" size="3" /><br />' . __('Number of images to migrate at Tools:Photopress:Maintain:Migrate at a time. Raise it if you have a lot of server resources or lower it if you do not (or get timeouts).','photopress') . '</td>
      </tr>
		<tr>
			<th scope="row">' . __('Images per page','photopress') . '</th>
			<td><input name="images_per_page" type="text" id="images_per_page" value="' . $pp_options['images_per_page'] . '" size="3" /><br />' . __('Number of images to show on pages with multiple images - in the album, the popup browse list, and in the manager.','photopress') . '</td>
		</tr>
		<tr>
			<th scope="row">' . __('CSS classes for inserted images','photopress') . '</th>
			<td><input name="image_class" type="text" id="image_class" value="' . $pp_options['image_class'] . '" size="10" /><br />' . __('alignleft, alignright, and centered are available in the default theme, or you can add new classes to your theme style file and enter the names here. Separate the classes with spaces. These will appear in a dropdown list in the uploader and browser, and the first one will be selected by default. With inexperienced users either centered or an empty class seem to work best.','photopress') . '</td>
		</tr>
		<tr>
			<th scope="row">' . __('CSS class for random thumbs','photopress') . '</th>
			<td><input name="rand_class" type="text" id="rand_class" value="' . $pp_options['rand_class'] . '" size="10" /><br />' . __('alignleft, alignright, and centered are available in the default theme, or you can add a new class to your theme and enter the name here.','photopress') . '</td>
		</tr>';
do_action('pp_options'); // add options from plugins
echo '	</table> 
	</fieldset>
	<p class="submit"><input type="submit" name="Submit" value="' . __('Update Options &raquo;','photopress') . '" /></p>
</form> 
</div>
';
}

add_action('admin_menu', 'pp_add_options_page');

// makes the Manage:Photopress Album pages
function pp_album_manager() {
	global $photopress_url,$wpdb,$pp_options,$user_level;
	do_action('pp_process');
	if (isset($_POST['pp_chmod_folder'])) {
		if (!is_dir($pp_options['photospath'])) { // if it's not there we'll try to create it
			@mkdir($pp_options['photospath'],0777);
		}
		if (is_dir($pp_options['photospath'])) { // try to change the permissions on the folder
			if (chmod($pp_options['photospath'],0777)) {
				echo '<div class="updated">' . __('Changed permissions successfully.','photopress') . '</div>';
			} else {
				echo '<div class="updated">' . __('Failed to change permissions.','photopress') . '</div>';
			}
		} else {
			echo '<div class="updated">' . __('Folder not found and could not be created. You will need to create the folder yourself.','photopress') . '</div>';
		}
	}
	if (isset($_POST['pp_migrate'])) {
			$migrated = pp_migrate();
			printf('<div class="updated">' . __('Migrated %s images into the Media Library.','photopress') . '</div>',$migrated);
	}
	if (isset($_POST['pp_replace_tags'])) {
		$replaced = pp_replace_tags();
		printf('<div class="updated">' . __('Migrated %s tags in %s posts.','photopress') . '</div>',$replaced[0],$replaced[1]);
	}
	if (isset($_POST['pp_migrate_tags'])) {
		$replaced = pp_replace_tags(TRUE);
		printf('<div class="updated">' . __('Replaced %s tags in %s posts.','photopress') . '</div>',$replaced[0],$replaced[1]);
	}
	if (isset($_POST['pp_import_orphans'])) {
		$imported = pp_import_orphans();
		if ($imported == 1) {
			echo '<div class="updated">' . __('1 image imported.','photopress') . '</div>';
		} else {
			echo '<div class="updated">' . $imported . __(' images imported.','photopress') . '</div>';
		}
	}
	if (isset($_POST['pp_reinstall'])) {
		$install = pp_table_install();
		echo '<div class="updated">' . __('Photopress table updated.','photopress') . '</div>';
	}
	if (isset($_POST['pp_change_cat'])) {
		$catrec = 0;
		foreach ((array)$_POST as $key=>$val) {
			if (is_array($val)) {
				if ($val['newcat'] == $val['oldcat']) {
					$update_cat = $val['dropcat'];
				} else {
					$update_cat = $val['newcat'];
				}
				$cat_update_array = array('oldslug'=>$val['oldslug'],'category'=>$update_cat,'hidden'=>$val['hidden'],'catsort'=>$val['catsort']);
				if (pp_change_cat($cat_update_array)) {
					$catrec++;
				}
			}
		}
		$wpdb->flush();
		echo '<div class="updated">' . $catrec . __(' album categories changed.','photopress') . '</div>';
	}
	if (isset($_POST['pp_album_update'])) {
		if ($_POST['category'] != $_POST['catdrop']) {
			$pp_updated_cat = $_POST['catdrop'];
		} elseif (!empty($_POST['newimgcat'])) {
			$pp_updated_cat = $_POST['newimgcat'];
		} else {
			$pp_updated_cat = $_POST['category'];
		}
		$pp_updated_array = array('imgfile'=>$_POST['imgfile'],'imgname'=>$_POST['imgname'],'imgdesc'=>$_POST['imgdesc'],'category'=>$pp_updated_cat);
		if (pp_table_update($pp_updated_array)) {
			echo '<div class="updated">' . __('Photopress album data updated.','photopress') . '</div>';
		} else {
			echo '<div class="updated">' . __('Photopress album data update failed for some reason.','photopress') . '</div>';
		}
	}
	if (isset($_POST['pp_mass_update'])) {
		$dels = 0;
		$updates = 0;
		foreach ((array)$_POST as $key=>$val) {
			if (is_array($val)) {
				if ($val['imgdelete'] == '1') {
					if (pp_delete_photo($val['imgfile'])) {
						$dels++;
					}
				} else {
					if ($val['category'] != $val['catdrop']) {
						$pp_updated_cat = $val['catdrop'];
					} elseif (!empty($val['newimgcat'])) {
						$pp_updated_cat = $val['newimgcat'];
					} else {
						$pp_updated_cat = $val['category'];
					}
					$pp_updated_array = array('imgfile'=>$val['imgfile'],'imgname'=>$val['imgname'],'imgdesc'=>$val['imgdesc'],'category'=>$pp_updated_cat);
					if (pp_table_update($pp_updated_array)) {
						$updates++;
					}
				}
				$wpdb->flush();
			}
		}
		echo '<div class="updated">' . $updates . __(' records updated. ','photopress') . $dels . __(' images deleted.','photopress') . '</div>';
	}
	if (isset($_POST['pp_rotate'])) {
		$rotated = pp_rotate($_POST['imgfile'],$_POST['pp_rotate'],$_POST['pp_copy']);
		if ($rotated == 1) {
			echo '<div class="updated">' . $_POST['imgfile'] . __(' was rotated successfully.','photopress') . '</div>';
		} else {
			echo '<div class="updated">' . $rotated . '</div>';
		}
	}
	if (isset($_GET['pp_delete'])) {
		if (pp_delete_photo($_GET['pp_delete'])) {
			echo '<div class="updated">' . $_GET['pp_delete'] . __(' was deleted.','photopress') . '</div>';
		} else {
			echo '<div class="updated">' . $_GET['pp_delete'] . __(' was not deleted for some reason.','photopress') . '</div>';
		}
	}
	if (isset($_POST['pp_filename_repair'])) {
		if ($repaired = pp_filename_repair()) {
			echo '<div class="updated">' . $repaired . __(' images had bad filenames and were repaired.','photopress') . '</div>';
		} else {
			echo '<div class="updated">' . __('No filename repair necessary.','photopress') . '</div>';
		}
	}
	if (isset($_POST['pp_db_cleanup'])) {
		if ($cleaned = pp_db_cleanup()) {
			echo '<div class="updated">' . $cleaned . __(' records had no matching images and were removed.','photopress') . '</div>';
		} else {
			echo '<div class="updated">' . __('No cleanup necessary.','photopress') . '</div>';
		}
	}
	if (isset($_POST['pp_mass_resize'])) {
		$resized = pp_mass_resize();
		echo '<div class="updated">';
		printf(__('Resized %s images, %s thumbs.','photopress'),$resized[0], $resized[1]);
		echo '</div>';
	}
	echo '
	<div class="wrap">
	<h2>' . __('Photopress Album Management','photopress') . '</h2>
	<p>
	';
	if (pp_count() > 0) {
		echo '<a href="tools.php?page=pp_album_manager" title="' . __('Category View','photopress') . '">' . __('Category View','photopress') . '</a> | ';
		if ($user_level >= $pp_options['mass_min_level']) {
			echo '<a href="tools.php?page=pp_album_manager&amp;pp_manage_mass=yes" title="' . __('Mass Edit','photopress') . '">' . __('Mass Edit','photopress') . '</a> | ';
		}
		echo '<a href="tools.php?page=pp_album_manager&amp;pp_change_cat=yes" title="' . __('Edit Categories','photopress') . '">' . __('Edit Categories','photopress') . '</a> | ';
	}
	if ($user_level >= $pp_options['mass_min_level']) {
		echo '<a href="tools.php?page=pp_album_manager&amp;pp_maintain=yes" title="' . __('Maintain','photopress') . '">' . __('Maintain','photopress') . '</a>';
	}
	echo '</p>
	</div>
	';
	// Begin Maintain Page
	if (isset($_GET['pp_maintain'])) {
		echo '
		<div class="wrap">
		<h2>' . __('Maintain','photopress') . '</h2>
		<p>' . __('Various maintenance tools.','photopress') . '</p>
		<table class="widefat">
		<tbody>
      <tr>
      <td><p>' . __('<strong>Migrate to Media Library</strong> copies Photopress images into the Media Library in batches, based on your images per migrate setting at Settings:Photopress. Test uploading to your Media Library before using this, making sure all image sizes are being generated properly.','photopress') . '</p></td>
      <td><form name="pp_album_cats" method="post" action=""><input type="hidden" name="pp_migrate" value="update" /><p class="submit"><input type="submit" name="Submit" value="' . __('Migrate','photopress') . ' &raquo;" /></p></form></td>
      </tr>
      <tr class="alternate">
      <td><p>' . __('<strong>Migrate Tags</strong> replaces all shortcode-like tags in posts with HTML linked to images to the Media Library. Thumbnails are replaced with thumbnails and larger images are replaced with medium Media Library images. This is not reversible, so you should back up your database first.','photopress') . '</p></td>
      <td><form name="pp_album_cats" method="post" action=""><input type="hidden" name="pp_migrate_tags" value="update" /><p class="submit"><input type="submit" name="Submit" value="' . __('Migrate Tags','photopress') . ' &raquo;" /></p></form></td>
      </tr>
      <tr>
      <td><p>' . __('<strong>Replace Tags</strong> replaces all shortcode-like tags in posts with HTML linking to images in your Photopress photos folder (not the album). Use this if you want to stop using Photopress but do not want to migrate your photos into the Media Library. This is not reversible, so you should probably back up your database first.','photopress') . '</p></td>
      <td><form name="pp_album_cats" method="post" action=""><input type="hidden" name="pp_replace_tags" value="update" /><p class="submit"><input type="submit" name="Submit" value="' . __('Replace Tags','photopress') . ' &raquo;" /></p></form></td>
      </tr>
		<tr class="alternate">
		<td><p>' . __('<strong>Import Photos</strong> imports any images in the photos folder that are not in the database. If you have added a bunch of photos via FTP this will put them in the database and create thumbnails. It may time out if you added a lot of images, but you should be able to restart it without losing your progress.','photopress') . '</p></td>
		<td><form name="pp_album_cats" method="post" action=""><input type="hidden" name="pp_import_orphans" value="update" /><p class="submit"><input type="submit" name="Submit" value="' . __('Import Photos','photopress') . ' &raquo;" /></p></form></td>
		</tr>
		<tr>
		<td><p>' . __('<strong>Update/install DB</strong> updates or creates the database, adding new columns if necessary and importing data if necessary. This is an alternative to de-activating and re-activating the plugin when upgrading.','photopress') . '</p></td>
		<td><form name="pp_album_cats" method="post" action=""><input type="hidden" name="pp_reinstall" value="update" /><p class="submit"><input type="submit" name="Submit" value="' . __('Install/update DB','photopress') . ' &raquo;" /></p></form></td>
		</tr>
		<tr class="alternate">
		<td><p>' . __('<strong>Cleanup DB</strong> checks for images in the DB that are not on disk and removes them, for instance if you have removed images manually.','photopress') . '</p></td>
		<td><form name="pp_album_cats" method="post" action=""><input type="hidden" name="pp_db_cleanup" value="update" /><p class="submit"><input type="submit" name="Submit" value="' . __('Cleanup DB','photopress') . ' &raquo;" /></p></form></td>
		</tr>
		<tr>
		<td><p>' . __('<strong>Create Folder</strong> attempts to change the permissions of the photos folder to 0777. This will frequently fail due to your server configuration.','photopress') . '</p></td>
		<td><form name="pp_album_cats" method="post" action=""><input type="hidden" name="pp_chmod_folder" value="update" /><p class="submit"><input type="submit" name="Submit" value="' . __('Create Folder','photopress') . ' &raquo;" /></p></form></td>
		</tr>
		<tr class="alternate">
		<td><p>' . __('<strong>Mass Resize</strong> checks your image and thumb sizes and resizes to your current setting if necessary. Also makes your thumbs square if necessary. The process may time out, but it should be possible to run it several times to complete the resizing and cropping.','photopress') . '</p></td>
		<td><form name="pp_album_cats" method="post" action=""><input type="hidden" name="pp_mass_resize" value="update" /><p class="submit"><input type="submit" name="Submit" value="' . __('Mass Resize','photopress') . ' &raquo;" /></p></form></td>
      </tr>';
		do_action('pp_maintain_button');
		echo '		</tbody>
		</table>
		</div>
		';
	// Begin Image Manager
	} elseif (isset($_GET['pp_manage_image'])) {
		$pp_manage_image = $_GET['pp_manage_image'];
		$pp_image_data = pp_get_data($pp_manage_image);
		$pp_imgfile = $pp_image_data['imgfile'];
		$pp_imgdesc = stripslashes($pp_image_data['imgdesc']);
		$pp_imgname = stripslashes($pp_image_data['imgname']);
		$pp_imgcat = stripslashes($pp_image_data['category']);
		$pp_imgtime = $pp_image_data['imgtime'];
		$pp_slug = $pp_image_data['catslug'];
		$pp_cats = pp_list_cats();
		if (empty($pp_image_data['catsort'])) {
			$sort = 'imgname';
		} else {
			$sort = $pp_image_data['catsort'];
		}
		$pp_cat_images = pp_images_with_data($pp_slug,$sort,0,999999);
		$pp_count_cat_images = sizeof($pp_cat_images);
		echo '
		<div class="wrap">
		';
		if ($pp_count_cat_images > 1) {
			$prevnext = '<div class="pp_prevnext">';
			for ($j = 0; $j < $pp_count_cat_images; $j++) {
				$item = $pp_cat_images[$j]['imgfile'];
				if ($pp_imgfile == $item) {
					if ($pp_cat_images[$j-1]['imgfile']) {
						$prevnext .= '<a class="pp_prev" href="' . get_settings('siteurl') . '/wp-admin/tools.php?page=pp_album_manager&amp;pp_manage_image=' . $pp_cat_images[$j-1]['imgfile'] . '" title="' . attribute_escape(stripslashes($pp_cat_images[$j-1]['imgfile'])) . '">' . __('Previous','photopress') . '</a>' . "\n";
					} else {
						$prevnext .= '<div class="pp_prev">' . "</div>\n";
					}
					if ($pp_cat_images[$j+1]['imgfile']) {
						$prevnext .= '<a class="pp_next" href="' . get_settings('siteurl') . '/wp-admin/tools.php?page=pp_album_manager&amp;pp_manage_image=' . $pp_cat_images[$j+1]['imgfile'] . '" title="' . attribute_escape(stripslashes($pp_cat_images[$j+1]['imgfile'])) . '">' . __('Next','photopress') . '</a>' . "\n";
					} else {
						$prevnext .= '<div class="pp_next">' . "</div>\n";
					}
				}
			}
			$prevnext .= "</div>\n";
			echo $prevnext;
		}
		echo '<div id="pp_block"><h3>' . __('Editing','photopress') . ' <em>' . $pp_imgfile . '</em> ' . __('from the','photopress') . ' <em><a href="tools.php?page=pp_album_manager&amp;pp_manage_cat=' . $pp_slug . '">' . $pp_imgcat . '</a></em> ' . __('category','photopress') . '</h3>
		<table width="100%" cellpadding="5">
		<tr><td>
		<form name="pp_album_cats" method="post" action="">
		<input type="hidden" name="pp_album_update" value="update" />
		<input type="hidden" name="imgfile" value="' . $pp_imgfile . '" />
		<input type="hidden" name="catslug" value="' . $pp_slug . '" />
		<input type="hidden" name="category" value="' . $pp_imgcat . '" />
		<img src="' . $pp_options['photosaddress'] . '/' . $pp_manage_image . '" alt="' . attribute_escape(stripslashes($pp_imgname)) . '" title="' . attribute_escape(stripslashes($pp_imgname)) . '" />
		</td><td>
		<p><strong>' . __('Category:','photopress') . '</strong><br /><select name="catdrop" id="catdrop">';
		echo pp_cat_dropdown($pp_cats,$pp_imgcat); 
		echo '</select><br />
		 ' . __('or enter new:','photopress') . ' <input type="text" name="newimgcat" value="" /></p>
		<p><strong>' . __('Name:','photopress') . '</strong><br /><input type="text" name="imgname" value="' . $pp_imgname . '" /></p>
		<p><strong>' . __('Description:','photopress') . '</strong><br /><textarea rows="2" cols ="30" name="imgdesc" id="imgdesc" class="uploadform">' . $pp_imgdesc . '</textarea></p>
		<p><strong>' . __('Date uploaded:','photopress') . '</strong><br />' . date("j M Y, H:i", $pp_imgtime) . '</p>
		<p class="submit">
		<input type="submit" name="Submit" value="' . __('Update','photopress') . ' &raquo;" />
		</p>';
		query_posts("s=$pp_manage_image");
		if ( have_posts() ) :
		echo '<p>' . __('Posts with this image:','photopress') . '</p>
			<ul>';
		while ( have_posts() ) : the_post();
		echo '<li><a href="';
		the_permalink();
		echo '">';
		the_title();
		echo '</a></li>';
		endwhile;
		echo '</ul>';
		else:
		if ($pp_options['allow_post_image_delete'] == '0') {
			echo "<p><a href=\"tools.php?page=pp_album_manager&amp;pp_manage_cat=" . $pp_slug . "&pp_delete=" . $pp_manage_image . "\" class=\"delete\" onclick=\"return confirm('";
			printf(__("OK to delete %s?","photopress"),$pp_manage_image);
			echo "')\">";
			printf(__("Delete %s","photopress"),$pp_manage_image);
			echo '</a></p>';
		}
		endif;
		if ($pp_options['allow_post_image_delete'] != '0') {
			echo "<p><a href=\"tools.php?page=pp_album_manager&amp;pp_manage_cat=" . $pp_slug . "&amp;pp_delete=" . $pp_manage_image . "\" class=\"delete\" onclick=\"return confirm('";
			printf(__("OK to delete %s?","photopress"),$pp_manage_image);
			echo "')\">";
			printf(__("Delete %s","photopress"),$pp_manage_image);
			echo '</a></p>';
		}
		echo '
		</form>
		<form name="pp_album_cats" method="post" action="">
		<input type="hidden" name="pp_rotate" value="update" />
		<input type="hidden" name="imgfile" value="' . $pp_imgfile . '" />
		<p class="submit">
		<strong>' . __('Rotate: ','photopress') . '</strong>
		<select name="pp_copy" id="pp_copy">
			<option value="copy">' . __('copy','photopress') . '</option>
			<option value="replace">' . __('replace','photopress') . '</option>
		</select> 
		<select name="pp_rotate" id="pp_rotate">
			<option value="270">' . __('90 CW','photopress') . '</option>
			<option value="90">' . __('90 CCW','photopress') . '</option>
			<option value="180">' . __('180','photopress') . '</option>
		</select>
		<input type="submit" name="Submit" value="' . __('Rotate','photopress') . ' &raquo;" />
		</p>
		</form>
		</td></tr></table>
		</div>
		</div>
		';
	// Begin Mass Image Manager
	} elseif (isset($_GET['pp_manage_mass'])) {
		$pp_cats = pp_list_cats();
		if (isset($_GET['pp_page'])) {
			$current_page = $_GET['pp_page'];
		} else {
			$current_page = 1;
		}
		echo '
		<div class="wrap">
		<h2>' . __('Mass Edit','photopress') . '</h2>
		<p><form>' . __('Filter: ','photopress') . '<select name="sort" onChange="setbrowsesort(this)">
			<option value="pp_manage_mass=yes&sort=imgtimeD"'; if ($_GET['sort'] == 'imgtimeD' || !isset($_GET['sort'])) { echo ' selected="selected"'; } echo '>' . __('New to Old','photopress') . '</option>
			<option value="pp_manage_mass=yes&sort=imgtimeA"'; if ($_GET['sort'] == 'imgtimeA') { echo ' selected="selected"'; } echo '>' . __('Old to New','photopress') . '</option>
			<option value="pp_manage_mass=yes&sort=imgfileA"'; if ($_GET['sort'] == 'imgfileA') { echo ' selected="selected"'; } echo '>' . __('A to Z','photopress') . '</option>
			<option value="pp_manage_mass=yes&sort=imgfileD"'; if ($_GET['sort'] == 'imgfileD') { echo ' selected="selected"'; } echo '>' . __('Z to A','photopress') . '</option>
			<option value="pp_manage_mass=yes&sort=imgcatA"'; if ($_GET['sort'] == 'imgcatA') { echo ' selected="selected"'; } echo '>' . __('A-Z by cat','photopress') . '</option>
			<option value="pp_manage_mass=yes&sort=imgcatD"'; if ($_GET['sort'] == 'imgcatD') { echo ' selected="selected"'; } echo '>' . __('Z-A by cat','photopress') . '</option>
			<option value="pp_manage_mass=yes&sort=defaultonly"'; if ($_GET['sort'] == 'defaultonly') { echo ' selected="selected"'; } echo '>' . __('Default Only','photopress') . '</option>
		</select></form></p>';
		if ($_GET['sort'] == 'imgfileD') {
			$pp_sort = 'imgfile DESC';
		} elseif ($_GET['sort'] == 'imgfileA') {
			$pp_sort = 'imgfile';
		} elseif ($_GET['sort'] == 'imgtimeA') {
			$pp_sort = 'imgtime';
		} elseif ($_GET['sort'] == 'imgcatA') {
			$pp_sort = 'imgcat';
		} elseif ($_GET['sort'] == 'imgcatD') {
			$pp_sort = 'imgcat DESC';
		} elseif ($_GET['sort'] == 'defaultonly') {
			$pp_sort = 'defaultonly';
		} else {
			$pp_sort = 'imgtime DESC';
		}
		if ($pp_sort == 'defaultonly') {
			$image_count = pp_count('default');
		} else {
			$image_count = pp_count();
		}
		$pages = (int)ceil(($image_count/$pp_options['images_per_page']));
		echo '<p>';
		if ($pages > 1) {
			for ($i = 1; $i <= $pages; $i++) {
				if ($i != $current_page) {
					echo '<a href="tools.php?page=pp_album_manager&amp;pp_manage_mass=yes&amp;pp_page=' . $i; if (isset($_GET['sort'])) { echo '&amp;sort=' . $_GET['sort']; } echo '">';
				}
				echo __('Page','photopress') . ' ' . $i;
				if ($i != $current_page) {
					echo '</a>';
				}
				if ($i < $pages) {
					echo ' | ';
				}
			}
			$list_start = ($current_page - 1) * $pp_options['images_per_page'];
		} else {
			$list_start = 0;
		}
		echo '</p>';
		echo '<form name="pp_album_cats" method="post" action="">
		<input type="hidden" name="pp_mass_update" value="update" />
		<table>
		<tr>
		<th scope="col">' . __('File','photopress') . '</th>
		<th colspan="2">' . __('Category','photopress') . '</th>
		<th scope="col">' . __('Name','photopress') . '</th>
		<th scope="col">' . __('Description','photopress') . '</th>
		<th scope="col">' . __('Delete','photopress') . '</th>
		</tr>
		';
		if ($pp_sort == 'defaultonly') {
			$current_image_list = pp_images_with_data('default','imgtime DESC',$list_start,$pp_options['images_per_page']);
		} else {
			$current_image_list = pp_images_with_data('',$pp_sort,$list_start,$pp_options['images_per_page']);
		}
		$i = 1;
		foreach ((array)$current_image_list as $image_data) {
			if (strlen($image_data['imgfile']) > 20) {
				$image_file_short = substr($image_data['imgfile'],0,17) . '...';
			} else {
				$image_file_short = $image_data['imgfile'];
			}
			echo '<tr>
			<td><input type="hidden" name="row' . $i . '[imgfile]" value="' . $image_data['imgfile'] . '" /><input type="hidden" name="row' . $i . '[catslug]" value="' . $image_data['catslug'] . '" />';
			$image_size = @getimagesize($pp_options['photospath'] . '/' . $image_data['imgfile']);
			echo '<a href="' . $pp_options['photosaddress'] . '/' . $image_data['imgfile'] . '" onclick="pp_image_popup(\'' . $pp_options['photosaddress'] . '/' . $image_data['imgfile'] . '\',' . $image_size[0] . ',' . $image_size[1] . ',\'' . js_escape(stripslashes($image_data['imgname'])) . '\'); return false;" title="' . attribute_escape(stripslashes($image_data['imgname'])) . '">';
			if ($pp_options['thumbs_in_mass_edit'] == '1') {
				$thumb_size = @getimagesize($pp_options['photospath'] . '/' . $pp_options['thumbprefix'] . $image_data['imgfile']);
				echo '<img src="' . $pp_options['photosaddress'] . '/' . $pp_options['thumbprefix'] . $image_data['imgfile'] . '" ' . $thumb_size[3] . ' alt="' . attribute_escape(stripslashes($image_data['imgfile'])) . '" /><br />';
			}
			echo $image_file_short . '</a></td>
			<td><p><select name="row' . $i . '[catdrop]">' . pp_cat_dropdown($pp_cats,$image_data['category']) . '</select><p/></td>
			<td><p><input type="hidden" name="row' . $i . '[category]" value="' . $image_data['category'] . '" /><input type="text" size="12" name="row' . $i . '[newimgcat]" value="" /><p/></td>
			<td><input type="text" size="12" name="row' . $i . '[imgname]" value="' . stripslashes($image_data['imgname']) . '" /></td>
			<td><input type="text" size="27" name="row' . $i . '[imgdesc]" value="' . stripslashes($image_data['imgdesc']) . '" /></td>
			<td>';
			if ($pp_options['allow_post_image_delete'] == '0') {
				$filename = $image_data['imgfile'];
				query_posts("s=$filename");
				if ( !have_posts() ) {
					echo '<input type="checkbox" name="row' . $i . '[imgdelete]" value="1" />';
				}
			} else {
					echo '<input type="checkbox" name="row' . $i . '[imgdelete]" value="1" />';
				}
			echo "</td>\n</tr>\n";
			$i++;
		}
		echo '
		</table>
		<p class="submit"><input type="submit" name="Submit" value="' . __('Update','photopress') . ' &raquo;" /></p>
		</form>
		</div>
		';
	// Begin Category Manager
	} elseif (isset($_GET['pp_manage_cat'])) {
		$pp_manage_slug = urldecode($_GET['pp_manage_cat']);
		$sluginfo = pp_sluginfo($pp_manage_slug);
		echo '
		<div class="wrap">
		<h2>' . __('Images in the','photopress') . ' <em>' . stripslashes($sluginfo['category']) . '</em> ' . __('category','photopress') . '</h2>
   	<p><form>' . __('Sort: ','photopress') . '<select name="sort" onChange="setbrowsesort(this)">
			<option value="pp_manage_cat=' . $pp_manage_slug . '&sort=imgtimeD"'; if ($_GET['sort'] == 'imgtimeD' || !isset($_GET['sort'])) { echo ' selected="selected"'; } echo '>' . __('New to Old','photopress') . '</option>
			<option value="pp_manage_cat=' . $pp_manage_slug . '&sort=imgtimeA"'; if ($_GET['sort'] == 'imgtimeA') { echo ' selected="selected"'; } echo '>' . __('Old to New','photopress') . '</option>
			<option value="pp_manage_cat=' . $pp_manage_slug . '&sort=imgfileA"'; if ($_GET['sort'] == 'imgfileA') { echo ' selected="selected"'; } echo '>' . __('A to Z','photopress') . '</option>
			<option value="pp_manage_cat=' . $pp_manage_slug . '&sort=imgfileD"'; if ($_GET['sort'] == 'imgfileD') { echo ' selected="selected"'; } echo '>' . __('Z to A','photopress') . '</option>
			</select></form></p>';
		if ($_GET['sort'] == 'imgfileD') {
			$pp_sort = 'imgfile DESC';
		} elseif ($_GET['sort'] == 'imgfileA') {
			$pp_sort = 'imgfile';
		} elseif ($_GET['sort'] == 'imgtimeA') {
			$pp_sort = 'imgtime';
		} else {
			$pp_sort = 'imgtime DESC';
		}
		if (isset($_GET['pp_page'])) {
			$current_page = $_GET['pp_page'];
		} else {
			$current_page = 1;
		}
		$image_count = pp_count($pp_manage_slug);
		$pages = (int)ceil($image_count/$pp_options['images_per_page']);
		if ($pages > 1) {
			echo '<p>';
			for ($i=1; $i<=$pages; $i++) {
				if ($i != $current_page) {
					echo '<a href="' . get_settings('siteurl') . '/wp-admin/tools.php?page=pp_album_manager&amp;pp_manage_cat=' . $pp_manage_slug . '&amp;pp_page=' . $i; if (isset($_GET['sort'])) { echo '&amp;sort=' . $_GET['sort']; } echo '">';
				}
				echo __('Page','photopress') . ' ' . $i;
				if ($i != $current_page) {
					echo '</a>';
				}
				if ($i < $pages) {
					echo ' | ';
				}
			}
			echo '</p>
			';
			$list_start = ($current_page - 1) * $pp_options['images_per_page'];
		} else { // there's only one page so it's the whole list
				$list_start = 0;
		}
		$current_image_list = pp_images_with_data($pp_manage_slug,$pp_sort,$list_start,$pp_options['images_per_page']);
		echo "<ul class='pp_slides'>\n";
		foreach((array)$current_image_list as $pp_image) {
			echo '<div class="pp_tag"><li class="pp_thumb"><a href="tools.php?page=pp_album_manager&amp;pp_manage_image=' . $pp_image['imgfile'] . '" title="' . attribute_escape(stripslashes($pp_image['imgname'])) . '"><img src="' . $pp_options['photosaddress'] . '/' . $pp_options['thumbprefix'] . $pp_image['imgfile'] . '" title="' . attribute_escape(stripslashes($pp_image['imgname'])) . '" alt="' . attribute_escape(stripslashes($pp_image['imgname'])) . '" /></a><br />';
			if (strlen(stripslashes($pp_image['imgname'])) > 15) {
				echo substr(stripslashes($pp_image['imgname']),0,12) . '...';
			} else {
				echo stripslashes($pp_image['imgname']);
			}
			echo "</li></div>\n";
		}
		echo "</ul>\n</div>\n";
	// Begin Category Editor
	} elseif (isset($_GET['pp_change_cat'])) {
		echo '<div class="wrap">
		<h2>' . __('Edit Categories','photopress') . '</h2>
		<form name="pp_album_cats" method="post" action="">
		<input type="hidden" name="pp_change_cat" value="update" />
		<table>
		<tr>
			<th scope="col">' . __('Choose Existing','photopress') . '</th>
			<th scope="col">' . __('Enter New','photopress') . '</th>
			<th scope="col">' . __('Hide in Album','photopress') . '</th>
			<th scope="col">' . __('Sort','photopress') . '</th>
		</tr>
		';
		$slugarray = pp_catlist();
		$pp_cats = pp_list_cats();
		$i = 0;
		foreach ((array)$slugarray as $slug) {
			$slug_count = pp_count($slug['catslug']);
			if ($slug_count > 0) {
				echo '<tr><td><input type="hidden" name="row' . $i . '[oldslug]" value="' . $slug['catslug'] . '" /><input type="hidden" name="row' . $i . '[oldcat]" value="' . stripslashes($slug['category']) . '" /><select name="row' . $i . '[dropcat]" id="dropcat">' . pp_cat_dropdown($pp_cats,stripslashes($slug['category'])) . "</select></td>\n<td>" . '<input type="text" value="' . stripslashes($slug['category']) . '" name="row' . $i . '[newcat]" />' . "</td>\n<td><label>" . '<input type="radio" name="row' . $i . '[hidden]" value="hide" ';
					if ($slug['hidden'] == 'hide') {
						echo 'checked="checked" ';
					}
					echo '/> ' . __('Hide','photopress') . "</label><br />\n<label>" . '<input type="radio" name="row' . $i . '[hidden]" value="display" ';
					if ($slug['hidden'] != 'hide') {
						echo 'checked="checked" ';
					}
					echo '/> ' . __('Display','photopress') . "</label><br />\n</td>\n<td>" . '<select name="row' . $i . '[catsort]">' . "\n<option" . ' value="imgname"';
					if ($slug['catsort'] == 'imgname') {
						echo ' selected="selected"';
					}
					echo '>' . __('A-Z by image name','photopress') . "</option>\n" . '<option value="imgname DESC"';
					if ($slug['catsort'] == 'imgname DESC') {
						echo ' selected="selected"';
					}
					echo '>' . __('Z-A by image name','photopress') . "</option>\n" . '<option value="imgfile"'; if ($catsort == 'imgfile') { echo ' selected="selected"'; } echo '>' . __('A-Z by file name','photopress') . '</option>
				<option value="imgfile DESC"';
					if ($slug['catsort'] == 'imgfile DESC') {
						echo ' selected="selected"';
					}
					echo '>' . __('Z-A by file name','photopress') . "</option>\n" . '<option value="imgtime"';
					if ($slug['catsort'] == 'imgtime') {
						echo ' selected="selected"';
					}
					echo '>' . __('New to Old','photopress') . "</option>\n" . '<option value="imgtime DESC"';
					if ($slug['catsort'] == 'imgtime DESC') {
						echo ' selected="selected"';
					}
					echo '>' . __('Old to New','photopress') . "</option>\n</select></td>\n</tr>\n";
				$i++;
			}
		}
		echo "</table>\n" . '<p class="submit"><input type="submit" name="Submit" value="' . __('Update Categories &raquo;','photopress') . '" /></p></form>' . "\n</div>\n";
	} elseif (pp_count() > 0) {
		$slugs = pp_catlist();
		echo '<div class="wrap">' . "<h2>\n" . __('Categories','photopress') . "</h2>\n<ul class='pp_slides'>\n";
		foreach ((array)$slugs as $slug) {
			$slug_count = pp_count($slug['catslug']);
			if ($slug_count > 0) {
				$highlight = pp_highlight($slug['catslug']);
				echo '<div class="pp_tag"><li class="pp_thumb"><a href="tools.php?page=pp_album_manager&amp;pp_manage_cat=' . $slug['catslug'] . '">' . $highlight . "</a><br />" . stripslashes($slug['category']) . ' (' . $slug_count . ")</li></div>\n";
			}
		}
		echo "</ul>\n</div>\n";
	}
}

function pp_add_manager_page() {
	global $pp_options;
	add_management_page('Photopress', 'Photopress', $pp_options['maintain_cap'], 'pp_album_manager', 'pp_album_manager');
}

add_action('admin_menu', 'pp_add_manager_page');

// Returns string for image tag of a slug's highlight, or a random image from that slug if there's no highlight.
function pp_highlight($slug) {
	global $pp_options;
	$sluginfo = pp_sluginfo($slug);
	if (!is_file($pp_options['photospath'] . '/' . $pp_options['thumbprefix'] . $slug['catrep'])) {
		$randimage = pp_random_image($slug,2);
		return $randimage[0];
	} else {
	}
}

// Process the category management array.
function pp_change_cat($cat_array) {
	global $table_prefix, $wpdb, $pp_options;
	$cat_table_name = $table_prefix . "pp_cats";
	$table_name = $table_prefix . "photopress";
	if ($updateslug = pp_slugify($cat_array['category'])) {
		if ($cat_array['oldslug'] != $updateslug) {
			$wpdb->query("UPDATE $table_name SET catslug='$updateslug' WHERE binary catslug='" . $cat_array['oldslug'] . "'");
		}
		return pp_cat_table_update(array('catslug'=>$updateslug,'category'=>$cat_array['category'],'hidden'=>$cat_array['hidden'],'catsort'=>$cat_array['catsort']));
	} else {
		return FALSE;
	}
}

// Delete function.
function pp_delete_photo($photo) {
	global $pp_options, $wpdb, $table_prefix;
	$pp_phototodelete = $pp_options['photospath'] . '/' . $photo;
	$pp_thumbtodelete = $pp_options['photospath'] . '/' . $pp_options['thumbprefix'] . $photo;
	$pp_origtodelete = $pp_options['photospath'] . '/' . $pp_options['origprefix'] . $photo;
	$table_name = $table_prefix . "photopress";
	if (!is_file($pp_thumbtodelete) || !is_file($pp_phototodelete)) {
		$wpdb->query("DELETE FROM $table_name WHERE binary imgfile = '$photo'");
		return FALSE;
	} else {
		@unlink($pp_phototodelete);
		@unlink($pp_thumbtodelete);
		if (is_file($pp_origtodelete)) {
			@unlink($pp_origtodelete);
		}
		$wpdb->query("DELETE FROM $table_name WHERE binary imgfile = '$photo'");
		return TRUE;
	}
	
}

// Rotate image function, rotates $image counterclockwise by $angle, returns TRUE or an error message.
function pp_rotate($image, $angle, $copy) {
	global $pp_options;
	$origdest = $pp_options['photospath'] . '/' . $image; // this should be the path to the image
	if (is_file($origdest)) { // if the file isn't there we shouldn't do anything
		$newdest = $origdest;
		if ($copy == 'copy') { // find a new name if making a copy
			$i = 1;
			while (is_file($newdest)) {
				$realbase = substr($image,0,strrpos($image, '.'));
				$newname = $realbase . '_' . $i . '.' . pathinfo($origdest,PATHINFO_EXTENSION);
				$newdest = $pp_options['photospath'] . '/' . $newname;
				$i++;
			}
		}
		$type = @getimagesize($origdest);
		// if the associated function doesn't exist - then it's not
		// handle. duh. i hope.
		if (!function_exists('imagegif') && $type[2] == 1) {
			$error = __('Filetype not supported.','photopress');
		} elseif(!function_exists('imagejpeg') && $type[2] == 2) {
			$error = __('Filetype not supported.','photopress');
		} elseif(!function_exists('imagepng') && $type[2] == 3) {
			$error = __('Filetype not supported.','photopress');
		} else {
		// create the copy from the original file, rotate, and write to disk
			if($type[2] == 1) {
				$newimage = imagecreatefromgif($origdest);
				$rotated = imagerotate($newimage,$angle,0);
				if(!imagegif($rotated, $newdest))
					$error = __('Failed to write rotated image to disk at ','photopress') . $newdest;
			} elseif($type[2] == 2) {
				$newimage = imagecreatefromjpeg($origdest);
				$rotated = imagerotate($newimage,$angle,0);
				if(!imagejpeg($rotated, $newdest, $pp_options['quality']))
					$error = __('Failed to write rotated image to disk at ','photopress') . $newdest;
			} elseif($type[2] == 3) {
				$newimage = imagecreatefrompng($origdest);
				$rotated = imagerotate($newimage,$angle,0);
				if(!imagepng($rotated, $newdest))
					$error = __('Failed to write rotated image to disk at ','photopress') . $newdest;
			}
			if (is_file($newdest)) {
				@chmod($newdest,0664);
				$thumbed = pp_resize($newdest, $pp_options['thumbsize'], $pp_options['thumbsize'], $pp_options['thumbprefix'], 1);
				@chmod($pp_options['photospath'] . '/' . $pp_options['thumbprefix'] . $newname,0664);
				$data = pp_get_data($image);
				pp_table_update(array($newname,$data['imgname'],$data['imgdesc'],$data['imgcat'],$data['catslug']));
			} else {
				$error = __('Failed to write rotated image to disk at ','photopress') . $newdest;
			}
			@imagedestroy($image);
		}
	} else {
		$error = $image . __(' does not exist.','photopress');
	}
	if (!empty($error)) {
		return $error;
	} else {
		return 1;
	}
}

// Install tables in the db for PP image data.
function pp_table_install() {
	global $table_prefix, $wpdb, $pp_options;
	$table_name = $table_prefix . "photopress";
	$cat_table_name = $table_prefix . "pp_cats";
	$sql = "CREATE TABLE ".$table_name." (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		imgfile varchar(100) NOT NULL,
		imgname varchar(55) NULL,
		imgdesc text NULL,
		catslug varchar(55) NULL,
		catid mediumint(9) NULL,
		catsort varchar(55) NULL,
		imgtags varchar(255) NULL,
		imgsizes varchar(55) NULL,
		attachid bigint(20) UNSIGNED NULL,
		imghide varchar(55) NULL,
		imgtime varchar(55) NULL,
		UNIQUE KEY id (id)
	); CREATE TABLE ".$cat_table_name." (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		catslug varchar(55) NOT NULL,
		category varchar(55) NULL,
		catrep varchar(255) NULL,
		catsort varchar(55) NULL,
		hidden varchar(55) NULL,
		UNIQUE KEY id (id)
	);";
	require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
	dbDelta($sql);
   // import image info stored in the WP Options by previous version of PP if there's data there and if there's no data in the category column of the category table (so we won't accidentally overwrite anything)
	if (get_option('pp_album_cats') && !is_array($wpdb->get_col("SELECT category FROM $cat_table_name LIMIT 5"))) {
		pp_import_old_data();
	}
	// make slugs if needed
	pp_slug_import();
	// populate the category table
	pp_import_cat_data();
	$pp_options['db_version'] = '1.8';
	update_option('pp_options', $pp_options);
	return TRUE;
}

// Imports category data into the category table.
function pp_import_cat_data() {
	global $table_prefix, $wpdb;
	$table_name = $table_prefix . "photopress";
	$cat_table_name = $table_prefix . "pp_cats";
	if ($slugs = $wpdb->get_col("SELECT catslug FROM $table_name")) {
		$slugs = array_unique($slugs);
		foreach ((array)$slugs as $slug) {
			if ($data = $wpdb->get_row("SELECT * FROM $table_name WHERE binary catslug='$slug'", ARRAY_A)) {
				if (empty($data['imghide'])) { $data['imghide'] = 'display'; }
				if (empty($data['catsort'])) { $data['catsort'] = 'imgname'; }
				pp_cat_table_update(array('catslug'=>$slug,'category'=>$data['imgcat'],'hidden'=>$data['imghide'],'catsort'=>$data['catsort']));
			}
		}
		return TRUE;
	} else {
		return FALSE;
	}
}

// Imports old Photopress data, stored in WP's Options table.
function pp_import_old_data() {
	$cat_array = array();
	$cat_array = get_option('pp_album_cats');
	foreach((array)$cat_array as $img=>$category) {
		$filename = substr_replace($img, '.', -4, 1);
		if (empty($category)) {
			$category = 'Default';
		}
		$name = strtr(substr($img, 0, -4), "_", " ");
		$catslug = pp_slugify($category);
		$import_array = array('imgfile'=>$filename, 'imgname'=>$name, 'imgdesc'=>$name, 'catslug'=>$catslug, 'imgtags'=>'');
		pp_table_update($import_array);
		$cat_array = array('category'=>$category, 'catslug'=>$catslug, 'imghide'=>'display', 'catsort'=>'imgname');
		pp_cat_table_update($cat_array);
	}
}

// Populates the slug field with slugified cat names, also checks time date field to see if any are null and populates them if so.
function pp_slug_import() {
	global $table_prefix, $wpdb;
	$table_name = $table_prefix . "photopress";
	if ($wpdb->get_var("SELECT count(*) FROM $table_name where catslug is null or catslug = ''") > 0) {
// first, look for old imgcats and update
		$cats = array_unique($wpdb->get_col("SELECT imgcat FROM $table_name"));
		foreach ($cats as $cat) {
			if (!empty($cat)) {
				$catslug = pp_slugify($cat);
				$catforquery = $wpdb->escape($cat);
				$wpdb->query("UPDATE $table_name SET catslug='$catslug' WHERE binary imgcat='$catforquery'");
			}
		}
// next, look for empty catslugs and update to default
		if ($wpdb->query("UPDATE $table_name SET catslug='default' WHERE catslug is null or catslug = ''")) {
			$cat_array = array('category'=>'Default', 'catslug'=>'default');
			pp_cat_table_update($cat_array);
		}
	}
	if ($notime = $wpdb->get_results("SELECT * FROM $table_name where imgtime is null",ARRAY_A) ) { // we just need to get the rows with null imgtimes and run table_update on them since that function gets the file's mtime anyway
		foreach ($notime as $notime_row) {
			if (is_array($notime_row)) {
				pp_table_update($notime_row);
			}
		}
	}
}

// run the table installer when the plugin is activated
if (isset($_GET['activate']) && $_GET['activate'] == 'true') {
	add_action('init', 'pp_table_install');
}

// Imports images in the photos folder that aren't in the database.
function pp_import_orphans() {
	global $wpdb, $table_prefix;
	$table_name = $table_prefix . 'photopress';
	$cat_table_name = $table_prefix . 'pp_cats';
	$full_array = pp_folder_contents();
	$in_db = $wpdb->get_col("SELECT imgfile FROM $table_name");
	$not_in_db = array_diff((array)$full_array, (array)$in_db);
	$imported = 0;
	foreach((array)$not_in_db as $imgfile) {
		$imgname = strtr(substr($imgfile, 0, strrpos($imgfile, '.')), "_", " "); // set imgname to the cleaned-up filename
		$import_array = array('imgfile'=>$imgfile,'imgname'=>$imgname);
		pp_table_update($import_array);
		$imported++;
	}
	return $imported;
}

// Filename-fixing function. Strips bad characters from file names, updates filenames and the DB.
function pp_filename_repair() {
	global $wpdb, $table_prefix, $pp_options;
	$table_name = $table_prefix . 'photopress';
	$images = pp_folder_contents();
	$repaired = 0;
	foreach ((array)$images as $image) {
		if (preg_match('/[^a-z0-9_.]/i',$image)) {
			$newname = preg_replace('/[^a-z0-9_.]/i', '_', $image);
			$destination = $pp_options['photospath'] . '/' . $newname;
			$i = 1;
			while (is_file($destination)) { // in case the new name is already taken
				$image_name = pathinfo($destination,PATHINFO_BASENAME);
				$realbase = substr($image_name,0,strrpos($image_name, '.'));
				$destination = $pp_options['photospath'] . '/' . $realbase . '_' . $i . '.' . pathinfo($destination,PATHINFO_EXTENSION);
				$i++;
			}
			if (is_file($pp_options['photospath'] . '/' . $image)) {
				@rename($pp_options['photospath'] . '/' . $image , $destination);
			}
			if (is_file($pp_options['photospath'] . '/' . $pp_options['thumbprefix'] . $image)) {
				@rename($pp_options['photospath'] . '/' . $pp_options['thumbprefix'] . $image , $pp_options['photospath'] . '/' . $pp_options['thumbprefix'] . pathinfo($destination,PATHINFO_BASENAME));
			}
			if (is_file($pp_options['photospath'] . '/' . $pp_options['origprefix'] . $image)) {
				@rename($pp_options['photospath'] . '/' . $pp_options['origprefix'] . $image , $pp_options['photospath'] . '/' . $pp_options['origprefix'] . pathinfo($destination,PATHINFO_BASENAME));
			}
			if (is_file($destination)) {
				$repaired++;
			}
			$escaped_oldimage = $wpdb->escape($image);
			$escaped_newimage = $wpdb->escape(pathinfo($destination,PATHINFO_BASENAME));
			$wpdb->query("UPDATE $table_name SET imgfile='$escaped_newimage' WHERE binary imgfile='$escaped_oldimage'");
		}
	}
	if ($fixed > 0) {
		return $fixed;
	} else {
		return FALSE;
	}
}

// Mass resize function, massively improved by Roge's suggestions and code.
function pp_mass_resize() {
	global $pp_options;
	$images = pp_folder_contents();
	$thumbsdone = 0;
	$imagesdone = 0;
	foreach ((array)$images as $image) {
		$pathtoimage = $pp_options['photospath'] . '/' . $image;
		$pathtothumb = $pp_options['photospath'] . '/' . $pp_options['thumbprefix'] . $image;
		$pathtoorig = $pp_options['photospath'] . '/' . $pp_options['origprefix'] . $image;
		if ($imginfo = getimagesize($pathtoimage)) {
			if (($imginfo[0] > $pp_options['maxsize']) || ($imginfo[1] > $pp_options['maxsize'])) {
				if (($pp_options['originals'] == '1') && !is_file($pathtoorig)) {
					@copy($pathtoimage,$pathtoorig);
				}
				if (pp_resize($pathtoimage, $pp_options['maxsize'], 0, '', 0)) {
					$imagesdone++;
				}
			} elseif (($imginfo[0] < $pp_options['maxsize']) && ($imginfo[1] < $pp_options['maxsize']) && is_file($pathtoorig)) {
				if ($originfo = getimagesize($pathtoorig)) {
					if (($originfo[0] > $imginfo[0]) || ($originfo[1] > $imginfo[1])) {
						@copy($pathtoorig,$pathtoimage);
						if (pp_resize($pathtoimage, $pp_options['maxsize'], 0, '', 0)) {
							$imagesdone++;
						}
					}
				}
			}
			$thumbinfo = getimagesize($pathtothumb);
			if (($pp_options['square'] == '1') && ($thumbinfo[0] != $thumbinfo[1])) {
				if (pp_resize($pathtoimage, $pp_options['thumbsize'], $pp_options['thumbsize'], $pp_options['thumbprefix'], 1)) {
					$thumbsdone++;
				}
			} elseif ((($thumbinfo[0] > $pp_options['thumbsize']) || ($thumbinfo[1] > $pp_options['thumbsize'])) || (($thumbinfo[0] < $pp_options['thumbsize']) && ($thumbinfo[1] < $pp_options['thumbsize'])) || (($pp_options['square'] == '0') && ($thumbinfo[0] == $thumbinfo[1]))) {
				if (pp_resize($pathtoimage, $pp_options['thumbsize'], $pp_options['thumbsize'], $pp_options['thumbprefix'], 1)) {
					$thumbsdone++;
				}
			}
		}
	}
	return array($imagesdone,$thumbsdone);
}

// Removes records from DB for images that are not on disk.
function pp_db_cleanup() {
	global $wpdb, $table_prefix, $pp_options;
	$table_name = $table_prefix . 'photopress';
	$in_db = $wpdb->get_col("SELECT imgfile FROM $table_name");
	if ($full_array = pp_folder_contents()) {
		$not_on_disk = array_diff($in_db, $full_array);
	} else {
		$not_on_disk = $in_db;
	}
	$records = 0;
	foreach ($not_on_disk as $photo) {
		if (!is_file($pp_options['photospath'] . '/' . $photo)) {
			if ($wpdb->query("DELETE FROM $table_name WHERE binary imgfile = '$photo'")) {
				$records++;
			}
		}
	}
	if ($records > 0) {
		return $records;
	} else {
		return FALSE;
	}
}

// Called by photopress_tag_process to process the [photopress:...] tags in posts into linked image tags. Receives the tag, returns a linked image.
function photopress_make_link($stuff,$ditch = FALSE) {
	global $pp_options;
	$file = explode(',',$stuff);
	if (!empty($file[2])) { // support earlier tags that didn't have own class
		$image_class = $file[2];
	} else {
		$image_class = 'alignnone';
	}
	if ($file[1] == 'thumb') {
		if ($thumbsize = @getimagesize($pp_options['photospath'] . '/' . $pp_options['thumbprefix'] . $file[0])) {
			$image_data = pp_get_data($file[0]);
			if (($pp_options['album'] == '1') && ($ditch == FALSE)) {
				return '<a href="' . $pp_options['albumaddress'] . $pp_options['cat_token'] . $image_data['catslug'] . $pp_options['images_token'] . $file[0] . '" title="' . attribute_escape(stripslashes($image_data['imgname'])) . '"><img src="' . $pp_options['photosaddress'] . '/' . $pp_options['thumbprefix'] . $file[0] . '" class="' . $image_class . '" alt="' . attribute_escape(stripslashes($image_data['imgname'])) . '" ' . $thumbsize[3] . ' /></a>';
			} else {
				if (is_file($pp_options['photospath'] . '/' . $pp_options['origprefix'] . $file[0])) {
					$image_size = @getimagesize($pp_options['photospath'] . '/' . $pp_options['origprefix'] . $file[0]);
					$image_address = $pp_options['photosaddress'] . '/' . $pp_options['origprefix'] . $file[0];
				} else {
					$image_size = @getimagesize($pp_options['photospath'] . '/' . $file[0]);
					$image_address = $pp_options['photosaddress'] . '/' . $file[0];
				}
				if (($pp_options['album'] == 'lightbox') | ($ditch == TRUE)) {
					$action = 'rel="lightbox"';
				} else {
					$action = 'onclick="pp_image_popup(\'' . $image_address . '\',' . $image_size[0] . ',' . $image_size[1] . ',\'' . js_escape(stripslashes($image_data['imgname'])) . '\'); return false;"';
				}
				return '<a href="' . $image_address . '" title="' . attribute_escape(stripslashes($image_data['imgname'])) . '" ' . $action . '><img src="' . $pp_options['photosaddress'] . '/' . $pp_options['thumbprefix'] . $file[0] . '" class="' . $image_class . '" alt="' . attribute_escape(stripslashes($image_data['imgname'])) . '" ' . $thumbsize[3] . ' /></a>';
			}
		} else {
			return ''; // if the image isn't there return nothing
		}
	} elseif ($file[1] == 'full') {
		if ($fullsize = @getimagesize($pp_options['photospath'] . '/' . $file[0])){
			if (($pp_options['album'] == '1') && ($ditch == FALSE)) {
				$image_data = pp_get_data($file[0]);
				return '<a href="' . $pp_options['albumaddress'] . $pp_options['cat_token'] . $image_data['catslug'] . $pp_options['images_token'] . $file[0] . '" title="' . attribute_escape(stripslashes($image_data['imgname'])) . '"><img src="' . $pp_options['photosaddress'] . '/' . $file[0] . '" class="' . $image_class . '" alt="' . attribute_escape(stripslashes($image_data['imgname'])) . '" ' . $fullsize[3] . ' /></a>';
			} else {
				if (is_file($pp_options['photospath'] . '/' . $pp_options['origprefix'] . $file[0])) {
					$image_size = @getimagesize($pp_options['photospath'] . '/' . $pp_options['origprefix'] . $file[0]);
					$image_address = $pp_options['photosaddress'] . '/' . $pp_options['origprefix'] . $file[0];
				} else {
					$image_size = @getimagesize($pp_options['photospath'] . '/' . $file[0]);
					$image_address = $pp_options['photosaddress'] . '/' . $file[0];
				}
				if (($pp_options['album'] == 'lightbox') | ($ditch == TRUE)) {
               $action = 'rel="lightbox"';
            } else {
               $action = 'onclick="pp_image_popup(\'' . $image_address . '\',' . $image_size[0] . ',' . $image_size[1] . ',\'' . js_escape(stripslashes($image_data['imgname'])) . '\'); return false;"';
            }
				return '<a href="' . $image_address . '" title="' . attribute_escape(stripslashes($image_data['imgname'])) . '" ' . $action . '><img src="' . $pp_options['photosaddress'] . '/' . $file[0] . '" class="' . $image_class . '" alt="' . attribute_escape(stripslashes($image_data['imgname'])) . '" ' . $fullsize[3] . ' /></a>';
			}
		} else {
			return ''; // if the image isn't there return nothing
		}
	} else {
		return ''; // if there's something wrong with the tag return nothing
	}
}

// Process WP content for PP tags, running photopress_make_link on any matches.
function photopress_tag_process($content = '') {
	if ( ('' == $content) || (! strstr($content, '[photopress:')) ) { return $content; }
	return preg_replace( "/\[photopress\:(.*?)\]/e","photopress_make_link('\\1')", $content);
}

function pp_album_insert($content = '') {
	if ( ('' == $content) || (! strstr($content, 'photopress_album')) ) { return $content; }
	return preg_replace('|(<p>)?(\n)*<!--photopress_album-->(\n)*(</p>)?|', pp_album(), $content);
}

// Super-dangerous function...goes through posts, replacing $search_text with $replace_text...could be used to switch from tags to html
function photopress_s_n_r($search_text,$replace_text) {
	global $wpdb;
	$query = "UPDATE $wpdb->posts ";
	$query .= "SET post_content = ";
	$query .= "REPLACE(post_content, '$search_text', '$replace_text') ";
	$wpdb->get_results($query);
}

function pp_migrate() {
	global $wpdb, $table_prefix, $pp_options;
   $table_name = $table_prefix . 'photopress';
	$pp_db_version = (float)$pp_options['pp_db_version'];
	if ($pp_db_version < 1.8) {
		pp_table_install();
	}
	$migrated = 0;
   $images = $wpdb->get_results("SELECT * FROM $table_name",ARRAY_A); // the photopress images
	foreach ($images as $image) {
		if ((!wp_attachment_is_image($image['attachid']))  && ($migrated < $pp_options['per_migrate'])) {
		$idtxt = '';
		$imagename = $image['imgfile'];
		$ppwud = wp_upload_dir(date('Y-m-d H:i:s',$image['imgtime'])); // where to put the image
		$pathtoorig = $pp_options['photospath'] . '/' . $pp_options['origprefix'] . $image['imgfile'];
		$pathtoimg = $pp_options['photospath'] . '/' . $image['imgfile'];
		if (is_file($pathtoorig)) {
			$migratethis = $pathtoorig;
		} else {
			$migratethis = $pathtoimg;
		}
		$uniquename = wp_unique_filename($ppwud['path'],$image['imgfile']);
		$destination = $ppwud['path'] . '/' . $uniquename;
		$dest_url = $ppwud['url'] . '/' . $uniquename;
		$pp_image_data = pp_get_data($image['imgfile']);
		$type = wp_check_filetype($migratethis);
		wp_mkdir_p($ppwud['path']);
		copy($migratethis,$destination);
		$stat = stat( dirname($destination));
		$perms = $stat['mode'] & 0000666;
		@ chmod( $destination,$perms);
		$post_id = array();
		$query = $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_content REGEXP '$imagename'");
		if ($post_id = $wpdb->get_row($query,ARRAY_A)) {
			$parent_id = $post_id['ID'];
		} else {
			$parent_id = '0';
		}
		$imgtime_gmt = gmdate('Y-m-d H:i:s',$image['imgtime']);
		$imgtime = gmdate('Y-m-d H:i:s',($image['imgtime']+(get_option('gmt_offset')*3600)));

		$attachment_obj = array(
      'post_parent' => $parent_id,
      'post_title' => $pp_image_data['imgname'],
      'post_date' => $imgtime,
      'post_date_gmt' => $imgtime_gmt,
      'post_name' => $pp_image_data['imgname'],
      'post_content' => $pp_image_data['imgdesc'],
      'post_excerpt' => $pp_image_data['category'],
      'post_mime_type' => $type['type'],
      'guid' => $dest_url);
		$attach_id = wp_insert_attachment( $attachment_obj, $destination);
		wp_update_attachment_metadata( $attach_id, wp_generate_attachment_metadata( $attach_id, $destination) );
		update_post_meta( $attach_id, '_wp_attachment_image_alt', addslashes($pp_image_data['imgname']) );
		$imgfile = $pp_image_data['imgfile'];
// need to add the attachment id to the photopress image table down here - also updates attachid in case of re-migrate
		$wpdb->query($wpdb->prepare("UPDATE $table_name SET attachid='$attach_id' WHERE binary imgfile='$imgfile'"));
		$wpdb->flush();
		$migrated++;
	}
	}
	return $migrated;
}

function pp_replace_tags($migrate = FALSE) {
	global $wpdb, $pp_options;
	$query = "SELECT post_content FROM $wpdb->posts WHERE post_content LIKE '%photopress%'";
	$rows = $wpdb->get_results($query,ARRAY_A);
	$wpdb->flush();
	$num_tags = 0;
	$num_posts = 0;
	foreach ($rows as $row) {
		if ( strstr($row['post_content'], '[photopress:') ) {
			$num_posts++;
			preg_match_all( "/\[photopress\:(.*?)\]/",$row['post_content'],$matches,PREG_SET_ORDER);
			foreach ($matches as $match) {
				$match_array = explode(',',$match[1]);
				// if no class found use the default
				if (!empty($match_array[2])) {
					$image_class = $match_array[2];
				} else {
					$image_class_array = explode(' ',$pp_options['image_class']);
					// the first one is the default
					$image_class = $image_class_array[0];
				}
				if ($migrate) {
					$image_data = pp_get_data($match_array[0]);
					if (!empty($image_data['attachid'])) {
						$attach_url = get_attachment_link($image_data['attachid']);
						if ($match_array[1] == 'thumb') {
							$linked_image = '<a href="' . $attach_url . '">' . get_image_tag($image_data['attachid'],$image_data['imgname'],$image_data['imgname'],'none','thumbnail') . '</a>';
						} else {
							$linked_image = '<a href="' . $attach_url . '">' . get_image_tag($image_data['attachid'],$image_data['imgname'],$image_data['imgname'],'none','medium') . '</a>';
						}
						photopress_s_n_r($match[0],$linked_image);
						$num_tags++;
					}
				} else {
					$pplink = photopress_make_link($match[1],TRUE);
					photopress_s_n_r($match[0],$pplink);
					$num_tags++;
				}
			}
		}
	}
	return array($num_tags,$num_posts);
}

// Not sure why, but a couple of these seem to need to be in a class to work.
class photopress_actions {
// Rewrite rules for WP 2-ish, inspired by the Ultimate Tag Warrior plugin.
	function &photopress_album_rewrite(&$wp_rewrite) {
		global $pp_options, $wp_rewrite;
		if ($pp_options['wppermalinks'] == 'index') {
			$root_token = 'index.php/';
		} else {
			$root_token = '';
		}
		if ($pp_options['usepgid'] == '1') { $beforetoken = "page_id"; } else { $beforetoken = "pagename"; }
		$newrules[$root_token . $pp_options['album_token'] . '/([A-Za-z0-9-]+)/([0-9]+)?/?$'] = "index.php?" . $beforetoken . "=" . $pp_options['album_token'] . "&pp_album=main&pp_cat=" . $wp_rewrite->preg_index(1) . "&pp_page=" . $wp_rewrite->preg_index(2);
		$newrules[$root_token . $pp_options['album_token'] . '/([A-Za-z0-9-]+)/(.+)?/?$'] = "index.php?" . $beforetoken . "=" . $pp_options['album_token'] . "&pp_album=main&pp_cat=" . $wp_rewrite->preg_index(1) . "&pp_image=" . $wp_rewrite->preg_index(2);
		$newrules[$root_token . $pp_options['album_token'] . '/([0-9]+)/?$'] = "index.php?" . $beforetoken . "=" . $pp_options['album_token'] . "&pp_album=main&pp_page=" . $wp_rewrite->preg_index(1);
		$newrules[$root_token . $pp_options['album_token'] . '/([A-Za-z0-9-]+)/?$'] = "index.php?" . $beforetoken . "=" . $pp_options['album_token'] . "&pp_album=main&pp_cat=" . $wp_rewrite->preg_index(1);
		$newrules[$root_token . '(' . $pp_options['album_token'] . ')/?$'] = "index.php?" . $beforetoken . "=" . $pp_options['album_token'] . "&pp_album=main";
		$wp_rewrite->rules = $newrules + $wp_rewrite->rules;
		return $wp_rewrite;
	}
}
// Adds some PP query vars to WP's array.
function pp_add_query_var($wpvar_array) {
	$wpvar_array[] = 'pp_album';
	$wpvar_array[] = 'pp_page';
	$wpvar_array[] = 'pp_cat';
	$wpvar_array[] = 'pp_image';
	return $wpvar_array;
}

add_filter('generate_rewrite_rules', array('photopress_actions','photopress_album_rewrite'));
add_filter('query_vars','pp_add_query_var');
add_filter('the_content', 'pp_album_insert');
add_filter('the_content', 'photopress_tag_process');
if ($pp_options['process_excerpt'] == '1') {
	add_filter('the_excerpt', 'photopress_tag_process');
}
?>
