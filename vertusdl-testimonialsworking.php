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

define( 'vdtestim_plugin_path', plugin_dir_path( __FILE__ ) );

global $default_options; 
$default_options = array(
		'widget_style_type' => 'style1',
		'use_gravatar' => 'yes',
		'default_gravatar_url' => vdtestim_plugin_path . 'images/defaultavatar-green.png',
		'slider_display_duration' => 15,
		'slider_fade_duration' => 2
		
		);



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

	global $default_options; 

	add_option( 'vdtestim_options', $default_options );

}
add_action( 'init', 'vdtestim_options_init' );



/************************************************
*
* Create Settings Page in Admin using add_submenu_page
*
************************************************/

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
			$options['default_gravatar_url'] = $_POST['default_gravatar_url'];
			$options['slider_display_duration'] = $_POST['slider_display_duration'];
			$options['slider_fade_duration'] = $_POST['slider_fade_duration'];

			update_option( 'vdtestim_options', $options );
		}
	}

	$options = get_option('vdtestim_options');

	$widget_style_type = $options['widget_style_type'];
	$use_gravatar = $options['use_gravatar'];
	$default_gravatar_url = $options['default_gravatar_url'];
	$slider_display_duration = $options['slider_display_duration'];
	$slider_fade_duration = $options['slider_fade_duration'];

	echo $widget_style_type;
	echo $use_gravatar;
	echo $default_gravatar_url;
	echo $slider_display_duration;
	echo $slider_fade_duration;

	require ( vdtestim_plugin_path . 'includes/admin/options-wrapper.php' );
}

/*******************************
*
* Changes Media Uploader Wording
*
*******************************/

function vdtestim_mediaupload_setup() {  
    global $pagenow;  
  
    if ( 'media-upload.php' == $pagenow || 'async-upload.php' == $pagenow ) {  
        // Now we'll replace the 'Insert into Post Button' inside Thickbox  
        add_filter( 'gettext', 'replace_uploader_text'  , 1, 3 ); 
    } 
} 
add_action( 'admin_init', 'vdtestim_mediaupload_setup' ); 
 
function replace_uploader_text($translated_text, $text, $domain) { 
    if ('Insert into Post' == $text) { 
        $referer = strpos( wp_get_referer(), 'vdtestim-settings' ); 
        if ( $referer != '' ) { 
            return __('Set this is my default gravatar!', 'vdtestim' );  
        }  
    }  
    return $translated_text;  
}

/*******************************
*
* Admin Options Form Validation
*
*******************************/



function vdtestim_options_validate( $input ) {
	global $default_options; 
	$valid_input = $default_options;
	
	$options = get_option('vdtestim_options');
	
	$submit = ! empty($input['submit']);
	$reset = ! empty($input['reset']);
	$delete_gravatar = ! empty($input['delete_gravatar']);
	
	if ( $submit ) {
		if ( $options['default_gravatar_url'] != $input['gravatar']  && $options['default_gravatar_url'] != '' )
			vdtestim_delete_image( $options['default_gravatar_url'] );
		
		$valid_input['default_gravatar_url'] = $input['gravatar'];
	}
	elseif ( $reset ) {
		vdtestim_delete_image( $options['default_gravatar_url'] );
		$valid_input['default_gravatar_url'] = $default_options['default_gravatar_url'];
	}
	elseif ( $delete_gravatar ) {
		vdtestim_delete_image( $options['default_gravatar_url'] );
		$valid_input['default_gravatar_url'] = '';
	}
	
	return $valid_input;

	function vdtestim_delete_image( $image_url ) {
	global $wpdb;
	
	// We need to get the image's meta ID..
	$query = "SELECT ID FROM wp_posts where guid = '" . esc_url($image_url) . "' AND post_type = 'attachment'";  
	$results = $wpdb -> get_results($query);

	// And delete them (if more than one attachment is in the Library
	foreach ( $results as $row ) {
		wp_delete_attachment( $row -> ID );
	}	
}

}
add_action( 'admin_init', 'vdtestim_options_validate' ); 







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
            'slide_testimonials' => 'yes',
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


