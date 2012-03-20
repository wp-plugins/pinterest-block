<?php
/*
  Plugin Name: Pinterest Block
  Plugin URI: http://pinterestplugin.com
  Description: Block selected posts and pages categories from getting pinned on Pinterest.
  Author: Phil Derksen
  Author URI: http://pinterestplugin.com
  Version: 1.0.0
  License: GPLv2
  Copyright 2012 Phil Derksen (phil@pinterestplugin.com)
*/  


//Set global variables

if ( ! defined( 'PBLOCK_PLUGIN_BASENAME' ) )
	define( 'PBLOCK_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

define( 'PBLOCK_META_TAG', '<meta name="pinterest" content="nopin" />' . "\n" );
	
//Plugin install/activation

function pblock_install() {
	
	//Deactivate plugin if WP version too low
    if ( version_compare( get_bloginfo( 'version' ), '3.0', '<' ) ) {
        deactivate_plugins( basename( __FILE__ ) );
    }
	
	//Setup default settings
	$pblock_options = array(
		'block_home_page' => 0,
		'block_front_page' => 0,
		'block_posts' => 0,
		'block_pages' => 0,
		'block_archives' => 0			
	);

	//Save default option values
	add_option( 'pblock_options', $pblock_options );
	add_option( 'pblock_ignore', 'false');
}

register_activation_hook( __FILE__, 'pblock_install' );


//Add settings page to admin menu

function pblock_create_menu() {
	$page = add_submenu_page( 'options-general.php', 'Pinterest Block Settings', 'Pinterest Block', 'manage_options', __FILE__, 'pblock_create_settings_page' );
	add_action('admin_print_styles-' . $page, 'pblock_add_admin_css_js');
}

add_action( 'admin_menu', 'pblock_create_menu' );


//Add Admin CSS/JS

function pblock_add_admin_css_js() {	
	wp_enqueue_script( 'jquery' );

	wp_enqueue_style( 'pinterest-block', plugins_url( '/css/pinterest-block-admin.css' , __FILE__ ) );
	wp_enqueue_script( 'pinterest-block', plugins_url( '/js/pinterest-block-admin.js', __FILE__ ), array( 'jquery' ) );
}


//Add script and css for Pointer funtionallity

function pblock_add_admin_css_js_pointer() {
	wp_enqueue_style( 'wp-pointer' );
    wp_enqueue_script( 'wp-pointer' );
	
    add_action( 'admin_print_footer_scripts', 'pblock_admin_print_footer_scripts' );
}

add_action( 'admin_enqueue_scripts', 'pblock_add_admin_css_js_pointer' );


//Add popup message when plugin installed 

function pblock_admin_print_footer_scripts() {
    $pblock_pointer_content = '<h3>Pinterest Block Installed!</h3>';

    $pblock_pointer_content .= '<p>' . esc_attr('Congratulations. You have just installed the Pinterest Block Plugin. Now just configure ' .
        'your settings to specify what gets blocked.') . '</p>';

    $pblock_url = admin_url( 'admin.php?page=' . PBLOCK_PLUGIN_BASENAME );
    
    global $pagenow;
    $pblock_ignore = get_option('pblock_ignore');
    
    if ( 'plugins.php' == $pagenow && $pblock_ignore == 'false' ) {
	?>

    <script type="text/javascript">
        //<![CDATA[
        jQuery(document).ready( function($) {
	
            $('#menu-plugins').pointer({
                content: '<?php echo $pblock_pointer_content; ?>',
                buttons: function( event, t ) {
                    button = jQuery('<a id="pointer-close" class="button-secondary">' + '<?php echo "Close"; ?>' + '</a>');
                    button.bind( 'click.pointer', function() {
                        t.element.pointer('close');
                    });
                    return button;
                },
                position: 'left',
                close: function() { }
        
            }).pointer('open');
          
            jQuery('#pointer-close').after('<a id="pointer-primary" class="button-primary" style="margin-right: 5px;" href="<?php echo esc_attr($pblock_url); ?>">' + 
                '<?php echo "Pinterest Block Settings"; ?>' + '</a>');
            
            jQuery('#pointer-primary').click( function() {
                <?php update_option('pblock_ignore' , 'true'); ?>
            });
		
            jQuery('#pointer-close').click( function() {
                <?php update_option('pblock_ignore' , 'true'); ?>
            });
        });
        //]]>
    </script>

	<?php
	}
}


//Register settings

function pblock_register_settings() {
	register_setting( 'pblock-settings-group', 'pblock_options' );
}

add_action( 'admin_init', 'pblock_register_settings' );


//Create settings page

function pblock_create_settings_page() {
	//Load options array
	$pblock_options = get_option( 'pblock_options' );
	
	?>
        <div class="wrap">
			<a href="http://pinterestplugin.com/" target="_blank"><div id="pinterest-button-icon-32" class="icon32"
                style="background: url(<?php echo plugins_url( '/img/pinterest-button-icon-med.png', __FILE__ ); ?>) no-repeat;"><br /></div></a>
            <h2>Pinterest Block Settings</h2>
            
            <div class="metabox-holder">
                <div class="pblock-settings postbox-container">
					<div class="meta-box-sortables ui-sortable">
						<?php settings_errors(); //Display status messages after action ("settings saved", errors) ?>
					
						<form method="post" action="options.php">
							<?php settings_fields( 'pblock-settings-group' ); ?>							
							
							 <div id="pblock-options" class="postbox pblock-postbox">
								 <!--Collapsable-->
								<div class="handlediv pblock-handlediv" title="Click to toggle"><br /></div>						
								<h3 class="hndle pblock-hndle">What types of pages should pinning be blocked on?</h3>
								
								<table class="form-table inside">
									<tr valign="top">
										<td>
											<input id="block_home_page" name="pblock_options[block_home_page]" type="checkbox" 
												<?php if ( $pblock_options['block_home_page'] ) echo 'checked="checked"'; ?> />
											<label for="block_home_page">Blog Home Page (or Latest Posts Page)</label>
										</td>
									</tr>
									<tr valign="top">
										<td>
											<input id="block_front_page" name="pblock_options[block_front_page]" type="checkbox" 
												<?php if ( $pblock_options['block_front_page'] ) echo 'checked="checked"'; ?> />
											<label for="block_front_page">Front Page (different from Home Page only if set in Settings > Reading)</label>
										</td>
									</tr>					
									<tr valign="top">
										<td>
											<input id="block_posts" name="pblock_options[block_posts]" type="checkbox" 
												<?php if ( $pblock_options['block_posts'] ) echo 'checked="checked"'; ?> />
											<label for="block_posts">Individual Posts</label>
										</td>
									</tr>
									<tr valign="top">
										<td>
											<input id="block_pages" name="pblock_options[block_pages]" type="checkbox" 
												<?php if ( $pblock_options['block_pages'] ) echo 'checked="checked"'; ?> />
											<label for="block_pages">WordPress Static "Pages"</label>
										</td>
									</tr>
									<tr valign="top">
										<td>
											<input id="block_archives" name="pblock_options[block_archives]" type="checkbox" 
												<?php if ( $pblock_options['block_archives'] ) echo 'checked="checked"'; ?> />
											<label for="block_archives">Archives (includes Category, Tag, Author and time-based pages)</label>
										</td>
									</tr>
								</table>
							</div>
							
							<div class="postbox pblock-postbox">
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
												<a href="http://wordpress.org/extend/plugins/pinterest-pin-it-button/" target="_blank">"Pin It" Button plugin</a> 
												to specify the posts and pages the button should be shown on.
											</p>
										</td>
									</tr>
								</table>
                            </div>
                            
							<div class="submit">
								<input name="Submit" type="submit" value="Save Settings" class="button-primary" />
                            </div>
						</form>
					</div>
				</div>
                
	            <div class="pblock-right-column postbox-container">
					<div class="meta-box-sortables ui-sortable">
						<div class="postbox pblock-postbox">
							<div class="handlediv pblock-handlediv" title="Click to toggle"><br /></div>
							<h3 class="hndle pblock-hndle">Other Links</h3>
							
							<div class="inside">
                                <ul>
                                    <li><a href="http://pinterestplugin.com/" target="_blank">Pinterest Plugin Updates</a> (Official Site)</li>
                                    <!--<li><a href="http://wordpress.org/extend/plugins/pinterest-follow-button/faq/" target="_blank">Frequently Asked Questions</a></li>-->
                                    <li><a href="http://pinterestplugin.com/user-support" target="_blank">User Support &amp; Feature Requests</a></li>
                                </ul>
							</div>
						</div>
						<div class="postbox pblock-postbox">
							<div class="handlediv pblock-handlediv" title="Click to toggle"><br /></div>
							<h3 class="hndle pblock-hndle">More Pinterest Plugins</h3>
							
							<div class="inside">
                                <ul>
                                    <li><a href="http://wordpress.org/extend/plugins/pinterest-pin-it-button/" target="_blank">Pinterest "Pin It" Button</a></li>
                                    <li><a href="http://wordpress.org/extend/plugins/pinterest-follow-button/" target="_blank">Pinterest "Follow" Button</a></li>
                                </ul>
							</div>
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
	$settings_link = '<a href="' . esc_attr( $url ) . '">' . esc_html( __( 'Settings') ) . '</a>';

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

	if ( empty( $pblock_checked ) || $pblock_checked === false )
		$pblock_checked = '';
	else
		$pblock_checked = 'checked="checked"';
	?>
	
	<p>
		<input name="pblock_enable_pinning" id="pblock_enable_pinning" value="1" <?php echo $pblock_checked; ?> type="checkbox" />
		<label for="pblock_enable_pinning">Block Pinning on this post/page.</label>
		<p class="description">
			If checked blocks pinning on this post/page (if <strong>Individual Posts</strong> (for posts) or <strong>WordPress Static "Pages"</strong> 
			(for pages) is also checked in <a href='<?php echo 'admin.php?page=' . PBLOCK_PLUGIN_BASENAME ?>'>Pinterest Block Settings</a>.
		</p>
		
		<input type="hidden" name="pblock_sharing_status_hidden" value="1" />
	</p>
		
	<?php
}


//Saves display option for individual post/page

function pblock_meta_box_save( $post_id ) {
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
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
	
	if ( ($pblock_options['block_home_page'] && is_home()) || 
	     ($pblock_options['block_front_page'] && is_front_page()) ||
		 ($pblock_options['block_archives'] && is_archive())	    		
	) {
		echo PBLOCK_META_TAG;
		return;
	}
	
	//Check for block for single page or post globally
	if ( ($pblock_options['block_posts'] && is_single()) || ($pblock_options['block_pages'] && is_page()) ) {
		echo PBLOCK_META_TAG;
		return;	
	}
	 
	global $wpdb;
    $sql = "SELECT post_id
            FROM $wpdb->postmeta
            WHERE meta_key = 'pblock_pinning_blocked' AND meta_value = '1'"; 
            
    $results = $wpdb->get_results( $sql, ARRAY_A );
        
    foreach( $results as $postid ) {
		$postID = $postid['post_id'];
		
		if ( is_single($postID) || is_page($postID) )
	    {	
			echo PBLOCK_META_TAG;
        }
    }
}

add_action('wp_head', 'pblock_add_block');

?>
