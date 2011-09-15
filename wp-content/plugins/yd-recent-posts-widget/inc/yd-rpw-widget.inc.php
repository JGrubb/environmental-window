<?php

/** Widget function: previous posts **/
function widget_yd_pp( $args, $cache_name = NULL, $spec_query = NULL ) {
	global $paged;
	if( $nb_posts === 0 ) $nb_posts = get_option( 'posts_per_page');
	$spec_query = 'post_type=post&showposts=' . $nb_posts . '&offset=' . ( get_option( 'posts_per_page') * $paged );
	widget_yd_rp( TRUE, 'previoustemplate' . $paged, $spec_query );
}

/** 
 * Widget function: recent posts 
 * Optional parameters can be passed as array in the first argument 
 * to overload default settings: 
 * 
 * title
 * spec_query - specific WP Query (can also be passed as string in 3rd argument)
 * cache_name - a bit deprecated (can also be passed as string in 2nd argument)
 * meta_field
 * title_cut
 * excerpt_cut
 * use_wpexcerpt
 * display_thumb
 * display_post_title
 * display_date
 * date_format
 * display_excerpt
 * bottom_text
 * bottom_link
 * image_style
 * default_image
 * strip_sqbt
 * strip_clbt
 * keep_html
 * display_ul
 * ellipsis_string
 * 
 ***/
