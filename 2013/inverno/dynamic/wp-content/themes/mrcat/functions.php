<?php 

$t_url = get_template_directory_uri();

function t_url() {
	global $t_url;
	echo $t_url;
}

/*add_action( 'phpmailer_init', 'fix_header' );

function fix_header( $phpmailer ) {
	$phpmailer->Sender = $phpmailer->From;
}*/

/* Setup */

add_action( 'after_setup_theme', 'my_setup' );
add_action( 'wp_enqueue_scripts', 'my_scripts' );

function my_setup() {
	register_nav_menu( 'menu', 'Menu' );
	register_nav_menu( 'campanha', 'Campanha' );
	register_nav_menu( 'franquias', 'Franquias' );
	register_nav_menu( 'men', 'Men' );
	register_nav_menu( 'women', 'Women' );

	/*add_theme_support( 'post-thumbnails', array( 'post' ) );
	set_post_thumbnail_size( 536, 225, true );*/
	add_image_size( 'home-large', 1440, 647, true );
	add_image_size( 'home-small', 310, 277, true );
	add_image_size( 'taxonomy-large', 960, 472, true );
	add_image_size( 'taxonomy-medium', 475, 410, true );
	add_image_size( 'taxonomy-small', 300, 260, true );
	add_image_size( 'product-small', 238, 172, true );
	add_image_size( 'product-large', 800, 600, true );
	add_image_size( 'campanha-large', 960, 640, false );
	add_image_size( 'clipping', 413, 228, true );
}

function my_scripts() {
	$uri = get_stylesheet_directory_uri();

	wp_deregister_script( 'jquery' );
	wp_register_script( 'jquery', 'http://code.jquery.com/jquery-1.8.3.min.js', array(), null, true );
	//wp_register_script( 'jquery', "{$uri}/js/jquery-1.8.3.min.js", array(), '1.8.3', true );
	wp_enqueue_script( 'jquery' );
	/*
	wp_enqueue_script( 'mask', "{$uri}/js/jquery.mask.min.js", array( 'jquery' ), null, true );
	wp_enqueue_script( 'fancybox', "{$uri}/js/fancybox/jquery.fancybox-1.3.4.pack.js", array( 'jquery' ), null, true );
	wp_enqueue_script( 'interface', "{$uri}/js/interface.js", array( 'jquery' ), filemtime( TEMPLATEPATH . '/js/interface.js' ), true );
	*/
	wp_enqueue_script( 'minified', "/min/g=mrcat-js", array( 'jquery' ), null, true );
}

add_filter( 'acf_options_page_title', 'my_acf_options_page_title' );

function my_acf_options_page_title( $title ) {
	return 'Redes Sociais';
}
 
/* Admin */

add_action( 'admin_menu', 'remove_menus' );
//add_action( 'admin_init', 'remove_editor_capabilities' );
add_action( 'login_enqueue_scripts', 'my_login_logo' );
add_filter( 'login_headerurl', 'my_login_logo_url' );
add_filter( 'login_headertitle', 'my_login_logo_url_title' );

function remove_menus() {
	global $menu;
	$restricted = array( __('Posts'), __('Links'), __('Comments') );
	end( $menu );
	while( prev( $menu ) ) {
		$value = explode( ' ',$menu[key( $menu )][0] );
		if ( in_array( $value[0] != NULL ? $value[0] : "" , $restricted ) )
			unset( $menu[key( $menu )] );
	}
}
/*
function remove_editor_capabilities(){
	$role = get_role( 'editor' );
	$role->remove_cap( 'edit_pages' );
	$role->remove_cap( 'edit_others_pages' );
	$role->remove_cap( 'edit_published_pages' );
	$role->remove_cap( 'publish_pages' );
	$role->remove_cap( 'delete_pages' );
	$role->remove_cap( 'delete_others_pages' );
	$role->remove_cap( 'delete_published_pages' );
	$role->remove_cap( 'delete_private_pages' );
	$role->remove_cap( 'edit_private_pages' );
	$role->remove_cap( 'read_private_pages' );
}	
*/
function my_login_logo() { ?>
<style type="text/css">
body.login { background: url(<?php echo get_bloginfo( 'template_directory' ) ?>/img/background.jpg) repeat }
body.login div#login h1 a {
	background-image: url(<?php echo get_bloginfo( 'template_directory' ) ?>/img/mr-cat.png);
	background-size: auto;
	height: 59px;
	margin-left: auto;
	margin-right: auto;
	width: 191px;
}
.login #nav a,
.login #backtoblog a { color: #3a2618 !important }
.login #nav a:hover,
.login #backtoblog a:hover { color: #705542 !important }
.wp-core-ui .button-primary {
	background: #3a2618;
	border-color: #3a2618;
}
.wp-core-ui .button-primary:hover {
	background: #705542;
	border-color: #705542;
}

