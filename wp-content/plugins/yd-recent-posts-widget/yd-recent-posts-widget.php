<?php
/**
 * @package YD_Recent-Posts-Widget
 * @author Yann Dubois
 * @version 3.0.1
 */

/*
 Plugin Name: YD Recent Posts with thumbnails
 Plugin URI: http://www.yann.com/en/wp-plugins/yd-recent-posts-widget
 Description: Installs a new sidebar widget that can display the recent posts with automatic thumbnail images. Highly customizable allowing different settings on the home page. Uses cache to avoid multiple database queries. You can choose to list all recent posts, or only list recent posts marked with a specific tag. You can display a different selection on the home page and on other pages. You can also insert a list of recent posts or previous posts with thumbnails in your templates and not use the widget.
 Author: Yann Dubois
 Version: 3.0.1
 Author URI: http://www.yann.com/
 */

/**
 * @copyright 2009-2010  Yann Dubois  ( email : yann _at_ abc.fr )
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
/**
 Revision 0.2:
 - In 0.1 the cache sometimes wouldn't expire even when new content was added -> fixed in 0.2
 - I18n + POT + French .mo files
 Revision 0.3:
 - Date display and format are now optional (lines 142-147 of v.0.2)
 Revision 0.4:
 - Initialize / reset default options properly
 - Get default date format from WP options
 - Bugfix: in WP2.7 the widget was called when in admin mode, giving strange results
 - Created/Added a default thumbnail image + set it as default during init
 Revision 0.5:
 - Added function and special tag to display te list outside of a widget
 - Added feature to skip posts already displayed on home page
 Revision 0.6:
 - No warnings in debug mode (hopefully?)
 - Supports pre-existing WordPress 2.0+ and 2.6+ thumbnails
 Revision 0.7:
 - Supports an optional specific cache and settings for usage within templates
 - Added the new "display_yd_previous_posts_here()" function!
 - Fixed WP_query redefinition / is_home() status loss issue
 - Fixed the private post display issue
 Revision 0.8:
 - New option to keep HTML formatting inside the post excerpts
 - New options to get rid of [...] and/or {...} special tags
 - New (default) option to display the list as a set of ul, li tags
 - Previous posts widget (beta)
 Revision 0.8.1:
 - Bugfix foreach() line 448
 - Bugfix warning line 198
 Revision 0.8.2:
 - Bugfix </div> in admin page
 - YD WP functions become generic ( if( !function_exists() ) )
 Revision 0.8.3:
 - Better default stylesheet
 - Bugfix: list sometimes included pages
  Revision 0.8.4:
 - Russian version (credit: FatCow)
 - Updated doc (compatibility)
   Revision 0.8.5:
 - Dutch version (credit: Rene @ Fethiye Hotels ( http://www.fethiyehotels.com ) )
 - Updated doc (compatibility w/ WP 2.9.2)
 Revision 0.9.0 (unsupported)
 - Native Timthumb support
 - Optional disable of home-page skip
 Revision 3.0.0
 - new admin interface
 - linkbackware
 - strip shortcode by default
 - tidy up file hierarchy
 - Wordpress 3.0 full compatibility
 - use WP thumbnail image
 - clear all caches
 - use shortcode
 - default stylesheet to get rid of bullets
 - call stylesheet from head
 - Updated css stylesheet
 - use WP manual excerpt
 - different setting on other pages is an option
 - new widget interface
 - display title / display abstract / display date / display image options
 - choose ellipsis string option
 - show posts from category option -> custom wp_query string option
 - function parameters overload (incl. title)
 - multiple widget - Support multiple instances of the widget
 - if you delete a post, the cache of the recent-post-widget is now renewed
 - default timthumb location
 - Timthumb cache write-permission check
 - links in plugin list menu
 - German version
 - Updated French version
 - Updated readme.txt doc (new format for revisions and upgrades)
 Revision 3.0.1
 - bugfix: on-line image style option did not save
 - bugfix: default image URL option did not save
 - bugfix: translation of the js link disable alert
 - bugfix: array casting in http_build_query() function (yd-rpw-widget.inc.php)
 - bugfix: allow default empty param in widget_yd_rp() function
 */
/**
 *	TODO:
 *  - Randomization? - not good idea, how to cache?
 *  - Option to only list posts of same category as current page
 */

include_once( 'inc/yd-rpw-widget.inc.php' );
if( is_admin() ) {
	include_once( 'inc/yd-rpw-admin.inc.php' );
} else {
	include_once( 'inc/yd-rpw-display.inc.php' );
}

