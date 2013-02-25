<?php
/*
Photopress popup upload/browse tool.
Original hack by shockingbird.com for b2.
Browse portion based on the Image Browser plugin (www.bistr-o-mathik.org/code/wordpress-plugins).
*/
require_once( dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php');
require_once( ABSPATH . 'wp-content/plugins/photopress/include.php');
if ($user_ID == '') //Checks to see if user has logged in
	die (__('Try logging in','photopress'));
if (!current_user_can($pp_options['upload_cap']))
	die (__('Ask the administrator to promote you.'));

// print the header (there's probably a better way to do this)
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>' . __('Photopress: upload, browse, and insert images','photopress') . '</title>
<link rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-admin/css/global.css" type="text/css" />
<link rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-admin/wp-admin.css" type="text/css" />
<link rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-admin/css/colors-fresh.css" type="text/css" />
<link rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/photopress/popup.css" type="text/css" />
<link rel="shortcut icon" href="' . get_bloginfo('wpurl') . '/wp-images/wp-favicon.png" />
<meta http-equiv="Content-Type" content="text/html; charset=' . get_settings('blog_charset') . '" />
<style type="text/css">
.pp_insert_button {
	font-weight: bold;
}
.pp_browse_table {
	width: 100%;
	text-align: right;
}
#insertimages {
	display: block;
}
#uploadcomplete {
	display: none;
}
</style>
<script type="text/javascript">
//<![CDATA[
window.focus();
window.onload = function() {
	if (typeof window.opener.document.post == "undefined") {
		var bbutton = document.getElementById("popupmenu").getElementsByTagName("li")[1];
		bbutton.style.display = "none";
		if (document.getElementById("insert_button")) {
			var insertbutton = document.getElementById("insertbutton");
			insertbutton.style.display = "none";
		}
		if (document.getElementById("insertimages")) {
			var insertimages = document.getElementById("insertimages");
			insertimages.style.display = "none";
		}
		if (document.getElementById("uploadcomplete")) {
			var uploadcomplete = document.getElementById("uploadcomplete");
			uploadcomplete.style.display = "block";
		}
		if (document.getElementById("hideuploads")) {
			var hideuploads = document.getElementById("hideuploads");
			hideuploads.style.display = "none";
		}
	}
}

