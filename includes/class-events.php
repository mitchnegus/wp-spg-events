<?php

/**
 * The file that defines the core plugin class.
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link
 * @since      1.0.0
 *
 * @package    SPG_Events
 * @subpackage SPG_Events/includes
 */
namespace SPG_Events;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    SPG_Events
 * @subpackage SPG_Events/includes
 * @author     Mitch Negus <mitchell.negus.57@gmail.com>
 */
class Events {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Events_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies and set the hooks for the admin area and public-facing side
	 * of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		// Set plugin overhead details
		if ( defined( 'SPG_EVENTS_VERSION' ) ) {
			$this->version = SPG_EVENTS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'spg-events';
		// Create an array of options that are added to the database by the plugin
		//    -> Keys are the in-code reference names
		//    -> Values are the option names in the database
		$this->plugin_options = array(
			'fall_semester_start'   => 'spg_fall_semester_start',
			'fall_semester_end'     => 'spg_fall_semester_end',
			'spring_semester_start' => 'spg_spring_semester_start',
			'spring_semester_end'   => 'spg_spring_semester_end'
		);
		// Create arrays of meta keys that are assigned to custom event posts and speakers
		$this->event_meta = array(
			array('meta_key' => 'event_semester', 'required' => true),
			array('meta_key' => 'event_date', 'required' => true),
			array('meta_key' => 'event_time', 'required' => false),
		 	array('meta_key' => 'event_location', 'required' => false)
		);
		$this->speaker_meta = array(
			array('meta_key' => 'speaker_thumbnail'),
		);
		$this->meta_titles = array(
			'event_semester'    => 'Semester',
			'event_date'        => 'Date',
			'event_time'        => 'Time',
			'event_location'    => 'Location',
			'speaker_thumbnail' => 'Photo'
		);		

		// Load plugin dependencies and set actions and filters for hooks
		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Events_Loader. Orchestrates the hooks of the plugin.
	 * - Events_Admin. Defines all hooks for the admin area.
	 * - Events_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-events-loader.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-events-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-events-public.php';

		$this->loader = new Events_Loader();

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Events_Admin( 
			$this->get_plugin_name(),
			$this->get_version(),
			$this->get_plugin_options(),
			$this->get_event_meta(),
			$this->get_speaker_meta(),
			$this->get_meta_titles()
	 	);

		// Set admin area styles
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		// Add admin area settings page
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_settings_page' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'add_settings' );
		// Provide admin area controls for event custom posts
		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'add_admin_event_fields');
		$this->loader->add_action( 'save_post', $plugin_admin, 'save_event_details');
		$this->loader->add_action( 'speakers_add_form_fields', $plugin_admin, 'add_admin_speaker_fields');
		$this->loader->add_action( 'speakers_edit_form_fields', $plugin_admin, 'edit_admin_speaker_fields');
		$this->loader->add_action( 'create_speakers', $plugin_admin, 'save_speaker_details');
		$this->loader->add_action( 'edit_speakers', $plugin_admin, 'save_speaker_details');
		// Update the columns on the browse event page
		$this->loader->add_action( 'manage_events_posts_custom_column', $plugin_admin, 'fill_event_columns', 10, 2 );
		$this->loader->add_filter( 'manage_events_posts_columns', $plugin_admin, 'set_event_columns' );
		$this->loader->add_filter( 'manage_edit-events_sortable_columns', $plugin_admin, 'set_event_sortable_columns');
		$this->loader->add_action( 'pre_get_posts', $plugin_admin, 'event_posts_orderby' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Events_Public( 
			$this->get_plugin_name(),
			$this->get_version(),
			$this->get_plugin_options(),
			$this->get_event_meta()
	 	);

		// Set public-facing styles and JavaScript
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		// Hook up our custom post to theme setup
		$this->loader->add_action( 'init', $plugin_public, 'register_event_post_type' );
		$this->loader->add_action( 'init', $plugin_public, 'register_speakers_taxonomy' );
		// Use custom templates for the event pages
		$this->loader->add_filter( 'archive_template', $plugin_public, 'use_event_archive_template' );
		$this->loader->add_action( 'pre_get_posts', $plugin_public, 'event_posts_archive_orderby' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {

		// Add theme support for thumbnails if not already included
		add_theme_support( 'post-thumbnails' );
		set_post_thumbnail_size( 200, 200 );
		// Run the loader (with hooks for actions and filters)
		$this->loader->run();

	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Events_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Retrieve the options that are added to the database by the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    An array of options that are set by the plugin.
	 */
	public function get_plugin_options() {
	    return $this->plugin_options;
  }

	/**
	 * Retrieve the custom event post meta keys.
	 *
	 * @since     1.0.0
	 * @return    string    An array of event meta keys used by the plugin.
	 */
	public function get_event_meta() {
		return $this->event_meta;
	}

	/**
	 * Retrieve the custom speaker taxonomy meta keys.
	 *
	 * @since     1.0.0
	 * @return    string    An array of speaker meta keys used by the plugin.
	 */
	public function get_speaker_meta() {
		return $this->speaker_meta;
	}

	/**
	 * Retrieve titles for the custom post meta keys.
	 *
	 * @since     1.0.0
	 * @return    string    An array of titles for custom post meta keys.
	 */
	public function get_meta_titles() {
		return $this->meta_titles;
	}

}
