<?php
/*
Plugin Name: Smooth Slider
Plugin URI: http://www.clickonf5.org/smooth-slider
Description: Smooth Slider adds a smooth content and image slideshow with customizable background and slide intervals to any location of your blog
Version: 2.3.5	
Author: Internet Techies
Author URI: http://www.clickonf5.org/
Wordpress version supported: 2.9 and above
*/

/*  Copyright 2009-2011  Internet Techies  (email : tedeshpa@gmail.com)

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
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
//Please visit Plugin page http://www.clickonf5.org/smooth-slider for Changelog
//on activation
//defined global variables and constants here
global $smooth_slider;
$smooth_slider = get_option('smooth_slider_options');
define('SLIDER_TABLE','smooth_slider'); //Slider TABLE NAME
define('PREV_SLIDER_TABLE','slider'); //Slider TABLE NAME
define('SLIDER_META','smooth_slider_meta'); //Meta TABLE NAME
define('SLIDER_POST_META','smooth_slider_postmeta'); //Meta TABLE NAME
define("SMOOTH_SLIDER_VER","2.3.5",false);//Current Version of Smooth Slider
if ( ! defined( 'SMOOTH_SLIDER_PLUGIN_BASENAME' ) )
	define( 'SMOOTH_SLIDER_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
if ( ! defined( 'SMOOTH_SLIDER_CSS_DIR' ) ){
	if($smooth_slider['ver']=='step') define( 'SMOOTH_SLIDER_CSS_DIR', WP_PLUGIN_DIR.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)).'/css/styles/' );
	else define( 'SMOOTH_SLIDER_CSS_DIR', WP_PLUGIN_DIR.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)).'/css/skins/' );
}
// Create Text Domain For Translations
load_plugin_textdomain('smooth-slider', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');

function install_smooth_slider() {
	global $wpdb, $table_prefix;
	$table_name = $table_prefix.SLIDER_TABLE;
	if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
		$sql = "CREATE TABLE $table_name (
					id int(5) NOT NULL AUTO_INCREMENT,
					post_id int(11) NOT NULL,
					date datetime NOT NULL,
					slider_id int(5) NOT NULL DEFAULT '1',
					UNIQUE KEY id(id)
				);";
		$rs = $wpdb->query($sql);
		
		$prev_table_name = $table_prefix.PREV_SLIDER_TABLE;
		
		if($wpdb->get_var("show tables like '$prev_table_name'") == $prev_table_name) {
			$prev_slider_data = ss_get_prev_slider();
			foreach ($prev_slider_data as $prev_slider_row){
				$prev_post_id = $prev_slider_row['id'];
				$prev_date_time = $prev_slider_row['date'];
				if ($prev_post_id) {
					$sql = "INSERT INTO $table_name (post_id,date) VALUES('$prev_post_id','$prev_date_time');";
					$result = $wpdb->query($sql);
				}
			}
		}
	}
	add_cf5_column_if_not_exist($table_name, 'slide_order', "ALTER TABLE ".$table_name." ADD slide_order int(5) NOT NULL DEFAULT '0';");

   	$meta_table_name = $table_prefix.SLIDER_META;
	if($wpdb->get_var("show tables like '$meta_table_name'") != $meta_table_name) {
		$sql = "CREATE TABLE $meta_table_name (
					slider_id int(5) NOT NULL AUTO_INCREMENT,
					slider_name varchar(100) NOT NULL default '',
					UNIQUE KEY slider_id(slider_id)
				);";
		$rs2 = $wpdb->query($sql);
		
		$sql = "INSERT INTO $meta_table_name (slider_id,slider_name) VALUES('1','Smooth Slider');";
		$rs3 = $wpdb->query($sql);
	}
	
	$slider_postmeta = $table_prefix.SLIDER_POST_META;
	if($wpdb->get_var("show tables like '$slider_postmeta'") != $slider_postmeta) {
		$sql = "CREATE TABLE $slider_postmeta (
					post_id int(11) NOT NULL,
					slider_id int(5) NOT NULL default '1',
					UNIQUE KEY post_id(post_id)
				);";
		$rs4 = $wpdb->query($sql);
	}
   // Need to delete the previously created options in old versions and create only one option field for Smooth Slider
   $default_slider = array();
   $default_slider = array('speed'=>'7', 
	                       'no_posts'=>'5', 
						   'bg_color'=>'#ffffff', 
						   'height'=>'200',
						   'width'=>'450',
						   'border'=>'1',
						   'brcolor'=>'#999999',
						   'prev_next'=>'0',
						   'goto_slide'=>'1',
						   'title_text'=>'Featured Posts',
						   'title_from'=>'0',
						   'title_font'=>'Georgia',
						   'title_fsize'=>'20',
						   'title_fstyle'=>'bold',
						   'title_fcolor'=>'#000000',
						   'ptitle_font'=>'Trebuchet MS',
						   'ptitle_fsize'=>'14',
						   'ptitle_fstyle'=>'bold',
						   'ptitle_fcolor'=>'#000000',
						   'img_align'=>'left',
						   'img_height'=>'120',
						   'img_width'=>'165',
						   'img_border'=>'1',
						   'img_brcolor'=>'#000000',
						   'content_font'=>'Verdana',
						   'content_fsize'=>'12',
						   'content_fstyle'=>'normal',
						   'content_fcolor'=>'#333333',
						   'content_from'=>'content',
						   'content_chars'=>'300',
						   'bg'=>'0',
						   'image_only'=>'0',
						   'allowable_tags'=>'',
						   'more'=>'Read More',
						   'img_size'=>'1',
						   'img_pick'=>array('1','slider_thumbnail','1','1','1','1'), //use custom field/key, name of the key, use post featured image, pick the image attachment, attachment order,scan images
						   'user_level'=>'edit_others_posts',
						   'custom_nav'=>'',
						   'crop'=>'0',
						   'transition'=>'5',
						   'autostep'=>'1',
						   'multiple_sliders'=>'0',
						   'navimg_w'=>'32',
						   'navimg_ht'=>'32',
						   'content_limit'=>'50',
						   'stylesheet'=>'default',
						   'shortcode'=>'1',
						   'rand'=>'0',
						   'ver'=>'j',
						   'support'=>'1',
						   'fouc'=>'0',
						   'noscript'=>'This page is having a slideshow that uses Javascript. Your browser either doesn\'t support Javascript or you have it turned off. To see this page as it is meant to appear please use a Javascript enabled browser.'
			              );
   
	   $smooth_slider = get_option('smooth_slider_options');
	   /*if($smooth_slider){
	      $default_slider['ver']='step';
	   }*/
	   	   
	   $img_pick = $smooth_slider['img_pick'];
  
       if(is_array($img_pick)) {
	    $cskey = $smooth_slider['img_pick'][1];
	   }
	   else{
	    $cskey = 'slider_thumbnail';
	   }
      
	   if(!is_array($img_pick)) {
	   //if(!isset($smooth_slider['img_pick'][0])) {
		   if($smooth_slider['img_pick'] == '1') {
			  $smooth_slider['img_pick'] = array('0',$cskey,'0','0','1','1');
		   }
		   elseif($smooth_slider['img_pick'] == '0'){
			  $smooth_slider['img_pick'] = array('1',$cskey,'0','0','1','0');
		   }
		   else {
			  $smooth_slider['img_pick'] = array('1',$cskey,'1','1','1','1');
		   }
	   }
	   