function insertcode(imgfile, imgsize, classname, imgname, catslug, width, height, twidth, theight, orig, origwidth, origheight) {
	var usealbum = "' . $pp_options['album'] . '";
	var inserttags = "' . $pp_options['insert_tags'] . '";
	var thumbprefix = "' . $pp_options['thumbprefix'] . '";
	if (imgsize == "thumb") {
		var thumbcode = thumbprefix;
		var imgwidth = twidth;
		var imgheight = theight;
	} else {
		var thumbcode = "";
		var imgwidth = width;
		var imgheight = height;
	}
	if (inserttags == "1") {
		var linkcode = "[photopress:" + imgfile + "," + imgsize + "," + classname + "]";
	} else {
		if (usealbum != "1") {
			if (orig == "1") {
				if (usealbum == "0") {
					var action = "onclick=\"pp_image_popup(\'' . $pp_options['photosaddress'] . '/' . $pp_options['origprefix'] . '" + imgfile + "\'," + origwidth + "," + origheight + "\); return false;\"";
				} else {
					var action = "rel=\"lightbox\"";
				}
				var linkcode = "<a href=\"' . $pp_options['photosaddress'] . '/' . $pp_options['origprefix'] . '" + imgfile + "\" " + action + " title=\"" + imgname + "\"><img src=\"' . $pp_options['photosaddress'] . '/" + thumbcode + imgfile + "\" width=\"" + imgwidth + "\" height=\"" + imgheight + "\" alt=\"" + imgname + "\" class=\"" + classname + "\" /></a>";
			} else {
				if (usealbum == "0") {
					var action = "onclick=\"pp_image_popup(\'' . $pp_options['photosaddress'] . '/' . '" + imgfile + "\'," + width + "," + height + "\); return false;\"";
				} else {
					var action = "rel=\"lightbox\"";
				}
				var linkcode = "<a href=\"' . $pp_options['photosaddress'] . '/" + imgfile + "\" " + action + " title=\"" + imgname + "\"><img src=\"' . $pp_options['photosaddress'] . '/" + thumbcode + imgfile + "\" width=\"" + imgwidth + "\" height=\"" + imgheight + "\" alt=\"" + imgname + "\" class=\"" + classname + "\" /></a>";
			}
		} else {
			var linkcode = "<a href=\"' . $pp_options['albumaddress'] . $pp_options['cat_token'] . '" + catslug + "' . $pp_options['images_token'] . '" + imgfile + "\" title=\"" + imgname + "\"><img src=\"' . $pp_options['photosaddress'] . '/" + thumbcode + imgfile + "\" alt=\"" + imgname + "\" width=\"" + imgwidth + "\" height=\"" + imgheight + "\" class=\"" + classname + "\" /></a>";
		}
	}
	var winder = window.top;
	if ( typeof winder.tinyMCE !== "undefined" && ( winder.ed = winder.tinyMCE.activeEditor ) && !winder.ed.isHidden() ) {
		winder.ed.focus();
		if (winder.tinymce.isIE)
			winder.ed.selection.moveToBookmark(winder.tinymce.EditorManager.activeEditor.windowManager.bookmark);
		winder.ed.execCommand("mceInsertContent", false, linkcode);
	} else {
		winder.edInsertContent(winder.edCanvas, linkcode);
	}
	return;
}
function setnumphotos(num) {
	window.location.href = "' . get_bloginfo('wpurl') . '/wp-content/plugins/photopress/popup.php?action=upload&num_photos=" + num.options[num.selectedIndex].value;
	return false;
}
function setbrowseoptions(stuff) {
	window.location.href = "' . get_bloginfo('wpurl') . '/wp-content/plugins/photopress/popup.php?action=browse" + stuff.options[stuff.selectedIndex].value;
	return false;
}
//]]>
</script>
</head>
<body>
<ul id="popupmenu">
	<li><a '; if ($_GET['action'] == 'upload' || $_POST['submit']) { echo 'class="current" ';} echo 'href="' . get_bloginfo('wpurl') . '/wp-content/plugins/photopress/popup.php?action=upload">' . __('Upload','photopress') . '</a></li>
	<li><a ';  if ($_GET['action'] == 'browse') { echo 'class="current" ';} echo 'href="' . get_bloginfo('wpurl') . '/wp-content/plugins/photopress/popup.php?action=browse">' . __('Browse','photopress') . '</a></li>
	<!--<li><a href="#" onclick="window.close()">' . __('Close window','photopress') . '</a></li>-->
</ul>
<div class="wrap">
';

