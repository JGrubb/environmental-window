<?php

/** add stylesheet **/
function add_yd_rpw_stylesheet() {
	$options = get_option('widget_yd_rp');
	$i = 0;
	if( $options[$i]["load_css"] ) {
		$plugin_dir = 'yd-recent-posts-widget';
	    $myStyleUrl = WP_PLUGIN_URL . '/' . $plugin_dir . '/css/yd_rp.css';
	    $myStyleFile = WP_PLUGIN_DIR . '/' . $plugin_dir . '/css/yd_rp.css';
	    if ( file_exists( $myStyleFile ) ) {
	        wp_register_style( 'myStyleSheets', $myStyleUrl );
	        wp_enqueue_style( 'myStyleSheets' );
	    }
	}
}
add_action('wp_print_styles', 'add_yd_rpw_stylesheet');

/** Display with PHP outside widget functions **/
function display_yd_recent_posts_here( $args = TRUE, $cache_name = NULL, $spec_query = NULL ) {
	$html = '';
	//$html .= '<ul>';
	//echo 'debug: $html .= widget_yd_rp( ' . $args . ', ' . $cache_name . ', ' . $spec_query . ' );<br/>';
	$html .= widget_yd_rp( $args, $cache_name, $spec_query );
	//$html .= '</ul>';
	if( is_array( $args ) ) {
		parse_str( $args, $my_args );
		$echo = $my_args['echo'];
		//$options[$i] = array_merge( $options[$i], $args ); // parameter overloading
	} else {
		if( $args === true ) $echo = true;
		if( $args === false ) $echo = false;
	}
	if( isset( $echo ) && $echo !== FALSE ) {
		echo $html;
	} else {
		return $html;
	}
}

function display_yd_recent_posts_shortcode( $args ) {
	$args['echo'] = false;
	return widget_yd_rp( $args );
}
add_shortcode('yd_list_posts', 'display_yd_recent_posts_shortcode');

function display_yd_previous_posts_here( $arg = 0 ) {
	global $paged;
	if( is_numeric( $arg ) ) {
		$nb_posts = $arg;
		if( $nb_posts === 0 ) $nb_posts = get_option( 'posts_per_page');
		$spec_query = 'post_type=post&showposts=' . $nb_posts . '&offset=' . ( get_option( 'posts_per_page') * $paged );
	} else {
		$spec_query = $arg;
	}
	//echo 'debug: display_yd_recent_posts_here( TRUE, hometemplate' . $paged . ', ' . $spec_query . ');<br/>';
	display_yd_recent_posts_here( TRUE, 'hometemplate' . $paged, $spec_query );
}

function display_yd_previous_posts_shortcode( $args ) {
	$args['echo'] = false;
	return display_yd_previous_posts_here( $args );
}
add_shortcode('display_yd_previous_posts_here', 'display_yd_previous_posts_shortcode');

/** Display inside content: DEPRECATED! use shortcode  **/
function yd_recent_posts_generate( $content ) {
	if (strpos($content, "<!-- YDRPW -->") !== FALSE) {
		$content = preg_replace('/<p>\s*<!--(.*)-->\s*<\/p>/i', "<!--$1-->", $content);
		$content = str_replace('<!-- YDRPW -->', display_yd_recent_posts_here( FALSE ), $content);
	}
	return $content;
}
add_filter('the_content', 'yd_recent_posts_generate');

function yd_rp_linkware() {
	$options = get_option( 'widget_yd_rp' );
	$i = 0;
	if( $options[$i]['disable_backlink'] ) echo "<!--\n";
	echo '<p style="text-align:center" class="yd_linkware"><small><a href="http://www.yann.com/en/wp-plugins/yd-recent-posts-widget">Featuring Recent Posts WordPress Widget development by YD</a></small></p>';
	if( $options[$i]['disable_backlink'] ) echo "\n-->";
}
add_action('wp_footer', 'yd_rp_linkware');

?>