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

$plugin_url = WP_PLUGIN_URL . '/vertusdl-testimonials';
$options = array();

/****************************
*
* Import New Post Type & Meta
*
****************************/

require ( 'inc/post_type_meta.php' );




/************************************************
*
* Create options and default settings in database
*
************************************************/

function vertusdl_options_init() {
	$new_options = array(
		'widget_style_type' => 'style1',
		'use_gravatar' => 'yes',
		//'testimonials_truncate' => 300,
		//'widget_height' => 400,
	);

	add_option( 'vertusdl_testimonial_options', $new_options );

}
add_action( 'init', 'vertusdl_options_init' );




function vertusdl_testimonials_admin_add_page () {

    add_submenu_page(
    	'edit.php?post_type=testimonials', 
    	'Testimonial Settings', 
    	'Settings', 
    	'manage_options',
    	'testimonial_settings', 
    	'vertusdl_testimonials_options_page'
    );
}
add_action( 'admin_menu', 'vertusdl_testimonials_admin_add_page' );




/************************************************
*
* Create Settings Page in Admin using add_submenu_page
*
************************************************/




function vertusdl_testimonials_options_page () { 

	if ( !current_user_can ( 'manage_options' ) ) {

		wp_die( 'You do not have sufficient permissions to access this page.' );

	}

	global $options;

	if ( isset( $_POST['vertusdl_form_submitted'] ) ) {

		$hidden_field = esc_html( $_POST['vertusdl_form_submitted'] );

		// Pass & save the information in the database

		if ( $hidden_field == 'Y') {


			$options['widget_style_type'] = $_POST['widget_style_type'];
			$options['use_gravatar'] = $_POST['use_gravatar'];

			update_option( 'vertusdl_testimonial_options', $options );
		}
	}

	$options = get_option('vertusdl_testimonial_options');

	$widget_style_type = $options['widget_style_type'];
	$use_gravatar = $options['use_gravatar'];

	echo $widget_style_type;
	echo $use_gravatar;

	require ( 'inc/options-wrapper.php' );
}




/**********************
*
* Register new widget
*
***********************/

class Vertusdl_Testimonials_Widget extends WP_Widget {

	function vertusdl_testimonials_widget() {
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

		require ('inc/widget/front-end.php');
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


		require ('inc/widget/widget-fields.php');
	}
}

function vertusdl_testimonials_register_widget() {
	register_widget( 'Vertusdl_Testimonials_Widget' );
}

add_action( 'widgets_init', 'vertusdl_testimonials_register_widget' );




/***************************************
 * 
 * Limit the text on the Testimonials Widget.
 *
 ***************************************/

function shorten_testimonial( $string, $max_chars = 2000, $append = "\xC2\xA0…" )
{

	$truncate_text_option = get_option('widget_vertusdl_testimonials_widget');
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


function vertusdl_testimonials_styles_scripts () {

	wp_enqueue_style( 'vertusdl_testimonials_frontend_css', plugins_url( 'vertusdl-testimonials/css/vertusdl-testimonials.css' ) );
	wp_enqueue_style( 'css_gallery', plugins_url( 'vertusdl-testimonials/css/gallery.build.css' ) );

	//Load the correct style sheet based on style type

	$options = get_option('vertusdl_testimonial_options');
	$widget_style_type = $options['widget_style_type'];

	if ( $widget_style_type == 'style1' ) {
		wp_enqueue_style( 'css_gallery_theme', plugins_url( 'vertusdl-testimonials/css/gallery.theme-1.php' ) );
	}
	elseif ( $widget_style_type == 'style2' ) {
		wp_enqueue_style( 'css_gallery_theme', plugins_url( 'vertusdl-testimonials/css/gallery.theme-2.php' ) );
	}
	

}
add_action ( 'wp_enqueue_scripts', 'vertusdl_testimonials_styles_scripts' );











?>