/*	    if(is_array($img_pick) and (count($img_pick)<6 or count($img_pick)>6)) {
		  $smooth_slider['img_pick'] = array('1',$cskey,'1','1','1','1');
		}
*/	   
	   if(!$smooth_slider) {
	     $smooth_slider = array();
	   }
	   
	   //if($smooth_slider and !isset($smooth_slider['ver'])){
	      $smooth_slider['stylesheet']='default';
	   //}
	   
	   foreach($default_slider as $key=>$value) {
	      if(!isset($smooth_slider[$key])) {
		     $smooth_slider[$key] = $value;
		  }
	   }
	   	   
	   $smooth_slider['ver']='j';
     
	 if($smooth_slider['user_level']<=10 and $smooth_slider['user_level'] >=1) {
		 if($smooth_slider['user_level']<=10 and $smooth_slider['user_level'] >7) {
		  $smooth_slider['user_level']='manage_options';
		 }
		 elseif($smooth_slider['user_level']<=7 and $smooth_slider['user_level'] >2){
		  $smooth_slider['user_level']='edit_others_posts';
		 } 
		  elseif($smooth_slider['user_level']==2){
		  $smooth_slider['user_level']='publish_posts';
		 } 
		 else {
		  $smooth_slider['user_level']='edit_posts';
		 }
	 }
	  
	   delete_option('smooth_slider_options');	  
	   update_option('smooth_slider_options',$smooth_slider);
}
register_activation_hook( __FILE__, 'install_smooth_slider' );
require_once (dirname (__FILE__) . '/includes/smooth-slider-functions.php');
require_once (dirname (__FILE__) . '/includes/sslider-get-the-image-functions.php');

