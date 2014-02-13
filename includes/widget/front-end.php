<?php 

	$vdtestim_options = get_option( 'vdtestim_global_settings' );
	$default_gravatar_url = $vdtestim_options['default_gravatar_url'];

	echo $before_widget;
	echo $before_title . $title . $after_title;	
	$widget_variables = get_option( 'vdtestim_style_settings' );
	$slide_testimonials = $widget_variables['slide'];
	$num_testimonials = $widget_variables['num_testimonials'];
?>



<div id="testimonials" class="<?php if ( $slide_testimonials === 'yes' ) echo "slider" ?> testimonials" >



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
		} else {
			// no posts found
		}

		// Restore original Post Data
		wp_reset_postdata();
	?>

	<div class="slider_controls">
        <div class="prev testimonial_slide" data-target="prev" >&lsaquo;</div>
        <div class="next testimonial_slide" data-target="next" >&rsaquo;</div>
        <ul class="pager_list"></ul>
    </div>

</div>



<?php
	echo $after_widget; 
?>