function widget_yd_rp( $args = TRUE, $cache_name = NULL, $spec_query = NULL ) {
	if( isset( $args ) && $args === FALSE ) {
		$echo = FALSE;
	} else {
		if( is_array( $args ) ) extract( $args );
		$echo = TRUE;
	}
	//var_dump( $args );
	global $wpdb;
	global $user_level;
	global $more;
	$plugin_dir = 'yd-recent-posts-widget';
	$options = get_option('widget_yd_rp');
	$default_cutlength = $options[0]['default_cutlength'];
	$current_querycount = get_num_queries();
	$html = '';
	$i = (int)str_replace( 'wydrp-', '', $args['widget_id'] );
	if( !$i ) $i = 1;
	if( is_admin() ) return;
	if( $spec_query || is_home() || $options[$i]["same_opages"] ) {
		//echo "HOME<br/>";
		$title = $options[$i]["home_title"];
		if( $options[$i]["home_tag"] ) {
			$wp_query_string = "post_type=post&showposts=" . $options[$i]["home_showposts"]
			. "&offset=0&tag=" . $options[$i]["home_tag"];
		} else {
			$nb_to_skip = get_option('posts_per_page');
			$my_query = "post_type=post&showposts=" . $options[$i]["home_showposts"];
			if( is_home() && $options[0]["skip_latest"] ) $my_query .= "&offset=" . $nb_to_skip;
			$wp_query_string = $my_query;
		}
		if ( $options[$i]["home_datemeta"] ) {
			$date_type = $options[$i]["home_datemeta"];
		} else {
			$date_type = 'post_date';
		}
		$bottom_text = $options[$i]["home_bottomtext"];
		$bottom_link = $options[$i]["home_bottomlink"];
		$list_type = 'home';
		$title_cutlength = ( isset( $options[$i]["home_title_cutlength"] )
							&& $options[$i]["home_title_cutlength"] != '' ) ? 
								$options[$i]["home_title_cutlength"] : 
								$default_cutlength;
		$abstract_cutlength = ( isset( $options[$i]["home_abstract_cutlength"] )
							&& $options[$i]["home_abstract_cutlength"] != ''  ) ? 
								$options[$i]["home_abstract_cutlength"] : 
								$default_cutlength;
		$use_wp_excerpt = $options[$i]["home_u_wpabstract"];
		$display_thumb = $options[$i]['home_d_post_thumb'];
		$display_post_title = $options[$i]['home_d_post_title'];
		$display_date = $options[$i]['home_d_post_date'];
		$date_format = $options[$i]['home_date_format'];
		$display_excerpt = $options[$i]['home_d_abstract'];
		if( $options[$i]['home_add_query'] ) $wp_query_string .= '&' . $options[$i]['home_add_query'];
	} else {
		//echo "PAGE<br/>";
		$title = $options[$i]["opage_title"];
		$wp_query_string = "post_type=post&showposts=" . $options[$i]["opage_showposts"] 
		. "&offset=0&tag=" . $options[$i]["opage_tag"];
		$date_type = 'post_date';
		$bottom_text = $options[$i]["opage_bottomtext"];
		$bottom_link = $options[$i]["opage_bottomlink"];
		$list_type = 'page';
		$title_cutlength = ( isset( $options[$i]["opage_title_cutlength"] )
							&& $options[$i]["opage_title_cutlength"] != '' ) ? 
								$options[$i]["opage_title_cutlength"] : 
								$default_cutlength;
		$abstract_cutlength = ( isset( $options[$i]["opage_abstract_cutlength"] )
							&& $options[$i]["opage_abstract_cutlength"] != '' ) ?
								$options[$i]["opage_abstract_cutlength"] : 
								$default_cutlength;
		$use_wp_excerpt = $options[$i]["opage_u_wpabstract"];
		$display_thumb = $options[$i]['opage_d_post_thumb'];
		$display_post_title = $options[$i]['opage_d_post_title'];
		$display_date = $options[$i]['opage_d_post_date'];
		$date_format = $options[$i]['opage_date_format'];
		$display_excerpt = $options[$i]['opage_d_abstract'];
		if( $options[$i]['opage_add_query'] ) $wp_query_string .= '&' . $options[$i]['opage_add_query'];
	}

	/** deprecated since v.3.0.0: ** /
	$title_cutlength = $options[$i]["title_cutlength"] ? $options[$i]["title_cutlength"] : $default_cutlength;
	$abstract_cutlength = $options[$i]["abstract_cutlength"] ? $options[$i]["abstract_cutlength"] : $default_cutlength;
	/** **/
	$image_style = $options[$i]["image_style"];
	$default_image = $options[$i]["default_image"];
	$load_css = $options[$i]["load_css"]; // widget-level option (deprecated in 3.0.0)
	if( $options[0]["load_css"] ) $load_css = false; // plugin-level option precedes.
	$strip_sqbt = $options[0]['strip_sqbt'];
	$strip_clbt = $options[0]['strip_clbt'];
	$keep_html = $options[0]['keep_html'];
	$display_ul = $options[0]["display_ul"];
	$ellipsis_string = $options[0]['ellipsis_string'];
	
	/**
	 * function parameter overload (new in v.3.0.0)
	 * 
	 */
	if( $args['title'] ) $title = $args['title'];
	if( $args['spec_query'] ) $spec_query = $args['spec_query'];
 	if( $args['cache_name'] ) $cache_name = $args['cache_name'];
 	if( $args['meta_field'] ) $date_type =  $args['meta_field'];
 	if( $args['title_cut'] ) $title_cutlength = $args['title_cut'];
 	if( $args['excerpt_cut'] ) $abstract_cutlength = $args['excerpt_cut'];
 	if( $args['use_wpexcerpt'] ) $use_wp_excerpt = $args['use_wpexcerpt'];
 	if( $args['display_thumb'] ) $display_thumb = $args['display_thumb'];
 	if( $args['display_post_title'] ) $display_post_title = $args['display_post_title'];
 	if( $args['display_date'] ) $display_date = $args['display_date'];
  	if( $args['date_format'] ) $date_format = $args['date_format'];
  	if( $args['display_excerpt'] ) $display_excerpt = $args['display_excerpt'];
  	if( $args['bottom_text'] ) $bottom_text = $args['bottom_text'];
  	if( $args['bottom_link'] ) $bottom_link = $args['bottom_link'];
  	if( $args['image_style'] ) $image_style = $args['image_style'];
  	if( $args['default_image'] ) $default_image = $args['default_image'];
  	if( $args['strip_sqbt'] ) $strip_sqbt = $args['strip_sqbt'];
  	if( $args['strip_clbt'] ) $strip_clbt = $args['strip_clbt'];
   	if( $args['keep_html'] ) $keep_html = $args['keep_html'];
   	if( $args['display_ul'] ) $display_ul = $args['display_ul'];
   	if( $args['ellipsis_string'] ) $ellipsis_string = $args['ellipsis_string'];

   	if( $spec_query ) {
		//echo 'debug: spec_query: <pre>'; var_dump( $spec_query ); echo '</pre>';
		//echo 'debug: echo: ' . $echo . '<br/>';
		$wp_query_string = $spec_query;
	}
   	
 	//specific overloaded usage within templates... (new in 0.7) -- deprecated?
	if( $cache_name ) {
		$list_type = $cache_name;
		$title = '';
		$date_type = 'post_date';
		$bottom_text = '';
		$bottom_link = '';
	}
	//

	$wp_query_string = apply_filters( 'yd_rp_query_filter', $wp_query_string );
	//echo "debug: wpqs: " . $wp_query_string . '<br/>';
	//var_dump( $wp_query_string );
	$my_wp_query = new WP_Query( $wp_query_string );
	$more = 0;
	if( is_array( $wp_query_string ) ) $wp_query_string = http_build_query( (array) $wp_query_string );
	$cache_key = md5( $wp_query_string . http_build_query( (array) $args ) );
	//echo 'debug: qu:' . $wp_query_string . '<br/>';
	//echo 'debug: cc:' . $cache_key . '<br/>';
	if( !check_yd_widget_cache( 'widget_yd_rp_' . $cache_key ) ) {
		//query_posts( $my_wp_query );
		$html .= $before_widget;
		/** this way of loading the css is deprecated since plugin v.3.0.0 -- we now use enqueue script
		 * 	and the option is now a plugin-level option [0] (used to be widget-level [1])
		 */
		if( $load_css )
			$html .= '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/' . $plugin_dir . '/css/yd_rp.css" />';
		if( $title )
		$html .= $before_title . $title . $after_title;
		$html .= '<div class="yd_rp_widget">';
		if ( $my_wp_query->have_posts() ) {
			//echo 'debug: have posts!<br/>';
			if( $display_ul ) $html .= '<ul>';
			while ( $my_wp_query->have_posts() ) {
				$my_wp_query->the_post();
				/** new in 0.7: make sure not to display private posts **/
				if( get_post_status() != 'publish' ) continue;
				$post = get_post( get_the_id() );
				if( $post->post_password != '' ) continue;

				// -- thumbnails ! --
				$link = '<a href="' . get_permalink() .'" rel="bookmark" title="' . __('Permanent link to:', 'yd-recent-posts-widget') . ' ' . get_the_title( '', '', FALSE ) . '">';
				$tn_url = false;
				
				if( $display_thumb ) {
					//WP2.9+
				    // If WordPress 2.9 or above and a Post Thumbnail has been specified
				    if ( $options[0]['use_wpthumb'] 
				    		&& ( function_exists( 'has_post_thumbnail' ) ) 
				    		&& ( has_post_thumbnail( $post->ID ) ) ) {
				        $tn = get_the_post_thumbnail( $post->ID );
				     	if( preg_match( '/src="([^"]+)"/i', $tn, $matches ) ) $tn_url = $matches[1];
				    }
					
					//WP2.7 (?)				
					if( !$tn_url ) {
						$values = get_post_custom_values("thumb");
						$tn_url = $values[0];
					}
					
					//Get first attachment
					if( !$tn_url ) $tn_url = $wpdb->get_var( "SELECT guid FROM " . $wpdb->posts . " WHERE post_type='attachment' and post_mime_type like 'image/%' and post_parent = " . get_the_id() . " and guid != '' LIMIT 1" );
	
					//Try to find first image in html
					if( !$tn_url ) {
						preg_match( '/<img[^>]+src=[\'"]([^\'"]+)[\'"]/', get_the_content(), $matches );
						$tn_url = $matches[1];
					}
	
					if( $options[0]['use_timthumb'] ) {
						if( $tn_url ) {
							$tn_url = preg_replace( "|^http://[^\/]+|", '', $tn_url ); // strip original domain (timthumb does not do external fetches)
							$tn_url = $options[0]['timthumb_path'] . '?src=' . $tn_url . 
							'&amp;h=' . $options[0]['timthumb_height'] . 
							'&amp;w=' . $options[0]['timthumb_width'] . '&amp;zc=1&amp;q=100';
						}
					} else {
						//WP2.0+ (wp-admin/includes/image.php:71-:75) .thumbnail file extension
						$extension = '.thumbnail';
						$tn_url = yd_check_thumbpath( $tn_url, $extension );
		
						//WP2.5+
						$extension = "-" . get_option( 'thumbnail_size_w' ) . 'x' . get_option( 'thumbnail_size_h' );
						$tn_url = yd_check_thumbpath( $tn_url, $extension );
					}
					
					if( !$tn_url ) $tn_url = $default_image;
				} // if( $display_thumb )
				
				if( $display_ul ) $html .= '<li>';
				$html .= '<h4>';
				$html .= $link;
				if( $display_thumb ) $html .= '<img src="' . $tn_url . '" style="' . $image_style . '" alt="' . get_the_title() . '" />';
				//$html .= '</a>';
				if( $use_wp_excerpt && function_exists( 'get_the_excerpt' ) && get_the_excerpt() ) {
					$cont = get_the_excerpt();
				} else {
					$cont = get_the_content( '', '' );
				}
				if( $strip_sqbt ) $cont = preg_replace( "/\[[^\]]+\]/", '', $cont );
				if( $strip_clbt ) $cont = preg_replace( "/{[^}]+}/", '', $cont );
				if( $keep_html ) {
					$cont = preg_replace( "/<img[^>]+>/i", '', $cont ); // get rid of images in excerpt
					if( $abstract_cutlength === 0 || $abstract_cutlength === '0' ) {
						$summary = $cont;
					} else {
						$summary = yd_cake_truncate( $cont, $abstract_cutlength, '', false, true );
					}
				} else {
					if( $abstract_cutlength === 0 || $abstract_cutlength === '0' ) {
						$summary = strip_tags( $cont );
					} else {
						$summary = yd_clean_cut( $cont, $abstract_cutlength );
					}
				}
				if( $display_date ) {
					if( $date_type != 'post_date' ) {
						$date = get_post_meta( get_the_id(), $date_type, true );
					} else {
						$date = get_the_time( $date_format );
					}
					$date .= __(':', 'yd-recent-posts-widget') . ' ';
				} else {
					$date = '';
				}
				if( $display_post_title ) {
					$post_title = get_the_title();
				} else {
					$post_title = '';
				}
				//$html .= '<a href="' . get_permalink() . '" rel="bookmark" title="' . get_the_title() . '">';
				if( $title_cutlength === 0 || $title_cutlength === '0' ) {
					$html .= $date . strip_tags( $post_title );
				} else {
					$html .= yd_clean_cut( ( $date . $post_title ), $title_cutlength );
				}
				$html .= '</a></h4>';
				if( $display_excerpt )
					$html .= '<div class="yd_rp_excerpt">' . $summary . $link 
							. $ellipsis_string . '</a></div>';
				if( $display_ul ) {
					$html .= '</li>';
				} else {
					$html .= '<br clear="all" />';
				}
			}
			if( $display_ul ) $html .= '</ul>';
		} else {
			//echo 'debug: no posts found for: ' . $wp_query_string . '<br/>';
		}
		$html .= '<a class="rpw_bottom_link" href="' . $bottom_link . '">' . $bottom_text . '</a>';
		$html .= '</div>' . $after_widget;
		update_yd_widget_cache( 'widget_yd_rp_' . $cache_key, $html );
	} else {
		//echo "debug: FROM CACHE<br/>";
		$html = get_yd_widget_cache( 'widget_yd_rp_' . $cache_key );
	}
	//if( $user_level > 0 ) $html .= ( get_num_queries() - $current_querycount ) . '&nbsp;queries.<br />';
	if( $echo ) {
		echo $html;
	} else {
		return $html;
	}
}