if ($_GET['action'] == 'browse') {
	$image_count = pp_count();
	if ($image_count > 0) {
		if ($image_count > 1) {
			$sort_array = array('imgtimeD'=>'New to Old','imgtimeA'=>'Old to New','imgfileA'=>'A to Z','imgfileD'=>'Z to A','imgcatA'=>'A-Z by Category','imgcatD'=>'Z-A by Category');
			echo '<p><form method="post" action="#">' . __('Sort: ','photopress') . '<select name="sort" onChange="setbrowseoptions(this)">';
			foreach ($sort_array as $sortval=>$sortdesc) {
				echo '<option value="&amp;sort=' . $sortval; if (isset($_GET['cat'])) { echo '&amp;cat=' . $_GET['cat']; } echo '"'; if ($_GET['sort'] == $sortval) { echo ' selected="selected"'; } echo '>' . $sortdesc . "</option>\n";
			}
			echo "</select><br />\n" . __('or show Category: ','photopress') . '<select name="category" onChange="setbrowseoptions(this)">
			<option value="'; if (isset($_GET['sort'])) { echo '&amp;sort=' . $_GET['sort']; } echo '"';
			$pp_slugs = pp_catlist();
			if ($_GET['cat'] == 'pp_browse_all_cats' || !isset($_GET['cat'])) {
				echo ' selected="selected"';
			} else {
				$current_cat = $_GET['cat'];
				$image_count = pp_count($_GET['cat']);
			}
			echo '>' . __('Show All','photopress') . '</option>
			';
			foreach ((array)$pp_slugs as $slug) {
				echo '<option value="&amp;cat=' . $slug['catslug']; if (isset($_GET['sort'])) { echo '&amp;sort=' . $_GET['sort']; } echo '"';
				if ($_GET['cat'] == $slug['catslug']) { echo ' selected="selected"'; }
				echo '>' . stripslashes($slug['category']) . '</option>
				';
			}
			echo "</select></form></p>\n";
		}
		if ($_GET['sort'] == 'imgfileD') {
			$pp_sort = 'imgfile DESC';
		} elseif ($_GET['sort'] == 'imgfileA') {
			$pp_sort = 'imgfile';
		} elseif ($_GET['sort'] == 'imgtimeA') {
			$pp_sort = 'imgtime';
		} elseif ($_GET['sort'] == 'imgcatD') {
			$pp_sort = 'imgcat DESC';
		} elseif ($_GET['sort'] == 'imgcatA') {
			$pp_sort = 'imgcat';
		} else {
			$pp_sort = 'imgtime DESC';
		}
		if ($image_count > 0) {
			$pages = (int)ceil($image_count/$pp_options['images_per_page']);
			if ($pages > 1) {
				if (isset($_GET['page'])) {
					$current_page = $_GET['page'];
				} else {
					$current_page = 1;
				}
				echo "<p><form method='post' action='#'>" . __('Page: ','photopress') . "<select name='page' onChange='setbrowseoptions(this)'>\n";
				for ($i = 1; $i <= $pages; $i++) {
						echo '<option value="&amp;page=' . $i; if (isset($_GET['sort'])) { echo '&amp;sort=' . $_GET['sort']; } if (isset($_GET['cat'])) { echo '&amp;cat=' . $_GET['cat']; } echo '"'; if ($current_page == $i) { echo ' selected="selected"'; } echo '>' . $i . "</option>\n";
				}
				$list_start = ($current_page - 1) * $pp_options['images_per_page'];
				echo "</select></form></p>\n";
			} else { // there's only one page so we'll start with the 0th image
				$list_start = 0;
			}
			echo "</div>\n";
			if (isset($_GET['cat']) && $_GET['cat'] != 'pp_browse_all_cats') {
				$current_image_list = pp_images_with_data($_GET['cat'],$pp_sort,$list_start,$pp_options['images_per_page']);
			} else {
				$current_image_list = pp_images_with_data(FALSE,$pp_sort,$list_start,$pp_options['images_per_page']);
			}
			foreach ((array)$current_image_list as $image) {
				$thumbsize = getimagesize($pp_options['photospath'] . '/' . $pp_options['thumbprefix'] . $image['imgfile']);
				$imgsize = getimagesize($pp_options['photospath'] . '/' . $image['imgfile']);
				echo '<div class="wrap"><table class="pp_browse_table"><tr><td><img src="' . $pp_options['photosaddress'] . '/' . $pp_options['thumbprefix'] . $image['imgfile'] . '" ' . $thumbsize[3] . ' title="' . attribute_escape($image['imgname']) . '" alt="' . attribute_escape($image['imgname']) . '" /></td>
				<td><p><form>
				<input type="hidden" name="file" value="' . $image['imgfile'] . '" />
				' . __('Size:','photopress') . '<select name="thumb">
					<option value="thumb"'; if ($pp_options['insert_thumb'] == '1') { echo ' selected="selected"'; } echo '>' . __('Thumbnail','photopress') . '</option>
					<option value="full"'; if ($pp_options['insert_thumb'] == '0') { echo ' selected="selected"'; } echo '>' . __('Full Image','photopress') . '</option>
				</select><br />
            ' . __('Style:','photopress') . '<select name="classes">';
				$classes = explode(' ',trim($pp_options['image_class']));
				foreach ((array)$classes as $class) {
					echo '<option value="' . $class . '"'; if ($class == $classes[0]) { echo ' selected="selected"'; } echo '>' . $class . '</option>
					';
				}
				if (strlen($image['imgfile']) > 20) {
					$image_file_short = substr($image['imgfile'],0,17) . '...';
				} else {
					$image_file_short = $image['imgfile'];
				}
				$insert_text = __('Insert','photopress') . " " . $image_file_short;
				$inserted_text = __('Inserted','photopress') . " " . $image_file_short;
				if (is_file($pp_options['photospath'] . '/' . $pp_options['origprefix'] . $image['imgfile'])) {
					$orig = '1';
					$origsize = getimagesize($pp_options['photospath'] . '/' . $pp_options['origprefix'] . $image['imgfile']);
					$origstring = ',' . $orig . ',' . $origsize[0] . ',' . $origsize[1];
				} else {
					$origstring = '';
				}
				echo '</select></p>
				<p class="submit"><input class="pp_insert_button" type="button" name="insert" value="' . $insert_text . '" onClick="insertcode(\'' . $image['imgfile'] . '\',this.form.thumb.options[this.form.thumb.selectedIndex].value,this.form.classes.options[this.form.classes.selectedIndex].value,\'' . js_escape(stripslashes($image['imgname'])) . '\',\'' . $image['catslug'] . '\',' . $imgsize[0] . ',' . $imgsize[1] . ',' . $thumbsize[0] . ',' . $thumbsize[1] . $origstring . '); this.form.insert.value=\'' . $inserted_text . '\'; return false;" /></p>
				</form></td></tr></table></div>
				';
			}
		}
	} else {
		echo '<p>' . __('No photos have been uploaded yet.','photopress') . '</p></div>';
	}
	echo '
</body>
</html>';
die();
}

