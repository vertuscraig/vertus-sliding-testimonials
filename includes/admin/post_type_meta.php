<?php

/***************************
*
* Register a new post type
*
****************************/

function vdtestim_posttype() {

	$labels = array(
		'name'                => __( 'Testimonials', 'vertusdl_testimonials' ),
		'singular_name'       => __( 'Testimonials', 'vertusdl_testimonials' ),
		'add_new'             => _x( 'Add New Testimonial', 'vertusdltestimonials', 'vertusdl_testimonials' ),
		'add_new_item'        => __( 'Add New Testimonial', 'vertusdl_testimonials' ),
		'edit_item'           => __( 'Edit Testimonial', 'vertusdl_testimonials' ),
		'new_item'            => __( 'New Testimonial', 'vertusdl_testimonials' ),
		'view_item'           => __( 'View Testimonial', 'vertusdl_testimonials' ),
		'search_items'        => __( 'Search Testimonials', 'vertusdl_testimonials' ),
		'not_found'           => __( 'No Testimonials found', 'vertusdl_testimonials' ),
		'not_found_in_trash'  => __( 'No Testimonials found in Trash', 'vertusdl_testimonials' ),
		'parent_item_colon'   => __( 'Parent Testimonial Name:', 'vertusdl_testimonials' ),
		'menu_name'           => __( 'Testimonials', 'vertusdl_testimonials' ),
	);

	$args = array(
		'labels'                   => $labels,
		'hierarchical'        => false,
		'description'         => 'shows testimonials',
		'taxonomies'          => array(),
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => null,
		'menu_icon'           => null,
		'show_in_nav_menus'   => true,
		'publicly_queryable'  => true,
		'exclude_from_search' => false,
		'has_archive'         => true,
		'query_var'           => true,
		'can_export'          => true,
		'rewrite'            => array( 'slug' => 'testimonial' ),
		'capability_type'     => 'post',
		'supports'            => array(
			'title', 'author', 'thumbnail',
		 	'revisions', 'page-attributes'
			)
	);
	register_post_type( 'testimonials', $args );
}
add_action( 'init', 'vdtestim_posttype' );


/*****************************
*
* Add Custom Metaboxes for 
* testimonials post type - 
* https://github.com/humanmade/Custom-Meta-Boxes
*
******************************/

// Include custom-meta-boxes.php

require_once( vdtestim_plugin_path . 'includes/lib/custom-metaboxes/custom-meta-boxes.php' );

// Function for new metaboxes

function vdtestim_metaboxes( array $meta_boxes ) {

	$fields = array(
		array( 'id' => 'vdtestim_email',  'name' => 'Email', 'desc' => 'Email of person providing testimonial (only to pull gravatar image)', 'type' => 'text', 'cols' => 6 ),
		array( 'id' => 'vdtestim_company',  'name' => 'Company', 'desc' => 'Company of person providing testimonial', 'type' => 'text', 'cols' => 6 ),
		array( 'id' => 'vdtestim_position',  'name' => 'Position', 'desc' => 'Position of person providing testimonial', 'type' => 'text', 'cols' => 6 ),
		array( 'id' => 'vdtestim_website',  'name' => 'Website', 'desc' => 'Website of person providing testimonial', 'type' => 'url', 'cols' => 6 ),
		array( 'id' => 'vdtestim_testimonial',  'name' => 'Testimonial', 'type' => 'wysiwyg', 'options' => array( 'editor_height' => '100' ), 'cols' => 12 ),
	);

	$meta_boxes[] = array(
		'title' => 'Testimonial Details',
		'pages' => 'testimonials',
		'fields' => $fields
	);

    return $meta_boxes; 
}
add_filter( 'cmb_meta_boxes', 'vdtestim_metaboxes' );

/*****************************
*
* Change Default Wordpress Post 
* Type Text 
*
******************************/

// In Featured Image Box

function vdtestim_custom_admin_post_thumbnail_html( $content ) {
    global $current_screen;
 
    if( 'testimonials' == $current_screen->post_type )
        return $content = str_replace( __( 'Set featured image' ), __( 'Upload Avatar Image' ), $content);
    elseif( 'testimonials' == $current_screen->post_type )
        return $content = str_replace( __( 'Featured Image' ), __( 'Profile Image' ), $content);
    else
        return $content;
}
add_action( 'admin_post_thumbnail_html', 'vdtestim_custom_admin_post_thumbnail_html' );


function vdtestim_change_featured_image_text()
{
    remove_meta_box( 'postimagediv', 'testimonials', 'side' );
    add_meta_box('postimagediv', __('Profile Image'), 'post_thumbnail_meta_box', 'testimonials', 'side', 'low');
}
add_action('do_meta_boxes', 'vdtestim_change_featured_image_text');


// In post title

function vdtestim_change_default_post_title_text( $title ){
     $screen = get_current_screen();
     if  ( $screen->post_type == 'testimonials' ) {
          return 'Name of person providing the testimonial';
     }
}
add_filter( 'enter_title_here', 'vdtestim_change_default_post_title_text' );