//This adds the post to the slider
function add_to_slider($post_id) {
global $smooth_slider;
 if(isset($_POST['sldr-verify']) and current_user_can( $smooth_slider['user_level'] ) ) {
	global $wpdb, $table_prefix, $post;
	$table_name = $table_prefix.SLIDER_TABLE;
	
	if(isset($_POST['slider']) and !isset($_POST['slider_name'])) {
	  $slider_id = '1';
	  if(is_post_on_any_slider($post_id)){
	     $sql = "DELETE FROM $table_name where post_id = '$post_id'";
		 $wpdb->query($sql);
	  }
	  
	  if(isset($_POST['slider']) and $_POST['slider'] == "slider" and !slider($post_id,$slider_id)) {
		$dt = date('Y-m-d H:i:s');
		$sql = "INSERT INTO $table_name (post_id, date, slider_id) VALUES ('$post_id', '$dt', '$slider_id')";
		$wpdb->query($sql);
	  }
	}
	if(isset($_POST['slider']) and $_POST['slider'] == "slider" and isset($_POST['slider_name'])){
	  $slider_id_arr = $_POST['slider_name'];
	  $post_sliders_data = ss_get_post_sliders($post_id);
	  
	  foreach($post_sliders_data as $post_slider_data){
		if(!in_array($post_slider_data['slider_id'],$slider_id_arr)) {
		  $sql = "DELETE FROM $table_name where post_id = '$post_id'";
		  $wpdb->query($sql);
		}
	  }
	    /*if(is_post_on_any_slider($post_id)){
	     $sql = "DELETE FROM $table_name where post_id = '$post_id'";
		 $wpdb->query($sql);
	    }*/
		foreach($slider_id_arr as $slider_id) {
			if(!slider($post_id,$slider_id)) {
				$dt = date('Y-m-d H:i:s');
				$sql = "INSERT INTO $table_name (post_id, date, slider_id) VALUES ('$post_id', '$dt', '$slider_id')";
				$wpdb->query($sql);
			}
		}
	}
	
	$table_name = $table_prefix.SLIDER_POST_META;
	if(isset($_POST['display_slider']) and !isset($_POST['display_slider_name'])) {
	  $slider_id = '1';
	}
	if(isset($_POST['display_slider']) and isset($_POST['display_slider_name'])){
	  $slider_id = $_POST['display_slider_name'];
	}
  	if(isset($_POST['display_slider'])){	
		  if(!ss_post_on_slider($post_id,$slider_id)) {
		    $sql = "DELETE FROM $table_name where post_id = '$post_id'";
		    $wpdb->query($sql);
			$sql = "INSERT INTO $table_name (post_id, slider_id) VALUES ('$post_id', '$slider_id')";
			$wpdb->query($sql);
		  }
	}
	$slider_style = get_post_meta($post_id,'slider_style',true);
	$post_slider_style=$_POST['slider_style'];
	if($slider_style != $post_slider_style and isset($post_slider_style) and !empty($post_slider_style)) {
	  update_post_meta($post_id, 'slider_style', $_POST['slider_style']);	
	}
	
	$thumbnail_key = $smooth_slider['img_pick'][1];
	$sslider_thumbnail = get_post_meta($post_id,$thumbnail_key,true);
	$post_slider_thumbnail=$_POST['sslider_thumbnail'];
	if($sslider_thumbnail != $post_slider_thumbnail and isset($post_slider_thumbnail) and !empty($post_slider_thumbnail)) {
	  update_post_meta($post_id, $thumbnail_key, $_POST['sslider_thumbnail']);	
	}
	
	$sslider_link = get_post_meta($post_id,'slide_redirect_url',true);
	$link=$_POST['sslider_link'];
	//$sldr_post=get_post($post_id);
	//if((!isset($link) or empty($link)) and $sldr_post->post_status == 'publish'  ){$link=get_permalink($post_id);}//from 2.3.3
	if($sslider_link != $link and isset($link) and !empty($link)) {
	  update_post_meta($post_id, 'slide_redirect_url', $link);	
	}
	
	$sslider_nolink = get_post_meta($post_id,'sslider_nolink',true);
	$post_sslider_nolink = $_POST['sslider_nolink'];
	if($sslider_nolink != $_POST['sslider_nolink'] and isset($post_sslider_nolink) and !empty($post_sslider_nolink)) {
	  update_post_meta($post_id, 'sslider_nolink', $_POST['sslider_nolink']);	
	}
	
  } //sldr_verify
}

