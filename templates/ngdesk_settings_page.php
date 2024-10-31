	
	
	  <div class="wrap" >
	  <h2></h2>
		<div style="display: inline-flex;width: 100%; background-color: lightgrey; border: groove;">
			  <img style="width: 60px; height: 60px;" src="<?php echo plugins_url( 'assets/ngdesk-logo.png' , dirname(__FILE__) ) ?>">
			<h1 style="align-self: flex-end; margin-left: 20px;">ngDesk Plugin</h1>
		</div>
		<hr>
		 <?php settings_errors(); ?>
		<div style="padding: 10px;width: 98%; background-color: lightgrey; border: groove;">
			<form method="post" action="options.php">
			<?php
				settings_fields( 'ngdesk_plugin_settings' );
				do_settings_sections( 'ngdesk_plugin' );
				submit_button();
			?>
		</form>
		</div>
	</div>