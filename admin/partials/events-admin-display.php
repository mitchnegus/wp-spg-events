<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin. This
 * file should primarily consist of HTML with a little bit of PHP.
 *
 * @link
 * @since      1.0.0
 *
 * @package    SPG_Events
 * @subpackage SPG_Events/admin/partials
 */
namespace SPG_Events;

/**
 * Display settings on the admin menu page.
 *
 * @since    1.0.0
 */

function display_label( $for, $label) {
	?>

	<label for="<?php echo esc_attr( $for ); ?>"><?php echo esc_html( $label ); ?></label>
	<br>

	<?php
}

function display_text_input( $name, $value, $required = false ) {

	if ( $required ) {
		$required = 'required';
	} else {
		$required = '';
	}
	?>

	<input id="<?php echo esc_attr( $name ); ?>" name="<?php echo esc_attr( $name ); ?>" type="text" value="<?php echo esc_attr( $value ) ?>" <?php echo $required; ?>/>
	<br>

	<?php
}

function display_time_input( $name, $value, $required = false ) {

	if ( $required ) {
		$required = 'required';
	} else {
		$required = '';
	}
	$time_list = [
		'12:00 AM', '12:30 AM', '1:00 AM', '1:30 AM', '2:00 AM', '2:30 AM',
		'3:00 AM', '3:30 AM', '4:00 AM', '4:30 AM', '5:00 AM', '5:30 AM',
		'6:00 AM', '6:30 AM', '7:00 AM', '7:30 AM', '8:00 AM', '8:30 AM',
		'9:00 AM', '9:30 AM', '10:00 AM', '10:30 AM', '11:00 AM', '11:30 AM',
		'12:00 PM', '12:30 PM', '1:00 PM', '1:30 PM', '2:00 PM', '2:30 PM',
		'3:00 PM', '3:30 PM', '4:00 PM', '4:30 PM', '5:00 PM', '5:30 PM',
		'6:00 PM', '6:30 PM', '7:00 PM', '7:30 PM', '8:00 PM', '8:30 PM',
		'9:00 PM', '9:30 PM', '10:00 PM', '10:30 PM', '11:00 PM', '11:30 PM',
	]
	?>

	<select id="<?php echo esc_attr( $name ); ?>" name="<?php echo esc_attr( $name ); ?>" <?php echo $required; ?>>

		<?php
		foreach ( $time_list as $time ) {
			if ( $time == $value ) {
				$selected = 'selected';
			} else {
				$selected = '';
			}
			?>

				<option value="<?php echo esc_html( $time ); ?>" <?php echo $selected; ?>>
					<?php echo esc_html( $time ); ?>
				</option>

			<?php
		}
		?>

	</select>
	<br>

	<?php
}