/** Install or reset plugin defaults **/
function yd_rp_plugin_reset( $force ) {
	/** Init values **/
	$yd_rp_plugin_version	= "3.0.1";
	$plugin_dir = 'yd-recent-posts-widget';
	$default_image_width	= 60;
	$default_image_height	= 60;
	$default_cutlength		= 128;
	$default_image_style	= 'padding-right:5px;padding-bottom:5px;float:left;';
	$default_date_format	= __('F j', 'yd-recent-posts-widget');
	$default_bottomlink		= 'http://www.yann.com/en/wp-plugins/yd-recent-posts-widget';
	$default_bottomtext		= '<small>[&rarr;YD Recent Posts Widget]</small>';
	$default_thumbnail_img	= 'http://www.yann.com/yd-recent-posts-widget-v301-logo.gif';
	$newoption				= 'widget_yd_rp';
	$newvalue				= '';
	if( $df = get_option( 'date_format' ) ) $default_date_format = $df;
	/** TODO **/
	//$default_image_width = get_option( 'thumbnail_size_w' ) ? get_option( 'thumbnail_size_w' ) : $default_image_width;
	//$default_image_height = get_option( 'thumbnail_size_h' ) ? get_option( 'thumbnail_size_h' ) : $default_image_height;
	// ...this would need to generate the CSS file dynamically at plugin init
	$default_image_style =	'width:' . $default_image_width . 'px;' .
							'height:' . $default_image_height . 'px;' . $default_image_style;
	$prev_options = get_option( $newoption );
	if( ( isset( $force ) && $force ) || !isset($prev_options['plugin_version']) ) {
		// those default options are set-up at plugin first-install or manual reset only
		// they will not be changed when the plugin is just upgraded or deactivated/reactivated
		// [0] = plugin-level options (applies to all widgets and function defaults)
		// [1] = widget-level (defined for each widget instance)
		$newvalue['plugin_version'] = $yd_rp_plugin_version;
		$newvalue[1]['image_style'] = $default_image_style; // deprecated since v.3.0.0
		$newvalue[0]['image_style'] = $default_image_style; // new since v.3.0.0
		$newvalue[1]['date_format'] = $default_date_format; // deprecated since v.3.0.0
		$newvalue[1]['home_date_format'] = $default_date_format; // new since v.3.0.0
		$newvalue[1]['opage_date_format'] = $default_date_format; // new since v.3.0.0
		$newvalue[1]['home_title'] = __('YD Recent Posts', 'yd-recent-posts-widget');
		$newvalue[1]['opage_title'] = __('YD Recent Posts', 'yd-recent-posts-widget');
		
			// all new since v.3.0.0:
		$newvalue[0]['default_cutlength'] = $default_cutlength;
		$newvalue[0]['ellipsis_string'] = '&hellip;&nbsp;&raquo;';
		$newvalue[1]['home_d_post_thumb'] = 1; 
		$newvalue[1]['home_d_post_title'] = 1;
		$newvalue[1]['home_d_abstract'] = 1;
		
		$newvalue[1]['same_opages'] = 1;
		
		$newvalue[1]['opage_d_post_thumb'] = 1;
		$newvalue[1]['opage_d_post_title'] = 1;
		$newvalue[1]['opage_d_abstract'] = 1;
		$newvalue[1]['opage_bottomlink'] = $default_bottomlink;
		$newvalue[1]['opage_bottomtext'] = $default_bottomtext;
			//
			
		$newvalue[1]['home_bottomlink'] = $default_bottomlink;
		$newvalue[1]['home_bottomtext'] = $default_bottomtext;
		$newvalue[1]['default_image'] = $default_thumbnail_img; // deprecated since v.3.0.0
		$newvalue[0]['default_image'] = $default_thumbnail_img; // new since v.3.0.0
		$newvalue[1]['load_css'] = 0; // deprecated since v.3.0.0
		$newvalue[0]['load_css'] = 1; // new since v.3.0.0
		$newvalue[0]['keep_html'] = 0; // don't strip html formatting from excerpts
		$newvalue[0]['strip_sqbt'] = 1; // strip special square bracket-enclosed tags (WP shortcode)
		$newvalue[0]['strip_clbt'] = 0; // strip special curly bracket-enclosed tags
		$newvalue[0]['display_ul'] = 1; // display as a ul li list (default)
		$newvalue[0]['skip_latest'] = 1; // skip latest posts on home page (default)
		$newvalue[0]['timthumb_path'] = preg_replace( '|http://[^/]+|', '', WP_PLUGIN_URL ) . '/' . $plugin_dir . '/timthumb/timthumb.php';
		$newvalue[0]['timthumb_width'] = $default_image_width;
		$newvalue[0]['timthumb_height'] = $default_image_height;
		$newvalue[0]['use_wpthumb'] = 1; // Wordpress 2.9+ built-in thumbnail function
		
		if( $prev_options ) {
			update_option( $newoption, $newvalue );
		} else {
			add_option( $newoption, $newvalue );
		}
	}
}
register_activation_hook(__FILE__, 'yd_rp_plugin_reset');

