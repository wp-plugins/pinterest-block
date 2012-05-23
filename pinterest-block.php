<?php
/*
  Plugin Name: Pinterest Block
  Plugin URI: http://pinterestplugin.com
  Description: Block selected posts and pages categories from getting pinned on Pinterest.
  Author: Phil Derksen
  Author URI: http://pinterestplugin.com
  Version: 1.0.1
  License: GPLv2
  Copyright 2012 Phil Derksen (phil@pinterestplugin.com)
*/  

/***************************
* Global Constants
***************************/

define( 'PBLOCK_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'PBLOCK_META_TAG', '<meta name="pinterest" content="nopin" />' . "\n" );

$pblock_options = get_option( 'pblock_options' );
	
//Plugin install/activation

function pblock_install() {
	//Deactivate plugin if WP version too low
    if ( version_compare( get_bloginfo( 'version' ), '3.0', '<' ) ) {
        deactivate_plugins( basename( __FILE__ ) );
    }
    
    //All settings values are off by default, so no need to initialize here
}

register_activation_hook( __FILE__, 'pblock_install' );

//Debugging

function pblock_debug_print( $value ) {
    print_r( '<br/><br/>' );
	print_r( $value );
}

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

//Add first-install pointer CSS/JS & functionality

function pblock_add_admin_css_js_pointer() {
	wp_enqueue_style( 'wp-pointer' );
    wp_enqueue_script( 'wp-pointer' );
	
    add_action( 'admin_print_footer_scripts', 'pblock_admin_print_footer_scripts' );
}

add_action( 'admin_enqueue_scripts', 'pblock_add_admin_css_js_pointer' );

//Add pointer popup message when plugin first installed

function pblock_admin_print_footer_scripts() {
    //Check option to hide pointer after initial display
    if ( !get_option( 'pblock_hide_pointer' ) ) {
        $pointer_content = '<h3>Pinterest Block Installed!</h3>';
        $pointer_content .= '<p>Congratulations. You have just installed the Pinterest Block Plugin. ' .
            'Now just configure your settings to specify what gets blocked.</p>';
         
        $url = admin_url( 'admin.php?page=' . PBLOCK_PLUGIN_BASENAME );
        
        ?>

        <script type="text/javascript">
            //<![CDATA[
            jQuery(document).ready( function($) {
                $("#menu-plugins").pointer({
                    content: '<?php echo $pointer_content; ?>',
                    buttons: function( event, t ) {
                        button = $('<a id="pointer-close" class="button-secondary">Close</a>');
                        button.bind("click.pointer", function() {
                            t.element.pointer("close");
                        });
                        return button;
                    },
                    position: "left",
                    close: function() { }
            
                }).pointer("open");
              
                $("#pointer-close").after('<a id="pointer-primary" class="button-primary" style="margin-right: 5px;" href="<?php echo $url; ?>">' + 
                    'Pinterest Block Settings');
            });
            //]]>
        </script>

        <?php
        
        //Update option so this pointer is never seen again
        update_option( 'pblock_hide_pointer', 1 );
	}
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
		<a href="http://pinterestplugin.com/" target="_blank"><div id="pinterest-button-icon-32" class="icon32"
			style="background: url(<?php echo plugins_url( '/img/pinterest-button-icon-med.png', __FILE__ ); ?>) no-repeat;"></div></a>
		<h2><?php _e( 'Pinterest Block Settings', 'pblock' ); ?></h2>
		
		<div id="poststuff" class="metabox-holder has-right-sidebar">

			<!-- Fixed right sidebar like WP post edit screen -->
			<div id="side-info-column" class="inner-sidebar">
				<div id="side-sortables" class="meta-box-sortables ui-sortable">
					<div class="pblock-admin-banner">
						<a href="http://pinterestplugin.com/ad-tpp-from-pblock" target="_blank">
							<img src="http://cdn.pinterestplugin.com/img/top-pinned-posts-ad-01.jpg" alt="Top Pinned Posts Pinterest Plugin for WordPress"></img>
						</a>
					</div>
                    
					<div class="postbox">
						<div class="handlediv pblock-handlediv" title="Click to toggle"><br /></div>
						<h3 class="hndle pblock-hndle"><?php _e( 'Spread the Word', 'pblock' ); ?></h3>
						
						<div class="inside">
                            <p><?php _e( 'Like this plugin? A share would be awesome!', 'pblock' ); ?></p>
							
							<table id="share_plugin_buttons">
								<tr>
									<td><?php echo pblock_share_twitter(); ?></td>
									<td><?php echo pblock_share_pinterest(); ?></td>
									<td><?php echo pblock_share_facebook(); ?></td>
								</tr>
							</table>
                            
                            <p>
                                &raquo; <a href="http://wordpress.org/extend/plugins/pinterest-pin-it-button/" target="_blank" class="external">
									<?php _e( 'Rate it on WordPress', 'pblock' ); ?></a>
                            </p>
						</div>
					</div>

					<div class="postbox">
						<div class="handlediv pblock-handlediv" title="Click to toggle"><br /></div>
						<h3 class="hndle pblock-hndle"><?php _e( 'Plugin Support', 'tpp' ); ?></h3>
						
						<div class="inside">
							<p>
								&raquo; <a href="http://pinterestplugin.com/support-pinterest-block" target="_blank" class="external">
								<?php _e( 'Support & Knowledge Base', 'pblock' ); ?></a>
							</p>
							<p>
								<?php _e( 'Email support provided to licensed users only.', 'pblock' ); ?>
							</p>
							<p>
								&raquo; <strong><a href="http://pinterestplugin.com/buy-support-pinterest-block" target="_blank" class="external">
								<?php _e( 'See Support Pricing', 'pblock' ); ?></a></strong>
							</p>							
						</div>
					</div>
					
					<div class="postbox">
						<div class="handlediv pblock-handlediv" title="Click to toggle"><br /></div>
						<h3 class="hndle pblock-hndle"><?php _e( 'More Pinterest Plugins', 'pblock' ); ?></h3>
						
						<div class="inside">
							<ul>
								<li>&raquo; <a href="http://pinterestplugin.com/top-pinned posts" target="_blank" class="external">Top Pinned Posts</a></li>
								<li>&raquo; <a href="http://pinterestplugin.com/pin-it-button/" target="_blank" class="external">"Pin It" Button</a></li>
								<li>&raquo; <a href="http://pinterestplugin.com/follow-button" target="_blank" class="external">"Follow" Button</a></li>
							</ul>
						</div>
					</div>
					
					<div class="postbox">
						<div class="handlediv pblock-handlediv" title="Click to toggle"><br /></div>
						<h3 class="hndle pblock-hndle"><?php _e( 'Pinterest Plugin News', 'pblock' ); ?></h3>
						
						<div class="inside">
							<? echo pblock_rss_news(); ?>
						</div>
					</div>

					<div class="postbox">
						<div class="handlediv pblock-handlediv" title="Click to toggle"><br /></div>
						<h3 class="hndle pblock-hndle"><?php _e( 'Subscribe by Email', 'pblock' ); ?></h3>
						
						<div class="inside">
							<p><?php _e( 'Want to know when new Pinterest plugins and features are released?', 'pblock' ); ?></p>
							&raquo; <strong><a href="http://pinterestplugin.com/newsletter-from-plugin" target="_blank" class="external">
								<?php _e( 'Get Updates', 'pblock' ); ?></a></strong>
						</div>
					</div>
				</div>
            </div>
			
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
												<a href="http://pinterestplugin.com/disable-pinning/" target="_blank">meta tag</a>.
											</p>
											</p>
												Besides the types of pages above, <strong>any individual post or page</strong> can be blocked.
												Just go to the edit screen for that post or page, scroll down to the bottom, and check the
												<strong>"Block Pinning"</strong> checkbox.
											</p>
											<p>
												Note that the meta tag only blocks people from pinning using the official bookmarklet or "Add" feature
												on Pinterest.com itself. <em>"Pin It" buttons on the page might still work.</em> Get the
												<a href="http://pinterestplugin.com/pin-it-button/" target="_blank">"Pin It" Button plugin</a> 
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

//Render rss items from pinterestplugin.com
//http://codex.wordpress.org/Function_Reference/fetch_feed

function pblock_rss_news() {
	// Get RSS Feed(s)
	include_once(ABSPATH . WPINC . '/feed.php');

	// Get a SimplePie feed object from the specified feed source.
	$rss = fetch_feed('http://pinterestplugin.com/feed/');
	
	if (!is_wp_error( $rss ) ) {
		// Checks that the object is created correctly 
		// Figure out how many total items there are, but limit it to 5. 
		$maxitems = $rss->get_item_quantity(3); 

		// Build an array of all the items, starting with element 0 (first element).
		$rss_items = $rss->get_items(0, $maxitems); 
	}
	
	?>

	<ul>
		<?php if ($maxitems == 0): ?>
			<li><?php _e( 'No items.', 'pblock' ); ?></li>
		<?php else: ?>
			<?php
			// Loop through each feed item and display each item as a hyperlink.
			foreach ( $rss_items as $item ): ?>
				<li>
					&raquo; <a href="<?php echo esc_url( $item->get_permalink() ); ?>" target="_blank" class="external">
						<?php echo esc_html( $item->get_title() ); ?></a>
				</li>
			<?php endforeach; ?>
		<?php endif; ?>
	</ul>
	
	<?php
}

//Render Facebook Share button
//http://developers.facebook.com/docs/share/

function pblock_share_facebook() {
	?>	
	<a name="fb_share" type="button" share_url="http://pinterestplugin.com/" alt="Share on Facebook"></a> 
	<script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" type="text/javascript"></script>	
	<?php
}

//Render Twitter button
//https://twitter.com/about/resources/buttons

function pblock_share_twitter() {
	?>
    <a href="https://twitter.com/share" class="twitter-share-button" data-url="http://pinterestplugin.com" data-text="I'm using the Pinterest Block Plugin for WordPress. It rocks!" data-count="none">Tweet</a>
    <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>	
	<?php
}

//Render Pin It button
//Render in iFrame otherwise it messes up the WP admin left menu

function pblock_share_pinterest() {
	?>
	<a href="http://pinterest.com/pin/create/button/?url=http%3A%2F%2Fpinterestplugin.com%2F&media=http%3A%2F%2Fpinterestplugin.com%2Fimg%2Fpinterest-block-wordpress-plugin.png&description=Pinterest%20Block%20WordPress%20Plugin%20--%20http%3A%2F%2Fpinterestplugin.com%2F" class="pin-it-button" count-layout="horizontal"><img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" /></a>
	<script type="text/javascript" src="//assets.pinterest.com/js/pinit.js"></script>
	<?php
}

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
