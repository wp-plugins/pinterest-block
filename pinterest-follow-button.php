<?php
/*
  Plugin Name: Pinterest "Follow" Button
  Plugin URI: http://pinterestplugin.com
  Description: Add a Pinterest "Follow" button to your sidebar with this widget. Also includes a shortcode.
  Author: Phil Derksen
  Author URI: http://pinterestplugin.com
  Version: 1.1.0
  License: GPLv2
  Copyright 2012 Phil Derksen (phil@pinterestplugin.com)
*/


//Set global variables

if ( ! defined( 'PFB_PLUGIN_BASENAME' ) )
	define( 'PFB_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
    
    
//Plugin install/activation

function pfb_install() {	
	//Deactivate plugin if WP version too low
    if ( version_compare( get_bloginfo( 'version' ), '3.0', '<' ) ) {
        deactivate_plugins( basename( __FILE__ ) );
    }
	
	//Save default option values
	add_option( 'pfb_ignore', 'false');	
}

register_activation_hook( __FILE__, 'pfb_install' );


//Add settings page to admin menu

function pfb_create_menu() {
	$page = add_submenu_page( 'options-general.php', 'Pinterest "Follow" Button Settings', 'Pinterest "Follow" Button', 'manage_options', __FILE__, 'pfb_create_settings_page' );
	add_action('admin_print_styles-' . $page, 'pfb_add_admin_css_js');
}

add_action( 'admin_menu', 'pfb_create_menu' );


//Add Admin CSS/JS

function pfb_add_admin_css_js() {	
	wp_enqueue_script( 'jquery' );

	wp_enqueue_style( 'pinterest-follow-button', plugins_url( '/css/pinterest-follow-button-admin.css' , __FILE__ ) );
	wp_enqueue_script( 'pinterest-follow-button', plugins_url( '/js/pinterest-follow-button-admin.js', __FILE__ ), array( 'jquery' ) );
}

//Add script and css for Pointer funtionallity

function pfb_add_admin_css_js_pointer() {
	wp_enqueue_style( 'wp-pointer' );
    wp_enqueue_script( 'wp-pointer' );
	
    add_action( 'admin_print_footer_scripts', 'pfb_admin_print_footer_scripts' );
}

add_action( 'admin_enqueue_scripts', 'pfb_add_admin_css_js_pointer' );


//Add popup message when plugin installed 

function pfb_admin_print_footer_scripts() {
    $pfb_pointer_content = '<h3>Pinterest "Follow" Button Installed!</h3>';

    $pfb_pointer_content .= '<p>' . esc_attr('Congratulations. You have just installed the Pinterest "Follow" Button Plugin. Now just head ' .
        'over to Widgets and drag a Follow button to your sidebar.') . '</p>';

    //$pfb_url = admin_url( 'admin.php?page=' . PFB_PLUGIN_BASENAME );
	$pfb_url = admin_url( 'widgets.php' );
    
    global $pagenow;
    $pfb_ignore = get_option('pfb_ignore');
    
    if ( 'plugins.php' == $pagenow && $pfb_ignore == 'false' ) {
	?>

    <script type="text/javascript">
        //<![CDATA[
        jQuery(document).ready( function($) {
	
            $('#menu-plugins').pointer({
                content: '<?php echo $pfb_pointer_content; ?>',
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
          
            jQuery('#pointer-close').after('<a id="pointer-primary" class="button-primary" style="margin-right: 5px;" href="<?php echo esc_attr($pfb_url); ?>">' + 
                '<?php echo "Widgets"; ?>' + '</a>');
            
            jQuery('#pointer-primary').click( function() {
                <?php update_option('pfb_ignore' , 'true'); ?>
            });
		
            jQuery('#pointer-close').click( function() {
                <?php update_option('pfb_ignore' , 'true'); ?>
            });
        });
        //]]>
    </script>

	<?php
	}
}


//Register settings

function pfb_register_settings() {
	register_setting( 'pfb-settings-group', 'pfb_options' );
}

add_action( 'admin_init', 'pfb_register_settings' );


//Create settings page

function pfb_create_settings_page() {
	?>
		<div class="wrap">
			<a href="http://pinterestplugin.com/" target="_blank"><div id="pinterest-button-icon-32" class="icon32"
				style="background: url(<?php echo plugins_url( '/img/pinterest-button-icon-med.png', __FILE__ ); ?>) no-repeat;"><br /></div></a>
			<h2>Pinterest "Follow" Button Settings</h2>
			
			<div class="metabox-holder">
				<div class="pfb-settings postbox-container">
					<div class="meta-box-sortables ui-sortable">
						<?php settings_errors(); //Display status messages after action ("settings saved", errors) ?>
					
						<form method="post" action="options.php">
							<?php settings_fields( 'pfb-settings-group' ); ?>							
							
                            <div class="postbox pfb-postbox">
								<div class="handlediv pfb-handlediv" title="Click to toggle"><br /></div>
								<h3 class="hndle pfb-hndle">Shortcode Instructions</h3>
                                
                                <div class="inside">
                                    <p>
                                        <em>If you just want to add the button to your sidebar, head over to  <a href="<?php echo admin_url( 'widgets.php' ); ?>">Widgets</a>.</em>
                                    </p>
                                    <p>
                                        Use the shortcode <code>[pinterest-follow]</code> to display the button within your content.
                                    </p>
                                    <p>
                                        Use the function <code><?php echo htmlentities('<?php echo do_shortcode(\'[pinterest-follow]\'); ?>'); ?></code>
										to display within template or theme files.
                                    </p>
                                    <p><strong>Shortcode parameters</strong></p>
                                    <p>
                                        - username: Pinterest username<br/>
                                        - button_type: 1 (default), 2, 3, 4 -- the 4 official button images from Pinterest<br/>
                                        - image_url: URL of a custom image button (leave out button_type attribute)<br/>
                                        - new_window: false (default), true -- if true opens Pinterest profile in a new window<br/>
                                        - float: none (default), left, right<br/>
                                        - remove_div: false (default), true -- if true removes surrounding div tag, which also removes float setting
                                    </p>
                                    <p><strong>Examples</strong></p>
                                    <p>
                                        <code>[pinterest-follow username="philderksen" button_type="1"]</code><br/>
                                        <code>[pinterest-follow username="philderksen" image_url="http://www.mysite.com/myimage.jpg" 
                                            new_window="true" float="right"]</code><br/>
                                    </p>
                                </div>
                            </div>							
							
							<div class="submit">
								<input name="Submit" type="submit" value="Save Settings" class="button-primary" />
                            </div>							
						</form>
					</div>
				</div>
                
	            <div class="pfb-right-column postbox-container">
					<div class="meta-box-sortables ui-sortable">
						<div class="postbox pfb-postbox">
							<div class="handlediv pfb-handlediv" title="Click to toggle"><br /></div>
							<h3 class="hndle pfb-hndle">Other Links</h3>
							
							<div class="inside">
                                <ul>
                                    <li><a href="http://pinterestplugin.com/" target="_blank">Pinterest Plugin Updates</a> (Official Site)</li>
                                    <!--<li><a href="http://wordpress.org/extend/plugins/pinterest-follow-button/faq/" target="_blank">Frequently Asked Questions</a></li>-->
                                    <li><a href="http://pinterestplugin.com/user-support" target="_blank">User Support &amp; Feature Requests</a></li>
                                </ul>
							</div>
						</div>
						<div class="postbox pfb-postbox">
							<div class="handlediv pfb-handlediv" title="Click to toggle"><br /></div>
							<h3 class="hndle pfb-hndle">More Pinterest Plugins</h3>
							
							<div class="inside">
                                <ul>
                                    <li><a href="http://wordpress.org/extend/plugins/pinterest-pin-it-button/" target="_blank">Pinterest "Pin It" Button</a></li>
                                    <li><a href="http://wordpress.org/extend/plugins/pinterest-block/" target="_blank">Pinterest Block</a></li>
                                </ul>
							</div>
						</div>                    
					</div>
				</div> 
                
			</div>
		</div>
	<?php
}


//Add Public CSS/JS

function pfb_add_public_css_js() {
	wp_enqueue_style( 'pinterest-follow-button', plugins_url( '/css/pinterest-follow-button.css' , __FILE__ ) );
}

add_action( 'wp_enqueue_scripts', 'pfb_add_public_css_js' );


//Function for rendering Follow button base html

function pfb_button_base( $pfb_username, $button_type, $img_url, $new_window, $float ) {
	$use_img_url = false;
	$img_width_height_attr = '';
	$default_img_filename = 'follow-on-pinterest-button.png';;
	$default_img_width = 156;
	$default_img_height = 26;
	
	//Check for valid button type (1-4)
	switch ( $button_type ) {
		//case 1 -- Use button type 1 if invalid button type
		case 1:
			$img_filename = $default_img_filename;
			$img_width = $default_img_width;
			$img_height = $default_img_height;
			break;
		case 2:
			$img_filename = 'pinterest-button.png';
			$img_width = 78;
			$img_height = 26;
			break;
		case 3:
			$img_filename = 'big-p-button.png';
			$img_width = 61;
			$img_height = 61;
			break;
		case 4:
			$img_filename = 'small-p-button.png';
			$img_width = 16;
			$img_height = 16;
			break;
		default:
			$use_img_url = true;
			break;
	}
	
	//Use default button type if no image url and invalid button type
	if ( $use_img_url && !$img_url ) {
		$img_filename = $default_img_filename;
		$img_width = $default_img_width;
		$img_height = $default_img_height;
		$use_img_url = false;
	}
	
	//If image URL not specified use button type
	if ( !$use_img_url ) {
		$img_url = 'http://passets-cdn.pinterest.com/images/' . $img_filename;
		$img_width_height_attr = 'width="' . $img_width . '" height="' . $img_height . '"';
	}
	
	$btn = '<a href="http://pinterest.com/' . urlencode($pfb_username) . '/" title="Follow Me on Pinterest" ' .
		( $new_window ? 'target="_blank"' : '' ) . '>' .
        '<img src="' . $img_url . '" ' . $img_width_height_attr . ' alt="Follow Me on Pinterest" /></a>';

	return $btn;
}


//Add Pinterest Follow Button Widget

class Pfb_Follow_Button_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'pfb-clearfix', 'description' => __( 'Add a Pinterest "Follow" button to your sidebar with this widget.') );
		$control_ops = array('width' => 400);  //doesn't use height
		parent::__construct('pfb_follow_button', __('Pinterest "Follow" Button'), $widget_ops, $control_ops);
	}

	function widget( $args, $instance ) {
		extract($args);
		
		$title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
		$pfb_username = $instance['pfb_username'];
		$pfb_img_option = $instance['pfb_follow_button_radio'];
		$pfb_url_of_img = $instance['pfb_url_of_img'];
		$float_follow = empty( $instance['float_follow'] ) ? 'none' : $instance['float_follow'];
		$new_window = (bool)$instance['new_window'];
		$pfb_remove_div = (bool)$instance['remove_div'];
		
		$baseBtn = pfb_button_base( $pfb_username, $pfb_img_option, $pfb_url_of_img, $new_window, $float_follow );
		
		echo $before_widget;
        
		if ( $title )
			echo $before_title . $title . $after_title;
            
		if ( $pfb_remove_div ) {
			echo $baseBtn;
		}
		else {
			//Surround with div tag
			$float_class = '';
			
			if ( $float == 'left' ) {
				$float_class = 'pfb-float-left';
			}
			elseif ( $float == 'right' ) {
				$float_class = 'pfb-float-right';
			}
		
			echo '<div class="pinterest-follow-btn-wrapper-widget ' . $float_class . '">' . $baseBtn . '</div>';
		}
	
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$new_instance = wp_parse_args( (array) $new_instance, array( 'title' => '',  'pfb_username' => '', 'pfb_follow_button_radio' => '1', 'float_follow' => 'none') );
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['pfb_username'] = strip_tags($new_instance['pfb_username']);
		$instance['pfb_follow_button_radio'] = strip_tags($new_instance['pfb_follow_button_radio']);
		$instance['pfb_url_of_img'] = strip_tags($new_instance['pfb_url_of_img']);
		$instance['float_follow'] = $new_instance['float_follow'];
		$instance['new_window'] = ( $new_instance['new_window'] ? 1 : 0 );
		$instance['remove_div'] = ( $new_instance['remove_div'] ? 1 : 0 );
		
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'pfb_username' => '', 'pfb_follow_button_radio' => '1','float_follow' => 'none') );
		$title = strip_tags($instance['title']); 
		$pfb_username = strip_tags($instance['pfb_username']);
		$pfb_follow_button_radio = $instance['pfb_follow_button_radio'];
		$pfb_url_of_img = strip_tags($instance['pfb_url_of_img']); 
        ?>
        
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title (optional):'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('pfb_username'); ?>"><?php _e('Pinterest Username (required):'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('pfb_username'); ?>" name="<?php echo $this->get_field_name('pfb_username'); ?>" type="text" value="<?php echo esc_attr($pfb_username); ?>" />
		</p>
		
        <p><label>Button image:</label></p>
        
		<table>
			<tr>
				<td><input type="radio" <?php if($pfb_follow_button_radio == 1){ echo'checked="checked"';} ?> name="<?php echo $this->get_field_name('pfb_follow_button_radio'); ?>" id="<?php echo $this->get_field_id('follow-on-pinterest-button'); ?>" value="1" /></td>
				<td><img src="http://passets-cdn.pinterest.com/images/follow-on-pinterest-button.png" width="156" height="26" alt="Follow Me on Pinterest" /></td>
			</tr>
			<tr>
				<td><input type="radio"  <?php if($pfb_follow_button_radio == 2){ echo'checked="checked"';} ?> name="<?php echo $this->get_field_name('pfb_follow_button_radio'); ?>" id="<?php echo $this->get_field_id('pinterest-button'); ?>" value="2"/></td>
				<td><img src="http://passets-cdn.pinterest.com/images/pinterest-button.png" width="78" height="26" alt="Follow Me on Pinterest" /></td>
			</tr>
			<tr>
				<td><input type="radio"  <?php if($pfb_follow_button_radio == 3){ echo'checked="checked"';} ?> name="<?php echo $this->get_field_name('pfb_follow_button_radio'); ?>" id="<?php echo $this->get_field_id('big-p-button'); ?>" value="3"/></td>
				<td><img src="http://passets-cdn.pinterest.com/images/big-p-button.png" width="61" height="61" alt="Follow Me on Pinterest" /></td>
			</tr>
			<tr>
				<td><input type="radio"  <?php if($pfb_follow_button_radio == 4){ echo'checked="checked"';} ?> name="<?php echo $this->get_field_name('pfb_follow_button_radio'); ?>" id="<?php echo $this->get_field_id('small-p-button'); ?>" value="4"/></td>
				<td><img src="http://passets-cdn.pinterest.com/images/small-p-button.png" width="16" height="16" alt="Follow Me on Pinterest" /></td>
			</tr>
			<tr>
				<td><input type="radio"  <?php if($pfb_follow_button_radio == 0){ echo'checked="checked"';} ?> name="<?php echo $this->get_field_name('pfb_follow_button_radio'); ?>" id="<?php echo $this->get_field_id('image_url'); ?>" value="0"/></td>
				<td>Specify your own image URL:</td>
			</tr>
		</table>

		<p>
			<input class="widefat" id="<?php echo $this->get_field_id('pfb_url_of_img'); ?>" name="<?php echo $this->get_field_name('pfb_url_of_img'); ?>" type="text" value="<?php echo esc_attr($pfb_url_of_img); ?>" />
		</p>
        <p>
            <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('new_window'); ?>" name="<?php echo $this->get_field_name('new_window'); ?>" <?php checked($instance['new_window'], true) ?> />
            <label for="<?php echo $this->get_field_id('new_window'); ?>"><?php _e( 'Open in a new window' ); ?></label>
        </p>
		<p>
            <label for="<?php echo $this->get_field_id('float_follow'); ?>"><?php _e('Align (float):'); ?></label> 
            <select name="<?php echo $this->get_field_name('float_follow'); ?>" id="<?php echo $this->get_field_id('float_follow'); ?>">
                <option value="none"<?php selected( $instance['float_follow'], 'none' ); ?>><?php _e('none (default)'); ?></option>
                <option value="left"<?php selected( $instance['float_follow'], 'left' ); ?>><?php _e('left'); ?></option>
                <option value="right"<?php selected( $instance['float_follow'], 'right' ); ?>><?php _e('right'); ?></option>
            </select>
		</p>
		<p>
			<input class="checkbox" <?php checked($instance['remove_div'], true) ?> id="<?php echo $this->get_field_id('remove_div'); ?>" name="<?php echo $this->get_field_name('remove_div'); ?>" type="checkbox"/>
			<label for="<?php echo $this->get_field_id('remove_div'); ?>">Remove div tag surrounding this widget button (also removes <strong>float</strong> setting)</label>
		</p>
        <p>
            <a href="<?php echo $url = admin_url( 'admin.php?page=' . PFB_PLUGIN_BASENAME ); ?>">Shortcode Instructions</a> |
            <a target="_blank" href="http://pinterestplugin.com/">User Support</a>
        </p>
        
        <?php
	}
}


