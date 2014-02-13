<?php

/**********************
*
* Register our settings in options table
*
***********************/

function vdtestim_register_settings() {

	$vdtestim_options = get_option( 'vdtestim_global_settings' );


// Create settings and fields for our global settings

	//* Settings Sections ***************************************
	
	// Creates Header For Global Options
	add_settings_section(
		'global_plugin_settings', 			// ID used to identify this section and with which to register options
		'Global Settings', 					// Title to be displayed on the administration page
		'vdtestim_global_callback',		 	// Callback
		'vdtestim_global_settings' 				// Page on which to add this section of options 
	);

	add_settings_section( 
		'style_plugin_settings',
		'Layout & Style Settings',
		'vdtestim_style_callback',
		'vdtestim_style_settings'
	);


	//* Settings Fields For Global ****************************************

	// Adds use_gravatar field
	add_settings_field(
		'vdtestim_use_gravatar',	
		'Testimonial Image',		
		'vdtestim_use_gravatar_field',
		'vdtestim_global_settings', 		
		'global_plugin_settings'	
	);

	if ( $vdtestim_options['use_gravatar'] == 'yes' ) {

		// Adds default_gravatar_url field
		add_settings_field(
			'vdtestim_default_gravatar_url',	
			'Default Avatar Image',		
			'vdtestim_gravatar_url_field',
			'vdtestim_global_settings', 		
			'global_plugin_settings'	
		);


		// Adds gravatar_preview field
		add_settings_field(
			'vdtestim_gravatar_preview',	
			'Avatar Image Preview',		
			'vdtestim_gravatar_preview_field',
			'vdtestim_global_settings', 		
			'global_plugin_settings',
			array(                               
	            'Default image, upload new one to replace.' 
	        ) 
		);


		
	}

	// Adds slider_display_duration fields
	add_settings_field(
		'vdtestim_slider_display_duration',	
		'Slider Display Duration',		
		'vdtestim_slider_display_duration_field',
		'vdtestim_global_settings', 		
		'global_plugin_settings'
	);

	// Adds slider_fade_duration fields
	add_settings_field(
		'vdtestim_slider_fade_duration',	
		'Slider Fade Duration',		
		'vdtestim_slider_fade_duration_field',
		'vdtestim_global_settings', 		
		'global_plugin_settings'
	);

	//* Settings Fields for style ******************************

	// Adds style selector field
	add_settings_field(
		'vdtestim_style_settings',	// ID used to identify the field throughout the theme
		'Choose The Style',			// The label to the left of the option interface element  
		'vdtestim_style_field', 	// Callback
		'vdtestim_style_settings', 	// The page on which this option will be displayed 
		'style_plugin_settings'		// The name of the section to which this field belongs
	);

	add_settings_field(
		'vdtestim_num_testimonials',
		'Number Of Testimonials',
		'vdtestim_num_testimonials_callback',
		'vdtestim_style_settings',
		'style_plugin_settings'
	);

	add_settings_field(
		'vdtestim_slide_testimonials',
		'Slide Testimonials',
		'vdtestim_slide_callback',
		'vdtestim_style_settings',
		'style_plugin_settings'
	);

	add_settings_field(
		'vdtestim_truncate_text',
		'Limit Text',
		'vdtestim_truncate_callback',
		'vdtestim_style_settings',
		'style_plugin_settings'
	);

	add_settings_field(
		'vdtestim_width',
		'Width',
		'vdtestim_width_callback',
		'vdtestim_style_settings',
		'style_plugin_settings'
	);

	//Adds style preview field
	add_settings_field(
		'vdtestim_style_preview',
		'Preview',
		'vdtestim_style_preview',
		'vdtestim_style_settings',
		'style_plugin_settings'
	);



	//* Register Settings **************************************

	// creates vdtestim_global_settings array in the options table
	register_setting(
		'vdtestim_global_settings', 
		'vdtestim_global_settings',
		'vdtestim_options_validate' 
	);

	register_setting(
		'vdtestim_style_settings',
		'vdtestim_style_settings'
	);
}
add_action('admin_init', 'vdtestim_register_settings');




/**********************
*
* Callback for our settings sections
*
***********************/

// Callback for global_plugin_settings
function vdtestim_global_callback () {
	?>
		<p><?php esc_attr_e( 'Manage Global Options', 'vdtestim' ); ?></p>
	<?php
}

