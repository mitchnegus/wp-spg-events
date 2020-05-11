<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link
 * @since      1.0.0
 *
 * @package    SPG_Events
 * @subpackage SPG_Events/admin
 */
namespace SPG_Events;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and hooks for managing the admin area
 * (including enqueuing the admin-specific stylesheet and JavaScript). An
 * instance of this class should be passed to the run() function defined
 * in Events_Loader as all of the hooks are actually defined in that
 * particular class. The Events_Loader will then create the relationship
 * between the defined hooks and the functions defined in this class.
 *
 * @package    SPG_Events
 * @subpackage SPG_Events/admin
 * @author     Mitch Negus <mitchell.negus.57@gmail.com>
 */
class Events_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version           The version of this plugin.
	 * @param      array     $options           An array of the options set and added to the database by the plugin.
	 * @param      array     $event_meta        An array of the meta fields for the custom event post type.
	 * @param      array     $event_meta        An array of the meta fields for the custom event post type.
	 * @param      array     $meta_titles       An array of the meta fields for the custom event post type.
	 */
	public function __construct( $plugin_name, $version, $event_meta, $speaker_meta, $meta_titles ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->events_custom_post_type = 'events';
		$this->event_meta = $event_meta;
		$this->speakers_custom_taxonomy = 'speakers';
		$this->speaker_meta = $speaker_meta;
		$this->meta_titles = $meta_titles;
		// All functions prefixed with 'display_' come from `partials`
		require_once plugin_dir_path( __FILE__ ) . 'partials/events-admin-display.php';
	}

	/**
	 * Register the stylesheets for the admin area.
	 * 
	 * (Executed by loader class)
	 * 
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'css/events-admin.css',
			array(),
			$this->version,
			'all'
	 	);

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * (Executed by loader class)
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'js/events-admin.js',
			array('jquery'),
			$this->version,
			true
		);
		wp_localize_script(
			$this->plugin_name,
			'events_admin_vars',
			array('image_attachment_id' => 267)
		);

	}

	/**
	 * Add fields to the admin area corresponding to event metadata.
	 *
	 * Event information other than the event's title, logo, and description
	 * (e.g. event date, time, and location) are stored as post metadata.
	 * Input boxes for that metadata in the admin area are defined here.
	 * (Executed by loader class)
	 *
	 * @since    1.0.0
	 */
	public function add_admin_event_fields() {

		add_meta_box(
			'event-info_meta',
			'Event Info',
			[$this, 'present_event_metabox'],
			$this->events_custom_post_type,
			'normal',
			'low'
		);

	}

	/**
	 * Save event details to the database after an admin decides to update.
	 *
	 * (Executed by loader class)
	 *
	 * @since    1.0.0
	 * @param    int        $post_id             The ID of the custom event post.
	 */
	public function save_event_details( $post_id ) {

			// Only save meta data for event posts
			if ( get_post_type( $post_id ) == $this->events_custom_post_type ) {

				// Do not save (empty) meta data if save comes from "Quick Edit"
				if (wp_verify_nonce($_POST['_inline_edit'], 'inlineeditnonce'))
				  return;

				foreach ( $this->event_meta as $meta ) {
					// Sanitize user input and update the post metadata
					$meta_key = $meta['meta_key'];
					$meta_value = sanitize_text_field($_POST[ $meta_key ]);
					update_post_meta( $post_id, $meta_key, $meta_value );
				}

			}

	}

	/**
	 * Add fields to the admin area corresponding to new speaker metadata.
	 *
	 * Speaker information other than the speaker's name and description (e.g.
	 * title, and photo) are stored as post metadata. Input boxes for that
	 * metadata in the admin area are defined here. This function only applies to
	 * new speaker metadata.
	 * (Executed by loader class)
	 *
	 * @since    1.0.0
	 */
	public function add_admin_speaker_fields() {

		$this->present_speaker_photo_select();

	}

	/**
	 * Add fields to the admin area corresponding to existing speaker metadata.
	 *
	 * Speaker information other than the speaker's name and description (e.g.
	 * title, and photo) are stored as post metadata. Input boxes for that
	 * metadata in the admin area are defined here. This function only applies to
	 * existing speaker metadata.
	 * (Executed by loader class)
	 *
	 * @since    1.0.0
	 * @param    WP_Term    $tag             The current taxonomy term object.
	 */
	public function edit_admin_speaker_fields( $tag ) {

		$speaker_id = $tag->term_id;
		$this->present_speaker_photo_select( $speaker_id );
		
	}

	/**
	 * Save speaker details to the database after an admin decides to update.
	 *
	 * (Executed by loader class)
	 *
	 * @since    1.0.0
	 * @param    int        $term_id             The ID of the custom speaker term.
	 */
	public function save_speaker_details( $term_id ) {

				foreach ( $this->speaker_meta as $meta ) {
					// Sanitize user input and update the post metadata
					$meta_key = $meta['meta_key'];
					$meta_value = sanitize_text_field($_POST[ $meta_key ]);
					update_term_meta( $term_id, $meta_key, $meta_value );
				}

	}

	/**
	 * Fill columns in the admin area with custom post information.
	 *
	 * In the admin area, an administrator can see a list of all events
	 * currently listed on the site. This function populates columns in that
	 * list with relevant information about each event.
	 * (Executed by loader class)
	 */
	public function fill_event_columns( $column ) {

		$column1 = 'event_date';
		$column2 = 'event_time';
		$column3 = 'event_location';
		$custom = get_post_custom();
		switch ( $column ) {
			case $column1:
				echo $custom[ $column1 ][0];
				break;
			case $column2:
				echo $custom[ $column2 ][0];
				break;
			case $column3:
				echo $custom[ $column3 ][0];
				break;
		}

	}

	/**
	 * Show columns on the list of all events in the admin area.
	 *
	 * (Executed by loader class)
	 *
	 * @since    1.0.0
	 * @return   array                            The columns to be displayed in the 'Events' section of the admin area.
	 */
	public function set_event_columns() {

		$columns = array(
			'cb' 			 => '<input type="checkbox" />',
			'title' 	 => __( 'Event' ),
			'event_date'     => __( $this->meta_titles['event_date'] ),
			'event_time'     => __( $this->meta_titles['event_time'] ),
			'event_location' => __( $this->meta_titles['event_location'] )
		);
		return $columns;

	}

	/**
	 * Allow custom post columns in the admin area to be sortable.
	 *
	 * (Executed by loader class)
	 *
	 * @since    1.0.0
	 * @param    array        $columns            The existing columns to be sorted in the 'Events' section of the admin area.
	 * @return   array                            The columns to be sorted in the 'Events' section of the admin area.
	 */
	public function set_event_sortable_columns( $columns ) {
		$columns['event_date'] = 'event_date';
		$columns['event_time'] = 'event_time';
		$columns['event_location'] = 'event_location';
		return $columns;
	}

	/**
	 * Define the ordering of the custom events posts.
	 *
	 * (Executed by loader class)
	 *
	 * @param    WP_QUERY    $query 							The post query to sort by.
	 */
	public function event_posts_orderby( $query ) {
		
		if ( ! is_admin() || ! $query->is_main_query() ) {
			return;
		}

		if ( $query->get( 'orderby' ) === 'event_date' ) {
			$query->set( 'meta_key', 'event_date' );
			$query->set( 'orderby', 'meta_value_num' );
		}
		if ( $query->get( 'orderby' ) === 'event_time' ) {
			$query->set( 'meta_key', 'event_time' );
			$query->set( 'orderby', 'meta_value_num' );
		}
		if ( $query->get( 'orderby' ) === 'event_location' ) {
			$query->set( 'meta_key', 'event_location' );
			$query->set( 'orderby', 'meta_value_num' );
		}

	}

	/**
	 * Present a text input in an admin area metabox for managing event info.
	 *
	 * @since 1.0.0
	 */
	public function present_event_metabox( $post ) {

		$titles = $this->meta_titles;
		$post_meta = get_post_meta( $post->ID );
		foreach ( $this->event_meta as $meta ) {
			// Get event meta parameters
			$meta_key = $meta['meta_key'];
			$meta_title = $titles[ $meta_key ];
			$meta_value = $post_meta[ $meta_key ][0];
			// Show the selection interface
			$required = $meta['required'];
			if ( $meta_key == 'event_date' ) {
				display_event_meta_input( 'date', $meta_key, $meta_title, $meta_value, $required);
			} else if ( $meta_key == 'event_time' ) {
				display_event_meta_input( 'time', $meta_key, $meta_title, $meta_value, $required);
			} else {
				display_event_meta_input( 'text', $meta_key, $meta_title, $meta_value, $required);
				}
		}

	}

	/**
	 * Add a field for selecting a photo for a speaker.
	 *
	 * @since    1.0.0
	 * @param    int         $speaker_id  	     	The ID of the speaker taxonomy term (empty if selecting a photo for a new speaker).
	 */
	public function present_speaker_photo_select( $speaker_id = '' ) {

		$meta_key = 'speaker_thumbnail';
		$description = 'The photo should be a square image of the speaker.';
		$title = $this->meta_titles[ $meta_key ];
		wp_enqueue_media();
		$attachment_id = get_term_meta( $speaker_id, $meta_key, $single = true );

		// Display field for setting image depending on the situation
		if ( empty( $attachment_id ) ) {
      // No image information found; use the default headshot template
		  $template_url = WSE_URL . 'img/headshot_template.png';
			if ( empty( $speaker_id ) ) {
				// This is a new speaker so no ID exists yet
			  display_new_speaker_photo_select( $title, $description, $template_url );
			} else {
		  	display_existing_speaker_photo_select( $title, $description, $template_url );
			}
		} else {
			// Get the image URL matching the attachment ID
			$photo_url = wp_get_attachment_image_src( $attachment_id )[0];
		  display_existing_speaker_photo_select( $title, $description, $photo_url );
		}
	}

}