/** Create Text Domain For Translations **/
add_action('init', 'yd_rp_plugin_textdomain');
function yd_rp_plugin_textdomain() {
	$plugin_dir = basename(dirname(__FILE__));
	load_plugin_textdomain(
		'yd-recent-posts-widget', 
		'wp-content/plugins/' . $plugin_dir . '/languages', $plugin_dir . '/languages' 
	);
}

// Tell Dynamic Sidebar about our new widget and its control
add_action('plugins_loaded', 'widget_rp_init');

// ============================ Generic YD WP functions ==============================

include_once( 'inc/yd-wp-lib.inc.php' );

if( !function_exists( 'check_yd_widget_cache' ) ) {
	function check_yd_widget_cache( $widg_id ) {
		$option_name = 'yd_cache_' . $widg_id;
		$cache = get_option( $option_name );
		//echo "rev: " . $cache["revision"] . " - " . get_yd_cache_revision() . "<br/>";
		if( $cache["revision"] != get_yd_cache_revision() ) {
			return FALSE;
		} else {
			return TRUE;
		}
	}
}

if( !function_exists( 'update_yd_widget_cache' ) ) {
	function update_yd_widget_cache( $widg_id, $html ) {
		//echo "uwc " . $widg_id;
		$option_name = 'yd_cache_' . $widg_id;
		$nvarr["html"] = $html;
		$nvarr["revision"] = get_yd_cache_revision();
		$newvalue = $nvarr;
		if ( get_option( $option_name ) ) {
			update_option( $option_name, $newvalue );
		} else {
			$deprecated=' ';
			$autoload='no';
			add_option($option_name, $newvalue, $deprecated, $autoload);
		}
	}
}

if( !function_exists( 'get_yd_widget_cache' ) ) {
	function get_yd_widget_cache( $widg_id ) {
		$option_name = 'yd_cache_' . $widg_id;
		$nvarr = get_option( $option_name );
		return $nvarr["html"];
	}
}

if( !function_exists( 'clear_yd_widget_cache' ) ) {
	function clear_yd_widget_cache( $widg_id ) {
		$caches = yd_get_all_widget_caches( 'yd_cache_' );
		foreach( $caches as $cache_name ) {
			$option_name = $cache_name;
			$nvarr["html"] = __('clear', 'yd-recent-posts-widget');
			$nvarr["revision"] = 0;
			$newvalue = $nvarr;
			update_option( $option_name, $newvalue );
		}
		/*
		$option_name = 'yd_cache_' . $widg_id;
		$nvarr["html"] = __('clear', 'yd-recent-posts-widget');
		$nvarr["revision"] = 0;
		$newvalue = $nvarr;
		if ( get_option( $option_name ) ) {
			update_option( $option_name, $newvalue );
		} else {
			$deprecated=' ';
			$autoload='no';
			add_option($option_name, $newvalue, $deprecated, $autoload);
		}
		*/
	}
}
add_action( 'deleted_post', 'clear_yd_widget_cache' );
add_action( 'publish_post', 'clear_yd_widget_cache' );

if( !function_exists( 'yd_get_all_widget_caches') ) {
	function yd_get_all_widget_caches( $widget_prefix ) {
		global $wpdb;
		$query = "SELECT option_name FROM $wpdb->options WHERE option_name LIKE '$widget_prefix%'";
		return $wpdb->get_col( $query );
	}
}

if( !function_exists( 'get_yd_cache_revision' ) ) {
	function get_yd_cache_revision() {
		global $wpdb;
		return $wpdb->get_var( "SELECT max( ID ) FROM " . $wpdb->posts .
			" WHERE post_type = 'post' and post_status = 'publish'" );
	}
}

if( !function_exists( 'yd_check_thumbpath' ) ) {
	function yd_check_thumbpath( $tn_url, $extension ) {
		if ( basename( $tn_url ) == $thumb = apply_filters( 'thumbnail_filename', basename( $tn_url ) ) )
		$thumb = preg_replace( '!(\.[^.]+)?$!', $extension . '$1', basename( $tn_url ), 1 );
		$thumburl = str_replace( basename( $tn_url ), $thumb, $tn_url );
		$wud = wp_upload_dir();
		if( isset( $wud["baseurl"] ) ) {
			//WP2.6+ hell!
			$upload_path = $wud["baseurl"];
			$upload_path = preg_replace( "|^" . get_option( 'siteurl' ) . "/|", "", $upload_path );
		} else {
			$upload_path = get_option( 'upload_path' );
		}
		$thumbpath = preg_replace( "|^(.*?)/" . $upload_path . "|", ABSPATH . $upload_path, $thumburl );
		//echo "up: " . $upload_path . " - $thumb - $thumburl - $thumbpath<br/>";
		if( file_exists( $thumbpath ) ) {
			return $thumburl;
		} else {
			return $tn_url;
		}
	}
}

