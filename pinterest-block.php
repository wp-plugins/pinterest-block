<?php

/**
 * Pinterest Block
 *
 * @package   PB
 * @author    Phil Derksen <pderksen@gmail.com>
 * @license   GPL-2.0+
 * @link      http://pinplugins.com
 * @copyright 2012-2015 Phil Derksen
 *
 * @wordpress-plugin
 * Plugin Name: Pinterest Block
 * Plugin URI: http://pinplugins.com/disable-pinning/
 * Description: Block selected posts and pages from getting pinned on Pinterest.
 * Version: 1.0.2
 * Author: Phil Derksen
 * Author URI: http://philderksen.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * GitHub Plugin URI: https://github.com/pderksen/WP-Pinterest-Block
 */

if ( ! defined( 'PBLOCK_PLUGIN_BASENAME' ) ) {
	define( 'PBLOCK_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'PBLOCK_META_TAG' ) ) {
	define( 'PBLOCK_META_TAG', '<meta name="pinterest" content="nopin" />' . "\n" );
}

if ( ! defined( 'PINPLUGIN_BASE_URL' ) ) {
	define( 'PINPLUGIN_BASE_URL', 'http://pinplugins.com/' );
}


$pblock_options = get_option( 'pblock_options' );
	
//Add settings page to admin menu
//Use $page variable to load CSS/JS ONLY for this plugin's admin page

function pblock_create_menu() {
	$page = add_submenu_page( 'options-general.php', 'Pinterest Block Settings', 'Pinterest Block', 'manage_options', __FILE__, 'pblock_create_settings_page' );
	add_action( 'admin_print_styles-' . $page, 'pblock_add_admin_css_js' );
}

add_action( 'admin_menu', 'pblock_create_menu' );

//Add Admin CSS/JS

function pblock_add_admin_css_js() {	
	wp_enqueue_script( 'jquery' );

	wp_enqueue_style( 'pinterest-block', plugins_url( '/css/pinterest-block-admin.css' , __FILE__ ) );
	wp_enqueue_script( 'pinterest-block', plugins_url( '/js/pinterest-block-admin.js', __FILE__ ), array( 'jquery' ) );
}

//Register settings

function pblock_register_settings() {
	register_setting( 'pblock-settings-group', 'pblock_options' );
}

add_action( 'admin_init', 'pblock_register_settings' );

//Create settings page

function pblock_create_settings_page() {
	global $pblock_options;
	?>
	
    <div class="wrap">
		<h2><?php _e( 'Pinterest Block Settings', 'pblock' ); ?></h2>
		
		<div id="poststuff" class="metabox-holder has-right-sidebar">

			<div id="post-body">
				<div id="post-body-content">
					<div class="meta-box-sortables ui-sortable">
						<?php settings_errors(); //Display status messages after action ("settings saved", errors) ?>
					
						<form method="post" action="options.php">
							<?php settings_fields( 'pblock-settings-group' ); ?>							
							
							 <div id="pblock-options" class="postbox">
								 <!--Collapsable-->
								<div class="handlediv pblock-handlediv" title="Click to toggle"><br /></div>						
								<h3 class="hndle pblock-hndle">What types of pages should pinning be blocked on?</h3>
								
								<table class="form-table inside">
									<tr valign="top">
										<td>
											<input id="block_home_page" name="pblock_options[block_home_page]" type="checkbox" value="1"
												<?php checked( (bool)$pblock_options['block_home_page'] ); ?> />
											<label for="block_home_page">Blog Home Page (or Latest Posts Page)</label>
										</td>
									</tr>
									<tr valign="top">
										<td>
											<input id="block_front_page" name="pblock_options[block_front_page]" type="checkbox" value="1"
												<?php checked( (bool)$pblock_options['block_front_page'] ); ?> />
											<label for="block_front_page">Front Page (different from Home Page only if set in Settings > Reading)</label>
										</td>
									</tr>					
									<tr valign="top">
										<td>
											<input id="block_posts" name="pblock_options[block_posts]" type="checkbox" value="1"
												<?php checked( (bool)$pblock_options['block_posts'] ); ?> />
											<label for="block_posts">Individual Posts</label>
										</td>
									</tr>
									<tr valign="top">
										<td>
											<input id="block_pages" name="pblock_options[block_pages]" type="checkbox" value="1"
												<?php checked( (bool)$pblock_options['block_pages'] ); ?> />
											<label for="block_pages">WordPress Static "Pages"</label>
										</td>
									</tr>
									<tr valign="top">
										<td>
											<input id="block_archives" name="pblock_options[block_archives]" type="checkbox" value="1"
												<?php checked( (bool)$pblock_options['block_archives'] ); ?> />
											<label for="block_archives">Archives (includes Category, Tag, Author and time-based pages)</label>
										</td>
									</tr>
								</table>
							</div>
							
							<div class="postbox">
								 <!--Collapsable-->
								<div class="handlediv pblock-handlediv" title="Click to toggle"><br /></div>						
								<h3 class="hndle pblock-hndle">Pinterest Block Details</h3>

								<table class="form-table inside">
									<tr valign="top">
										<td>
											<p>
												Pinterest Block simply disables pinning by inserting the official  
												<a href="<?php echo PINPLUGIN_BASE_URL ?>disable-pinning/" target="_blank">meta tag</a>.
											</p>
											</p>
												Besides the types of pages above, <strong>any individual post or page</strong> can be blocked.
												Just go to the edit screen for that post or page, scroll down to the bottom, and check the
												<strong>"Block Pinning"</strong> checkbox.
											</p>
											<p>
												Note that the meta tag only blocks people from pinning using the official bookmarklet or "Add" feature
												on Pinterest.com itself. <em>"Pin It" buttons on the page might still work.</em> Get the
												<a href="<?php echo PINPLUGIN_BASE_URL ?>pin-it-button/" target="_blank">"Pin It" Button plugin</a>
												to specify the posts and pages the button should or should not be shown on.
											</p>
										</td>
									</tr>
								</table>
							</div>
							
							<div class="submit-settings">
								<input name="submit" type="submit" value="Save Settings" class="button-primary" />
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php
}

//Add a link to the settings page on the plugins list entry

function pblock_plugin_action_links( $links, $file ) {
	if ( $file != PBLOCK_PLUGIN_BASENAME )
		return $links;

	$url = admin_url( 'admin.php?page=' . PBLOCK_PLUGIN_BASENAME );
	$settings_link = '<a href="' . $url . '">' . esc_html( __( 'Settings') ) . '</a>';

	array_unshift( $links, $settings_link );

	return $links;
}

add_filter( 'plugin_action_links', 'pblock_plugin_action_links', 10, 2 );

//Adds a meta box to the main column on the Post and Page edit screens

function pblock_add_meta_box() {
	add_meta_box( 'pblock_meta','Pinterest Block Settings', 'pblock_meta_box_content', 'page', 'advanced', 'high' );
	add_meta_box( 'pblock_meta','Pinterest Block Settings', 'pblock_meta_box_content', 'post', 'advanced', 'high' );
}

add_action( 'admin_init', 'pblock_add_meta_box' );


//Renders the post/page meta box checkbox html

function pblock_meta_box_content( $post ) {
	$pblock_checked = get_post_meta( $post->ID, 'pblock_pinning_blocked', 1 );
	?>
	
	<p>
		<input name="pblock_enable_pinning" id="pblock_enable_pinning" value="1" <?php checked( $pblock_checked ); ?> type="checkbox" />
		<label for="pblock_enable_pinning">Block Pinning on this post/page.</label>
		<input type="hidden" name="pblock_sharing_status_hidden" value="1" />
	</p>
		
	<?php
}

//Saves display option for individual post/page

function pblock_meta_box_save( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return $post_id;

	// Record pinning disable
	if ( isset( $_POST['post_type'] ) && ( 'post' == $_POST['post_type'] || 'page' == $_POST['post_type'] ) ) {
		if ( current_user_can( 'edit_post', $post_id ) ) {
			if ( isset( $_POST['pblock_sharing_status_hidden'] ) ) {
				if ( isset( $_POST['pblock_enable_pinning'] ) ) {
					update_post_meta( $post_id, 'pblock_pinning_blocked', 1 );
				}
				else {
					delete_post_meta( $post_id, 'pblock_pinning_blocked' );
				}
			}
		}
	}

	return $post_id;
}

add_action( 'save_post', 'pblock_meta_box_save' );

//Add meta tag to public pages where pinning is blocked

function pblock_add_block() {
	$pblock_options = get_option( 'pblock_options' );
 	global $post;
	$postID = $post->ID;
	
    //Determine if block on current page from main admin settings
	if (
        ( $pblock_options['block_home_page'] && is_home() ) || 
	    ( $pblock_options['block_front_page'] && is_front_page() ) ||
		( is_single() && ( $pblock_options['block_posts'] ) ) ||
        ( is_page() && ( $pblock_options['block_pages'] ) && !is_front_page() ) ||
        
        //archive pages besides categories (tag, author, date, search)
        //http://codex.wordpress.org/Conditional_Tags
        ( is_archive() && ( $pblock_options['block_archives'] ) && 
            ( is_tag() || is_author() || is_date() || is_search() ) 
        )        
       ) {
		echo PBLOCK_META_TAG;
		return;
	}
	
    //Determine if block on current page from single post settings
	if ( get_post_meta( $postID, 'pblock_pinning_blocked', 1 ) ) {			
		echo PBLOCK_META_TAG;
	}    
}

add_action('wp_head', 'pblock_add_block');
