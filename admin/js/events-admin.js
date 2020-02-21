/* 
 * Admin area JavaScript.
 *
 * Currently opens a dialogue to select an image from the media library.
 */

// Credit: Mike Jolley (mikejolley.com)
jQuery( document ).ready( function( $ ) {

  // Uploading files
  var file_frame;

  jQuery('#upload_image_button').on('click', function( event ){
    event.preventDefault();

    // If the media frame already exists, reopen it.
    if ( file_frame ) {
      // Open frame
      file_frame.open();
      return;
    }

    // Create the media frame.
    file_frame = wp.media.frames.file_frame = wp.media({
      title: 'Featured Image',
      button: {text: 'Set featured image'},
      multiple: false    // Set to true to allow multiple files to be selected
    });

    // When an image is selected, run a callback
    file_frame.on( 'select', function() {

      // Only get one image from the uploader (multiple images set to false)
      attachment = file_frame.state().get('selection').first().toJSON();
  
      // Show a preview of the image by setting the image preview URL
      $('#image-preview').attr('src', attachment.url).css('width', 'auto');
      // Set the hidden field attachment ID
      $('#image_attachment_id').val(attachment.id);
    });
  
    // Finally, open the modal
    file_frame.open();
  });
  
});