//Removes the post from the slider, if you uncheck the checkbox from the edit post screen
function remove_from_slider($post_id) {
	global $wpdb, $table_prefix;
	$table_name = $table_prefix.SLIDER_TABLE;
	
	// authorization
	if (!current_user_can('edit_post', $post_id))
		return $post_id;
	// origination and intention
	if (!wp_verify_nonce($_POST['sldr-verify'], 'SmoothSlider'))
		return $post_id;
	
    if(empty($_POST['slider']) and is_post_on_any_slider($post_id)) {
		$sql = "DELETE FROM $table_name where post_id = '$post_id'";
		$wpdb->query($sql);
	}
	
	$display_slider = $_POST['display_slider'];
	$table_name = $table_prefix.SLIDER_POST_META;
	if(empty($display_slider) and ss_slider_on_this_post($post_id)){
	  $sql = "DELETE FROM $table_name where post_id = '$post_id'";
		    $wpdb->query($sql);
	}
} 
  
function delete_from_slider_table($post_id){
    global $wpdb, $table_prefix;
	$table_name = $table_prefix.SLIDER_TABLE;
    if(is_post_on_any_slider($post_id)) {
		$sql = "DELETE FROM $table_name where post_id = '$post_id'";
		$wpdb->query($sql);
	}
	$table_name = $table_prefix.SLIDER_POST_META;
    if(ss_slider_on_this_post($post_id)) {
		$sql = "DELETE FROM $table_name where post_id = '$post_id'";
		$wpdb->query($sql);
	}
}

// Slider checkbox on the admin page

function smooth_slider_edit_custom_box(){
   add_to_slider_checkbox();
}
/* Prints the edit form for pre-WordPress 2.5 post/page */
function smooth_slider_old_custom_box() {

  echo '<div class="dbx-b-ox-wrapper">' . "\n";
  echo '<fieldset id="myplugin_fieldsetid" class="dbx-box">' . "\n";
  echo '<div class="dbx-h-andle-wrapper"><h3 class="dbx-handle">' . 
        __( 'Smooth Slider', 'smooth-slider' ) . "</h3></div>";   
   
  echo '<div class="dbx-c-ontent-wrapper"><div class="dbx-content">';

  // output editing form

  smooth_slider_edit_custom_box();

  // end wrapper

  echo "</div></div></fieldset></div>\n";
}

function smooth_slider_add_custom_box() {
 global $smooth_slider;
 if (current_user_can( $smooth_slider['user_level'] )) {
	if( function_exists( 'add_meta_box' ) ) {
	    $post_types=get_post_types(); 
		foreach($post_types as $post_type) {
		  add_meta_box( 'sslider_box1', __( 'Smooth Slider' , 'smooth-slider'), 'smooth_slider_edit_custom_box', $post_type, 'advanced' );
		}
		//add_meta_box( $id,   $title,     $callback,   $page, $context, $priority ); 
	} else {
		add_action('dbx_post_advanced', 'smooth_slider_old_custom_box' );
		add_action('dbx_page_advanced', 'smooth_slider_old_custom_box' );
	}
 }
}
/* Use the admin_menu action to define the custom boxes */
add_action('admin_menu', 'smooth_slider_add_custom_box');

