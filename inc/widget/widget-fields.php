<p>
  <label>Title</label> 
  <input class="widefat" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
</p>

<p>
	Total Testimonials:&nbsp; 
	<?php 
		$post_count = wp_count_posts( 'testimonials' );
		$published_posts = $post_count->publish;
		echo $published_posts;
	?>
</p>
<p>
  <label>How many of your testimonials would you you like to display? (leave blank for all)</label> 
  <input size="4" name="<?php echo $this->get_field_name('num_testimonials'); ?>" type="text" value="<?php echo $num_testimonials; ?>" />
</p>

<p>
  <label>Limit text in widget:</label> 
  <input size="4" name="<?php echo $this->get_field_name('truncate_text'); ?>" type="number" value="<?php echo $truncate_text; ?>" />
</p>


<p>
  <label>Slide Testimonials?</label> 
  <input type="checkbox" name="<?php echo $this->get_field_name('slide_testimonials'); ?>" value="1" <?php checked( $slide_testimonials, 1 ); ?> />
</p>