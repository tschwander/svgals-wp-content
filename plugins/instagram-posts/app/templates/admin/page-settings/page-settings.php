<div class="wrap">

	<h2><?php echo esc_html( $page_title ); ?></h2>

	<form id="instagram_posts" method="post" action="options.php">
		<?php
			settings_fields( $settings_name );
			do_settings_sections( $settings_name );
			submit_button();
		?>
	</form>
	<?php 
		if ($authurl){
			echo '<a href="'.$authurl.'">Refresh Token abrufen</a><br />';
		}
		if ($calendarurl){
			echo '<a href="'.$calendarurl.'">Kalender abrufen</a><br />';
		}
		if($refresh_token){
			echo 'Refresh Token:<br />'.$refresh_token .'<br />';
		}
		if($calendars){
			echo '<ul>';
			foreach($calendars as $calendar){
				echo '<li>';
				echo 	$calendar->getName() .":<br />" .$calendar->getId();
				echo '</li>';
			}
			echo '</ul>';
		}
		
	?>
</div> <!-- .wrap -->
