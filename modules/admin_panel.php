<div>
	<h2>Fine Dashboard - Source</h2>

	<?php
		if(isset($_POST['alert_content'])){
			update_option('alert_content', $_POST['alert_content']);
		}
	?>
	<div style="max-width: 1200px; margin: auto;">
		<h3>Alert warning</h3>
		<form method='post'>
			<?php
				$content = get_option('alert_content');
				wp_editor( $content, 'alert_content', $settings = array('textarea_rows'=> '5') );

				submit_button('Save', 'primary');
			?>
		</form>
	</div>
	<div style="display: flex; gap: 1rem;">
		<?php
			if(isset($_POST['widget_content_one'])){
				update_option('widget_content_one', $_POST['widget_content_one']);
			}
		?>
		<div style="flex: 1;">
			<h3>Widget one</h3>
			<form method='post'>
				<?php
					$content = get_option('widget_content_one');
					wp_editor( $content, 'widget_content_one', $settings = array('textarea_rows'=> '10') );

					submit_button('Save', 'primary');
				?>
			</form>
		</div>
		<?php
			if(isset($_POST['widget_content_two'])){
				update_option('widget_content_two', $_POST['widget_content_two']);
			}
		?>
		<div style="flex: 1;">
			<h3>Widget two</h3>
			<form method='post'>
				<?php
					$content = get_option('widget_content_two');
					wp_editor( $content, 'widget_content_two', $settings = array('textarea_rows'=> '10') );

					submit_button('Save', 'primary');
				?>
			</form>
		</div>
		<?php
			if(isset($_POST['widget_content_three'])){
				update_option('widget_content_three', $_POST['widget_content_three']);
			}
		?>
		<div style="flex: 1;">
			<h3>Widget three</h3>
			<form method='post'>
				<?php
					$content = get_option('widget_content_three');
					wp_editor( $content, 'widget_content_three', $settings = array('textarea_rows'=> '10') );

					submit_button('Save', 'primary');
				?>
			</form>
		</div>
	</div>
</div>
