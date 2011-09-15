<?php

/** Create custom admin menu page **/
add_action('admin_menu', 'yd_rp_plugin_menu');
function yd_rp_plugin_menu() {
	add_options_page(
	__('YD Recent Posts Options',
		'yd-recent-posts-widget'), 
	__('YD Recent Posts', 'yd-recent-posts-widget'),
	8,
	__FILE__,
		'yd_rp_plugin_options'
		);
}

function yd_rp_plugin_options() {
	global $wpdb;
	$plugin_dir = 'yd-recent-posts-widget';
	$eg_timthumb_scriptpath = preg_replace( '|http://[^/]+|', '', WP_PLUGIN_URL ) . '/' . $plugin_dir . '/timthumb/timthumb.php';
	$support_url	= 'http://www.yann.com/en/wp-plugins/yd-recent-posts-widget';
	$yd_logo		= 'http://www.yann.com/yd-recent-posts-widget-v300-logo.gif';
	$jstext			= preg_replace( "/'/", "\\'", __( 'This will disable the link in your blog footer. ' .
							'If you are using this plugin on your site and like it, ' .
							'did you consider making a donation?' .
							' - Thanks.', 'yd-recent-posts-widget' ) );
	$d = false;
	if( $_GET['debug'] == 1 ) $d = true;
	?>
	<script type="text/javascript">
	<!--
	function donatemsg() {
		alert( '<?php echo $jstext ?>' );
	}
	//-->
	</script>
	<?php
	echo '<div class="wrap">';
	
	// ---
	// options/settings page header section: h2 title + warnings / updates
	// ---

	echo '<h2>' . __('YD Recent Posts Widget with Thumbnails', 'yd-recent-posts-widget') . '</h2>';
	
	if( isset( $_GET["do"] ) ) {
		echo '<div class="updated">';
		if($d) echo '<p>' . __('Action:', 'yd-recent-posts-widget') . ' '
		. __('I should now', 'yd-recent-posts-widget') . ' ' . $_GET["do"] . '.</p>';
		if(			$_GET["do"] == __('Clear cache', 'yd-recent-posts-widget') ) {
			clear_yd_widget_cache( 'widget_yd_rp_home' );
			clear_yd_widget_cache( 'widget_yd_rp_page' );
			clear_yd_widget_cache( 'widget_yd_rp_hometemplate1' ); // TODO? Clear other pages template cache?
			echo '<p>' . __('Caches are cleared', 'yd-recent-posts-widget') . '</p>';
		} elseif(	$_GET["do"] == __('Reset plugin settings', 'yd-recent-posts-widget') ) {
			yd_rp_plugin_reset( 'force' );
			echo '<p>' . __('Plugin settings are reset', 'yd-recent-posts-widget') . '</p>';
		} elseif(	$_GET["do"] == __('Update plugin settings', 'yd-recent-posts-widget') ) {
			yd_rp_plugin_update_options();
			echo '<p>' . __('Plugin settings are updated', 'yd-recent-posts-widget') . '</p>';
		}
		echo '</div>'; // / updated
	} else {
		echo '<div class="updated">';
		echo '<p>'
		. '<a href="' . $support_url . '" target="_blank" title="Plugin FAQ">';
		echo __('Welcome to YD recent Posts Admin Page.', 'yd-recent-posts-widget')
		. '</a></p>';
		echo '</div>'; // / updated
	}

	$options = get_option( 'widget_yd_rp' );
	$i = 0;
	if( ! is_array( $options ) ) {
		// Something went wrong
		echo '<div class="error">';
		echo __( 'Uh-oh. Looks like I lost my settings. Sorry.', 'yd-recent-posts-widget' );
		echo '<form method="get" style="display:inline;" action="">';
		echo '<input type="submit" name="do" value="' . __( 'Reset plugin settings', 'yd-recent-posts-widget' ) . '" /><br/>';
		echo '<input type="hidden" name="page" value="' . $_GET["page"] . '" />';
		echo '</form>';
		echo '</div>'; // / updated
		return false;
	}
	
	/**
	 * Check Timthumb cache write permission
	 */
	if( $options[0]['use_timthumb'] ) {
		$timthumb_dir = preg_replace( '/timthumb\.php$/', '', $options[0]['timthumb_path'] );
		$timthumb_dir = preg_replace( '|^/wp-content/plugins|', '', $timthumb_dir );
		$tt_cache_dir = WP_PLUGIN_DIR . $timthumb_dir . 'cache';
		if( !is_writable( $tt_cache_dir ) ) {
			echo '<div class="error">';
			//echo WP_PLUGIN_DIR . '<br/>';
			echo __( 'Warning: To use Timthumb, this folder should have permissions set so it can be writable:', 'yd-recent-posts-widget' );
			echo '<br/><strong>' .  $tt_cache_dir . '</strong>';
			echo '</div>';
		}
	}
	
	// ---
	// Right sidebar
	// ---
	
	echo '<div class="metabox-holder has-right-sidebar">';
	echo '<div class="inner-sidebar">';
	echo '<div class="meta-box-sortabless ui-sortable">';

	// == Block 1 ==

	echo '<div class="postbox">';
	echo '<h3 class="hndle">' . __( 'Considered donating?', 'yd-recent-posts-widget' ) . '</h3>';
	echo '<div class="inside" style="text-align:center;"><br/>';
	echo '<a href="' . $support_url . '" target="_blank" title="Plugin FAQ">'
	. '<img src="' . $yd_logo . '" alt="YD logo" /></a>'
	. '<br/><small>' . __( 'Enjoy this plugin?', 'yd-recent-posts-widget' ) . '<br/>' . __( 'Help me improve it!', 'yd-recent-posts-widget' ) . '</small><br/>'
	. '<form action="https://www.paypal.com/cgi-bin/webscr" method="post">'
	. '<input type="hidden" name="cmd" value="_s-xclick"/>'
	. '<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----'
	. '" />'
	. '<input type="image" src="https://www.paypal.com/' . __( 'en_US', 'yd-recent-posts-widget' ) . '/i/btn/btn_donateCC_LG.gif" name="submit" alt="PayPal - The safer, easier way to pay online!" />'
	. '<img alt="" border="0" src="https://www.paypal.com/' . __( 'en_US', 'yd-recent-posts-widget' ) . '/i/scr/pixel.gif" width="1" height="1" />'
	. '</form>'
	. '<small><strong>' . __( 'Thanks', 'yd-recent-posts-widget' ) . ' - Yann.</strong></small><br/><br/>';
	
	echo '</div>'; // / inside
	echo '</div>'; // / postbox
	
	// == Block 2 ==
	
	echo '<div class="postbox">';
	echo '<h3 class="hndle">' . __( 'Credits', 'yd-recent-posts-widget' ) . '</h3>';
	echo '<div class="inside" style="padding:10px;">';
	echo 'v.' . $options['plugin_version'] . '<br/>';
	echo '<b>' . __( 'Funding', 'yd-recent-posts-widget' ) . '</b>';
	echo '<ul>';
	echo '<li>' . __( 'Initial:', 'yd-recent-posts-widget' ) . ' <a href="http://www.nogent-citoyen.com">Nogent Citoyen</a></li>';
	//echo '<li>' . __( 'Additional:', 'yd-recent-posts-widget' ) . '  <a href="your_site_goes_here!">Why not you?</a></li>';
	echo '</ul>';
	echo '<b>' . __( 'Translations', 'yd-recent-posts-widget' ) . '</b>';
	echo '<ul>';
	echo '<li>' . __( 'English:', 'yd-recent-posts-widget' ) . ' <a href="http://www.yann.com">Yann</a></li>';
	echo '<li>' . __( 'French:', 'yd-recent-posts-widget' ) . ' <a href="http://www.yann.com">Yann</a></li>';
	echo '<li>' . __( 'Russian:', 'yd-recent-posts-widget' ) . ' <a href="http://www.fatcow.com/">Marcis</a></li>';
	echo '<li>' . __( 'Dutch:', 'yd-recent-posts-widget' ) . ' <a href="http://www.fethiyehotels.com">Rene</a></li>';
	echo '<li>' . __( 'German:', 'yd-recent-posts-widget' ) . ' <a href="http://www.pangaea.nl/diensten/exact-webshop">Rian</a></li>';
	echo '</ul>';
	echo __( 'If you want to contribute to a translation of this plugin, please drop me a line by ', 'yd-recent-posts-widget' );
	echo '<a href="mailto:yann@abc.fr">' . __('e-mail', 'yd-recent-posts-widget' ) . '</a> ';
	echo __( 'or leave a comment on the ', 'yd-recent-posts-widget' );
	echo '<a href="' . $support_url . '">' . __( 'plugin\'s page', 'yd-recent-posts-widget' ) . '</a>. ';
	echo __( 'You will get credit for your translation in the plugin file and the documentation page, ', 'yd-recent-posts-widget' );
	echo __( 'as well as a link on this page and on my developers\' blog.', 'yd-recent-posts-widget' );
		
	echo '</div>'; // / inside
	echo '</div>'; // / postbox
	
	// == Block 3 ==
	
	echo '<div class="postbox">';
	echo '<h3 class="hndle">' . __( 'Support' ) . '</h3>';
	echo '<div class="inside" style="padding:10px;">';
	echo '<b>' . __( 'Free support', 'yd-recent-posts-widget' ) . '</b>';
	echo '<ul>';
	echo '<li>' . __( 'Support page:', 'yd-recent-posts-widget' );
	echo ' <a href="' . $support_url . '">' . __( 'here.', 'yd-recent-posts-widget' ) . '</a>';
	echo ' ' . __( '(use comments!)', 'yd-recent-posts-widget' ) . '</li>';
	echo '</ul>';
	echo '<p><b>' . __( 'Professional consulting', 'yd-recent-posts-widget' ) . '</b><br/>';
	echo '<a href="http://www.yann.com/en/about">';
	echo '<img src="' . WP_PLUGIN_URL . '/' . $plugin_dir . '/img/yann_80x80.jpg" style="width:80px;height:80px;float:left;margin-right:4px;" alt="Yann" />';
	echo '</a>';
	echo __( 'I am available as an experienced free-lance Wordpress plugin developer and web consultant. ', 'yd-recent-posts-widget' );
	echo __( 'Please feel free to <a href="mailto:yann@abc.fr">check with me</a> for any adaptation or specific implementation of this plugin. ', 'yd-recent-posts-widget' );
	echo '<a href="http://www.yann.com/en/custom-developments">';
	echo __( 'Or for any WP-related custom development or consulting work. Hourly rates available.', 'yd-recent-posts-widget' ) . '</a></p>';
	echo '</div>'; // / inside
	echo '</div>'; // / postbox
	
	echo '</div>'; // / meta-box-sortabless ui-sortable
	echo '</div>'; // / inner-sidebar

	// ---
	// Main content area
	// ---
	
	echo '<div class="has-sidebar sm-padded">';
	echo '<div id="post-body-content" class="has-sidebar-content">';
	echo '<div class="meta-box-sortabless">';

	//---
	echo '<form method="get" style="display:inline;" action="">';
	//---
	
	// == Main plugin options ==
	
	echo '<div class="postbox">';
	echo '<h3 class="hndle">' . __( 'Main plugin settings:', 'yd-recent-posts-widget' ) . '</h3>';
	echo '<div class="inside">';
	
	echo '<table style="margin:10px;table-layout:fixed;width:95%">';
	echo '<tr><th valign="top" align="left" style="width:50%">' . __('Setting:', 'yd-recent-posts-widget') .
		'</th><th align="left" style="width:50%">' . __('Value:', 'yd-recent-posts-widget') . '</th></tr>';

	echo '<tr><td>' . __('Load default CSS stylesheet', 'yd-recent-posts-widget') .
		'</td><td><input type="checkbox" name="yd_rp-load_css-0" value="1" ';
	if( $options[$i]["load_css"] == 1 ) echo 'checked="checked" ';
	echo "></td></tr>";
	echo '<tr><td>' . __('Image inline CSS Style:', 'yd-recent-posts-widget') .
		'</td><td><input class="widefat" id="yd_rp-image_style-0" ' .
		'name="yd_rp-image_style-0" type="text" ' .
		'value="' . preg_replace( '/"/', '&quot;', $options[$i]['image_style'] ) .'" /></td></tr>';
	echo '<tr><td>' . __('Default image URL:', 'yd-recent-posts-widget') .
		'</td><td><input class="widefat" id="yd_rp-default_image-0" ' .
		'name="yd_rp-default_image-0" type="text" ' .
		'value="' . preg_replace( '/"/', '&quot;', $options[$i]['default_image'] ) . '" /></td></tr>';
	echo '<tr><td>' . __('Display as a ul / li list:', 'yd-recent-posts-widget') .
		'</td><td><input type="checkbox" name="yd_rp-display_ul-0" value="1" ';
	if( $options[$i]["display_ul"] == 1 ) echo 'checked="checked" ';
	echo "></td></tr>";
	echo '<tr><td>' . __('Skip latest posts on home page:', 'yd-recent-posts-widget') .
		'</td><td><input type="checkbox" name="yd_rp-skip_latest-0" value="1" ';
	if( $options[$i]["skip_latest"] == 1 ) echo 'checked="checked" ';
	echo "></td></tr>";
	echo '<tr><td>' . __('Keep HTML formatting in excerpts:', 'yd-recent-posts-widget') .
		'</td><td><input type="checkbox" name="yd_rp-keep_html-0" value="1" ';
	if( $options[$i]["keep_html"] == 1 ) echo 'checked="checked" ';
	echo "></td></tr>";
	echo '<tr><td>' . __('Strip shortcodes/captions/[square bracket-enclosed] special tags:', 'yd-recent-posts-widget') .
		'</td><td><input type="checkbox" name="yd_rp-strip_sqbt-0" value="1" ';
	if( $options[$i]["strip_sqbt"] == 1 ) echo 'checked="checked" ';
	echo "></td></tr>";
	echo '<tr><td>' . __('Strip {curly bracket-enclosed} special tags:', 'yd-recent-posts-widget') .
		'</td><td><input type="checkbox" name="yd_rp-strip_clbt-0" value="1" ';
	if( $options[$i]["strip_clbt"] == 1 ) echo 'checked="checked" ';
	echo "></td></tr>";
	echo '<tr><td>' . __('Use WP2.9+ post thumbnails:', 'yd-recent-posts-widget') .
		'</td><td><input type="checkbox" name="yd_rp-use_wpthumb-0" value="1" ';
	if( $options[$i]["use_wpthumb"] == 1 ) echo 'checked="checked" ';
	echo "></td></tr>";
	echo '<tr><td>' . __('Default text string cut length:', 'yd-recent-posts-widget') .
		'</td><td><input type="text" name="yd_rp-default_cutlength-0" value="'
		. $options[$i]["default_cutlength"] . '" size="3" ';
	echo ">";
	echo '<em>(';
	echo __('# of characters to keep', 'yd-recent-posts-widget');
	echo ')</em>';
	echo "</td></tr>";
	echo '<tr><td>' . __('Ellipsis string:', 'yd-recent-posts-widget') .
		'</td><td><input type="text" name="yd_rp-ellipsis_string-0" value="'
		. $options[$i]["ellipsis_string"] . '" size="10" ';
	echo ">";
	echo '<em>(';
	echo __('appended to end of excerpt', 'yd-recent-posts-widget');
	echo ')</em>';
	echo "</td></tr>";
		
	echo '</table>';
	echo '</div>'; // / inside
	echo '</div>'; // / postbox
	
	// == Timthumb options ==
	
	echo '<div class="postbox">';
	echo '<h3 class="hndle">' . __( 'Timthumb settings (optional):', 'yd-recent-posts-widget' ) . '</h3>';
	echo '<div class="inside">';

	echo '<table style="margin:10px;table-layout:fixed;width:95%">';
	echo '<tr><th valign="top" align="left" style="width:50%">' . __('Setting:', 'yd-recent-posts-widget') .
		'</th><th align="left" style="width:50%">' . __('Value:', 'yd-recent-posts-widget') . '</th></tr>';
	
	echo '<tr><td>' . __('Use Timthumb:', 'yd-recent-posts-widget') .
		'</td><td><input type="checkbox" name="yd_rp-use_timthumb-0" value="1" ';
	if( $options[$i]["use_timthumb"] == 1 ) echo 'checked="checked" ';
	echo "></td></tr>";
	echo '<tr><td>' . __('Timthumb complete path:', 'yd-recent-posts-widget') .
		'</td><td><input class="widefat" type="text" name="yd_rp-timthumb_path-0" value="' .
		$options[$i]["timthumb_path"] . '"> ' .
		'<br/><em>(' . __('eg:', 'yd-recent-posts-widget') . ' ' . $eg_timthumb_scriptpath . ' )</em></td></tr>';
	echo '<tr><td>' . __('Width:', 'yd-recent-posts-widget') .
		'</td><td><input type="text" name="yd_rp-timthumb_width-0" value="' .
		$options[$i]["timthumb_width"] . '" size="3"></td></tr>';
	echo '<tr><td>' . __('Height:', 'yd-recent-posts-widget') .
		'</td><td><input type="text" name="yd_rp-timthumb_height-0" value="' .
		$options[$i]["timthumb_height"] . '" size="3"></td></tr>';
		
	echo '</table>';
	echo '</div>'; // / inside
	echo '</div>'; // / postbox
	
	// == Other options ==

	echo '<div class="postbox">';
	echo '<h3 class="hndle">' . __( 'Other options:', 'yd-recent-posts-widget' ) . '</h3>';
	echo '<div class="inside">';
	echo '<table style="margin:10px;">';
	
	// Debug messages
	echo "
		<tr>
			<th scope=\"row\" align=\"right\"><label for=\"debug\">" 
			. __('Show debug messages:', 'yd-recent-posts-widget') . "
			</label></th>";
	echo "	<td><input type=\"checkbox\" name=\"debug\" value=\"1\" id=\"debug\" ";
	if( $_GET['debug'] == 1 )
		echo ' checked="checked" ';
	echo " /></td></tr>";
	
	// Disable backlink
	echo '<tr><th scope="row" align="right"><label for="yd_rp-disable_backlink-0">' 
			. __( 'Disable backlink in the blog footer:', 'yd-recent-posts-widget' ) .
		'</label></th><td><input type="checkbox" name="yd_rp-disable_backlink-0" value="1" id="yd_rp-disable_backlink-0" ';
	if( $options[$i]["disable_backlink"] == 1 ) echo ' checked="checked" ';
	echo ' onclick="donatemsg()" ';
	echo ' /></td></tr>';
			
	//---
	
	echo '</table>';
	
	echo '</div>'; // / inside
	echo '</div>'; // / postbox
	
	echo '<p class="submit">';
	echo '<input type="submit" name="do" value="' . __('Update plugin settings', 'yd-recent-posts-widget') . '" />';
	echo '<input type="hidden" name="page" value="' . $_GET["page"] . '" />';
	echo '<input type="hidden" name="time" value="' . time() . '" />';
	echo '<input type="submit" name="do" value="' . __('Reset plugin settings', 'yd-recent-posts-widget') . '" />';

	echo '<input type="submit" name="do" value="' . __('Clear cache', 'yd-recent-posts-widget') . '"><br/>';
	
	echo '</p>'; // / submit
	echo '</form>';
	
	//---

	/**
	echo '<div class="postbox">';
	echo '<h3 class="hndle">' . __( 'Cache content:', 'yd-recent-posts-widget' ) . '</h3>';
	echo '<div class="inside">';
	
	echo '<p>' . __('Homepage cache content:', 'yd-recent-posts-widget') . '</p>';
	echo '<div class="yd_rp_widget"><ul>' . get_yd_widget_cache( 'widget_yd_rp_home' ) . '</ul></div>';

	echo '<p>' . __('Other pages cache content:', 'yd-recent-posts-widget') . '</p>';
	echo '<div class="yd_rp_widget"><ul>' . get_yd_widget_cache( 'widget_yd_rp_page' ) . '</ul></div>';

	echo '</div>'; // / inside
	echo '</div>'; // / postbox
	**/
	
	echo '</div>'; // / meta-box-sortabless
	echo '</div>'; // / has-sidebar-content
	echo '</div>'; // / has-sidebar sm-padded
	echo '</div>'; // / metabox-holder has-right-sidebar
	
	echo '</div>'; // /wrap
}

/** Update display options of the options admin page **/
function yd_rp_plugin_update_options(){
	$to_update = Array(
		'display_ul',
		'keep_html',
		'strip_sqbt',
		'strip_clbt',
		'use_timthumb',
		'timthumb_path',
		'timthumb_width',
		'timthumb_height',
		'skip_latest',
		'disable_backlink',
		'use_wpthumb',
		'load_css',
		'default_cutlength',
		'ellipsis_string',
		'image_style',
		'default_image'
	);
	if( yd_update_options( 'widget_yd_rp', 0, $to_update, $_GET, 'yd_rp-' ) ) {
		clear_yd_widget_cache( 'widget_yd_rp_home' );
		clear_yd_widget_cache( 'widget_yd_rp_page' );
		clear_yd_widget_cache( 'widget_yd_rp_hometemplate1' ); // TODO? Clear other pages template cache?
	}
}

?>