/** Widget options **/
function widget_yd_rp_control( $args, $vars = array() ) {
	$number = $args['number'];
	if( $number < 1 ) $number = 1;
	//echo 'debug: widget number ' . $number . '<br/>';
	$options = get_option( 'widget_yd_rp' );
	$to_update = Array(
		'home_title',
		'home_tag',
		'home_showposts',
		'home_datemeta',
		'home_bottomtext',
		'home_bottomlink',
			//new widget options since plugin v.3.0.0
		'home_d_post_thumb',
		'home_d_post_title',
		'home_d_post_date',
		'home_d_abstract',
		'home_u_wpabstract',
		'home_title_cutlength',
		'home_abstract_cutlength',
		'home_date_format',
		'home_add_query',
	
		'same_opages',

		'opage_title',
		'opage_tag',
		'opage_showposts',
		'opage_bottomtext',
		'opage_bottomlink',
			//new widget options since plugin v.3.0.0
		'opage_d_post_thumb',
		'opage_d_post_title',
		'opage_d_post_date',
		'opage_d_abstract',
		'opage_u_wpabstract',
		'opage_title_cutlength',
		'opage_abstract_cutlength',
		'opage_date_format',
		'opage_datemeta',
		'opage_add_query',
	
			//old options deprecated since plugin v.3.0.0
		'title_cutlength',		//deprecated -> home/page level
		'abstract_cutlength',	//deprecated -> home/page level
		'load_css',				//deprecated -> plugin level 0
		'image_style',			//deprecated -> plugin level 0
		'default_image',		//deprecated -> plugin level 0
		'display_date',			//deprecated -> home/page level
		'date_format'			//deprecated -> home/page level
	);
	if ( $_POST["yd_rp-submit-$number"] ) {
		if( yd_update_options( 'widget_yd_rp', $number, $to_update, $_POST, 'yd_rp-' ) ) {
			clear_yd_widget_cache( 'widget_yd_rp_home' );
			clear_yd_widget_cache( 'widget_yd_rp_page' );
			clear_yd_widget_cache( 'widget_yd_rp_hometemplate1' ); // TODO? Clear other pages template cache?
			$options = get_option( 'widget_yd_rp' );
		}
	}
	foreach( $to_update as $key ) {
		$v[$key] = htmlspecialchars( $options[$number][$key], ENT_QUOTES );
	}
	?>
	<div style="float: right"><a
	href="http://www.yann.com/en/wp-plugins/yd-recent-posts-widget"
	title="Help!" target="_blank">?</a></div>
	
	<?php // =========================== Home options =================================== ?>
	
	<h5><?php  echo __( 'Home page widget options:', 'yd-recent-posts-widget' ) ?></h5>
	<p>
		<label for="yd_rp-home_title-<?php echo "$number"; ?>">
			<?php echo __('Title: <em>(optional - leave blank for no title)</em>', 'yd-recent-posts-widget' ) ?>
		</label>
		<input
			class="widefat" 
			id="yd_rp-home_title-<?php echo "$number"; ?>"
			name="yd_rp-home_title-<?php echo "$number"; ?>" 
			type="text"
			value="<?php echo $v['home_title']; ?>" />
	</p>
	<p>
		<label for="yd_rp-home_tag-<?php echo "$number"; ?>">
			<?php echo __('Only show posts with this tag:', 'yd-recent-posts-widget') ?>
		</label>
		<input 
			id="yd_rp-home_tag-<?php echo "$number"; ?>"
			name="yd_rp-home_tag-<?php echo "$number"; ?>" 
			type="text"
			size="10"
			value="<?php echo $v['home_tag']; ?>" />
			<em>(
			<?php echo __( 'optional - leave blank for all', 'yd-recent-posts-widget' ) ?>
			)</em>
	</p>
	<p>
		<label for="yd_rp-home_showposts-<?php echo "$number"; ?>">
			<?php echo __('# no. of posts to show:', 'yd-recent-posts-widget') ?>
		</label>
		<input
			id="yd_rp-home_showposts-<?php echo "$number"; ?>"
			name="yd_rp-home_showposts-<?php echo "$number"; ?>" 
			type="text"
			size = "3"
			value="<?php echo $v['home_showposts']; ?>" />
	</p>
	<p>
		<label for="yd_rp-home_d_post_thumb-<?php echo "$number"; ?>">
			<?php echo __('Display post thumbnail:', 'yd-recent-posts-widget') ?>
		</label>
		<input
			id="yd_rp-home_d_post_thumb-<?php echo "$number"; ?>"
			name="yd_rp-home_d_post_thumb-<?php echo "$number"; ?>"
			type="checkbox"
			value="1"
			<?php if( $v['home_d_post_thumb'] ) echo "checked=\"checked\""; ?> />
	</p>		
	<p>
		<label for="yd_rp-home_d_post_title-<?php echo "$number"; ?>">
			<?php echo __('Display post title:', 'yd-recent-posts-widget') ?>
		</label>
		<input
			id="yd_rp-home_d_post_title-<?php echo "$number"; ?>"
			name="yd_rp-home_d_post_title-<?php echo "$number"; ?>"
			type="checkbox"
			value="1"
			<?php if( $v['home_d_post_title'] ) echo "checked=\"checked\""; ?> />
	</p>
	<p>
		<label for="yd_rp-home_title_cutlength-<?php echo "$number"; ?>">
			<?php echo __('Title cut length:', 'yd-recent-posts-widget') ?>
		</label>
		<input
			id="yd_rp-home_title_cutlength-<?php echo "$number"; ?>"
			name="yd_rp-home_title_cutlength-<?php echo "$number"; ?>"
			type="text"
			size="3"
			value="<?php echo $v['home_title_cutlength']; ?>" />
		<em>(
		<?php echo __('# of characters to keep. 0 = keep all.' ) ?>
		)</em>
	</p>
	<p>
		<label for="yd_rp-home_d_post_date-<?php echo "$number"; ?>">
			<?php echo __('Display post date:', 'yd-recent-posts-widget') ?>
		</label>
		<input
			id="yd_rp-home_d_post_date-<?php echo "$number"; ?>"
			name="yd_rp-home_d_post_date-<?php echo "$number"; ?>"
			type="checkbox"
			value="1"
			<?php if( $v['home_d_post_date'] ) echo "checked=\"checked\""; ?> />
	</p>
	<p>
		<label for="yd_rp-home_date_format-<?php echo "$number"; ?>">
			<?php echo __('Date format:', 'yd-recent-posts-widget') ?>
		</label>
		<input
			id="yd_rp-home_date_format-<?php echo "$number"; ?>"
			name="yd_rp-home_date_format-<?php echo "$number"; ?>"
			type="text"
			size="10"
			value="<?php echo $v['home_date_format']; ?>" />
			<em>(
			<?php echo __('Use Php date format string', 'yd-recent-posts-widget') ?>
			)</em>
	</p>
	<p>
		<label for="yd_rp-home_datemeta-<?php echo "$number"; ?>">
			<?php echo __('Display meta:', 'yd-recent-posts-widget') ?>
		</label>
		<input
			id="yd_rp-home_datemeta-<?php echo "$number"; ?>"
			name="yd_rp-home_datemeta-<?php echo "$number"; ?>" 
			type="text"
			size="10"
			value="<?php echo $v['home_datemeta']; ?>" />
			<em>(
			<?php echo __('optional - additional post meta field to display', 'yd-recent-posts-widget') ?>
			)</em>
	</p>
	<p>
		<label for="yd_rp-home_d_abstract-<?php echo "$number"; ?>">
			<?php echo __('Display post excerpt:', 'yd-recent-posts-widget') ?>
		</label>
		<input
			id="yd_rp-home_d_abstract-<?php echo "$number"; ?>"
			name="yd_rp-home_d_abstract-<?php echo "$number"; ?>"
			type="checkbox"
			value="1"
			<?php if( $v['home_d_abstract'] ) echo "checked=\"checked\""; ?> />
	</p>
	<p>
		<label for="yd_rp-home_u_wpabstract-<?php echo "$number"; ?>">
			<?php echo __('Use WP excerpt field:', 'yd-recent-posts-widget') ?>
		</label>
		<input
			id="yd_rp-home_u_wpabstract-<?php echo "$number"; ?>"
			name="yd_rp-home_u_wpabstract-<?php echo "$number"; ?>"
			type="checkbox"
			value="1"
			<?php if( $v['home_u_wpabstract'] ) echo "checked=\"checked\""; ?> />
	</p>
	<p>
		<label for="yd_rp-home_abstract_cutlength-<?php echo "$number"; ?>">
			<?php echo __('Excerpt cut length:', 'yd-recent-posts-widget') ?>
		</label>
		<input
			id="yd_rp-home_abstract_cutlength-<?php echo "$number"; ?>"
			name="yd_rp-home_abstract_cutlength-<?php echo "$number"; ?>"
			type="text"
			size="3"
			value="<?php echo $v['home_abstract_cutlength']; ?>" />
		<em>(
			<?php echo __( "# of characters to keep. 0 = cut at 'more'" ) ?>
		)</em>
	</p>
	<p>
		<label for="yd_rp-home_add_query-<?php echo "$number"; ?>">
			<?php echo __('Additional query parameters:', 'yd-recent-posts-widget') ?>
		</label>
		<input
			id="yd_rp-home_add_query-<?php echo "$number"; ?>"
			name="yd_rp-home_add_query-<?php echo "$number"; ?>"
			type="text"
			size="20"
			value="<?php echo $v['home_add_query']; ?>" />
		<em>(
			<?php echo __( "optional", 'yd-recent-posts-widget' ) ?>
		)</em>
	</p>
	<p>
		<label for="yd_rp-home_bottomtext-<?php echo "$number"; ?>">
			<?php echo __('Bottom text:', 'yd-recent-posts-widget') ?>
		</label>
		<input
			class="widefat"
			id="yd_rp-home_bottomtext-<?php echo "$number"; ?>"
			name="yd_rp-home_bottomtext-<?php echo "$number"; ?>" 
			type="text"
			value="<?php echo $v['home_bottomtext']; ?>" />
	</p>
	<p>
		<label for="yd_rp-home_bottomlink-<?php echo "$number"; ?>">
			-
			<?php echo __('link:', 'yd-recent-posts-widget') ?>
		</label>
		<input
			class="widefat"
			id="yd_rp-home_bottomlink-<?php echo "$number"; ?>"
			name="yd_rp-home_bottomlink-<?php echo "$number"; ?>"
			type="text"
			value="<?php echo $v['home_bottomlink']; ?>" />
	</p>
<hr />

	<?php // =========================== Pages options =================================== ?>
	
	<?php
		if( $v['same_opages'] ) {
			$disp_opages = 'none';
			$visi_opages = 'hidden';
		} else {
			$disp_opages = 'block';
			$visi_opages = 'visible';
		}
	?>
	<script type="text/javascript">
	function yd_toggle_opages( ck ) {
		var dv = document.getElementById( 'yd_opages_settings-<?php echo $number ?>' );
		if( ck ) {
			dv.style.display = 'none';
			dv.style.visibility = 'hidden';
		} else {
			dv.style.display = 'block';
			dv.style.visibility = 'visible';
		}
	} 
	</script>
	<p>
		<label for="yd_rp-same_opages-<?php echo "$number"; ?>">
			<?php echo __('Same settings on other pages:', 'yd-recent-posts-widget') ?>
		</label>
		<input
			id="yd_rp-same_opages-<?php echo "$number"; ?>"
			name="yd_rp-same_opages-<?php echo "$number"; ?>"
			type="checkbox"
			value="1"
			onclick="yd_toggle_opages(this.checked);"
			<?php if( $v['same_opages'] ) echo "checked=\"checked\""; ?> />
	</p>
	
<div id="yd_opages_settings-<?php echo $number ?>" style="display:<?php echo $disp_opages ?>;visibility:<?php echo $visi_opages ?>;">
	<h5><?php  echo __( 'Other pages widget options:', 'yd-recent-posts-widget' ) ?></h5>
	<p>
		<label for="yd_rp-opage_title-<?php echo "$number"; ?>">
			<?php echo __('Title: <em>(optional - leave blank for no title)</em>', 'yd-recent-posts-widget') ?>
		</label>
		<input
			class="widefat" 
			id="yd_rp-opage_title-<?php echo "$number"; ?>"
			name="yd_rp-opage_title-<?php echo "$number"; ?>" 
			type="text"
			value="<?php echo $v['opage_title']; ?>" />
	</p>
	<p>
		<label for="yd_rp-opage_tag-<?php echo "$number"; ?>">
			<?php echo __('Only show posts with this tag:', 'yd-recent-posts-widget') ?>
		</label>
		<input
			id="yd_rp-opage_tag-<?php echo "$number"; ?>"
			name="yd_rp-opage_tag-<?php echo "$number"; ?>" 
			type="text"
			size="10"
			value="<?php echo $v['opage_tag']; ?>" />
			<em>(
			<?php echo __( 'optional - leave blank for all', 'yd-recent-posts-widget' ) ?>
			)</em>
	</p>
	<p>
		<label for="yd_rp-opage_showposts-<?php echo "$number"; ?>">
			<?php echo __('# no. of posts to show:', 'yd-recent-posts-widget') ?>
		</label>
		<input
			id="yd_rp-opage_showposts-<?php echo "$number"; ?>"
			name="yd_rp-opage_showposts-<?php echo "$number"; ?>"
			type="text"
			size="3"
			value="<?php echo $v['opage_showposts']; ?>" />
	</p>
	<p>
		<label for="yd_rp-opage_d_post_thumb-<?php echo "$number"; ?>">
			<?php echo __('Display post thumbnail:', 'yd-recent-posts-widget') ?>
		</label>
		<input
			id="yd_rp-opage_d_post_thumb-<?php echo "$number"; ?>"
			name="yd_rp-opage_d_post_thumb-<?php echo "$number"; ?>"
			type="checkbox"
			value="1"
			<?php if( $v['opage_d_post_thumb'] ) echo "checked=\"checked\""; ?> />
	</p>		
	<p>
		<label for="yd_rp-opage_d_post_title-<?php echo "$number"; ?>">
			<?php echo __('Display post title:', 'yd-recent-posts-widget') ?>
		</label>
		<input
			id="yd_rp-opage_d_post_title-<?php echo "$number"; ?>"
			name="yd_rp-opage_d_post_title-<?php echo "$number"; ?>"
			type="checkbox"
			value="1"
			<?php if( $v['opage_d_post_title'] ) echo "checked=\"checked\""; ?> />
	</p>
	<p>
		<label for="yd_rp-opage_title_cutlength-<?php echo "$number"; ?>">
			<?php echo __('Title cut length:', 'yd-recent-posts-widget') ?>
		</label>
		<input
			id="yd_rp-opage_title_cutlength-<?php echo "$number"; ?>"
			name="yd_rp-opage_title_cutlength-<?php echo "$number"; ?>"
			type="text"
			size="3"
			value="<?php echo $v['opage_title_cutlength']; ?>" />
		<em>(
		<?php echo __('# of characters to keep. 0 = keep all.' ) ?>
		)</em>
	</p>
	<p>
		<label for="yd_rp-opage_d_post_date-<?php echo "$number"; ?>">
			<?php echo __('Display post date:', 'yd-recent-posts-widget') ?>
		</label>
		<input
			id="yd_rp-opage_d_post_date-<?php echo "$number"; ?>"
			name="yd_rp-opage_d_post_date-<?php echo "$number"; ?>"
			type="checkbox"
			value="1"
			<?php if( $v['opage_d_post_date'] ) echo "checked=\"checked\""; ?> />
	</p>
	<p>
		<label for="yd_rp-opage_date_format-<?php echo "$number"; ?>">
			<?php echo __('Date format:', 'yd-recent-posts-widget') ?>
		</label>
		<input
			id="yd_rp-opage_date_format-<?php echo "$number"; ?>"
			name="yd_rp-opage_date_format-<?php echo "$number"; ?>"
			type="text"
			size="10"
			value="<?php echo $v['opage_date_format']; ?>" />
			<em>(
			<?php echo __('Use Php date format string', 'yd-recent-posts-widget') ?>
			)</em>
	</p>
	<p>
		<label for="yd_rp-opage_d_abstract-<?php echo "$number"; ?>">
			<?php echo __('Display post excerpt:', 'yd-recent-posts-widget') ?>
		</label>
		<input
			id="yd_rp-opage_d_abstract-<?php echo "$number"; ?>"
			name="yd_rp-opage_d_abstract-<?php echo "$number"; ?>"
			type="checkbox"
			value="1"
			<?php if( $v['opage_d_abstract'] ) echo "checked=\"checked\""; ?> />
	</p>
	<p>
		<label for="yd_rp-opage_u_wpabstract-<?php echo "$number"; ?>">
			<?php echo __('Use WP excerpt field:', 'yd-recent-posts-widget') ?>
		</label>
		<input
			id="yd_rp-opage_u_wpabstract-<?php echo "$number"; ?>"
			name="yd_rp-opage_u_wpabstract-<?php echo "$number"; ?>"
			type="checkbox"
			value="1"
			<?php if( $v['opage_u_wpabstract'] ) echo "checked=\"checked\""; ?> />
	</p>
	<p>
		<label for="yd_rp-opage_abstract_cutlength-<?php echo "$number"; ?>">
			<?php echo __('Excerpt cut length:', 'yd-recent-posts-widget') ?>
		</label>
		<input
			id="yd_rp-opage_abstract_cutlength-<?php echo "$number"; ?>"
			name="yd_rp-opage_abstract_cutlength-<?php echo "$number"; ?>"
			type="text"
			size="3"
			value="<?php echo $v['opage_abstract_cutlength']; ?>" />
		<em>(
			<?php echo __( "# of characters to keep. 0 = cut at 'more'" ) ?>
		)</em>
	</p>
	<p>
		<label for="yd_rp-opage_add_query-<?php echo "$number"; ?>">
			<?php echo __('Additional query parameters:', 'yd-recent-posts-widget') ?>
		</label>
		<input
			id="yd_rp-opage_add_query-<?php echo "$number"; ?>"
			name="yd_rp-opage_add_query-<?php echo "$number"; ?>"
			type="text"
			size="20"
			value="<?php echo $v['opage_add_query']; ?>" />
		<em>(
			<?php echo __( "optional", 'yd-recent-posts-widget' ) ?>
		)</em>
	</p>
	<p>
		<label for="yd_rp-opage_bottomtext-<?php echo "$number"; ?>">
			<?php echo __('Bottom text:', 'yd-recent-posts-widget') ?>
		</label>
		<input
			class="widefat"
			id="yd_rp-opage_bottomtext-<?php echo "$number"; ?>"
			name="yd_rp-opage_bottomtext-<?php echo "$number"; ?>"
			type="text"
			value="<?php echo $v['opage_bottomtext']; ?>" />
	</p>
	<p>
		<label for="yd_rp-opage_bottomlink-<?php echo "$number"; ?>">
			-
			<?php echo __('link:', 'yd-recent-posts-widget') ?>
		</label>
		<input
			class="widefat"
			id="yd_rp-opage_bottomlink-<?php echo "$number"; ?>"
			name="yd_rp-opage_bottomlink-<?php echo "$number"; ?>"
			type="text"
			value="<?php echo $v['opage_bottomlink']; ?>" />
	</p>
</div>

	<?php /** DEPRECATED
	// =========================== Other options (DEPRECATED) ============================= ?>
<hr />
	<?php echo __('Title cut length:', 'yd-recent-posts-widget') ?>
<input style="width: 50px;"
	id="yd_rp-title_cutlength-<?php echo "$number"; ?>"
	name="yd_rp-title_cutlength-<?php echo "$number"; ?>" type="text"
	value="<?php echo $v['title_cutlength']; ?>" />
	<?php echo __('Excerpt cut length:', 'yd-recent-posts-widget') ?>
<input style="width: 50px;"
	id="yd_rp-abstract_cutlength-<?php echo "$number"; ?>"
	name="yd_rp-abstract_cutlength-<?php echo "$number"; ?>" type="text"
	value="<?php echo $v['abstract_cutlength']; ?>" />
<br />
	<?php /** 
	widget-level css inclusion deprecated since plugin v.3.0.0 ** /
	echo __('Load CSS:', 'yd-recent-posts-widget') ?>
<input style="width: 15px;" id="yd_rp-load_css-<?php echo "$number"; ?>"
	name="yd_rp-load_css-<?php echo "$number"; ?>" type="checkbox"
	value="1" <?php if( $v['load_css'] ) echo "checked=\"checked\""; ?> />
	<?php /** ** ?>
	<?php echo __('Image CSS Style:', 'yd-recent-posts-widget') ?>
<input style="width: 450px;"
	id="yd_rp-image_style-<?php echo "$number"; ?>"
	name="yd_rp-image_style-<?php echo "$number"; ?>" type="text"
	value="<?php echo $v['image_style']; ?>" />
<br />
	<?php echo __('Default image URL:', 'yd-recent-posts-widget') ?>
<input style="width: 300px;"
	id="yd_rp-default_image-<?php echo "$number"; ?>"
	name="yd_rp-default_image-<?php echo "$number"; ?>" type="text"
	value="<?php echo $v['default_image']; ?>" />
<br />
	<?php echo __('Display date:', 'yd-recent-posts-widget') ?>
<input style="width: 15px;"
	id="yd_rp-display_date-<?php echo "$number"; ?>"
	name="yd_rp-display_date-<?php echo "$number"; ?>" type="checkbox"
	value="1" <?php if( $v['display_date'] ) echo "checked=\"checked\""; ?> />
	<?php echo __('Date format:', 'yd-recent-posts-widget') ?>
<input style="width: 100px;"
	id="yd_rp-date_format-<?php echo "$number"; ?>"
	name="yd_rp-date_format-<?php echo "$number"; ?>" type="text"
	value="<?php echo $v['date_format']; ?>" />
	/** **/ ?>
<input
	type="hidden" id="yd_rp-submit-<?php echo "$number"; ?>"
	name="yd_rp-submit-<?php echo "$number"; ?>" value="1" />
	<?php
}

