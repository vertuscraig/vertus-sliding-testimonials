<?php 		

	echo $before_widget;
	echo $before_title . $title . $after_title;	

?>

<div class="testimonials">

	<?php

		// WP_Query arguments
		$args = array (
			'post_type'              => 'testimonials',
			'posts_per_page'         => $num_testimonials
		);

		// The Query
		$query = new WP_Query( $args );

		// The Loop
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post(); ?>
				
				<figure class="item">

					<?php


					$vdtestim_company  = get_post_meta( get_the_id(), 'vdtestim_company',     true );
					$vdtestim_website  = get_post_meta( get_the_id(), 'vdtestim_website',     true );
					$vdtestim_position = get_post_meta( get_the_id(), 'vdtestim_position',    true );
					$vdtestim_email    = get_post_meta( get_the_id(), 'vdtestim_email',       true );
					$vdtestim_wpautop  = get_post_meta( get_the_id(), 'vdtestim_testimonial', true );
					$vdtestim_text     = wpautop( $vdtestim_wpautop );
					$size = 50;
					$default = plugins_url( 'vertusdl-testimonials/images/defaultavatar-green.png' );
					$grav_url = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $vdtestim_email ) ) ) . "?d=" . urlencode( 'http://www.vertusdigital.co.uk/wp-content/uploads/2013/12/defaultavatar-green.png' ) . "&s=" . $size;

					// To Do - Change gravatar default back to pluginurl in production!!!!!!!!

					?>
					
					<p><?php echo vdtestim_shorten_testimonial( $vdtestim_text ); ?>
					<a href="<?php echo get_permalink(); ?>">Read More</a></p>

					<p>
						<span="testimonials-before">by: </span><span="testimonials-name"><?php the_title(); ?></span><br />
						<span="testimonials-before">from: </span><a href="<?php echo $vdtestim_website; ?>" target="_blank" rel="nofollow" title="Link to <?php echo $vdtestim_company;?> Website."><span"testimonials-company"><?php echo $vdtestim_company; ?></span></a>
					</p>
					<img src="<?php echo $grav_url; ?>" alt="" height="50" width="50"/>

				</figure>

				<?php
			}
		} else {
			// no posts found
		}

		// Restore original Post Data
		wp_reset_postdata();
	?>

</div>



<?php
	echo $after_widget; 
?>