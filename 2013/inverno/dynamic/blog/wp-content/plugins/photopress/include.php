<?php
/* Photopress shared include file */

$plugin_dir = basename(dirname(__FILE__));
load_plugin_textdomain( 'photopress', 'wp-content/plugins/' . $plugin_dir, $plugin_dir );

$pp_options = array();

if (get_option('pp_options')) {
	$pp_options = get_option('pp_options');
}

if ($pp_options['usepgid'] != '0' && $pp_options['usepgid'] != '1') {
   $pp_options['usepgid'] = '0';
}

if (empty($pp_options['album_sort'])) {
	$pp_options['album_sort'] = 'asc_cat';
}

if (empty($pp_options['photospath'])) {
	$pp_options['photospath'] = ABSPATH . 'wp-content/photos';
}

if (empty($pp_options['wpaddress'])) {
	$pp_options['wpaddress'] = get_settings('siteurl');
}

if (empty($pp_options['photosaddress'])) {
	$pp_options['photosaddress'] = $pp_options['wpaddress'] . '/wp-content/photos';
}

if ($pp_options['use_permalinks'] != '0' && $pp_options['use_permalinks'] != '1') {
	$pp_options['use_permalinks'] = '0';
}

if ($pp_options['insert_tags'] != '0' && $pp_options['insert_tags'] != '1') {
	$pp_options['insert_tags'] = '1';
}

if (empty($pp_options['album_token'])) {
	$pp_options['album_token'] = 'album';
}

if (empty($pp_options['album_name'])) {
	$pp_options['album_name'] = 'Photo Album';
}

if ($pp_options['originals'] != '0' && $pp_options['originals'] != '1') {
	$pp_options['originals'] = '0';
}

if (empty($pp_options['origprefix'])) {
	$pp_options['origprefix'] = 'orig_';
}

if (empty($pp_options['upload_cap'])) {
	$pp_options['upload_cap'] = 'publish_posts';
}

if (empty($pp_options['maintain_cap'])) {
	$pp_options['maintain_cap'] = 'publish_posts';
}

if (empty($pp_options['maxk'])) {
	$pp_options['maxk'] = '2048';
}

if (empty($pp_options['maxsize'])) {
	$pp_options['maxsize'] = '450';
}

if (empty($pp_options['thumbsize'])) {
	$pp_options['thumbsize'] = '100';
}

if ($pp_options['square'] != '0' && $pp_options['square'] != '1') {
	$pp_options['square'] = '1';
}

if (empty($pp_options['quality'])) {
	$pp_options['quality'] = '85';
}

if (empty($pp_options['allowedtypes'])) {
	$pp_options['allowedtypes'] = 'jpg jpeg gif png';
}

if ($pp_options['allow_post_image_delete'] != '0' && $pp_options['allow_post_image_delete'] != '1') {
	$pp_options['allow_post_image_delete'] = '0';
}

if ($pp_options['thumbs_in_mass_edit'] != '0' && $pp_options['thumbs_in_mass_edit'] != '1') {
	$pp_options['thumbs_in_mass_edit'] = '1';
}

if ($pp_options['show_posts'] != '0' && $pp_options['show_posts'] != '1') {
	$pp_options['show_posts'] = '1';
}

if ($pp_options['insert_thumb'] != '0' && $pp_options['insert_thumb'] != '1') {
	$pp_options['insert_thumb'] = '1';
}

if ($pp_options['album'] != '0' && $pp_options['album'] != '1' && $pp_options['album'] != 'lightbox') {
	$pp_options['album'] = '1';
}

if ($pp_options['meta_link'] != '0' && $pp_options['meta_link'] != '1') {
	$pp_options['meta_link'] = '1';
}

if ($pp_options['meta_rand'] != '0' && $pp_options['meta_rand'] != '1') {
	$pp_options['meta_rand'] = '1';
}

if (empty($pp_options['per_migrate'])) {
	$pp_options['per_migrate'] = '20';
}

if (empty($pp_options['images_per_page'])) {
	$pp_options['images_per_page'] = '20';
}