function add_to_slider_checkbox() {
	global $post, $smooth_slider;
	if (current_user_can( $smooth_slider['user_level'] )) {
		$extra = "";
		
		$post_id = $post->ID;
		
		if(isset($post->ID)) {
			$post_id = $post->ID;
			if(is_post_on_any_slider($post_id)) { $extra = 'checked="checked"'; }
		} 
		
		$post_slider_arr = array();
		
		$post_sliders = ss_get_post_sliders($post_id);
		if($post_sliders) {
			foreach($post_sliders as $post_slider){
			   $post_slider_arr[] = $post_slider['slider_id'];
			}
		}
		
		$sliders = ss_get_sliders();
?>
		<div id="slider_checkbox">
				<input type="checkbox" class="sldr_post" name="slider" value="slider" <?php echo $extra;?> />
				<label for="slider"><?php _e('Add this post/page to','smooth-slider'); ?> </label>
				<select name="slider_name[]" multiple="multiple" size="2" style="height:4em;">
                <?php foreach ($sliders as $slider) { ?>
                  <option value="<?php echo $slider['slider_id'];?>" <?php if(in_array($slider['slider_id'],$post_slider_arr)){echo 'selected';} ?>><?php echo $slider['slider_name'];?></option>
                <?php } ?>
                </select>
                
         <?php if($smooth_slider['multiple_sliders'] == '1') {?>
                <br />
                <br />
                <br />
                
                <input type="checkbox" class="sldr_post" name="display_slider" value="1" <?php if(ss_slider_on_this_post($post_id)){echo "checked";}?> />
				<label for="display_slider"><?php _e('Display ','smooth-slider'); ?>
				<select name="display_slider_name">
                <?php foreach ($sliders as $slider) { ?>
                  <option value="<?php echo $slider['slider_id'];?>" <?php if(ss_post_on_slider($post_id,$slider['slider_id'])){echo 'selected';} ?>><?php echo $slider['slider_name'];?></option>
                <?php } ?>
                </select> <?php _e('on this Post/Page (you need to add the Smooth Slider template tag manually on your page.php/single.php or whatever page template file)','smooth-slider'); ?></label>
          <?php } ?>
          
				<input type="hidden" name="sldr-verify" id="sldr-verify" value="<?php echo wp_create_nonce('SmoothSlider');?>" />
	    </div>
        <br />
        <div>
        <?php
        $slider_style = get_post_meta($post->ID,'slider_style',true);
        ?>
         <select name="slider_style" >
			<?php 
            $directory = SMOOTH_SLIDER_CSS_DIR;
            if ($handle = opendir($directory)) {
                while (false !== ($file = readdir($handle))) { 
                 if($file != '.' and $file != '..') { ?>
                  <option value="<?php echo $file;?>" <?php if (($slider_style == $file) or (empty($slider_style) and $smooth_slider['stylesheet'] == $file)){ echo "selected";}?> ><?php echo $file;?></option>
             <?php  } }
                closedir($handle);
            }
            ?>
        </select> <label for="slider_style"><?php _e('Stylesheet to use if slider is displayed on this Post/Page','smooth-slider'); ?> </label><br /> <br />
        
  <?php         $thumbnail_key = $smooth_slider['img_pick'][1];
                $sslider_thumbnail= get_post_meta($post_id, $thumbnail_key, true); 
				$sslider_link= get_post_meta($post_id, 'slide_redirect_url', true);  
				$sslider_nolink=get_post_meta($post_id, 'sslider_nolink', true);
  ?>
                <label for="sslider_thumbnail"><?php _e('Custom Thumbnail Image(url)','smooth-slider'); ?>
                <input type="text" name="sslider_thumbnail" class="sslider_thumbnail" value="<?php echo $sslider_thumbnail;?>" size="75" />
                <br /> </label> <br /><br />
                <fieldset>
                <label for="sslider_link"><?php _e('Slide Link URL ','smooth-slider'); ?>
                <input type="text" name="sslider_link" class="sslider_link" value="<?php echo $sslider_link;?>" size="50" /><small><?php _e('If left empty, it will be by default linked to the permalink.','smooth-slider'); ?></small> </label><br />
                <label for="sslider_nolink"> 
                <input type="checkbox" name="sslider_nolink" class="sslider_nolink" value="1" <?php if($sslider_nolink=='1'){echo "checked";}?>  /> <?php _e('Do not link this slide to any page(url)','smooth-slider'); ?></label>
                 </fieldset>
                 </div>
        
<?php }
}

//CSS for the checkbox on the admin page
function slider_checkbox_css() {
?><style type="text/css" media="screen">#slider_checkbox{margin: 5px 0 10px 0;padding:3px;font-weight:bold;}#slider_checkbox input,#slider_checkbox select{font-weight:bold;}#slider_checkbox label,#slider_checkbox input,#slider_checkbox select{vertical-align:top;}</style>
<?php
}