// Callback for style_plugin_settings
function vdtestim_style_callback () {
	?>
		<p><?php esc_attr_e( 'Manage Style Options', 'vdtestim' ); ?></p>
	<?php
}




/**********************
*
* Callbacks for our settings fields
*
***********************/

//* Global Setting Callbacks *********************************

function vdtestim_use_gravatar_field () {

	$vdtestim_options = get_option( 'vdtestim_global_settings' );

	?>

	<h4>Use Gravatar Image In Profile If No Profile Images Is Uploaded</h4>

	<select name="vdtestim_global_settings[use_gravatar]">
		<option name="vdtestim_global_settings[use_gravatar]" value="yes"<?php selected( $vdtestim_options['use_gravatar'], 'yes' ); ?> >Yes</option>
		<option name="vdtestim_global_settings[use_gravatar]" value="no"<?php selected( $vdtestim_options['use_gravatar'], 'no' ); ?> >No</option>
	</select>

	<?php
}

function vdtestim_gravatar_url_field () {

	$vdtestim_options = get_option( 'vdtestim_global_settings' );
	$default_avatar = plugins_url() . '/vertusdl-testimonials/images/defaultavatar-green.png';

	?> 
		
		<h4>Upload the default avatar image below</h4>
		
		<!-- Uploads & Displays Default Gravatar Image -->

		<div class="gravatar_uploader">
		  	<input type="hidden" id="default_gravatar_url" name="vdtestim_global_settings[default_gravatar_url]" value="<?php echo esc_url( $vdtestim_options['default_gravatar_url'] ); ?>" /> 
			<input id="upload_gravatar_button" type="button" class="button" value="<?php esc_attr_e( 'Add Image', 'vdtestim' ); ?>" />

			<?php if ( $vdtestim_options['default_gravatar_url'] != '' ): ?>  
			            <input id="delete_gravatar_button" name="vdtestim_global_settings[delete_gravatar]" type="submit" class="button" value="<?php esc_attr_e( 'Delete Gravatar', 'vdtestim' ); ?>" />  
			<?php endif; ?> 

			<span class="description"><?php esc_attr_e('Add a default gravatar image.', 'vdtestim' ); ?></span> 
		</div>

	<?php
}

function vdtestim_gravatar_preview_field ($args) {

	$vdtestim_options = get_option( 'vdtestim_global_settings' );
	$default_avatar = plugins_url() . '/vertusdl-testimonials/images/defaultavatar-green.png';

	if ( $vdtestim_options['default_gravatar_url'] != '' ) {
		?>
		<div id="gravatar_upload_preview">
		        <img style="max-width:100%;" src="<?php echo esc_url( $vdtestim_options['default_gravatar_url'] ); ?>" height="100px" width="100px" /> 
		        <p class="description"><?php if ( $vdtestim_options['default_gravatar_url'] == $default_avatar ) { echo esc_attr_e( $args[0], 'vdtestim' ); } ?></p>
		</div>
		<?php
	}
	else { ?><p class="description"><?php esc_attr_e('Upload an avatar image to see the preview', 'vdtestim' ); ?></p><?php }
}

function vdtestim_slider_display_duration_field () {

	$vdtestim_options = get_option( 'vdtestim_global_settings' );

	?>

		<label title='Slider Display Duration'>
			<input type="text" value="<?php echo sanitize_text_field( $vdtestim_options['slider_display_duration'], 'vdtestim' ); ?>" class="small-text" name="vdtestim_global_settings[slider_display_duration]"/><span class="description">Seconds <br />Set Slider Display Duration</span>
		</label>

	<?php

}

function vdtestim_slider_fade_duration_field () {

	$vdtestim_options = get_option( 'vdtestim_global_settings' );

	?>
		<label title='Slider Fade Duration'>
			<input type="text" value="<?php echo sanitize_text_field( $vdtestim_options['slider_fade_duration'], 'vdtestim' ); ?>" class="small-text" name="vdtestim_global_settings[slider_fade_duration]"/><span class="description">Seconds <br />Set Slider Fade Duration</span>
		</label>

	<?php

}


//* Style Setting Callbacks *********************************