if (empty($pp_options['thumbprefix'])) {
	$pp_options['thumbprefix'] = 'thumb_';
}

if (empty($pp_options['image_class'])) {
	$pp_options['image_class'] = 'pp_image centered alignleft alignright';
}

if (empty($pp_options['rand_class'])) {
	$pp_options['rand_class'] = 'pp_image';
}

do_action('pp_defaults'); // adds defaults from plugins

// find out what sort of permalinks the blog uses
if ($pp_struct = get_settings('permalink_structure')) {
	if (strstr($pp_struct, 'index.php')) {
		$pp_options['wppermalinks'] = 'index';
	} else {
		$pp_options['wppermalinks'] = 'mod';
	}
} else {
	$pp_options['wppermalinks'] = 'none';
}

// set the addresses and tokens based on what we've learned
if ($pp_options['use_permalinks'] == '1' && $pp_options['wppermalinks'] != 'none') {
	if ($pp_options['wppermalinks'] == 'index') {
		$pp_options['albumaddress'] = trailingslashit($pp_options['wpaddress']) . 'index.php/' . $pp_options['album_token'];
	} else {
		$pp_options['albumaddress'] = trailingslashit($pp_options['wpaddress']) . $pp_options['album_token'];
	}
	$pp_options['cat_token'] = '/';
	$pp_options['images_token'] = '/';
	$pp_options['page_token'] = '/';
	$pp_options['end_token'] = '/';
} else {
	if ($pp_options['usepgid'] == '1') { $beforetoken = 'page_id'; } else { $beforetoken = 'pagename'; }
	$pp_options['albumaddress'] = trailingslashit($pp_options['wpaddress']) . '?' . $beforetoken . '=' . $pp_options['album_token'] . '&amp;?pp_album=main';
	$pp_options['cat_token'] = '&amp;pp_cat=';
	$pp_options['images_token'] = '&amp;pp_image=';
	$pp_options['page_token'] = '&amp;pp_page=';
	$pp_options['end_token'] = '';
}

// Update function to save an array of data to the category table. The array should must include the category and catslug, and may include catrep, catsort, and hidden.
function pp_cat_table_update($update_array) {
	global $table_prefix, $wpdb, $pp_options;
	$table_name = $table_prefix . "pp_cats";
	if (!empty($update_array['category']) && !empty($update_array['catslug'])) {
		$catslug = $wpdb->escape($update_array['catslug']);
		$existing = $wpdb->get_var("SELECT * FROM $table_name WHERE binary catslug='$catslug'");
		$category = $wpdb->escape($update_array['category']);
		if (!empty($update_array['catrep'])) {
			$catrep = $wpdb->escape($update_array['catrep']);
		} elseif (!empty($existing['catrep'])) {
			$catrep = $wpdb->escape($existing['catrep']);
		} else {
			$catrep = '';
		}
		if (!empty($update_array['catsort'])) {
			$catsort = $wpdb->escape($update_array['catsort']);
		} elseif (!empty($existing['catsort'])) {
			$catsort = $wpdb->escape($existing['catsort']);
		} else {
			$catsort = 'imgname';
		}
		$hidden = $wpdb->escape($update_array['hidden']);
		// if the category slug is in the table then update that row
		if ($existing) {
			return $wpdb->query("UPDATE $table_name SET catslug='$catslug', category='$category', catrep='$catrep', catsort='$catsort', hidden='$hidden' WHERE binary catslug='$catslug'");
		// if the category slug isn't in the table add a new row
		} else {
			return $wpdb->query("INSERT INTO $table_name SET catslug='$catslug', category='$category', catrep='$catrep', catsort='$catsort', hidden='$hidden'");
		}
	} else {
		return FALSE;
	}
}

