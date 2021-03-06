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
 



/**********************
*
* Get & Save Default Options
*
***********************/

function vdtestim_get_default_options() {
	$default_options = array(
		'use_gravatar' => 'yes',
		'default_gravatar_url' => plugins_url() . '/vertusdl-testimonials/images/defaultavatar-green.png',
		'slider_display_duration' => 15,
		'slider_fade_duration' => 2
			
	);
	return $default_options;
}

function vdtestim_default_style_options() {
	$default_options = array(
		'style' => 'style1',
		'num_testimonials' => '4',
		'slide' => 'yes',
		'truncate_text' => '400',
		'height' => '400',
		'width' => '500'
	);
	return $default_options;
}

function vdtestim_options_init() {
     $vdtestim_options = get_option( 'vdtestim_global_settings' );
     $vdtestim_style_options = get_option( 'vdtestim_style_settings' );
	 
	 //Check for Global Options
     if ( false === $vdtestim_options ) {
		  // If not, we'll save our default options
          $vdtestim_options = vdtestim_get_default_options();
		  add_option( 'vdtestim_global_settings', $vdtestim_options );
     }
     //Check for Style Options
     if ( false === $vdtestim_style_options ) {
          $vdtestim_style_options = vdtestim_default_style_options();
		  add_option( 'vdtestim_style_settings', $vdtestim_style_options );
     }
	 
}
add_action( 'init', 'vdtestim_options_init' );






/****************************
*
* Import New Post Type & Meta
*
****************************/

require ( vdtestim_plugin_path . 'includes/admin/post_type_meta.php' );




/************************************************
*
* Creates The Settings Page & Options
*
************************************************/

// Import our setting
require ( vdtestim_plugin_path . 'includes/admin/settings.php' );

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

	require ( vdtestim_plugin_path . 'includes/admin/options-wrapper.php' );
	
}

//var_dump($vdtestim_options);

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

		require ( vdtestim_plugin_path . 'includes/widget/front-end.php');
	}

	function update( $new_instance, $old_instance ) {
		// Save widget options

		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}

	function form( $instance ) {
		$defaults = array(
            'title' => __('Enter Title Here')
        );

        $instance = wp_parse_args( (array) $instance, $defaults);
		
		// Output admin widget options form

		$title = esc_attr( $instance['title']);

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
	$vdtestim_style_options = get_option( 'vdtestim_style_settings' );
    $truncate_text = $vdtestim_style_options['truncate_text'];

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

	$vdtestim_style_options = get_option( 'vdtestim_style_settings' );
	$global_options = get_option('vdtestim_global_settings');

	// Add and remove what is and isn't needed in back end

	//$options = get_option('vdtestim_options');

	wp_register_style( 'vdtestim_frontend_css', plugins_url( 'vertusdl-testimonials/css/vertusdl-testimonials.css' ) );
	wp_register_script( 'vdtestim_slider_js', plugins_url( 'vertusdl-testimonials/js/slider.js' ), array('jquery') );
	wp_register_script( 'vdtestim_scripts_js', plugins_url( 'vertusdl-testimonials/js/scripts.js' ), array('jquery','media-upload','thickbox') );  
  
    if ( 'testimonials_page_testimonial_settings' == get_current_screen() -> id ) {  
        wp_enqueue_script('jquery');  
  
        wp_enqueue_script('thickbox');  
        wp_enqueue_style('thickbox');  
  
        wp_enqueue_script('media-upload');  
        wp_enqueue_script('vdtestim_scripts_js');  

        wp_enqueue_scripts('vdtestim_slider_js');
        wp_enqueue_style('vdtestim_frontend_css');

        wp_deregister_style('twentytwelve-style' );
        
        //Load the correct style sheet based on style type



        if ( $vdtestim_style_options["style"] == 'style1' ) {
        	wp_enqueue_style( 'vdtestim_theme', plugins_url( 'vertusdl-testimonials/css/theme-1.css' ) );
        }
        elseif ( $vdtestim_style_options['style'] == 'style2' ) {
        	wp_enqueue_style( 'vdtestim_theme', plugins_url( 'vertusdl-testimonials/css/theme-2.css' ) );
        }

        wp_localize_script( 'vdtestim_slider_js', 'vdtestim_php_vars', array(
        		'slideDur' => $global_options['slider_display_duration'] * 1000,
        		'fadeDur' => $global_options['slider_fade_duration'] * 1000,
        	)
        );

  
    }  

}
add_action ( 'admin_enqueue_scripts', 'vdtestim_back_scripts' );

/*************************************************************************
 * 
 * Enqueues Front End Styles, Scripts, Loads PHP Variables into slider JS .
 *
 *************************************************************************/


function vdtestim_styles_scripts () {

	$style_options = get_option('vdtestim_style_settings');
	$global_options = get_option('vdtestim_global_settings');

	wp_enqueue_style( 'vdtestim_frontend_css', plugins_url( 'vertusdl-testimonials/css/vertusdl-testimonials.css' ) );
	wp_enqueue_script( 'vdtestim_slider_js', plugins_url( 'vertusdl-testimonials/js/slider.js' ), array('jquery') );
	wp_enqueue_script( 'vdtestim_frontend_js', plugins_url( 'vertusdl-testimonials/js/frontend.js' ), array('jquery') );




	wp_localize_script( 'vdtestim_slider_js', 'vdtestim_php_vars', array(
			'slideDur' => $global_options['slider_display_duration'] * 1000,
			'fadeDur' => $global_options['slider_fade_duration'] * 1000,
		)
	);


	//Load the correct style sheet based on style type

	$widget_style_type = $style_options['style'];

	if ( $widget_style_type == 'style1' ) {
		wp_enqueue_style( 'vdtestim_theme', plugins_url( 'vertusdl-testimonials/css/theme-1.css' ) );
	}
	elseif ( $widget_style_type == 'style2' ) {
		wp_enqueue_style( 'vdtestim_theme', plugins_url( 'vertusdl-testimonials/css/theme-2.css' ) );
	}
	

}
add_action ( 'wp_enqueue_scripts', 'vdtestim_styles_scripts' );










