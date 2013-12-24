<div class="wrap">
	
	<div id="icon-options-general" class="icon32"></div>
	<h2>Vertus Sliding Testimonials Settings Page</h2>
	
	<div id="poststuff">
	
		<div id="post-body" class="metabox-holder columns-2">
		
			<!-- main content -->
			<div id="post-body-content">
				
				<div class="meta-box-sortables ui-sortable">
					
					<div class="postbox">
					
						<h3><span>Settings</span></h3>
						<div class="inside">

							<form method="post" name="vertusdl_options" action="">

								<input type="hidden" name="vertusdl_form_submitted" value="Y">

								<fieldset>

									<h3>Choose The Style</h3>

									<legend class="screen-reader-text"><span>input type="radio"</span></legend>

									<label title='choose style 1'>
										<input type="radio" name="widget_style_type" <?php checked( $options['widget_style_type'], 'style1' ); ?> value="style1" /> <span>Choose Style 1 (Default)</span>
									</label>

									<br />

									<label title='choose style 2'>
										<input type="radio" name="widget_style_type" <?php checked( $options['widget_style_type'], 'style2' ); ?> value="style2" /> <span>Choose Style 2</span>
									</label>

								</fieldset>

								<h3>Use Gravatar Image In Profile If No Profile Images Is Uploaded</h3>

								<select name="use_gravatar">
									<option value="yes" <?php selected( $options['use_gravatar'], 'yes' ); ?> >Yes</option>
									<option value="no" <?php selected( $options['use_gravatar'], 'no' ); ?> >No</option>
								</select>

								<p><input class="button-primary" type="submit" name="submit_style" value="Submit" /></p>

							</form>
						</div> <!-- .inside -->
					
					</div> <!-- .postbox -->
					
				</div> <!-- .meta-box-sortables .ui-sortable -->
				
			</div> <!-- post-body-content -->
			
			<!-- sidebar -->
			<div id="postbox-container-1" class="postbox-container">
				
				<div class="meta-box-sortables">
					
					<div class="postbox">
					
						<h3><span>Sidebar Content Header</span></h3>
						<div class="inside">
							Content space
						</div> <!-- .inside -->
						
					</div> <!-- .postbox -->
					
				</div> <!-- .meta-box-sortables -->
				
			</div> <!-- #postbox-container-1 .postbox-container -->
			
		</div> <!-- #post-body .metabox-holder .columns-2 -->
		
		<br class="clear">
	</div> <!-- #poststuff -->
	
</div> <!-- .wrap -->