add_action('publish_post', 'add_to_slider');
add_action('publish_page', 'add_to_slider');
add_action('edit_post', 'add_to_slider');
add_action('publish_post', 'remove_from_slider');
add_action('edit_post', 'remove_from_slider');
add_action('deleted_post','delete_from_slider_table');

function smooth_slider_plugin_url( $path = '' ) {
	global $wp_version;
	if ( version_compare( $wp_version, '2.8', '<' ) ) { // Using WordPress 2.7
		$folder = dirname( plugin_basename( __FILE__ ) );
		if ( '.' != $folder )
			$path = path_join( ltrim( $folder, '/' ), $path );

		return plugins_url( $path );
	}
	return plugins_url( $path, __FILE__ );
}

function get_string_limit($output, $max_char)
{
    $output = str_replace(']]>', ']]&gt;', $output);
    $output = strip_tags($output);

  	if ((strlen($output)>$max_char) && ($espacio = strpos($output, " ", $max_char )))
	{
        $output = substr($output, 0, $espacio).'...';
		return $output;
   }
   else
   {
      return $output;
   }
}

function smooth_slider_get_first_image($post) {
	$first_img = '';
	ob_start();
	ob_end_clean();
	$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
	$first_img = $matches [1] [0];
	return $first_img;
}
add_filter( 'plugin_action_links', 'sslider_plugin_action_links', 10, 2 );

function sslider_plugin_action_links( $links, $file ) {
	if ( $file != SMOOTH_SLIDER_PLUGIN_BASENAME )
		return $links;

	$url = sslider_admin_url( array( 'page' => 'smooth-slider-settings' ) );

	$settings_link = '<a href="' . esc_attr( $url ) . '">'
		. esc_html( __( 'Settings') ) . '</a>';

	array_unshift( $links, $settings_link );

	return $links;
}
class Smooth_Slider_Simple_Widget extends WP_Widget {
	function Smooth_Slider_Simple_Widget() {
		$widget_options = array('classname' => 'sslider_wclass', 'description' => 'Insert Smooth Slider' );
		$this->WP_Widget('sslider_wid', 'Smooth Slider - Simple', $widget_options);
	}

	function widget($args, $instance) {
		extract($args, EXTR_SKIP);
	    global $smooth_slider;
		
		echo $before_widget;
		if($smooth_slider['multiple_sliders'] == '1') {
		$slider_id = empty($instance['slider_id']) ? '1' : apply_filters('widget_slider_id', $instance['slider_id']);
		}
		else{
		 $slider_id = '1';
		}

		echo $before_title . $after_title; 
		 get_smooth_slider($slider_id);
		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
	    global $smooth_slider;
		$instance = $old_instance;
		if($smooth_slider['multiple_sliders'] == '1') {
		   $instance['slider_id'] = strip_tags($new_instance['slider_id']);
		}

		return $instance;
	}

	function form($instance) {
	    global $smooth_slider;
		if($smooth_slider['multiple_sliders'] == '1') {
			$instance = wp_parse_args( (array) $instance, array( 'slider_id' => '' ) );
			$slider_id = strip_tags($instance['slider_id']);
			$sliders = ss_get_sliders();
			$sname_html='<option value="0" selected >Select the Slider</option>';
	 
		  foreach ($sliders as $slider) { 
			 if($slider['slider_id']==$slider_id){$selected = 'selected';} else{$selected='';}
			 $sname_html =$sname_html.'<option value="'.$slider['slider_id'].'" '.$selected.'>'.$slider['slider_name'].'</option>';
		  } 
	?>
				<p><label for="<?php echo $this->get_field_id('slider_id'); ?>">Select Slider Name: <select class="widefat" id="<?php echo $this->get_field_id('slider_id'); ?>" name="<?php echo $this->get_field_name('slider_id'); ?>"><?php echo $sname_html;?></select></label></p>
<?php  }
	}
}
add_action( 'widgets_init', create_function('', 'return register_widget("Smooth_Slider_Simple_Widget");') );

require_once (dirname (__FILE__) . '/slider_versions/j.php');
require_once (dirname (__FILE__) . '/settings/settings.php');
require_once (dirname (__FILE__) . '/includes/media-images.php');
?>