</style>
<?php }

function my_login_logo_url() {
	return get_bloginfo( 'url' );
}
function my_login_logo_url_title() {
	return 'Ir para o início';
}

/* Redirects */

add_action( 'template_redirect', 'my_redirects' );

function my_redirects() {
	if ( is_tax() ) {
		if ( $redirect_to = get_option( get_query_var( 'taxonomy' ) . '_' . get_queried_object_id() . '_redirect_to' ) ) {
			wp_redirect( $redirect_to, 301 );
			exit;
		}
	}
}

/* SEO */

add_action( 'wpseo_opengraph', 'og_description' );

function og_description( $src ) {
	if ( 'produto' == get_post_type() ) 
		echo "<meta property='og:description' content='Adorei este produto da Mr.Cat. Confira aqui!' />\n";
}

/* Functions */

function fetch_data( $url ) {
	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt( $ch, CURLOPT_TIMEOUT, 50 );
	curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, TRUE ); 
	curl_setopt( $ch, CURLOPT_CAINFO, TEMPLATEPATH . "/cacert.pem" );
	$result = curl_exec( $ch );
	curl_close( $ch );
	return $result;
}

function get_taxonomy_parent( $term, $taxonomy = 'categorias' ) {
	$term = is_object( $term ) ? $term : get_term_by( 'slug', $term, $taxonomy );
	$parent = get_term( $term->parent, $taxonomy );
	if ( $parent->parent )
		$parent = get_term( $parent->parent, $taxonomy );
	if ( $parent->parent )
		$parent = get_term( $parent->parent, $taxonomy );
	return $parent;
}

function get_lojas( $pageID = 0 ) {
	global $post;

	$UF = array(
		'Acre' 				  => 'AC',
		'Alagoas' 			  => 'AL',
		'Amapá' 			  => 'AP',
		'Amazonas' 			  => 'AM',
		'Bahia' 			  => 'BA',
		'Ceará' 			  => 'CE',
		'Distrito Federal' 	  => 'DF',
		'Espírito Santo' 	  => 'ES',
		'Goiás' 			  => 'GO',
		'Maranhão' 			  => 'MA',
		'Mato Grosso' 		  => 'MT',
		'Mato Grosso do Sul'  => 'MS',
		'Minas Gerais' 		  => 'MG',
		'Pará' 				  => 'PA',
		'Paraíba' 			  => 'PB',
		'Paraná' 			  => 'PR',
		'Pernambuco' 		  => 'PE',
		'Piauí' 			  => 'PI',
		'Roraima' 			  => 'RR',
		'Rondônia' 			  => 'RO',
		'Rio de Janeiro' 	  => 'RJ',
		'Rio Grande do Norte' => 'RN',
		'Rio Grande do Sul'   => 'RS',
		'Santa Catarina' 	  => 'SC',
		'São Paulo' 		  => 'SP',
		'Sergipe' 			  => 'SE',
		'Tocantins' 		  => 'TO'
	);

	$data = array();
	$lojas = get_field( 'lojas', $pageID ? $pageID : $post->ID );
	foreach( $lojas as $index => $loja ) {
		$estado = $loja['estado'];
		$cidade = $loja['cidade'];
		$bairro = $loja['bairro'];

		$arr = array(
			'uf' => $UF[$estado],
			'slug' => "loja-{$index}",
			'nome' => $loja['nome'],
			'telefone' => $loja['telefone'],
			'endereco' => $loja['endereco'],
		);

		if ( !$data[$estado] ) {
			$data[$estado] = array( $cidade => array( $bairro => array( $arr ) ) );
		} else {
			if ( !$data[$estado][$cidade] ) {
				$data[$estado][$cidade] = array( $bairro => array( $arr ) );
			} else {
				if ( !$data[$estado][$cidade][$bairro] ) 
					$data[$estado][$cidade][$bairro] = array( $arr );
				else 
					$data[$estado][$cidade][$bairro][] = $arr;
			}
		}

	}
	tksort( $data );
	return $data;
}

function tksort( &$array ) {
	ksort( $array );
	foreach( array_keys($array) as $k ) 
		if ( gettype( $array[$k] ) == 'array' ) 
			tksort( $array[$k] );
}

/* Ajax */