if ($_GET['action'] == 'upload') {
	if (!is_writable($pp_options['photospath'])) {
		echo '<h3>' . __('The photos folder is not writable','photopress') . '</h3>
		<p>';
		printf(__("It doesn't look like you can use Photopress at this time because the directory you have specified (<code>%s</code>) isn't writable. Check the permissions on the directory and for typos in your configuration.","photopress"), $pp_options['photospath']);
		echo '</p>
	</div>
</body>
</html>';
		die();
	}
	if (!extension_loaded('gd')) {
		$gdprefix = (PHP_SHLIB_SUFFIX === 'dll') ? 'php_' : '';
		if (!dl($prefix . 'gd.' . PHP_SHLIB_SUFFIX)) {
			echo '<h3>' . __('The GD module is not installed','photopress') . '</h3>
			<p>' . __('It appears that you do not have the GD module for PHP installed. This module is required in order to resize images.','photopress') . '</p>
	</div>
</body>
</html>';
			die();
		}
	}
	if (isset($_GET['num_photos'])) {
		$num_photos = $_GET['num_photos'];
	} else {
		$num_photos = 1;
	}
	echo '<p>' . __('Number to upload:','photopress') . '<select name="num_photos" onChange="setnumphotos(this)">';
		for ($i=1;$i<=10;$i++) {
			echo '<option value="' . $i . '" ';
				if ((int)$num_photos == $i) {
					echo 'selected="selected"';
				}
			echo '>' . $i . '</option>
			';
		}
		echo '</select></p>
		<form action="' . get_bloginfo('wpurl') . '/wp-content/plugins/photopress/popup.php" method="post" enctype="multipart/form-data">
		<input type="hidden" name="MAX_FILE_SIZE" value="' . 1024*$pp_options['maxk'] . '" />
		<input type="hidden" name="upload" id="upload" value="yes" />';
		for ($i=0; $i<$num_photos; $i++) {
			echo '<p><input type="file" name="file_name[]" id="file_name" class="uploadform" /></p>
			';
		}
		echo '<p class="submit"><input type="submit" name="submit" value="' . __('Upload &raquo;','photopress') . '" /></p>
		</form>
	</div>
</body>
</html>';
die();
}

