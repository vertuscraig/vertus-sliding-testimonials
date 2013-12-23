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
* Create Settings Page in Admin using add_submenu_page
*
************************************************/

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



function vertusdl_testimonials_options_page () { 
//Default Option Values 

//$wp_vertus_style_type = 'style1';
$wp_vertus_use_gravatar = 'yes';

	if ( !current_user_can ( 'manage_options' ) ) {

		wp_die( 'You do not have sufficient permissions to access this page.' );

	}

	if ( isset( $_POST['vertusdl_form_submitted'] ) ) {

		$hidden_field = esc_html( $_POST['vertusdl_form_submitted'] );

		// Pass & save the information in the database

		if ( $hidden_field == 'Y') {


			$vertusdl_style_type = $_POST['vertusdl_style_type'];
			$vertusdl_use_gravatar = $_POST['vertusdl_use_gravatar'];

			$options['vertusdl_style_type'] = $vertusdl_style_type;
			$options['vertusdl_use_gravatar'] = $vertusdl_use_gravatar;

			update_option( 'vertusdl_testimonials', $options );
		}
	}

$options = get_option( 'vertusdl_testimonials' );

	if ( $options != '' ) {

		$vertusdl_style_type = $options['vertusdl_style_type'];
		$vertusdl_use_gravatar  = $options['vertusdl_use_gravatar'];

	}


	echo $vertusdl_style_type;
	echo $vertusdl_use_gravatar;

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
		parent::__construct( false, 'Vertus Sliding Testimonial Widget' );
	}

	function widget( $args, $instance ) {
		// Widget output
		extract($args);

		$title = apply_filters( 'widget_title', $instance['title'] );
		$num_testimonials = $instance['num_testimonials'];
		$slide_testimonials = $instance['slide_testimonials'];
		$truncate_text = $instance['truncate_text'];

		$options = get_option( 'vertusdl_testimonials' );

		$vertusdl_style_type = $options['vertusdl_style_type'];
		$vertusdl_use_gravatar  = $options['vertusdl_use_gravatar'];

		require ('inc/widget/front-end.php');
	}

	function update( $new_instance, $old_instance ) {
		// Save widget options

		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['num_testimonials'] = strip_tags($new_instance['num_testimonials']);
		$instance['slide_testimonials'] = strip_tags($new_instance['slide_testimonials']);
		$instance['truncate_text'] = strip_tags($new_instance['truncate_text']);

		return $instance;
	}

	function form( $instance ) {

		global $truncate_text;
		// Output admin widget options form

		$title = esc_attr( $instance['title']);
		$num_testimonials = esc_attr( $instance['num_testimonials']);
		$slide_testimonials = esc_attr( $instance['slide_testimonials']);
		$truncate_text = esc_attr( $instance['truncate_text']);

		update_option( 'vertusdl_testimonials_truncate', $truncate_text );

		$options = get_option( 'vertusdl_testimonials' );

		$vertusdl_style_type = $options['vertusdl_style_type'];
		$vertusdl_use_gravatar  = $options['vertusdl_use_gravatar'];

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

	$truncate_text = get_option( 'vertusdl_testimonials_truncate' );

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

	wp_deregister_script('jquery');
	   wp_register_script('jquery', "http" . ($_SERVER['SERVER_PORT'] == 443 ? "s" : "") . "://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js", false, null);
	   wp_enqueue_script('jquery');
	wp_enqueue_style( 'vertusdl_testimonials_frontend_css', plugins_url( 'vertusdl-testimonials/css/vertusdl-testimonials.css' ) );
	wp_enqueue_script( 'vertusdl_testimonials_frontend_js', plugins_url('/js/jquery.cycle2.min.js', __FILE__, array('jquery'), '', true) );
	//wp_enqueue_script( 'vertusdl_testimonials_backend_js', plugins_url( 'vertusdl-testimonials/js/scripts.js', 'array('jquery')', '', true ) );
}
add_action ( 'wp_enqueue_scripts', 'vertusdl_testimonials_styles_scripts' );











?>