function vdtestim_style_field () {

	$vdtestim_style_options = get_option( 'vdtestim_style_settings' );

	?>

	<legend class="screen-reader-text"><span>input type="radio"</span></legend>

	<label title="choose style 1">
		<input type="radio" name="vdtestim_style_settings[style]"<?php checked( $vdtestim_style_options["style"], "style1" ); ?>value="style1" /><span>Style 1</span>
	</label><br />

	<label title="choose style 2">
		<input type="radio" name="vdtestim_style_settings[style]"<?php checked( $vdtestim_style_options["style"], "style2" ); ?>value="style2" /><span>Style 2</span>
	</label>

	<?php
}

function vdtestim_num_testimonials_callback () {

	$vdtestim_style_options = get_option( 'vdtestim_style_settings' );

	?>

	 <p>
		Total Testimonials:&nbsp; 
		<?php 
			$post_count = wp_count_posts( 'testimonials' );
			$published_posts = $post_count->publish;
			echo $published_posts;
		?>
	</p>

	<p>
	  <label>How many of your testimonials would you you like to display?</label> 
	  <input size="4" name="vdtestim_style_settings[num_testimonials]" type="text" value="<?php echo sanitize_text_field( $vdtestim_style_options['num_testimonials'], 'vdtestim' ); ?>" />
	</p>

	<?php

}

function vdtestim_slide_callback () {

	$vdtestim_style_options = get_option( 'vdtestim_style_settings' );

	?>
	<p>
	  <label>Slide Testimonials?</label> 
	  <select name="vdtestim_style_settings[slide]">
	                  <option value="yes" <?php selected( $vdtestim_style_options['slide'], 'yes' ); ?> >Yes</option>
	                  <option value="no" <?php selected( $vdtestim_style_options['slide'], 'no' ); ?> >No</option>
	  </select>
	</p>
	<?php

}

function vdtestim_truncate_callback () {

	$vdtestim_style_options = get_option( 'vdtestim_style_settings' );

	?>
	<p>
	  <label>Limit amount of text display in widget:</label> 
	  <input size="4" name="vdtestim_style_settings[truncate_text]" type="text" value="<?php echo sanitize_text_field( $vdtestim_style_options['truncate_text'], 'vdtestim' ); ?>" />
	</p>
	<?php

}

function vdtestim_width_callback () {

	$vdtestim_style_options = get_option( 'vdtestim_style_settings' );

	?>
	<p>
	  <label>Width of Widget:</label> 
	  <input size="4" name="vdtestim_style_settings[width]" type="text" value="<?php echo sanitize_text_field( $vdtestim_style_options['width'], 'vdtestim' ); ?>" />
	</p>
	<?php

}

function vdtestim_style_preview () {

	//same as widget output

	$vdtestim_options = get_option( 'vdtestim_global_settings' );
	$vdtestim_style_options = get_option( 'vdtestim_style_settings' );
	$default_gravatar_url = $vdtestim_options['default_gravatar_url'];

	$slide_testimonials = $vdtestim_style_options['slide'];
	$num_testimonials = $vdtestim_style_options['num_testimonials'];
	
	?>


	<div id="testimonials" class="<?php if ( $slide_testimonials === 'yes' ) echo "slider" ?> testimonials">



		<?php



			// WP_Query arguments
			$args = array (
				'post_type'              => 'testimonials',
				'posts_per_page'         => $num_testimonials
			);

			// The Query
			$query = new WP_Query( $args );

			// The Loop
			$i = 0;
			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();

					?>
					
					<div class="testimonial <?php if ( $slide_testimonials == 'yes' ) { echo 'slide'; } else { echo 'no-slide'; } if ($i === $num_testimonials -1 ) { echo ' last'; } ?> ">

						<?php


						$vdtestim_company  = get_post_meta( get_the_id(), 'vdtestim_company',     true );
						$vdtestim_website  = get_post_meta( get_the_id(), 'vdtestim_website',     true );
						$vdtestim_position = get_post_meta( get_the_id(), 'vdtestim_position',    true );
						$vdtestim_email    = get_post_meta( get_the_id(), 'vdtestim_email',       true );
						$vdtestim_wpautop  = get_post_meta( get_the_id(), 'vdtestim_testimonial', true );
						$vdtestim_text     = wpautop( $vdtestim_wpautop );
						$size = 50;
						$default = plugins_url( 'vertusdl-testimonials/images/defaultavatar-green.png' );
						$grav_url = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $vdtestim_email ) ) ) . "?d=" . urlencode( $default_gravatar_url ) . "&s=" . $size;

						// To Do - Change gravatar default back to pluginurl in production!!!!!!!!

						?>


						
						<blockquote><p><?php echo vdtestim_shorten_testimonial( $vdtestim_text ); ?>
						<a href="<?php echo get_permalink(); ?>">Read More</a></p></blockquote>

						<p>
							<img src="<?php echo $grav_url; ?>" alt="" height="50" width="50"/>
							<span class="testimonials-before">by: </span><span class="testimonials-name"><?php the_title(); ?></span><br />
							<span class="testimonials-before">from: </span><a href="<?php echo $vdtestim_website; ?>" target="_blank" rel="nofollow" title="Link to <?php echo $vdtestim_company;?> Website."><span class="testimonials-company"><?php echo $vdtestim_company; ?></span></a>
						</p>	

					</div>

					<?php

					$i++;
				}
			} 
		?>

		<?php if ( $slide_testimonials == 'yes' ) { ?>

			<div class="slider_controls">
		        <div class="prev testimonial_slide" data-target="prev" >&lsaquo;</div>
		        <div class="next testimonial_slide" data-target="next" >&rsaquo;</div>
		        <ul class="pager_list"></ul>
		    </div>

		<?php } ?> 

	</div>


	<?php
}