function is_ajax() {
	if ( ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' && ! empty( $_GET['ajax'] ) )
		return true;
	return false;
}

function json_content() {
	$json = ob_get_contents();
	ob_end_clean();

	global $post;
	echo json_encode( array(
		'ID' => $post->ID,
		'slug' => is_front_page() ? 'home' : $post->post_name,
		'post_content' => $json,
	) );
}

/* Custom Post Types */

add_action( 'init', 'register_cpt_produto' );

function register_cpt_produto() {

	$labels = array( 
		'name' => 'Produtos',
		'singular_name' => 'Produto',
		'add_new' => 'Add New',
		'add_new_item' => 'Add New Produto',
		'edit_item' => 'Edit Produto',
		'new_item' => 'New Produto',
		'view_item' => 'View Produto',
		'search_items' => 'Search Produtos',
		'not_found' => 'No produtos found',
		'not_found_in_trash' => 'No produtos found in Trash',
		'parent_item_colon' => 'Parent Produto:',
		'menu_name' => 'Produtos',
	);

	$args = array( 
		'labels' => $labels,
		'hierarchical' => false,
		
		'supports' => array( 'title', 'custom-fields' ),
		'taxonomies' => array( 'categorias' ),
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'menu_position' => 5,
		
		'show_in_nav_menus' => false,
		'publicly_queryable' => true,
		'exclude_from_search' => false,
		'has_archive' => true,
		'query_var' => true,
		'can_export' => true,
		'rewrite' => true,
		'capability_type' => 'page'
	);

	register_post_type( 'produto', $args );
}

add_action( 'init', 'register_cpt_clipping' );

function register_cpt_clipping() {

    $labels = array( 
        'name' => 'Clippings',
        'singular_name' => 'Clipping',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Clipping',
        'edit_item' => 'Edit Clipping',
        'new_item' => 'New Clipping',
        'view_item' => 'View Clipping',
        'search_items' => 'Search Clippings',
        'not_found' => 'No clippings found',
        'not_found_in_trash' => 'No clippings found in Trash',
        'parent_item_colon' => 'Parent Clipping:',
        'menu_name' => 'Clippings',
    );

    $args = array( 
        'labels' => $labels,
        'hierarchical' => false,
        
        'supports' => array( 'title', 'custom-fields', 'author' ),
        
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 5,
        
        'show_in_nav_menus' => false,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => true,
        'capability_type' => 'post'
    );

    register_post_type( 'clipping', $args );
}

/* Custom Taxonomy */

add_action( 'init', 'register_taxonomy_categorias' );

function register_taxonomy_categorias() {

	$labels = array( 
		'name' => 'Categorias',
		'singular_name' => 'Categoria',
		'search_items' => 'Search Categorias',
		'popular_items' => 'Popular Categorias',
		'all_items' => 'All Categorias',
		'parent_item' => 'Parent Categoria',
		'parent_item_colon' => 'Parent Categoria:',
		'edit_item' => 'Edit Categoria',
		'update_item' => 'Update Categoria',
		'add_new_item' => 'Add New Categoria',
		'new_item_name' => 'New Categoria',
		'separate_items_with_commas' => 'Separate categorias with commas',
		'add_or_remove_items' => 'Add or remove categorias',
		'choose_from_most_used' => 'Choose from the most used categorias',
		'menu_name' => 'Categorias',
	);

	$args = array( 
		'labels' => $labels,
		'public' => true,
		'show_in_nav_menus' => true,
		'show_ui' => true,
		'show_tagcloud' => false,
		'show_admin_column' => true,
		'hierarchical' => true,

		'rewrite' => true,
		'query_var' => true
	);

	register_taxonomy( 'categorias', array('produto'), $args );
}

/* Hide others media */

// Show only posts and media related to logged in author
add_action('pre_get_posts', 'query_set_only_author' );
function query_set_only_author( $wp_query ) {
    global $current_user;
    if( is_admin() && !current_user_can('edit_others_posts') ) {
        $wp_query->set( 'author', $current_user->ID );
        add_filter('views_edit-post', 'fix_post_counts');
        add_filter('views_upload', 'fix_media_counts');
    }
}

// Fix post counts
function fix_post_counts($views) {
    global $current_user, $wp_query;
    unset($views['mine']);
    $types = array(
        array( 'status' =>  NULL ),
        array( 'status' => 'publish' ),
        array( 'status' => 'draft' ),
        array( 'status' => 'pending' ),
        array( 'status' => 'trash' )
    );
    foreach( $types as $type ) {
        $query = array(
            'author'      => $current_user->ID,
            'post_type'   => 'post',
            'post_status' => $type['status']
        );
        $result = new WP_Query($query);
        if( $type['status'] == NULL ):
            $class = ($wp_query->query_vars['post_status'] == NULL) ? ' class="current"' : '';
            $views['all'] = sprintf(__('<a href="%s"'. $class .'>All <span class="count">(%d)</span></a>', 'all'),
                admin_url('edit.php?post_type=post'),
                $result->found_posts);
        elseif( $type['status'] == 'publish' ):
            $class = ($wp_query->query_vars['post_status'] == 'publish') ? ' class="current"' : '';
            $views['publish'] = sprintf(__('<a href="%s"'. $class .'>Published <span class="count">(%d)</span></a>', 'publish'),
                admin_url('edit.php?post_status=publish&post_type=post'),
                $result->found_posts);
        elseif( $type['status'] == 'draft' ):
            $class = ($wp_query->query_vars['post_status'] == 'draft') ? ' class="current"' : '';
            $views['draft'] = sprintf(__('<a href="%s"'. $class .'>Draft'. ((sizeof($result->posts) > 1) ? "s" : "") .' <span class="count">(%d)</span></a>', 'draft'),
                admin_url('edit.php?post_status=draft&post_type=post'),
                $result->found_posts);
        elseif( $type['status'] == 'pending' ):
            $class = ($wp_query->query_vars['post_status'] == 'pending') ? ' class="current"' : '';
            $views['pending'] = sprintf(__('<a href="%s"'. $class .'>Pending <span class="count">(%d)</span></a>', 'pending'),
                admin_url('edit.php?post_status=pending&post_type=post'),
                $result->found_posts);
        elseif( $type['status'] == 'trash' ):
            $class = ($wp_query->query_vars['post_status'] == 'trash') ? ' class="current"' : '';
            $views['trash'] = sprintf(__('<a href="%s"'. $class .'>Trash <span class="count">(%d)</span></a>', 'trash'),
                admin_url('edit.php?post_status=trash&post_type=post'),
                $result->found_posts);
        endif;
    }
    return $views;
}

// Fix media counts
function fix_media_counts($views) {
    global $wpdb, $current_user, $post_mime_types, $avail_post_mime_types;
    $views = array();
    $count = $wpdb->get_results( "
        SELECT post_mime_type, COUNT( * ) AS num_posts 
        FROM $wpdb->posts 
        WHERE post_type = 'attachment' 
        AND post_author = $current_user->ID 
        AND post_status != 'trash' 
        GROUP BY post_mime_type
    ", ARRAY_A );
    foreach( $count as $row )
        $_num_posts[$row['post_mime_type']] = $row['num_posts'];
    $_total_posts = array_sum($_num_posts);
    $detached = isset( $_REQUEST['detached'] ) || isset( $_REQUEST['find_detached'] );
    if ( !isset( $total_orphans ) )
        $total_orphans = $wpdb->get_var("
            SELECT COUNT( * ) 
            FROM $wpdb->posts 
            WHERE post_type = 'attachment' 
            AND post_author = $current_user->ID 
            AND post_status != 'trash' 
            AND post_parent < 1
        ");
    $matches = wp_match_mime_types(array_keys($post_mime_types), array_keys($_num_posts));
    foreach ( $matches as $type => $reals )
        foreach ( $reals as $real )
            $num_posts[$type] = ( isset( $num_posts[$type] ) ) ? $num_posts[$type] + $_num_posts[$real] : $_num_posts[$real];
    $class = ( empty($_GET['post_mime_type']) && !$detached && !isset($_GET['status']) ) ? ' class="current"' : '';
    $views['all'] = "<a href='upload.php'$class>" . sprintf( __('All <span class="count">(%s)</span>', 'uploaded files' ), number_format_i18n( $_total_posts )) . '</a>';
    foreach ( $post_mime_types as $mime_type => $label ) {
        $class = '';
        if ( !wp_match_mime_types($mime_type, $avail_post_mime_types) )
            continue;
        if ( !empty($_GET['post_mime_type']) && wp_match_mime_types($mime_type, $_GET['post_mime_type']) )
            $class = ' class="current"';
        if ( !empty( $num_posts[$mime_type] ) )
            $views[$mime_type] = "<a href='upload.php?post_mime_type=$mime_type'$class>" . sprintf( translate_nooped_plural( $label[2], $num_posts[$mime_type] ), $num_posts[$mime_type] ) . '</a>';
    }
    $views['detached'] = '<a href="upload.php?detached=1"' . ( $detached ? ' class="current"' : '' ) . '>' . sprintf( __( 'Unattached <span class="count">(%s)</span>', 'detached files' ), $total_orphans ) . '</a>';
    return $views;
}