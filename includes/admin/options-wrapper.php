<div class="wrap">
	
	<div id="icon-options-general" class="icon32"></div>
	<h2>Vertus Sliding Testimonials Settings Page</h2>
	

		<?php settings_errors(); ?>

		<?php
		if( isset( $_GET[ 'tab' ] ) ) {
		    $active_tab = $_GET[ 'tab' ];
		} // end if
		$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'global_options';
		?>

		<h2 class="nav-tab-wrapper">
		    <a href="?post_type=testimonials&page=testimonial_settings&tab=global_options" class="nav-tab <?php echo $active_tab == 'global_options' ? 'nav-tab-active' : ''; ?>">Global Options</a>
		    <a href="?post_type=testimonials&page=testimonial_settings&tab=style_options" class="nav-tab <?php echo $active_tab == 'style_options' ? 'nav-tab-active' : ''; ?>">Style Options</a>
		</h2>

		<form method="POST" name="vdtestim_options" action="options.php">

		<input type="hidden" name="vdtestim_form_submitted" value="Y">

		

		<?php 

		if ( $active_tab == 'global_options') { 
			settings_fields('vdtestim_global_settings');
			do_settings_sections('vdtestim_global_settings');
		} else {
			settings_fields( 'vdtestim_style_settings' );
			do_settings_sections( 'vdtestim_style_settings' );
		}
		?>



		<p>
			<input class="button-primary" type="submit" name="vdtestim_global_settings[submit]" value="<?php esc_attr_e('Save Settings', 'vdtestim' ); ?>" />

			<input class="button-secondary" type="submit" name="vdtestim_global_settings[reset]" value="<?php esc_attr_e('Reset Defaults', 'vdtestim'); ?>" />
		</p>

		</form>
	
</div> <!-- .wrap -->