/*******************************
*
*  Form Validation
*
*******************************/

function vdtestim_options_validate( $input ) {

	$default_options = vdtestim_get_default_options();
	$vdtestim_options = get_option( 'vdtestim_global_settings' );
	$valid_input = $vdtestim_options;
	
	$submit = ! empty($input['submit']) ? true : false;;
	$reset = ! empty($input['reset']) ? true : false;;
	$delete_gravatar = ! empty($input['delete_gravatar']) ? true : false;;
	
	if ( $submit ) {
		if ( $vdtestim_options['use_gravatar'] == 'yes' ) {
			if ( $vdtestim_options['default_gravatar_url'] != $input['default_gravatar_url']  && $vdtestim_options['default_gravatar_url'] != '' ) {
				vdtestim_delete_gravatar( $vdtestim_options['default_gravatar_url'] );
			}
		}

		//$valid_input['style'] = $input['style'];
		$valid_input['use_gravatar'] = $input['use_gravatar'];

		if ( $vdtestim_options['use_gravatar'] == 'no' && $vdtestim_options['default_gravatar_url'] != $default_options['default_gravatar_url']) {
			$valid_input['default_gravatar_url'] = $vdtestim_options['default_gravatar_url'];
		}
		elseif ( $vdtestim_options['use_gravatar'] == 'no' && $vdtestim_options['default_gravatar_url'] == $default_options['default_gravatar_url']) {
			$valid_input['default_gravatar_url'] = $default_options['default_gravatar_url'];
		}
		else { $valid_input['default_gravatar_url'] = $input['default_gravatar_url']; }
		
		$valid_input['slider_display_duration'] = $input['slider_display_duration'];
		$valid_input['slider_fade_duration'] = $input['slider_fade_duration'];
	}
	elseif ( $reset ) {
		vdtestim_delete_gravatar( $vdtestim_options['default_gravatar_url'] );

		//$valid_input['style'] = $default_options['style'];
		$valid_input['use_gravatar'] = $default_options['use_gravatar'];
		$valid_input['default_gravatar_url'] = $default_options['default_gravatar_url'];
		$valid_input['slider_display_duration'] = $default_options['slider_display_duration'];
		$valid_input['slider_fade_duration'] = $default_options['slider_fade_duration'];
	}
	elseif ( $delete_gravatar ) {
		vdtestim_delete_gravatar( $vdtestim_options['default_gravatar_url'] );
		$valid_input['default_gravatar_url'] = '';
	}
	
	return $valid_input;

}

function vdtestim_delete_gravatar( $image_url ) {
	global $wpdb;
	
	// We need to get the image's meta ID..
	$query = "SELECT ID FROM wp_posts where guid = '" . esc_url($image_url) . "' AND post_type = 'attachment'";  
	$results = $wpdb -> get_results($query);

	// And delete them (if more than one attachment is in the Library
	foreach ( $results as $row ) {
		wp_delete_attachment( $row -> ID );
	}	
} 