if ($_POST['submit']) {
	if ($_POST['upload']) {
		echo '<h3>' . __('Uploading Images','photopress') . '</h3>
		<p>' . __('Your uploads have been attempted. Problems with each image are reported below. You can enter some information about your images now or just save the defaults and move on to the next screen, where you can insert images into your post.','photopress') . '</p>
		</div>
		<form action="' . get_bloginfo('wpurl') . '/wp-content/plugins/photopress/popup.php" method="post" enctype="multipart/form-data">
		<input type="hidden" name="data_update" value="yes" />
		';
		$allowedtypes = explode(' ', trim(strtolower($pp_options['allowedtypes'])));
		$pp_categories = pp_list_cats();
		$j=0;
		if (is_array($_FILES['file_name']['error'])) {
			foreach ($_FILES['file_name']['error'] as $key=>$error) {
				if ($error == UPLOAD_ERR_OK) {
					$image_ext = trim(strtolower(pathinfo($_FILES['file_name']['name'][$key],PATHINFO_EXTENSION)));
					if (function_exists('exif_imagetype')) {
						$image_type = exif_imagetype($_FILES['file_name']['tmp_name'][$key]);
					} else {
						$image_type = getimagesize($_FILES['file_name']['tmp_name'][$key]);
					}
					if (in_array($image_ext,$allowedtypes) && FALSE != $image_type) { // the extension is okay and the file is an image
						$image_name = pathinfo($_FILES['file_name']['name'][$key],PATHINFO_BASENAME);
						$base_name = substr($image_name,0,strrpos($image_name, '.'));
						$clean_base = preg_replace('/[^a-z0-9_]/i', '_', $base_name);
						$extension = pathinfo($_FILES['file_name']['name'][$key],PATHINFO_EXTENSION);
						$destination = $pp_options['photospath'] . '/' . $clean_base . '.' . $extension;
						$i=1;
						while (is_file($destination)) { // if it's a dupe give it a new name
							$destination = $pp_options['photospath'] . '/' . $clean_base . '_' . $i . '.' . $extension;
							$i++;
						}
						@move_uploaded_file($_FILES['file_name']['tmp_name'][$key],$destination);
						do_action('pp_upload'); // add plugin stuff to do to uploads - your plugin should probably do stuff to $destination, the path to the file
						if ($pp_options['originals'] == '1') {
							$origdest = $pp_options['photospath'] . '/' . $pp_options['origprefix'] . pathinfo($destination,PATHINFO_BASENAME);
							@copy($destination, $origdest);
							@chmod($origdest, 0664);
						}
						$resized = pp_resize($destination, $pp_options['maxsize'], 0, '', 0);
						if ($resized != 1) { // if resize fails we'll delete the upload and display an error
							@unlink($destination);
							echo '<div class="wrap">';
							echo '<p>' . $resized . '</p><p>';
							printf(__('Error uploading <strong>%s</strong>. Try resizing the image and upload it again.','photopress'),$_FILES['file_name']['name'][$key]);
							echo '</p></div>';
						} else {
							@chmod($destination,0664);
							$thumbed = pp_resize($destination, $pp_options['thumbsize'], $pp_options['thumbsize'], $pp_options['thumbprefix'], 1);
							$imgfile = pathinfo($destination,PATHINFO_BASENAME);
							@chmod($pp_options['photospath'] . '/' . $pp_options['thumbprefix'] . $imgfile,0664);
							$imgname = strtr(substr($imgfile,0,strrpos($imgfile, '.')),'_',' ');
							$update_array = array('imgfile'=>$imgfile,'imgname'=>$imgname,'imgdesc'=>$imgname,'category'=>'Default');
							$updated = pp_table_update($update_array);
							$thumbsize = getimagesize($pp_options['photospath'] . '/' . $pp_options['thumbprefix'] . $imgfile);
							if (strlen($imgfile) > 30) {
								$image_file_short = substr($imgfile,0,27) . '...';
							} else {
								$image_file_short = $imgfile;
							}
							echo '<div class="wrap"><input type="hidden" name="row' . $j . '[imgfile]" value="' . $imgfile . '" /><p><img src="' . $pp_options['photosaddress'] . '/' . $pp_options['thumbprefix'] . $imgfile . '" ' . $thumbsize[3] . ' alt="' . attribute_escape($imgname) . '" title="' . attribute_escape($imgname) . '" /><br />' . __('File: ','photopress') . $image_file_short . '<br />' . __('Name: ','photopress') . '<input type="text" name="row' . $j . '[imgname]" class="uploadform" size="15" value="' . attribute_escape($imgname) . '" /><br />' . __('Description: ','photopress') . '<input type="text" name="row' . $j . '[imgdesc]" class="uploadform" size="15" value="' . attribute_escape($imgname) . '" /><br />' . __('Category: ','photopress') . '<input type="text" name="row' . $j . '[imgcat]" class="uploadform" size="15" value="" /><br />';
							echo '<select name="row' . $j . '[imgcatdrop]">';
							echo pp_cat_dropdown($pp_categories);
							echo "</select>\n</p></div>\n";
							$j++;
						}
					} else {
						echo '<div class="wrap">';
						printf(__('Error uploading <strong>%1$s</strong>. The file is not one of the allowed types (%2$s). Check the file and try again.','photopress'),$_FILES['file_name']['name'][$key],$pp_options['allowedtypes']);
						echo "</div>\n";
					}
				} else {
					echo '<div class="wrap">';
					if ($error == UPLOAD_ERR_INI_SIZE) {
						echo __('There was an error during upload. Your image may be larger than PHP allows.','photopress');
					} elseif ($error == UPLOAD_ERR_FORM_SIZE) {
						printf(__('There was an error uploading <strong>%1$s</strong>, most likely because it is larger than <strong>%2$s kilobytes</strong>.','photopress'),$_FILES['file_name']['name'][$key],$pp_options['maxk']);
					} elseif ($error == UPLOAD_ERR_PARTIAL) {
						printf(__('There was an error uploading <strong>%s</strong>. The upload may have been interrupted.','photopress'),$_FILES['file_name']['name'][$key]);
					} elseif ($error == UPLOAD_ERR_NO_FILE) {
						echo __('There was an error during upload. You may have left the form empty.','photopress');
					} elseif ($error == UPLOAD_ERR_NO_TMP_DIR) {
						echo __('There was an error during upload. The temp directory on your server was not found.','photopress');
					} elseif ($error == UPLOAD_ERR_CANT_WRITE) {
						echo  __('There was an error during upload. The temp directory on your server is not writable.','photopress');
					} else {
						echo  __('There was an error during upload.','photopress');
					}
					echo '</div>';
				}
			}
		} else {
			echo '<div class="wrap">' . __('There was an error during upload. Your image may be larger than PHP allows.','photopress') . '</div>';
		}
		if ($j > 0) { // suppress the next button if no images were uploaded successfully
			echo '<div class="wrap"><p class="submit"><input type="submit" name="submit" value="' . __('Next','photopress') . '" /></p></div>';
		}
		echo '
		</form>
</body>
</html>';
die();
	} elseif (isset($_POST['data_update'])) {
		echo '<div id="insertimages"><h3>' . __('Insert Images','photopress') . '</h3>
		<p>' . __('Your data has been saved. You can now insert the images into your post by clicking the buttons below. Change how the images will appear in your post using the drop-down menus.','photopress') . '</p></div>
		<div id="uploadcomplete"><h3>' . __('Uploads Complete','photopress') . '</h3>
		<p>' . __('Your data has been saved. You can now upload more images or close this window.','photopress') . '</p></div>
		</div>
		<div id="hideuploads">';
		foreach ((array)$_POST as $key=>$data_update) {
			if (is_array($data_update)) {
				if (!empty($data_update['imgcat'])) {
					$cat_update = $data_update['imgcat'];
				} else {
					$cat_update = $data_update['imgcatdrop'];
				}
				$update_array = array('imgfile'=>$data_update['imgfile'],'imgname'=>$data_update['imgname'],'imgdesc'=>$data_update['imgdesc'],'category'=>$cat_update);
				pp_table_update($update_array);
				$updated_data = pp_get_data($data_update['imgfile']);
				$thumbsize = getimagesize($pp_options['photospath'] . '/' . $pp_options['thumbprefix'] . $data_update['imgfile']);
				echo '<div class="wrap"><form><table class="pp_browse_table"><tr><td><img src="' . $pp_options['photosaddress'] . '/' . $pp_options['thumbprefix'] . $data_update['imgfile'] . '" ' . $thumbsize[3] . ' title="' . attribute_escape($updated_data['imgname']) . '" alt="' . attribute_escape($updated_data['imgname']) . '" /></td>
				<td>
				<input type="hidden" name="file" value="' . $data_update['imgfile'] . '" />
				' . __('Size','photopress') . '<select name="thumb">
					<option value="thumb"'; if ($pp_options['insert_thumb'] == '1') { echo ' selected="selected"'; } echo '>' . __('Thumbnail','photopress') . '</option>
					<option value="full"'; if ($pp_options['insert_thumb'] == '0') { echo ' selected="selected"'; } echo '>' . __('Full Image','photopress') . '</option>
				</select><br />
				' . __('Style:','photopress') . '<select name="classes">';
				$classes = explode(' ',trim($pp_options['image_class']));
				foreach ((array)$classes as $class) {
					echo '<option value="' . $class . '"'; if ($class == $classes[0]) { echo ' selected="selected"'; } echo '>' . $class . '</option>
					';
				}
				if (strlen($data_update['imgfile']) > 20) {
					$image_file_short = substr($data_update['imgfile'],0,17) . '...';
				} else {
					$image_file_short = $data_update['imgfile'];
				}
				$thumbsize = @getimagesize($pp_options['photospath'] . '/' . $pp_options['thumbprefix'] . $data_update['imgfile']);
				$imgsize = @getimagesize($pp_options['photospath'] . '/' . $data_update['imgfile']);
				$insert_text = __('Insert','photopress') . " " . $image_file_short;
				$inserted_text = __('Inserted','photopress') . " " . $image_file_short;
				echo '</select></td></tr></table>
				<div>
				<p class="submit"><input class="pp_insert_button" type="button" name="insert" value="' . $insert_text . '" onClick="insertcode(\'' . $data_update['imgfile'] . '\',this.form.thumb.options[this.form.thumb.selectedIndex].value,this.form.classes.options[this.form.classes.selectedIndex].value,\'' . js_escape(stripslashes($updated_data['imgname'])) . '\',\'' . $updated_data['catslug'] . '\',' . $imgsize[0] . ',' . $imgsize[1] . ',' . $thumbsize[0] . ',' . $thumbsize[1] . '); this.form.insert.value=\'' . $inserted_text . '\'; return false;" /></p></div>
				</form></div>
				';
			}
		}
		echo '
</div>
</body>
</html>';
die();
	}
}
?>
