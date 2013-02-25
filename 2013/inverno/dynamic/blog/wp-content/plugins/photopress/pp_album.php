<?php

// Wrapper function to display the album. Checks both _GET and WP's get_query_var to see if pp_image or pp_cat are set, defaults to the main view otherwise.
function pp_album() {
	if (get_query_var('pp_image') != '') {
		return pp_display_image(stripslashes(get_query_var('pp_image')));
	} elseif (!empty($_GET['pp_image'])) {
		return pp_display_image(stripslashes($_GET['pp_image']));
	} elseif (!empty($_GET['pp_cat'])) {
		return pp_display_cat(stripslashes($_GET['pp_cat']));
	} elseif (get_query_var('pp_cat') != '') {
		return pp_display_cat(stripslashes(get_query_var('pp_cat')));
	} else {
		return pp_display_main();
	}
}

// Displays a paged list of images from $category_slug.
function pp_display_cat($category_slug) {
	global $pp_options;
	$pp_content = '';
	$sluginfo = pp_sluginfo($category_slug);
	if ($sluginfo) {
	$category = stripslashes($sluginfo['category']);
	$pp_content .= "<div id='pp_wrap'>\n<h3>" . $category . "</h3>\n";
	if (!empty($_GET['pp_page'])) {
		$current_page = $_GET['pp_page'];
	} elseif (get_query_var('pp_page') != '') {
		$current_page = get_query_var('pp_page');
	} else {
		$current_page = 1;
	}
	if (!$sort = $sluginfo['catsort']) {
		$sort = 'imgname';
	}
	$image_count = pp_count($sluginfo['catslug']);
	$pages = (int)ceil($image_count/$pp_options['images_per_page']);
	if ($pages > 1) {
		$pp_content .= "<p id='pp_page_links'>\n";
		if ($current_page > 1) {
			$pp_content .= "<a href='" . $pp_options['albumaddress'] . $pp_options['cat_token'] . $category_slug . $pp_options['page_token'] . ($current_page - 1) . "'>";
		}
		$pp_content .= __('Previous','photopress');
		if ($current_page > 1) {
			$pp_content .= "</a>";
		}
		$pp_content .= ' | ';
		for ($i=1; $i<=$pages; $i++) {
			if ($i != $current_page) {
				$pp_content .= "<a href='" . $pp_options['albumaddress'] . $pp_options['cat_token'] . $category_slug . $pp_options['page_token'] . $i . "'>";
			}
			$pp_content .= $i;
			if ($i != $current_page) {
				$pp_content .= "</a>";
			}
			if ($i < $pages) {
				$pp_content .= " | ";
			}
		}
		$pp_content .= " | ";
		if ($current_page < $pages) {
			$pp_content .= "<a href='" . $pp_options['albumaddress'] . $pp_options['cat_token'] . $category_slug . $pp_options['page_token'] . ($current_page + 1) . "'>";
		}
		$pp_content .= __('Next','photopress');
		if ($current_page < $pages) {
			$pp_content .= "</a>";
		}
		$pp_content .= "</p>\n";
		$list_start = ($current_page - 1) * $pp_options['images_per_page'];
	} else {
		$list_start = 0;
	}
	$cat_images = pp_images_with_data($category_slug,$sort,$list_start,$pp_options['images_per_page']);
	foreach((array)$cat_images as $key=>$array_img) {
		$thumbsize = @getimagesize($pp_options['photospath'] . '/' . $pp_options['thumbprefix'] . $array_img['imgfile']);
		if (strlen($array_img['imgname']) > 18) {
			$cleanedname = substr(stripslashes($array_img['imgname']),0,15) . '...';
		} else {
			$cleanedname = stripslashes($array_img['imgname']);
		}
		$pp_content .= "<div class='pp_cell'><div class='pp_incell'><a href='" . $pp_options['albumaddress'] . $pp_options['cat_token'] . $array_img['catslug'] . $pp_options['images_token'] . $array_img['imgfile'] . "' title='" . attribute_escape(stripslashes($array_img['imgname'])) . "'><img src='" . $pp_options['photosaddress'] . "/" . $pp_options['thumbprefix'] . $array_img['imgfile'] . "' " . $thumbsize[3] . " alt='" . attribute_escape(stripslashes($array_img['imgname'])) . "' /><br />" . $cleanedname . "</a></div></div>\n";
	}
	$pp_content .= "</div>\n";
	return $pp_content;
	} else {
		$error = "<p>" . __("Category not found.","photopress") . "</p>\n";
		return $error;
	}
}