// Shared update function to save an array of data to the main table. The array should include image filename (imgfile). Image name (imgname), description (imgdesc), category slug (catslug), and a serialized array of tags (imgtags) may also be included. File modification time (imgtime) is updated from the file. All fields are escaped before DB insertion.
function pp_table_update($update_array) {
	global $table_prefix, $wpdb, $pp_options;
	$table_name = $table_prefix . "photopress";
	$cat_table_name = $table_prefix . "pp_cats";
	if (!empty($update_array['imgfile'])) { // the rest can be empty or missing but this must be there
		$imgfile = $wpdb->escape($update_array['imgfile']);
		$imgname = $wpdb->escape($update_array['imgname']);
		$imgdesc = $wpdb->escape($update_array['imgdesc']);
		if (!empty($update_array['category'])) {
			$category = $wpdb->escape($update_array['category']);
			$slug = pp_slugify($update_array['category']);
		} else {
			$category = $wpdb->escape('Default');
			$slug = 'default';
		}
		if (!$wpdb->get_var("SELECT catslug FROM $cat_table_name WHERE binary catslug='$slug'")) {
			pp_cat_table_update(array('catslug'=>$slug,'category'=>$category,'catrep'=>'1','catsort'=>'imgname','hidden'=>'display'));
		}
		$imgtags = $wpdb->escape($update_array['imgtags']);
		$imghide = $wpdb->escape($update_array['imghide']);
		$imgtime = $wpdb->escape(filemtime(trailingslashit($pp_options['photospath']) . $update_array['imgfile']));
		// if the image is in the table update that row with the new data
		if ($wpdb->get_var("SELECT imgfile FROM $table_name WHERE binary imgfile='$imgfile'")) {
			return $wpdb->query("UPDATE $table_name SET imgname='$imgname', imgdesc='$imgdesc', catslug='$slug', imgtags='$imgtags', imghide='$imghide', imgtime='$imgtime' WHERE binary imgfile='$imgfile'");
		// if the image isn't in the table add a new row
		} else {
			return $wpdb->query("INSERT INTO $table_name SET imgfile='$imgfile', imgname='$imgname', imgdesc='$imgdesc', catslug='$slug', imgtags='$imgtags', imghide='$imghide', imgtime='$imgtime'");
		}
	} else {
		return FALSE;
	}
}

// Gets an array of data for a single image from the database.
function pp_get_data($image) {
	global $table_prefix, $wpdb, $pp_options;
	$table_name = $table_prefix . 'photopress';
	$cat_table_name = $table_prefix . 'pp_cats';
	if ($results = $wpdb->get_row("SELECT * FROM $table_name LEFT JOIN $cat_table_name ON ($table_name.catslug=$cat_table_name.catslug) WHERE binary imgfile='$image'", ARRAY_A)) {
		return $results;
	} else {
		return FALSE;
	}
}

// Gets $slug's row from the category table.
function pp_sluginfo($slug) {
	global $table_prefix, $wpdb, $pp_options;
	$cat_table_name = $table_prefix . 'pp_cats';
	$slug_for_query = $wpdb->escape($slug);
	if ($results = $wpdb->get_row("SELECT * FROM $cat_table_name WHERE binary catslug='$slug_for_query'", ARRAY_A)) {
		return $results;
	} else {
		return FALSE;
	}
}

// Counts images in $slug, or all images otherwise.
function pp_count($slug=FALSE) {
	global $table_prefix, $wpdb;
	$table_name = $table_prefix . 'photopress';
	if (empty($slug)) {
		if ($count = $wpdb->get_var("SELECT count(*) FROM $table_name")) {
			return $count;
		} else {
			return FALSE;
		}
	} else {
		$slug_for_query = $wpdb->escape($slug);
		$count = $wpdb->get_var("SELECT count(*) FROM $table_name WHERE binary catslug='$slug_for_query'");
		if ($count > 0) {
			return $count;
		} else {
			return FALSE;
		}
	}
}

