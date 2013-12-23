<?php 		

	echo $before_widget;
	echo $before_title . $title . $after_title;	



?>

<ul class="vertusdl-testimonials frontend cycle-slideshow"
	data-cycle-fx="fade" 
    data-cycle-timeout="6000"
    data-cycle-slides="> li"
    >

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
			
			<li>

			<?php

			$vertusdl_company = get_post_meta( get_the_id(), 'vertusdl_testimonials_company', true );
			$vertusdl_website = get_post_meta( get_the_id(), 'vertusdl_testimonials_website', true );
			$vertusdl_position = get_post_meta( get_the_id(), 'vertusdl_testimonials_position', true );
			$vertusdl_email = get_post_meta( get_the_id(), 'vertusdl_testimonials_email', true );
			$vertusdl_testimonial_wpautop = get_post_meta( get_the_id(), 'vertusdl_testimonials_testimonial', true );
			$vertusdl_testimonial_text = wpautop( $vertusdl_testimonial_wpautop );
			$size = 50;
			$default = plugins_url( 'vertusdl-testimonials/images/defaultavatar-green.png' );
			$grav_url = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $vertusdl_email ) ) ) . "?d=" . urlencode( 'http://www.vertusdigital.co.uk/wp-content/uploads/2013/12/defaultavatar-green.png' ) . "&s=" . $size;

			// To Do - Change gravatar default back to pluginurl in production!!!!!!!!

			?>


			
				

				<p><?php echo shorten_testimonial( $vertusdl_testimonial_text ); ?>
				<a href="<?php echo get_permalink(); ?>">Read More</a></p>

				



			
			<p>
				<span="testimonials-before">by: </span><span="testimonials-name"><?php the_title(); ?></span><br />
				<span="testimonials-before">from: </span><a href="<?php echo $vertusdl_website; ?>" target="_blank" rel="nofollow" title="Link to <?php echo $vertusdl_company;?> Website."><span"testimonials-company"><?php echo $vertusdl_company; ?></span></a>
			</p>
			<img src="<?php echo $grav_url; ?>" alt="" height="50" width="50"/>
			

			</li>

			<?php
		}
	} else {
		// no posts found
	}

	// Restore original Post Data
	wp_reset_postdata();
?>

</ul>



<?php
	echo $after_widget; 

?>