// Displays the list of photo categories.
function pp_display_main() {
   if (!empty($_GET['pp_page'])) {
      $current_page = $_GET['pp_page'];
   } elseif (get_query_var('pp_page') != '') {
      $current_page = get_query_var('pp_page');
   } else {
      $current_page = 1;
   }
	global $pp_options;
	$pp_content = '';
	$pp_count = pp_count();
	if ($pp_count > 0) {
		$cat_slugs = array();
		$cat_slugs = pp_catlist($pp_options['album_sort']);
		$cat_count = count($cat_slugs);
		$pages = (int)ceil($cat_count/$pp_options['images_per_page']);
		if ($pages > 1) {
			$pp_content .= "<p id='pp_page_links'>\n";
			if ($current_page > 1) {
				$pp_content .= "<a href='" . $pp_options['albumaddress'] . $pp_options['page_token'] . ($current_page - 1) . "'>";
			}
			$pp_content .= __('Previous','photopress');
			if ($current_page > 1) {
				$pp_content .= "</a>";
			}
			$pp_content .= ' | ';
			for ($i=1; $i<=$pages; $i++) {
				if ($i != $current_page) {
					$pp_content .= "<a href='" . $pp_options['albumaddress'] . $pp_options['page_token'] . $i . "'>";
				}
				$pp_content .= $i;
				if ($i != $current_page) {
					$pp_content .= "</a>";
				}
				if ($i < $pages) {
					$pp_content .= " | ";
				}
			}
			$pp_content .= " | ";
			if ($current_page < $pages) {
				$pp_content .= "<a href='" . $pp_options['albumaddress'] . $pp_options['page_token'] . ($current_page + 1) . "'>";
			}
			$pp_content .= __('Next','photopress');
			if ($current_page < $pages) {
				$pp_content .= "</a>";
			}
			$pp_content .= "</p>\n";
			$slice_start = ($current_page - 1) * $pp_options['images_per_page'];
			$cat_slugs = array_slice($cat_slugs,$slice_start, $pp_options['images_per_page']);
		}
		$pp_content .= "<div id='pp_wrap'>\n";
		foreach((array)$cat_slugs as $slug) {
			if ($slug['hidden'] != 'hide' && ($image_count = pp_count($slug['catslug']))) {
				$randimage = pp_random_image($slug['catslug'],2,'album');
				if (strlen($slug['category']) > 12) {
					$catname = substr(stripslashes($slug['category']),0,10) . '&hellip;';
				} else {
					$catname = stripslashes($slug['category']);
				}
				$pp_content .= "<div class='pp_cell'><div class='pp_incell'><a href='" . $pp_options['albumaddress'] . $pp_options['cat_token'] . $slug['catslug'] . $pp_options['end_token'] . "' title='" . attribute_escape(stripslashes($slug['category'])) . "'>" . $randimage[0] . "<br />" . $catname . " (" . $image_count . ")</a></div></div>\n";
			}
		}
		$pp_content .= "</div>\n";
	} else {
		$pp_content .= "<p>" . __('No photos found.','photopress') . "</p>\n</div>\n";
	}
	return $pp_content;
}

