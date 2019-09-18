<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link
 * @since      1.0.0
 *
 * @package    SPG_Events
 * @subpackage SPG_Events/public
 */
namespace SPG_Events;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and hooks for managing the public front
 * end (including enqueuing the public-facing stylesheet and JavaScript). An
 * instance of this class should be passed to the run() function defined
 * in Events_Loader as all of the hooks are actually defined in that
 * particular class. The Events_Loader will then create the
 * relationship between the defined hooks and the functions defined in this
 * class.
 *
 * @package    SPG_Events
 * @subpackage SPG_Events/public
 * @author     Mitch Negus <mitchell.negus.57@gmail.com>
 */
class Events_Public {

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
	 * @param    string    $plugin_name       The name of the plugin.
	 * @param    string    $version           The version of this plugin.
	 * @param    array     $options           An array of the options set and added to the database by the plugin.
	 * @param    array     $event_meta       An array of the meta fields for the custom project post type.
	 */
	public function __construct( $plugin_name, $version, $event_meta ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->event_meta = $event_meta;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 * 
	 * (Executed by loader class)
	 * 
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'css/events-public.css',
			array(),
			$this->version,
			'all'
	 	);

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * (Executed by loader class)
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'js/events-public.js',
			array('jquery'),
			$this->version,
			true
		);

	}

 	/**
	 * Register the custom post type for a event.
	 *
	 * Each event has an individual post that stores its information (title,
	 * description, thumbnail, contact, speaker, etc.). This post is also accessed
	 * for display on the general events page, where all events are listed.
	 * (Executed by loader class)
	 *
	 * @since    1.0.0
	 */
	public function register_event_post_type() {

		$labels = array(
			'name' 					=> __( 'Events' ),
			'singular_name' => __( 'Event' ),
			'add_new_item' 	=> __( 'Add New Event' ),
			'edit_item' 		=> __( 'Edit Event' ),
			'view_item'     => __( 'View Event' ),
			'search_items'  => __( 'Search Events' )
		);

		$args = array(
			'labels' 			=> $labels,
			'public'			=> true,
			'has_archive' => true,
			'rewrite' 		=> array( 'slug' => 'events' ),
			'supports' 		=> array( 'title', 'editor', 'thumbnail' ),
			'menu_icon' 	=> 'dashicons-calendar-alt'
		);

		register_post_type( 'events', $args );
		flush_rewrite_rules();

	}

	/**
	 * Register the speaker taxonomy for an event post.
	 *
	 * Each event can feature one or more speakers. This taxonomy allows speakers
	 * to be associated directly with an event.
	 * (Executed by loader class)
	 *
	 * @since		1.0.0
	 */
	public function register_speakers_taxonomy() {

		$labels = array(
			'name'          => __( 'Speakers' ),
			'singular_name' => __( 'Speaker' ),
			'add_new_item'  => __( 'Add New Speaker' ),
			'edit_item'     => __( 'Edit Speaker' ),
			'view_item'     => __( 'View Speaker' ),
			'search_items'  => __( 'Search Speakers' ),
			'back_to_items' => __( 'Back to Speakers' )
		);

		$args = array(
			'labels'       => $labels,
			'rewrite'      => array( 'slug' => 'speakers' ),
			'hierarchical' => false
		);

		register_taxonomy( 'speakers', array( 'events' ), $args );

	}
		
	/**
	 * Set the custom post archive template for the 'Events' page.
	 * 
	 * (Executed by loader class)
	 *
	 * @since    1.0.0
	 * @param    string     $archive_template     The path to the current archive post template that is being used by Wordpress.
	 * @return   string                           The path to the replacement archive post template to be used instead.
	 */
	public function use_event_archive_template( $archive_template ) {

		if ( is_post_type_archive( 'events' ) ) {
			$archive_template = WSE_PATH . 'public/templates/archive-events.php';
	 	}
		return $archive_template;

	}

	/**
	 * Define the ordering of the custom events posts.
	 *
	 * (Executed by loader class)
	 *
	 * @param    WP_QUERY    $query 							The post query to sort by.
	 */
	public function event_posts_archive_orderby( $query ) {

		if ( $query->is_main_query() && is_post_type_archive( 'events' ) ) {
			$query->set( 'meta_key', 'event_date' );
			$query->set( 'orderby', 'meta_value');
		}

	}
	
}