//Add function to the widgets_init hook. 
add_action( 'widgets_init', 'pfb_load_follow_button_widget' );


// Function that registers Follow Button widget. 
function pfb_load_follow_button_widget() {
	register_widget( 'Pfb_Follow_Button_Widget' );
}

//Register shortcode: [pinterest-follow username="" button_type="" image_url="" new_window="false" float="none" remove_div="false"]

function pfb_button_shortcode_html($attr) {
	$attr['username'] = ( empty( $attr['username'] ) ? '' : $attr['username'] );
	$attr['button_type'] = ( empty( $attr['button_type'] ) ? '' : $attr['button_type'] );
	$attr['image_url'] = ( empty( $attr['image_url'] ) ? '' : $attr['image_url'] );
	$attr['float'] = ( empty( $attr['float'] ) ? 'none' : $attr['float'] );
	$new_window_bool = ( $attr['new_window'] == 'true' );
	$remove_div_bool = ( $attr['remove_div'] == 'true' );

	$baseBtn = pfb_button_base( $attr['username'], $attr['button_type'], $attr['image_url'], $new_window_bool, $attr['float'] );
	
	if ( $remove_div_bool ) {
		return $baseBtn;
	}	
	else {
		//Surround with div tag
		$float_class = '';
		
		if ( $float == 'left' ) {
			$float_class = 'pfb-float-left';
		}
		elseif ( $float == 'right' ) {
			$float_class = 'pfb-float-right';
		}
	
		return '<div class="pinterest-follow-btn-wrapper-shortcode ' . $float_class . '">' . $baseBtn . '</div>';
	}
}

add_shortcode( 'pinterest-follow', 'pfb_button_shortcode_html' );

?>