// Displays a single image in the album, linked to a popup with the image or to the original if it's there.
function pp_display_image($image) {
	global $pp_options;
	$pp_content = '';
	if (file_exists($pp_options['photospath'] . '/' . $image)) {
      if (isset($_POST['pp_album_update']) && current_user_can($pp_options['upload_cap'])) {
			$pp_updated_array = array('imgfile'=>$_POST['imgfile'],'imgname'=>$_POST['imgname'],'imgdesc'=>$_POST['imgdesc'],'category'=>$_POST['category']);
			if (pp_table_update($pp_updated_array)) {
				$pp_img_updated = '<p class="updated">' . __('Image data updated.','photopress') . '</p>';
			}
		}
		if ($image_data = pp_get_data($image)) {
			$image_cat = stripslashes($image_data['category']);
			$imgdesc = stripslashes($image_data['imgdesc']);
			$imgname = stripslashes($image_data['imgname']);
			if (!empty($image_data['catsort'])) {
				$sort = $image_data['catsort'];
			} else {
				$sort = 'imgname';
			}
		}
		$pp_content .= "<div id='pp_wrap'>\n<h3 id='pp_cat_heading'><a href='" . $pp_options['albumaddress'] . $pp_options['cat_token'] . $image_data['catslug'] . $pp_options['end_token'] . "'>" . $image_cat . "</a> : " . $imgname . "</h3>\n";
		$cat_array = pp_images_with_data($image_data['catslug'],$sort,0,999999);
		$image_size = @getimagesize($pp_options['photospath'] . '/' . $image);
		$itemcount = sizeof($cat_array);
		if ($itemcount > 1) {
			$prevnext = "<div id='pp_prevnext'>";
			for ($j = 0; $j < $itemcount; $j++) {
				$item = $cat_array[$j]['imgfile'];
				if ($image == $item) {
					if ($cat_array[$j-1]['imgfile']) {
						$prevnext .= "<div class='pp_prev'><a href='" . $pp_options['albumaddress'] . $pp_options['cat_token'] . $image_data['catslug'] . $pp_options['images_token'] . $cat_array[$j-1]['imgfile'] . "' title='" . attribute_escape(stripslashes($cat_array[$j-1]['imgname'])) . "'>" . __('Previous','photopress') . "</a></div>";
					} else {
						$prevnext .= "<div class='pp_prev'></div>";
					}
					if ($cat_array[$j+1]['imgfile']) {
						$prevnext .= "<div class='pp_next'><a href='" . $pp_options['albumaddress'] . $pp_options['cat_token'] . $image_data['catslug'] . $pp_options['images_token'] . $cat_array[$j+1]['imgfile'] . "' title='" . attribute_escape(stripslashes($cat_array[$j+1]['imgname'])) . "'>" . __('Next','photopress') . "</a></div>";
					} else {
						$prevnext .= "<div class='pp_next'></div>";
					}
				}
			}
			$prevnext .= "</div>\n";
			$pp_content .= $prevnext;
		}
		if (is_file($pp_options['photospath'] . '/' . $pp_options['origprefix'] . $image)) {
			$pop_size = @getimagesize($pp_options['photospath'] . '/' . $pp_options['origprefix'] . $image);
			$pop_file = $pp_options['origprefix'] . $image;
		} else {
			$pop_size = $image_size;
			$pop_file = $image;
		}
		if ($pp_options['album'] == 'lightbox') {
			$action = 'rel="lightbox"';
		} else {
			$action = 'onclick="pp_image_popup(\'' . $pp_options['photosaddress'] . '/' . $pop_file . '\',' . $pop_size[0] . ',' . $pop_size[1] . ',\'' . js_escape($imgname) . '\'); return false;"';
		}
		$pp_content .= '<div id="pp_lgphoto"><a href="' . $pp_options['photosaddress'] . '/' . $pop_file . '" ' . $action . ' title="' . attribute_escape(stripslashes($imgname)) . '"><img src="' . $pp_options['photosaddress'] . '/' . $image . '" ' . $image_size[3] . ' alt="' . attribute_escape(stripslashes($imgname)) . '" /></a></div>' . "\n";
		if (!empty($imgdesc)) {
			$pp_content .= "<p>" . stripslashes($imgdesc) . "</p>\n";
		}
		if ( $pp_options['show_posts'] == '1' ) {
			$my_query = new WP_Query("s=$image");
			if ( $my_query->have_posts() ) :
				$pp_content .= "<p><strong>" . __('Posts with this image','photopress') . ":</strong></p>\n<ul>";
			while ( $my_query->have_posts() ) : $my_query->the_post();
				$my_post = $my_query->post;
				$pp_content .= "<li><a href='" . get_permalink($my_post->ID) . "'>" . $my_post->post_title . "</a></li>\n";
			endwhile;
			$pp_content .= "</ul>\n";
			else:
			endif;
		}
		if (current_user_can($pp_options['upload_cap'])) {
			$pp_content .= '<p><a class="pptoggleedit" style="cursor:pointer">' . __('Edit image info','photopress') . ' &raquo;</a></p><div class="ppshowhideedit">' . $pp_img_updated . '<form name="pp_album_edit" method="post" action="">
			<input type="hidden" name="pp_album_update" value="update" />
			<input type="hidden" name="catslug" value="' . $image_data['catslug'] . '" />
			<input type="hidden" name="imgfile" value="' . $image_data['imgfile'] . '" />
			<p><strong>' . __('Name:','photopress') . '</strong><br /><input type="text" name="imgname" value="' . stripslashes($image_data['imgname']) . '" /></p>
			<p><strong>' . __('Category:','photopress') . '</strong><br /><input type="text" name="category" value="' . $image_cat . '" /></p>
			<p><strong>' . __('Description:','photopress') . '</strong><br /><textarea style="width: 100%" name="imgdesc" id="imgdesc" class="uploadform">' . stripslashes($image_data['imgdesc']) . '</textarea></p>
			<input type="submit" name="Submit" value="' . __('Save changes','photopress') . ' &raquo;" /></div>';
		}
	} else {
		$pp_content .= "<p>" . __("Image not found.","photopress") . "</p>\n";
	}
	$pp_content .= "</div>\n";
	return $pp_content;
}
?>