// Just for viewing widget options
//$variables = get_option('widget_vdtestim_widget');
//var_dump($variables);


/***************************************
 * 
 * Limit the text on the Testimonials Widget.
 *
 ***************************************/

function vdtestim_shorten_testimonial( $string, $max_chars = 2000, $append = "\xC2\xA0…" )
{

	$widget_options = get_option('widget_vdtestim_widget');
    $truncate_text = $widget_options[2]['truncate_text'];

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

/*************************************************************************
 * 
 * Enqueues Back End Styles, Scripts.
 *
 *************************************************************************/

function vdtestim_back_scripts () {

	// Add and remove what is and isn't needed in back end

	//$options = get_option('vdtestim_options');

	//wp_enqueue_style( 'vdtestim_frontend_css', plugins_url( 'vertusdl-testimonials/css/vertusdl-testimonials.css' ) );
	//wp_enqueue_script( 'vdtestim_slider_js', plugins_url( 'vertusdl-testimonials/js/slider.js' ), array('jquery') );
	wp_register_script( 'vdtestim_scripts_js', plugins_url( 'vertusdl-testimonials/js/scripts.js' ), array('jquery','media-upload','thickbox') );  
  
    if ( 'testimonials_page_testimonial_settings' == get_current_screen() -> id ) {  
        wp_enqueue_script('jquery');  
  
        wp_enqueue_script('thickbox');  
        wp_enqueue_style('thickbox');  
  
        wp_enqueue_script('media-upload');  
        wp_enqueue_script('vdtestim_scripts_js');  
  
    }  

	//wp_enqueue_script( 'vdtestim_scripts_js', plugins_url( 'vertusdl-testimonials/js/scriptstest.js' ), array('jquery') );
	//wp_localize_script( 'vdtestim_slider_js', 'vdtestim_php_vars', array(
	//		'slideDur' => $options['slider_display_duration'] * 1000,
	//		'fadeDur' => $options['slider_fade_duration'] * 1000,
	//	)
	//);


	//Load the correct style sheet based on style type

	//$widget_style_type = $options['widget_style_type'];

	//if ( $widget_style_type == 'style1' ) {
	//	wp_enqueue_style( 'vdtestim_theme', plugins_url( 'vertusdl-testimonials/css/theme-1.css' ) );
	//}
	//elseif ( $widget_style_type == 'style2' ) {
	//	wp_enqueue_style( 'vdtestim_theme', plugins_url( 'vertusdl-testimonials/css/theme-2.css' ) );
	//}
	

}
add_action ( 'admin_enqueue_scripts', 'vdtestim_back_scripts' );

/*************************************************************************
 * 
 * Enqueues Front End Styles, Scripts, Loads PHP Variables into slider JS .
 *
 *************************************************************************/


function vdtestim_styles_scripts () {

	$options = get_option('vdtestim_options');

	wp_enqueue_style( 'vdtestim_frontend_css', plugins_url( 'vertusdl-testimonials/css/vertusdl-testimonials.css' ) );
	wp_enqueue_script( 'vdtestim_slider_js', plugins_url( 'vertusdl-testimonials/js/slider.js' ), array('jquery') );

	wp_localize_script( 'vdtestim_slider_js', 'vdtestim_php_vars', array(
			'slideDur' => $options['slider_display_duration'] * 1000,
			'fadeDur' => $options['slider_fade_duration'] * 1000,
		)
	);


	//Load the correct style sheet based on style type

	$widget_style_type = $options['widget_style_type'];

	if ( $widget_style_type == 'style1' ) {
		wp_enqueue_style( 'vdtestim_theme', plugins_url( 'vertusdl-testimonials/css/theme-1.css' ) );
	}
	elseif ( $widget_style_type == 'style2' ) {
		wp_enqueue_style( 'vdtestim_theme', plugins_url( 'vertusdl-testimonials/css/theme-2.css' ) );
	}
	

}
add_action ( 'wp_enqueue_scripts', 'vdtestim_styles_scripts' );










