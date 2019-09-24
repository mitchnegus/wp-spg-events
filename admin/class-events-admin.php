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
	 * @param      array     $event_meta       An array of the meta fields for the custom event post type.
	 */
	public function __construct( $plugin_name, $version, $event_meta, $meta_titles ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->events_custom_post_type = 'events';
		$this->event_meta = $event_meta;
		$this->meta_titles = $meta_titles;
		// All functions prefixed with 'display_' come from `partials`
		require_once plugin_dir_path( __FILE__ ) . 'partials/events-admin-display.php';
	}

	/**
	 * Add fields to the admin area corresponding to custom post metadata.
	 *
	 * Event information other than the event's title, logo, and description
	 * (e.g. event date, time, and location) are stored as post metadata.
	 * Input boxes for that metadata in the admin area are defined here.
	 * (Executed by loader class)
	 *
	 * @since    1.0.0
	 */
	public function add_admin_fields() {

		add_meta_box(
			'event_info-meta',
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

				foreach ( $this->event_meta as $meta ) {
					// Sanitize user input and update the post metadata
					$meta_key = $meta['meta_key'];
					$meta_value = sanitize_text_field($_POST[ $meta_key ]);
					// Make sure that a "Quick Edit" is not saving empty info
					if ( ! empty( $meta_value ) ) {
						update_post_meta( $post_id, $meta_key, $meta_value );
					}
				}

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
	 * @param    array                            The existing columns to be sorted in the 'Events' section of the admin area.
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
	 * @param    WP_POST    $post                 The post associated with the current event.
	 */
	public function present_event_metabox( $post ) {

		$titles = $this->meta_titles;
		foreach ( $this->event_meta as $meta ) {
			// Get event meta parameters
			$meta_key = $meta['meta_key'];
			$custom = get_post_custom( $post->ID );
			$meta_value = $custom[ $meta_key ][0];
			// Show the selection interface
			display_label( $meta_key, $titles[ $meta_key ] );
			$required = $meta['required'];
			if ( $meta_key == 'event_date' ) {
				display_input( 'date', $meta_key, $meta_value, $required);
			} else if ( $meta_key == 'event_time' ) {
				display_input( 'time', $meta_key, $meta_value, $required );
			} else {
				display_input( 'text', $meta_key, $meta_value, $required );
			}
		}

	}

}