// Gets category table as an associative array.
function pp_catlist($sort = "asc_cat") {
	global $table_prefix, $wpdb;
	$cat_table_name = $table_prefix . 'pp_cats';
	$pp_table_name = $table_prefix . 'photopress';
	if ($sort == "asc_id") {
      $sort_sql = $cat_table_name . ".id";
   } elseif ($sort == "desc_id") {
      $sort_sql = $cat_table_name . ".id DESC";
   } elseif ($sort == "desc_cat") {
      $sort_sql = $cat_table_name . ".category DESC";
   } else {
      $sort_sql = $cat_table_name . ".category";
   }
	if ($results = $wpdb->get_results("SELECT DISTINCT $cat_table_name.* FROM $cat_table_name INNER JOIN $pp_table_name ON $cat_table_name.catslug = $pp_table_name.catslug ORDER BY $sort_sql",ARRAY_A)) {
		return $results;
	} else {
		return FALSE;
	}
}

// Just gets an array of category names.
function pp_list_cats() {
	if ($cat_array = pp_catlist()) {
		$results = array();
		foreach ((array)$cat_array as $cat_element) {
			$results[] = $cat_element['category'];
		}
		return $results;
	} else {
		return FALSE;
	}
}

// Receives a category name, returns a slugified category.
function pp_slugify($cat) {
	if ($slug = sanitize_title_with_dashes($cat)) {
		if (ctype_digit($slug)) {
			$slug = '_' . $slug;
		}	
		return $slug;
	} else {
		return FALSE;
	}
}

// Gets an array of $rows image arrays in $slug (if given, from whole table otherwise), sorted by $sort, starting with $offset.
function pp_images_with_data($slug=FALSE,$sort='imgname',$offset=0,$rows=50) {
	global $table_prefix, $wpdb;
	$table_name = $table_prefix . "photopress";
	$cat_table_name = $table_prefix . "pp_cats";
	if (empty($sort)) { $sort = 'imgname'; }
	if (!empty($slug)) {
		$slugforquery = $wpdb->escape($slug);
		if ($results = $wpdb->get_results("SELECT * FROM $table_name LEFT JOIN $cat_table_name ON ($table_name.catslug=$cat_table_name.catslug) WHERE binary $table_name.catslug='$slugforquery' ORDER BY $sort LIMIT $offset,$rows",ARRAY_A)) {
			return $results;
		} else {
			return FALSE;
		}
	} else {
		if ($results = $wpdb->get_results("SELECT * FROM $table_name LEFT JOIN $cat_table_name ON ($table_name.catslug=$cat_table_name.catslug) ORDER BY $sort LIMIT $offset,$rows", ARRAY_A)) {
			return $results;
		} else {
			return FALSE;
		}
	}
}

// Returns an array of image names within $pp_options['photospath'], ignoring thumbs and originals. Also ignores allowed types because it's too late for that I think. Doesn't check whether the images have data in the db, so we can use this to import photos.
function pp_folder_contents() {
	global $pp_options;
	$handle = opendir($pp_options['photospath']);
	$list_array = array();
	while (false !== ($folder_contents = readdir($handle))) {
		if ($folder_contents != '.' && $folder_contents != '..' && !preg_match('/^' . $pp_options['thumbprefix'] . '|^' . $pp_options['origprefix'] . '/',$folder_contents)) { // get rid of '.', '..', thumbs, and originals
			$list_array[] = $folder_contents;
		}
	}
	@closedir($handle);
	if (count($list_array) > 1) {
		sort($list_array);
	}
	if (count($list_array) > 0) {
		return $list_array;
	} else {
		return FALSE;
	}
}

// Make a list of html option tags for a category select list (the part between the select tags), adding Default and setting it as selected if no selected cat is provided (or if the one provided has no images).
function pp_cat_dropdown($pp_cats,$selected='Default') {
	$pp_cats[] = 'Default';
	$pp_cats = array_unique($pp_cats);
	$code = '';
	if (count($pp_cats) > 1) {
		$cats_lowercase = array_map('strtolower', $pp_cats);
		array_multisort($cats_lowercase, SORT_ASC, SORT_STRING, $pp_cats);
	}
	foreach ($pp_cats as $category) {
		$code .= '<option value="' . stripslashes($category) . '"';
		if ($selected == stripslashes($category)) { $code .= ' selected="selected"'; }
		$code .= '>' . stripslashes($category) . '</option>';
	}
	return $code;
}