function widget_rp_init() {
	// Check for the required API functions
	if ( !function_exists('wp_register_sidebar_widget') || !function_exists('wp_register_widget_control') )
		return;
	// wp_register_sidebar_widget( $id, $name, $output_callback, $options );
	//register_sidebar_widget( __('YD Recent Posts', 'yd-recent-posts-widget'), 'widget_yd_rp' );
	$prefix = 'wydrp';
	$widget_ops = array(
		'classname' => 'widget_yd_rp',
		'description' => __(
			'Displays the recent posts of your site, with automatic thumbnail image.', 
			'yd-recent-posts-widget' 
		)
	);
	$control_ops = array('width' => 470, 'height' => 600, 'id_base' => $prefix);
	$options = get_option('widget_yd_rp');
	$idx = 0;
	foreach( array_keys($options) as $idx ){
		if( !is_numeric( $idx ) || $idx == 0 ) continue; // key 0 stores the plugin-level options
		wp_register_sidebar_widget( 
			$prefix . '-' . $idx, 
			__('YD Recent Posts', 'yd-recent-posts-widget'), 
			'widget_yd_rp', 
			array_merge( $widget_ops, array( 'number' => $idx ) )
		);
		wp_register_widget_control( 
			$prefix . '-' . $idx, 
			__('YD Recent Posts', 'yd-recent-posts-widget'), 
			'widget_yd_rp_control', 
			$control_ops,
			array( 'number' => $idx ) 
		);
		$last_idx = $idx;
	}
	/** **/
	$idx = $last_idx + 1; //always register one more!
	wp_register_sidebar_widget( 
		$prefix . '-' . $idx, 
		__('YD Recent Posts', 'yd-recent-posts-widget'), 
		'widget_yd_rp', 
		array_merge( $widget_ops, array( 'number' => $idx ) ) 
	);
	wp_register_widget_control( 
		$prefix . '-' . $idx, 
		__('YD Recent Posts', 'yd-recent-posts-widget'), 
		'widget_yd_rp_control', 
		$control_ops,
		array( 'number' => $idx )
	);
	/** **/
	//register_widget_control( __('YD Previous Posts', 'yd-recent-posts-widget'), 'widget_yd_rp_control', 470, 470, 1 );
	//register_sidebar_widget( __('YD Previous Posts', 'yd-recent-posts-widget'), 'widget_yd_pp' );
	wp_register_sidebar_widget( 'wydpp1', __('YD Previous Posts', 'yd-recent-posts-widget'), 'widget_yd_pp' );
}

?>