// ============================ Generic other functions ==============================

// clean cut function supporting HTML
// credits http://www.gsdesign.ro/blog/cut-html-string-without-breaking-the-tags/
// original credits: http://cakephp.org/
// maybe it breaks with utf-8
/**
 * Truncates text.
 *
 * Cuts a string to the length of $length and replaces the last characters
 * with the ending if the text is longer than length.
 *
 * @param string  $text String to truncate.
 * @param integer $length Length of returned string, including ellipsis.
 * @param string  $ending Ending to be appended to the trimmed string.
 * @param boolean $exact If false, $text will not be cut mid-word
 * @param boolean $considerHtml If true, HTML tags would be handled correctly
 * @return string Trimmed string.
 */
if( !function_exists( 'yd_cake_truncate' ) ) {
	function yd_cake_truncate($text, $length = 100, $ending = '...', $exact = true, $considerHtml = false) {
		if ($considerHtml) {
			// if the plain text is shorter than the maximum length, return the whole text
			if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
				return $text;
			}
		
			// splits all html-tags to scanable lines
			preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
		
			$total_length = strlen($ending);
			$open_tags = array();
			$truncate = '';
		
			foreach ($lines as $line_matchings) {
				// if there is any html-tag in this line, handle it and add it (uncounted) to the output
				if (!empty($line_matchings[1])) {
					// if it's an "empty element" with or without xhtml-conform closing slash (f.e. <br/>)
					if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
						// do nothing
						// if tag is a closing tag (f.e. </b>)
					} else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
						// delete tag from $open_tags list
						$pos = array_search($tag_matchings[1], $open_tags);
						if ($pos !== false) {
							unset($open_tags[$pos]);
						}
						// if tag is an opening tag (f.e. <b>)
					} else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
						// add tag to the beginning of $open_tags list
						array_unshift($open_tags, strtolower($tag_matchings[1]));
					}
					// add html-tag to $truncate'd text
					$truncate .= $line_matchings[1];
				}
		
				// calculate the length of the plain text part of the line; handle entities as one character
				$content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
				if ($total_length+$content_length> $length) {
					// the number of characters which are left
					$left = $length - $total_length;
					$entities_length = 0;
					// search for html entities
					if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
						// calculate the real length of all entities in the legal range
						foreach ($entities[0] as $entity) {
							if ($entity[1]+1-$entities_length <= $left) {
								$left--;
								$entities_length += strlen($entity[0]);
							} else {
								// no more characters left
								break;
							}
						}
					}
					$truncate .= substr($line_matchings[2], 0, $left+$entities_length);
					// maximum lenght is reached, so get off the loop
					break;
				} else {
					$truncate .= $line_matchings[2];
					$total_length += $content_length;
				}
		
				// if the maximum length is reached, get off the loop
				if($total_length>= $length) {
					break;
				}
			}
		} else {
			if (strlen($text) <= $length) {
				return $text;
			} else {
				$truncate = substr($text, 0, $length - strlen($ending));
			}
		}
	
		// if the words shouldn't be cut in the middle...
		if (!$exact) {
			// ...search the last occurance of a space...
			$spacepos = strrpos($truncate, ' ');
			if (isset($spacepos)) {
				// ...and cut the text in this position
				$truncate = substr($truncate, 0, $spacepos);
			}
		}
	
		// add the defined ending to the text
		$truncate .= $ending;
		
		if($considerHtml) {
			// close all unclosed html-tags
			foreach ($open_tags as $tag) {
	                $truncate .= '</' . $tag . '>';
		    }
		}
	        
		return $truncate;
	        
	} 
}

/** Add links on the plugin page (short description) **/
add_filter( 'plugin_row_meta', 'yd_rpw_links' , 10, 2 );
function yd_rpw_links( $links, $file ) {
	$base = plugin_basename(__FILE__);
	if ( $file == $base ) {
		$links[] = '<a href="options-general.php?page=yd-recent-posts-widget%2Finc%2Fyd-rpw-admin.inc.php">' . __('Settings') . '</a>';
		$links[] = '<a href="http://www.yann.com/en/wp-plugins/yd-recent-posts-widget">' . __('Support') . '</a>';
	}
	return $links;
}
?>