// Resizing function, blatently stolen from the built-in WP uploader, circa 1.5.
function pp_resize($file, $maxsize, $minsize, $prefix = '', $isthumb) {
	global $pp_options;
	// 1 = GIF, 2 = JPEG, 3 = PNG
	if (file_exists($file)) {
		$type = getimagesize($file);
		// if the associated function doesn't exist - then it's not
		// handle. duh. i hope.
		if (!function_exists('imagegif') && $type[2] == 1) {
			$error = __('Filetype not supported. Image not resized.','photopress');
		} elseif(!function_exists('imagejpeg') && $type[2] == 2) {
			$error = __('Filetype not supported. Image not resized.','photopress');
		} elseif(!function_exists('imagepng') && $type[2] == 3) {
			$error = __('Filetype not supported. Image not resized.','photopress');
		} else {
			// create the initial copy from the original file
			if ($type[2] == 1) {
				$image = imagecreatefromgif($file);
			} elseif($type[2] == 2) {
				$image = imagecreatefromjpeg($file);
			} elseif($type[2] == 3) {
				$image = imagecreatefrompng($file);
			}
			// anti-upsize fix contributed by Jono (jono@redcliffs.net)
			// set the current image width and heights
			$image_width = $type[0];
			$image_height = $type[1];
			// if image is larger than defined max size
			if($image_width >= $maxsize || $image_height >= $maxsize || $image_width <= $minsize || $image_height <= $minsize) {
				// figure out the longest side            
				if($image_width > $image_height) {
					$off_w = (int)($image_width - $image_height) / 2;
					$off_h = 0;
					$sq_sz = $image_height;
					$image_new_width = $maxsize;
					$image_ratio = $image_width/$image_new_width;
					$image_new_height = $image_height/$image_ratio;
				} elseif ($image_height > $image_width) {
					$off_w = 0;
					$off_h = (int)($image_height - $image_width) / 2;
					$sq_sz = $image_width;
					$image_new_height = $maxsize;
					$image_ratio = $image_height/$image_new_height;
					$image_new_width = $image_width/$image_ratio;
				} else { // square image
					$image_new_height = $maxsize;
					$image_new_width = $maxsize;
					$off_w = 0;
					$off_h = 0;
					$sq_sz = $image_width;
				}
				if ($isthumb == 1 && $pp_options['square'] == '1') {
					$resized = imagecreatetruecolor($maxsize, $maxsize);
					imagecopyresampled($resized, $image, 0, 0, $off_w, $off_h, $maxsize, $maxsize, $sq_sz, $sq_sz);
				} else {
					$resized = imagecreatetruecolor($image_new_width, $image_new_height);
					imagecopyresampled($resized, $image, 0, 0, 0, 0, $image_new_width, $image_new_height, $type[0], $type[1]);
				}
				// move the thumbnail to it's final destination
				$path = explode('/', $file);
				$resizedpath = substr($file, 0, strrpos($file, '/')) . '/' . $prefix . $path[count($path)-1];
				if($type[2] == 1) {
					if(!imagegif($resized, $resizedpath)) {
						$error = __('Photo path invalid','photopress');
					}
				} elseif($type[2] == 2) {
					if(!imagejpeg($resized, $resizedpath, $pp_options['quality'])) {
						$error = __('Photo path invalid','photopress');
					}
				} elseif($type[2] == 3) {
					if(!imagepng($resized, $resizedpath)) {
						$error = __('Photo path invalid','photopress');
					}
				}
			} else { // image is smaller than max size, do nothing, or copy if thumb
				if ($isthumb == 1) {
					$path = explode('/', $file);
					$resizedpath = substr($file, 0, strrpos($file, '/')) . '/' . $prefix . $path[count($path)-1];
					if (!copy($file,$resizedpath)) {
						$error = __('Failed to copy small image to thumbnail.','photopress');
					}
				}
			}
		}
	}
	@imagedestroy($image);
	@imagedestroy($resized);
	if(!empty($error)) {
		return $error;
	} else {
		return 1;
	}
}

?>
