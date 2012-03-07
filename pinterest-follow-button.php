<?php
/*
  Plugin Name: Pinterest "Follow" Button
  Plugin URI: http://pinterestplugin.com
  Description: Add a Pinterest "Follow" button to your sidebar with this widget.
  Version: 1.0.0
  Author: Phil Derksen
  Author URI: http://pinterestplugin.com
*/

/*  Copyright 2012 Phil Derksen (phil@pinterestplugin.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
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
}

register_activation_hook( __FILE__, 'pfb_install' );

//Add Admin CSS/JS

function pfb_add_admin_css_js() {
	//wp_enqueue_script( 'jquery' );
	
	wp_enqueue_style( 'pinterest-follow-button', plugins_url( '/css/pinterest-follow-button-admin.css' , __FILE__ ) );
    //wp_enqueue_script( 'pinterest-follow-button', plugins_url( '/js/pinterest-follow-button-admin.js', __FILE__ ), array( 'jquery' ) );
}

add_action( 'admin_enqueue_scripts', 'pfb_add_admin_css_js' );

//Add Public CSS/JS

function pfb_add_public_css_js() {
    //wp_enqueue_script( 'jquery' );
    
	wp_enqueue_style( 'pinterest-follow-button', plugins_url( '/css/pinterest-follow-button.css' , __FILE__ ) );
    //wp_enqueue_script( 'pinterest-follow-button', plugins_url( '/js/pinterest-follow-button.js', __FILE__ ), array( 'jquery' ) );
}

add_action( 'wp_enqueue_scripts', 'pfb_add_public_css_js' );

//Add Pinterest Follow Button Widget

class Pib_Follow_Button_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'pib_widget_follow_button', 'description' => __( 'Add a Pinterest "Follow" button to your sidebar with this widget.') );
		parent::__construct('pib_follow_button', __('Pinterest "Follow" Button'), $widget_ops);
	}

	function widget( $args, $instance ) {
		extract($args);
		
		$title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
		$pib_img_option = $instance['pib_follow_button_radio'];
		$pibusername = $instance['pibusername'];
		$newwindow = $instance['newwindow'] ? '1' : '0';
		$float_follow = empty( $instance['float_follow'] ) ? 'none' : $instance['float_follow'];
		
		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;            
	
		if($pib_img_option == 1){
			if($newwindow){
				if($float_follow == 'center') {
					echo '<div style="width:156px;margin:0px auto;"><a href="http://pinterest.com/'.$pibusername.'/" target="_blank"><img src="http://passets-cdn.pinterest.com/images/follow-on-pinterest-button.png" width="156" height="26" alt="Follow Me on Pinterest" /></a></div>';
				}
				else {
					echo '<a href="http://pinterest.com/'.$pibusername.'/" target="_blank" style="float:'.$float_follow.'"><img src="http://passets-cdn.pinterest.com/images/follow-on-pinterest-button.png" width="156" height="26" alt="Follow Me on Pinterest" /></a>';
				}
			}
			else{
				if($float_follow == 'center') {
					echo '<div style="width:156px;margin:0px auto;"><a href="http://pinterest.com/'.$pibusername.'/" ><img src="http://passets-cdn.pinterest.com/images/follow-on-pinterest-button.png" width="156" height="26" alt="Follow Me on Pinterest" /></a></div>';
				}
				else {
					echo '<a href="http://pinterest.com/'.$pibusername.'/" style="float:'.$float_follow.'"><img src="http://passets-cdn.pinterest.com/images/follow-on-pinterest-button.png" width="156" height="26" alt="Follow Me on Pinterest" /></a>';
				}
			}
		}		
		
		elseif($pib_img_option == 2){
			if($newwindow){
				if($float_follow == 'center') {
					echo'<div style="width:78px;margin:0px auto;"><a href="http://pinterest.com/'.$pibusername.'/" target="_blank"><img src="http://passets-cdn.pinterest.com/images/pinterest-button.png" width="78" height="26" alt="Follow Me on Pinterest" /></a></div>';
				}
				else {
					echo'<a href="http://pinterest.com/'.$pibusername.'/" target="_blank" style="float:'.$float_follow.'"><img src="http://passets-cdn.pinterest.com/images/pinterest-button.png" width="78" height="26" alt="Follow Me on Pinterest" /></a>';
				}
			}
			else {
				if($float_follow == 'center') {
					echo'<div style="width:78px;margin:0px auto;"><a href="http://pinterest.com/'.$pibusername.'/"><img src="http://passets-cdn.pinterest.com/images/pinterest-button.png" width="78" height="26" alt="Follow Me on Pinterest" /></a></div>';	
				}
				else {
					echo'<a href="http://pinterest.com/'.$pibusername.'/" style="float:'.$float_follow.'"><img src="http://passets-cdn.pinterest.com/images/pinterest-button.png" width="78" height="26" alt="Follow Me on Pinterest" /></a>';
				}
			}
		}
		
		elseif($pib_img_option == 3){
			if($newwindow){
				if($float_follow == 'center') {
					echo'<div style="width:61px;margin:0px auto;"><a href="http://pinterest.com/'.$pibusername.'/" target="_blank"><img src="http://passets-cdn.pinterest.com/images/big-p-button.png" width="61" height="61" alt="Follow Me on Pinterest" /></a></div>';	
				}
				else {
					echo'<a href="http://pinterest.com/'.$pibusername.'/" target="_blank" style="float:'.$float_follow.'"><img src="http://passets-cdn.pinterest.com/images/big-p-button.png" width="61" height="61" alt="Follow Me on Pinterest" /></a>';
				}
			}
			else {
				if($float_follow == 'center') {
					echo'<div style="width:61px;margin:0px auto;"><a href="http://pinterest.com/'.$pibusername.'/"><img src="http://passets-cdn.pinterest.com/images/big-p-button.png" width="61" height="61" alt="Follow Me on Pinterest" /></a></div>';
				}
				else {
					echo'<a href="http://pinterest.com/'.$pibusername.'/" style="float:'.$float_follow.'"><img src="http://passets-cdn.pinterest.com/images/big-p-button.png" width="61" height="61" alt="Follow Me on Pinterest" /></a>';
				}
			}
		}
		
		elseif($pib_img_option == 4){
			if($newwindow){
				if($float_follow == 'center') {
					echo'<div style="width:16px;margin:0px auto;"><a href="http://pinterest.com/'.$pibusername.'/" target="_blank"><img src="http://passets-cdn.pinterest.com/images/small-p-button.png" width="16" height="16" alt="Follow Me on Pinterest" /></a></div>';
				}
				else {
					echo'<a href="http://pinterest.com/'.$pibusername.'/" target="_blank" style="float:'.$float_follow.'"><img src="http://passets-cdn.pinterest.com/images/small-p-button.png" width="16" height="16" alt="Follow Me on Pinterest" /></a>';
				}
			}
			else {
				if($float_follow == 'center') {
					echo'<div style="width:16px;margin:0px auto;"><a href="http://pinterest.com/'.$pibusername.'/"><img src="http://passets-cdn.pinterest.com/images/small-p-button.png" width="16" height="16" alt="Follow Me on Pinterest" /></a></div>';
				}
				else {
					echo'<a href="http://pinterest.com/'.$pibusername.'/" style="float:'.$float_follow.'"><img src="http://passets-cdn.pinterest.com/images/small-p-button.png" width="16" height="16" alt="Follow Me on Pinterest" /></a>';
				}
			}
		}
		
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$new_instance = wp_parse_args( (array) $new_instance, array( 'title' => '',  'pibusername' => '', 'pib_follow_button_radio' => '1', 'float_follow' => 'none') );
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['pibusername'] = strip_tags($new_instance['pibusername']);
		$instance['pib_follow_button_radio'] = strip_tags($new_instance['pib_follow_button_radio']);
		$instance['newwindow'] = !empty($new_instance['newwindow']) ? 1 : 0;
		$instance['float_follow'] = $new_instance['float_follow'];
		
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'pibusername' => '', 'pib_follow_button_radio' => '1','float_follow' => 'none') );
		$title = strip_tags($instance['title']); 
		$pibusername = strip_tags($instance['pibusername']);
		$pib_follow_button_radio = $instance['pib_follow_button_radio'];
		$newwindow = isset( $instance['newwindow'] ) ? (bool) $instance['newwindow'] : false;
		
        ?>
        
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title (optional):'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('pibusername'); ?>"><?php _e('Pinterest Username (required):'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('pibusername'); ?>" name="<?php echo $this->get_field_name('pibusername'); ?>" type="text" value="<?php echo esc_attr($pibusername); ?>" />
		</p>
		
        <p><label>Button image:</label></p>
        
		<table>
			<tr>
				<td><input type="radio" <?php if($pib_follow_button_radio == 1){ echo'checked="checked"';} ?> name="<?php echo $this->get_field_name('pib_follow_button_radio'); ?>" id="<?php echo $this->get_field_id('follow-on-pinterest-button'); ?>" value="1" /></td>
				<td><img src="http://passets-cdn.pinterest.com/images/follow-on-pinterest-button.png" width="156" height="26" alt="Follow Me on Pinterest" /></td>
			</tr>
			<tr>
				<td><input type="radio"  <?php if($pib_follow_button_radio == 2){ echo'checked="checked"';} ?> name="<?php echo $this->get_field_name('pib_follow_button_radio'); ?>" id="<?php echo $this->get_field_id('pinterest-button'); ?>" value="2"/></td>
				<td><img src="http://passets-cdn.pinterest.com/images/pinterest-button.png" width="78" height="26" alt="Follow Me on Pinterest" /></td>
			</tr>
			<tr>
				<td><input type="radio"  <?php if($pib_follow_button_radio == 3){ echo'checked="checked"';} ?> name="<?php echo $this->get_field_name('pib_follow_button_radio'); ?>" id="<?php echo $this->get_field_id('big-p-button'); ?>" value="3"/></td>
				<td><img src="http://passets-cdn.pinterest.com/images/big-p-button.png" width="61" height="61" alt="Follow Me on Pinterest" /></td>
			</tr>
			<tr>
				<td><input type="radio"  <?php if($pib_follow_button_radio == 4){ echo'checked="checked"';} ?> name="<?php echo $this->get_field_name('pib_follow_button_radio'); ?>" id="<?php echo $this->get_field_id('small-p-button'); ?>" value="4"/></td>
				<td><img src="http://passets-cdn.pinterest.com/images/small-p-button.png" width="16" height="16" alt="Follow Me on Pinterest" /></td>
			</tr>
		</table>
		<br />
		<p><input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('newwindow'); ?>" name="<?php echo $this->get_field_name('newwindow'); ?>" <?php checked( $newwindow ); ?> />
		<label for="<?php echo $this->get_field_id('newwindow'); ?>"><?php _e( 'Open in a new window' ); ?></label><br />
			
			
		<p><label for="<?php echo $this->get_field_id('float_follow'); ?>"><?php _e('Align (float):'); ?></label> 
					<select name="<?php echo $this->get_field_name('float_follow'); ?>" id="<?php echo $this->get_field_id('float_follow'); ?>">
						<option value="none"<?php selected( $instance['float_follow'], 'none' ); ?>><?php _e('none (default)'); ?></option>
						<option value="left"<?php selected( $instance['float_follow'], 'left' ); ?>><?php _e('left'); ?></option>
						<option value="right"<?php selected( $instance['float_follow'], 'right' ); ?>><?php _e('right'); ?></option>
						<option value="center"<?php selected( $instance['float_follow'], 'center' ); ?>><?php _e('center'); ?></option>
					</select>
				</p>
        <?php
	}
}

//Add function to the widgets_init hook. 
add_action( 'widgets_init', 'pib_load_follow_button_widget' );

// Function that registers Follow Button widget. 
function pib_load_follow_button_widget() {
	register_widget( 'Pib_Follow_Button_Widget' );
}

?>