<?php

/*
 *	Plugin Name: Vertus Sliding Testimonials
 *	Plugin URI: http://www.vertusdigital.co.uk/
 *	Description: Creates Widgets and Shortcodes for Sliding Testimonials
 *	Version: 1.0
 *	Author: Craig Bennett
 *	Author URI: http://www.vertusdigital.co.uk/
 *	License: GPL2
 *
*/

/**********************
*
* Global Variables
*
***********************/

$options = array();
define( 'vdtestim_plugin_path', plugin_dir_path( __FILE__ ) );

/****************************
*
* Import New Post Type & Meta
*
****************************/

require ( vdtestim_plugin_path . 'includes/admin/post_type_meta.php' );




/************************************************
*
* Create options and default settings in database
*
************************************************/
 
function vdtestim_options_init() {
	$new_options = array(
		'widget_style_type' => 'style1',
		'use_gravatar' => 'yes',
		'slider_display_duration' => 15,
		'slider_fade_duration' => 2
		
	);

	add_option( 'vdtestim_options', $new_options );

}
add_action( 'init', 'vdtestim_options_init' );



function vdtestim_admin_add_page () {

    add_submenu_page(
    	'edit.php?post_type=testimonials', 
    	'Testimonial Settings', 
    	'Settings', 
    	'manage_options',
    	'testimonial_settings', 
    	'vdtestim_options_page'
    );
}
add_action( 'admin_menu', 'vdtestim_admin_add_page' );




/************************************************
*
* Create Settings Page in Admin using add_submenu_page
*
************************************************/




function vdtestim_options_page () { 

	$options = get_option( 'vdtestim_options' );

	if ( !current_user_can ( 'manage_options' ) ) {

		wp_die( 'You do not have sufficient permissions to access this page.' );

	}

	global $options;

	if ( isset( $_POST['vdtestim_form_submitted'] ) ) {

		$hidden_field = esc_html( $_POST['vdtestim_form_submitted'] );

		// Pass & save the information in the database

		if ( $hidden_field == 'Y') {

			$options['widget_style_type'] = $_POST['widget_style_type'];
			$options['use_gravatar'] = $_POST['use_gravatar'];
			$options['slider_display_duration'] = $_POST['slider_display_duration'];
			$options['slider_fade_duration'] = $_POST['slider_fade_duration'];

			update_option( 'vdtestim_options', $options );
		}
	}

	$options = get_option('vdtestim_options');

	$widget_style_type = $options['widget_style_type'];
	$use_gravatar = $options['use_gravatar'];
	$slider_display_duration = $options['slider_display_duration'];
	$slider_fade_duration = $options['slider_fade_duration'];

	echo $widget_style_type;
	echo $use_gravatar;
	echo $slider_display_duration;
	echo $slider_fade_duration;

	require ( vdtestim_plugin_path . 'includes/admin/options-wrapper.php' );
}




/**********************
*
* Register new widget
*
***********************/

class Vdtestim_Widget extends WP_Widget {

	function vdtestim_widget() {
		// Instantiate the parent object
		parent::__construct( false, 'A Sliding Testimonial Widget', array('description' => __('A sliding testimonial widget from Vertus Digital')));
	}

	function widget( $args, $instance ) {
		// Widget output
		extract($args);

		$title = apply_filters( 'widget_title', $instance['title'] );
		$num_testimonials = $instance['num_testimonials'];
		$slide_testimonials = $instance['slide_testimonials'];
		$truncate_text = $instance['truncate_text'];
		$widget_height = $instance['widget_height'];

		require ( vdtestim_plugin_path . 'includes/widget/front-end.php');
	}

	function update( $new_instance, $old_instance ) {
		// Save widget options

		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['num_testimonials'] = strip_tags($new_instance['num_testimonials']);
		$instance['slide_testimonials'] = strip_tags($new_instance['slide_testimonials']);
		$instance['truncate_text'] = strip_tags($new_instance['truncate_text']);
		$instance['widget_height'] = strip_tags($new_instance['widget_height']);
		return $instance;
	}

	function form( $instance ) {
		$defaults = array(
            'title' => __('Enter Title Here'),
            'num_testimonials' => '2',
            'slide_testimonials' => '1',
            'truncate_text' => '300',
            'widget_height' => '400'
        );

        $instance = wp_parse_args( (array) $instance, $defaults);
		
		// Output admin widget options form

		$title = esc_attr( $instance['title']);
		$num_testimonials = esc_attr( $instance['num_testimonials']);
		$slide_testimonials = esc_attr( $instance['slide_testimonials']);
		$truncate_text = esc_attr( $instance['truncate_text']);
		$widget_height = esc_attr( $instance['widget_height']);


		require ( vdtestim_plugin_path . 'includes/widget/admin-fields.php');
	}
}

function vdtestim_register_widget() {
	register_widget( 'Vdtestim_Widget' );
}

add_action( 'widgets_init', 'vdtestim_register_widget' );




/***************************************
 * 
 * Limit the text on the Testimonials Widget.
 *
 ***************************************/

function vdtestim_shorten_testimonial( $string, $max_chars = 2000, $append = "\xC2\xA0…" )
{

	$truncate_text_option = get_option('widget_vdtestim_widget');
    $truncate_text = $truncate_text_option[2]['truncate_text'];

	if ( $truncate_text != '' ) {
		$max_chars = $truncate_text;
	}

    $string = strip_tags( $string );
    $string = html_entity_decode( $string, ENT_QUOTES, 'utf-8' );
    // \xC2\xA0 is the no-break space
    $string = trim( $string, "\n\r\t .-;–,—\xC2\xA0" );
    $length = strlen( utf8_decode( $string ) );

    // Nothing to do.
    if ( $length < $max_chars )
    {
        return $string;
    }

    // mb_substr() is in /wp-includes/compat.php as a fallback if
    // your the current PHP installation doesn’t have it.
    $string = mb_substr( $string, 0, $max_chars, 'utf-8' );

    // No white space. One long word or chinese/korean/japanese text.
    if ( FALSE === strpos( $string, ' ' ) )
    {
        return $string . $append;
    }

    // Avoid breaks within words. Find the last white space.
    if ( extension_loaded( 'mbstring' ) )
    {
        $pos   = mb_strrpos( $string, ' ', 'utf-8' );
        $short = mb_substr( $string, 0, $pos, 'utf-8' );
    }
    else
    {
        // Workaround. May be slow on long strings.
        $words = explode( ' ', $string );
        // Drop the last word.
        array_pop( $words );
        $short = implode( ' ', $words );
    }

    return $short . $append;
}

/***************************************
 * 
 * Enqueue Front End Styles.
 *
 ***************************************/


function vdtestim_styles_scripts () {

	wp_enqueue_style( 'vdtestim_frontend_css', plugins_url( 'vertusdl-testimonials/css/vertusdl-testimonials.css' ) );

	//Load the correct style sheet based on style type

	$options = get_option('vdtestim_options');
	$widget_style_type = $options['widget_style_type'];

	if ( $widget_style_type == 'style1' ) {
		wp_enqueue_style( 'vdtestim_theme', plugins_url( 'vertusdl-testimonials/css/theme-1.css' ) );
	}
	elseif ( $widget_style_type == 'style2' ) {
		wp_enqueue_style( 'vdtestim_theme', plugins_url( 'vertusdl-testimonials/css/theme-2.css' ) );
	}
	

}
add_action ( 'wp_enqueue_scripts', 'vdtestim_styles_scripts' );










