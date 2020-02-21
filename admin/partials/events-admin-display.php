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
 * Return the string required if the argument is true.
 *
 * @since    1.0.0
 */
function check_required( $required ) {

	if ( $required ) {
		$required = 'required';
	} else {
		$required = '';
	}
	return $required;
	
}

/**
 * Display settings on the admin events page.
 *
 * @since    1.0.0
 */
function display_event_meta_input( $type, $name, $label, $value, $required = false ) {
	
	$required = check_required( $required )
	?>
	
	<div class="wp-spg-events">
		<div class="event-info">
			<label for="<?php echo esc_attr( $name ); ?>"><?php echo esc_html( $label ); ?></label>
			<br>
			<input id="<?php echo esc_attr( $name ); ?>" name="<?php echo esc_attr( $name ); ?>" type="<?php echo esc_attr( $type ); ?>" value="<?php echo esc_attr( $value ) ?>" <?php echo $required; ?>/>
		</div>
	</div>

	<?php
}

/**
 * Display a photo selection option on the new speaker form.
 *
 * @since    1.0.0
 */
function display_new_speaker_photo_select( $label, $description, $photo_url ) {
	?>
	
	<div class="wp-spg-events">
		<div class="form-field term-<?php echo esc_html( strtolower( $label ) ); ?>-wrap">
			<div class="image-preview-wrapper">
					<img id="image-preview" src="<?php echo esc_url( $photo_url ); ?>">
			</div>
	    <input id="upload_image_button" type="button" class="button" value="Set featured image"/>
	    <input id="image_attachment_id" type="hidden" name="speaker_thumbnail" value="">
			<p><?php echo esc_html( $description ); ?></p>
			<p><?php echo $attachment_id; ?></p>
		</div>
	</div>

	<?php
}

/**
 * Display a photo selection option on the edit speaker form.
 *
 * @since    1.0.0
 */
function display_existing_speaker_photo_select( $label, $description, $photo_url ) {
	?>
	
	<tr class="form-field term-<?php echo esc_html( strtolower( $label ) ); ?>-wrap">
		<th scope="row">
			<div class="wp-spg-events">
				<div class="image-preview-wrapper">
					<img id="image-preview" src="<?php echo esc_url( $photo_url ); ?>">
				</div>
			</div>
		</th>
		<td>
	    <input id="upload_image_button" type="button" class="button" value="Set featured image"/>
    	<input id="image_attachment_id" type="hidden" name="speaker_thumbnail" value="">
			<p class="description"><?php echo esc_html( $description ); ?></p>
			<p><?php echo $attachment_id; ?></p>
		</td>
	</tr>

	<?php
}
