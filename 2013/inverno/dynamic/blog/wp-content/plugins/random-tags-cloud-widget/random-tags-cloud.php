<?php
/*
Plugin Name: Random Tags Cloud Widget
Plugin URI: http://www.yakupgovler.com/?p=402
Description: Displays your tags by selecting randomly. You can customize other tag cloud's settings..
Version: 1.2
Author: Yakup GÖVLER
Author URI: http://www.yakupgovler.com
*/

function yg_tags_init() {
	if (!function_exists('register_sidebar_widget')) {
		return;
	}

function yg_tags_widget($args) {
		extract($args);
		$options = get_option('yg_tags');
		$title = htmlspecialchars($options['title']);

		$parameters = array(
		  'smallest' => intval($options['smallest']),
		  'largest' => intval($options['largest']),
		  'unit' => $options['unit'],
		  'number' => intval($options['number']),
		  'format' => $options['format'],
		  'rsec' => $options['rsec'],
		  'orderby' => $options['orderby'],
		  'order' => $options['order'],
		  'exclude' =>$options['exclude'],
		  'include' =>$options['include'],
		  'tagspage_id' => intval($options['tagspage_id'])
		);		

        echo $before_widget.$before_title.$title.$after_title;
         yg_tag_cloud( $parameters );
        echo $after_widget;
}
/* We cloned wp_tag_cloud as yg_tag_cloud and get_terms as yg_get_terms. There are little differences.      */
function yg_tag_cloud( $args = '' ) {
	$defaults = array(
		'smallest' => 8, 'largest' => 22, 'unit' => 'pt', 'number' => 45,
		'format' => 'flat', 'orderby' => 'name', 'order' => 'ASC',
		'exclude' => '', 'include' => '', 'link' => 'view', 'tagspage_id' => 0
	);
	
	$args = wp_parse_args( $args, $defaults );
	
    if (intval($args['rsec']) != 1) $secim = array('orderby' => 'count', 'order' => 'DESC');
      else $secim = array('orderby'=>'RAND', 'order'=>'');
	  
    $tags = yg_get_terms('post_tag', array_merge($args, $secim));
	if ( empty( $tags ) )
		return;
	foreach ( $tags as $key => $tag ) {
		if ( 'edit' == $args['link'] )
			$link = get_edit_tag_link( $tag->term_id );
		else
			$link = get_tag_link( $tag->term_id );
		if ( is_wp_error( $link ) )
			return false;

		$tags[ $key ]->link = $link;
		$tags[ $key ]->id = $tag->term_id;
	}
	
	$return = wp_generate_tag_cloud( $tags, $args );
	if ( is_wp_error( $return ) )
		return false;

	$return = apply_filters( 'wp_tag_cloud', $return, $args );
    if ($args['tagspage_id']) {
	  $return .= '<div class="alltags_link"><a href="' . get_permalink($args['tagspage_id']) . '" title="Tags Archive" alt="Tags Archive">Show All Tags &#187;</a></div>';
    }
	echo $return;
}

function &yg_get_terms($taxonomies, $args = '') {
	global $wpdb;
	$empty_array = array();

	$single_taxonomy = false;
	if ( !is_array($taxonomies) ) {
		$single_taxonomy = true;
		$taxonomies = array($taxonomies);
	}

	foreach ( (array) $taxonomies as $taxonomy ) {
		if ( ! is_taxonomy($taxonomy) )
			return new WP_Error('invalid_taxonomy', __('Invalid Taxonomy'));
	}

	$in_taxonomies = "'" . implode("', '", $taxonomies) . "'";

	$defaults = array('orderby' => 'name', 'order' => 'ASC',
		'hide_empty' => true, 'exclude' => '', 'include' => '',
		'number' => '', 'fields' => 'all', 'slug' => '', 'parent' => '',
		'hierarchical' => true, 'child_of' => 0, 'get' => '', 'name__like' => '',
		'pad_counts' => false, 'offset' => '', 'search' => '');
	$args = wp_parse_args( $args, $defaults );
	$args['number'] = absint( $args['number'] );
	$args['offset'] = absint( $args['offset'] );
	if ( !$single_taxonomy || !is_taxonomy_hierarchical($taxonomies[0]) ||
		'' != $args['parent'] ) {
		$args['child_of'] = 0;
		$args['hierarchical'] = false;
		$args['pad_counts'] = false;
	}

	if ( 'all' == $args['get'] ) {
		$args['child_of'] = 0;
		$args['hide_empty'] = 0;
		$args['hierarchical'] = false;
		$args['pad_counts'] = false;
	}
	extract($args, EXTR_SKIP);

	if ( $child_of ) {
		$hierarchy = _get_term_hierarchy($taxonomies[0]);
		if ( !isset($hierarchy[$child_of]) )
			return $empty_array;
	}

	if ( $parent ) {
		$hierarchy = _get_term_hierarchy($taxonomies[0]);
		if ( !isset($hierarchy[$parent]) )
			return $empty_array;
	}

	$filter_key = ( has_filter('list_terms_exclusions') ) ? serialize($GLOBALS['wp_filter']['list_terms_exclusions']) : '';
	$key = md5( serialize( compact(array_keys($defaults)) ) . serialize( $taxonomies ) . $filter_key );
	$last_changed = wp_cache_get('last_changed', 'terms');
	if ( !$last_changed ) {
		$last_changed = time();
		wp_cache_set('last_changed', $last_changed, 'terms');
	}
	$cache_key = "get_terms:$key:$last_changed";

	if ( $cache = wp_cache_get( $cache_key, 'terms' ) ) {
		$terms = apply_filters('get_terms', $cache, $taxonomies, $args);
		return $terms;
	}

	if ( 'count' == $orderby )
		$orderby = 'tt.count';
	else if ( 'name' == $orderby )
		$orderby = 't.name';
	else if ( 'slug' == $orderby )
		$orderby = 't.slug';
	else if ( 'term_group' == $orderby )
		$orderby = 't.term_group';
	else if ( 'RAND' == $orderby )
		$orderby = 'RAND()';	
	else
		$orderby = 't.term_id';

	$where = '';
	$inclusions = '';
	if ( !empty($include) ) {
		$exclude = '';
		$interms = preg_split('/[\s,]+/',$include);
		if ( count($interms) ) {
			foreach ( (array) $interms as $interm ) {
				if (empty($inclusions))
					$inclusions = ' AND ( t.term_id = ' . intval($interm) . ' ';
				else
					$inclusions .= ' OR t.term_id = ' . intval($interm) . ' ';
			}
		}
	}

	if ( !empty($inclusions) )
		$inclusions .= ')';
	$where .= $inclusions;

	$exclusions = '';
	if ( !empty($exclude) ) {
		$exterms = preg_split('/[\s,]+/',$exclude);
		if ( count($exterms) ) {
			foreach ( (array) $exterms as $exterm ) {
				if (empty($exclusions))
					$exclusions = ' AND ( t.term_id <> ' . intval($exterm) . ' ';
				else
					$exclusions .= ' AND t.term_id <> ' . intval($exterm) . ' ';
			}
		}
	}

	if ( !empty($exclusions) )
		$exclusions .= ')';
	$exclusions = apply_filters('list_terms_exclusions', $exclusions, $args );
	$where .= $exclusions;

	if ( !empty($slug) ) {
		$slug = sanitize_title($slug);
		$where .= " AND t.slug = '$slug'";
	}

	if ( !empty($name__like) )
		$where .= " AND t.name LIKE '{$name__like}%'";

	if ( '' != $parent ) {
		$parent = (int) $parent;
		$where .= " AND tt.parent = '$parent'";
	}

	if ( $hide_empty && !$hierarchical )
		$where .= ' AND tt.count > 0';

	if ( !empty($number) ) {
		if( $offset )
			$number = 'LIMIT ' . $offset . ',' . $number;
		else
			$number = 'LIMIT ' . $number;

	} else
		$number = '';

	if ( !empty($search) ) {
		$search = like_escape($search);
		$where .= " AND (t.name LIKE '%$search%')";
	}

	$select_this = '';
	if ( 'all' == $fields )
		$select_this = 't.*, tt.*';
	else if ( 'ids' == $fields )
		$select_this = 't.term_id, tt.parent, tt.count';
	else if ( 'names' == $fields )
		$select_this = 't.term_id, tt.parent, tt.count, t.name';

	$query = "SELECT $select_this FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy IN ($in_taxonomies) $where ORDER BY $orderby $order $number";

	if ( 'all' == $fields ) {
		$terms = $wpdb->get_results($query);
		update_term_cache($terms);
	} else if ( ('ids' == $fields) || ('names' == $fields) ) {
		$terms = $wpdb->get_results($query);
	}

	if ( empty($terms) ) {
		$cache[ $key ] = array();
		wp_cache_set( 'get_terms', $cache, 'terms' );
		$terms = apply_filters('get_terms', array(), $taxonomies, $args);
		return $terms;
	}

	if ( $child_of ) {
		$children = _get_term_hierarchy($taxonomies[0]);
		if ( ! empty($children) )
			$terms = & _get_term_children($child_of, $terms, $taxonomies[0]);
	}

	if ( $pad_counts && 'all' == $fields )
		_pad_term_counts($terms, $taxonomies[0]);

	if ( $hierarchical && $hide_empty && is_array($terms) ) {
		foreach ( $terms as $k => $term ) {
			if ( ! $term->count ) {
				$children = _get_term_children($term->term_id, $terms, $taxonomies[0]);
				if( is_array($children) )
					foreach ( $children as $child )
						if ( $child->count )
							continue 2;

				// It really is empty
				unset($terms[$k]);
			}
		}
	}
	reset ( $terms );

	$_terms = array();
	if ( 'ids' == $fields ) {
		while ( $term = array_shift($terms) )
			$_terms[] = $term->term_id;
		$terms = $_terms;
	} elseif ( 'names' == $fields ) {
		while ( $term = array_shift($terms) )
			$_terms[] = $term->name;
		$terms = $_terms;
	}

	wp_cache_add( $cache_key, $terms, 'terms' );

	$terms = apply_filters('get_terms', $terms, $taxonomies, $args);
	return $terms;
}

/*****************************************************/
function yg_tags_options() {

		$options = get_option('yg_tags');
		if (!is_array($options)) {
			$options = array('title' => 'Tags Cloud', 'number' => '45', 'unit' => 'pt', 'smallest' => '8', 'largest' => '22', 'format' => 'flat', 'rsec' => 0, 'orderby' => 'name', 'order' => 'ASC', 'exclude' => '', 'include' => '', 'tagspage_id'=>'');
			update_option('yg_tags', $options);
		}		
		if ( $_POST['yg-tags-submit'] ) {
			$newoptions['title'] = (trim($_POST['yg-tags-title']) != "") ? trim(strip_tags(stripslashes($_POST['yg-tags-title']))) : 'tags';
			$newoptions['number'] = (trim($_POST['yg-tags-number']) != "") ? trim(strip_tags(stripslashes($_POST['yg-tags-number']))) : '45';
			$newoptions['unit'] = trim(strip_tags(stripslashes($_POST['yg-tags-unit'])));
			$newoptions['smallest'] = (trim($_POST['yg-tags-smallest']) != "") ? trim(strip_tags(stripslashes($_POST['yg-tags-smallest']))) : '8';
			$newoptions['largest'] = (trim($_POST['yg-tags-largest']) != "") ? trim(strip_tags(stripslashes($_POST['yg-tags-largest']))) : '22';

			$newoptions['format'] = trim(strip_tags(stripslashes($_POST['yg-tags-format'])));
		    $newoptions['rsec'] = ($_POST['yg-tags-rsec']) ? 1 : 0;			
			$newoptions['orderby'] = trim(strip_tags(stripslashes($_POST['yg-tags-orderby'])));
			$newoptions['order'] = trim(strip_tags(stripslashes($_POST['yg-tags-order'])));
			
			$newoptions['exclude'] = (trim($_POST['yg-tags-exclude']) != "") ? str_replace(" ", "", strip_tags(stripslashes($_POST['yg-tags-exclude']))) :'';
			$newoptions['include'] = (trim($_POST['yg-tags-include']) != "") ? str_replace(" ", "", strip_tags(stripslashes($_POST['yg-tags-include']))) :'';
			
			$newoptions['tagspage_id'] = trim(strip_tags(stripslashes(($_POST['yg-tags-tagspage']))));
			$options = $newoptions;
			update_option('yg_tags', $options);

		}

		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		$number = htmlspecialchars($options['number'], ENT_QUOTES);
		$unit = htmlspecialchars($options['unit'], ENT_QUOTES);
		$smallest = htmlspecialchars($options['smallest'], ENT_QUOTES);
		$largest = htmlspecialchars($options['largest'], ENT_QUOTES);

		$format = htmlspecialchars($options['format'], ENT_QUOTES);
		$rsec = intval($options['rsec']);
		$orderby = htmlspecialchars($options['orderby'], ENT_QUOTES);
		$order = htmlspecialchars($options['order'], ENT_QUOTES);
		$exclude = htmlspecialchars($options['exclude'], ENT_QUOTES);
		$include = htmlspecialchars($options['include'], ENT_QUOTES);
        $tagspage_id = htmlspecialchars($options['tagspage_id'], ENT_QUOTES);
?>
		<p>
		 <label for="yg-tags-title">
		  <?php _e( 'Title:' ); ?> 
		  <input class="widefat" id="yg-tags-title" name="yg-tags-title" type="text" value="<?php echo $title; ?>" />
		 </label>
		</p>
		<p>
		 <label for="yg-tags-number">Number of Tags: 
		 <input style="width: 15%; padding: 3px;" id="yg-tags-number" name="yg-tags-number" type="text" value="<?php echo $number; ?>" />
		 </label>
		</p>
		<p>
		 <label for="yg-tags-unit">Font Unit: 
		  <select name="yg-tags-unit" id="yg-tags-unit" class="widefat" size="1">
			<option value="px" <?php echo ($unit=="px")?'selected':''?>>Pixel</option>
			<option value="pt" <?php echo ($unit=="pt")?'selected':''?>>Point</option>
			<option value="em" <?php echo ($unit=="em")?'selected':''?>>Em</option>
			<option value="%" <?php echo ($unit=="%")?'selected':''?>>Percent</option>
			</select>
		 </label>
		</p>
		<p>
		 <label for="yg-tags-smallest">Smallest Font Size: 
		 <input style="width: 15%; padding: 3px;" id="yg-tags-smallest" name="yg-tags-smallest" type="text" value="<?php echo $smallest; ?>" />
		 </label>
		</p>
		<p>
		 <label for="yg-tags-largest">Largest Font Size: 
		 <input style="width: 15%; padding: 3px;" id="yg-tags-largest" name="yg-tags-largest" type="text" value="<?php echo $largest; ?>" />
		 </label>
		</p>
		<p>
		 <label for="yg-tags-format">Cloud Format: 
		  <select name="yg-tags-format" id="yg-tags-format" class="widefat" size="1">
				   		<option value="flat" <?php echo ($format=="flat")?'selected':''?>>Flat</option>
				   		<option value="list" <?php echo ($format=="list")?'selected':''?>>List</option>
			</select>
		 </label>
		</p>
		<p>
		 <label for="yg-tags-rsec">Select Tags Randomly <input type="checkbox" class="checkbox" id="yg-tags-rsec" name="yg-tags-rsec" <?php echo ($rsec == 1)?'checked="checked"':'' ?>/> 
		 </label> 
		</p> 		
		<p>
		 <label for="yg-tags-orderby">Sort by: 
		  <select name="yg-tags-orderby" id="yg-tags-orderby" class="widefat" size="1">
				   		<option value="name" <?php echo ($orderby=="name")?'selected':''?>>Name</option>
				   		<option value="count" <?php echo ($orderby=="count")?'selected':''?>>Count</option>
			</select>
		 </label>
		</p>
		<p>
		 <label for="yg-tags-order">Sort Order: 
		  <select name="yg-tags-order" id="yg-tags-order" class="widefat" size="1">
				   		<option value="ASC" <?php echo ($order=="ASC")?'selected':''?>>ASC (A-Z)</option>
				   		<option value="DESC" <?php echo ($order=="DESC")?'selected':''?>>DESC (Z-A)</option>
				   		<option value="RAND" <?php echo ($order=="RAND")?'selected':''?>>Random</option>	
			</select>
		 </label>
		</p>
		<p>
		 <label for="yg-tags-exclude">Exclude Tags: 
		    <input class="widefat" id="yg-tags-exclude" name="yg-tags-exclude" type="text" value="<?php echo $exclude; ?>" />
			<br /><small>(Category ids separated with a comma)</small>
		 </label>
		</p>
		<p>
		 <label for="yg-tags-include">Include Tags: 
		    <input class="widefat" id="yg-tags-include" name="yg-tags-include" type="text" value="<?php echo $include; ?>" />
			<br /><small>(Category ids separated with a comma)</small>
		 </label>
		</p>
		<p>
		 <label for="yg-tags-tagspage">Your Tags Page ID: 
		    <input style="width: 15%; padding: 3px;" id="yg-tags-tagspage" name="yg-tags-tagspage" type="text" value="<?php echo $tagspage_id; ?>" />
			<br /><small>(to show 'Show All Tags' link)</small>
		 </label>
		</p>		
		<input type="hidden" id="yg-tags-submit" name="yg-tags-submit" value="1" />

<?php
}
	// Register Widget
	register_sidebar_widget('Random Tags Cloud', 'yg_tags_widget');
	register_widget_control('Random Tags Cloud', 'yg_tags_options');
}

add_action('plugins_loaded', 'yg_tags_init');
?>