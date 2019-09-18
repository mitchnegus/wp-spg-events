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

function display_input( $type, $name, $value, $required = false ) {

	if ( $required ) {
		$required = 'required';
	} else {
		$required = '';
	}
	?>

	<input id="<?php echo esc_attr( $name ); ?>" name="<?php echo esc_attr( $name ); ?>" type="<?php echo esc_attr( $type ); ?>" value="<?php echo esc_attr( $value ) ?>" <?php echo $required; ?>/>
	<br>

	<?php
}
