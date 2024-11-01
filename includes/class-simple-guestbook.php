<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @since      1.0.0
 *
 * @package    Simple_Guestbook
 * @subpackage Simple_Guestbook/includes
 */

class Simple_Guestbook {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Simple_Guestbook_Loader    $loader    Maintains and registers all hooks for the plugin.
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
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct($version, $plugin_name, $option_name, $base_name) {
		$this->version = $version;
		$this->plugin_name =  $plugin_name;
		$this->option_name = $option_name;
		$this->base_name = $base_name;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Simple_Guestbook_Loader. Orchestrates the hooks of the plugin.
	 * - Simple_Guestbook_i18n. Defines internationalization functionality.
	 * - Simple_Guestbook_Admin. Defines all hooks for the admin area.
	 * - Simple_Guestbook_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-simple-guestbook-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-simple-guestbook-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-simple-guestbook-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-simple-guestbook-public.php';

		/**
		 * The class responsible for handling options of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-simple-guestbook-options.php';

		$this->loader = new Simple_Guestbook_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Simple_Guestbook_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Simple_Guestbook_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Simple_Guestbook_Admin( $this->get_plugin_name(), $this->get_version(), $this->get_plugin_optionname() );

        $this->loader->add_filter( 'plugin_action_links_' . $this->get_plugin_basename(), $plugin_admin, 'add_plugin_settings_link');
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_options_page' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_setting' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_media_scripts' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Simple_Guestbook_Public( $this->get_plugin_name(), $this->get_version() );
		
		$this->loader->add_action( 'init', $plugin_public, 'register_shortcode' );
		$this->loader->add_action( 'the_content', $plugin_public, 'simple_guestbook_content');
		$this->loader->add_action( 'comments_template', $plugin_public, 'disable_comments');
		$this->loader->add_action( 'after_setup_theme', $plugin_public, 'html5_theme_setup');
		$this->loader->add_filter( 'pre_render_block', $plugin_public, 'disable_block_comments', 10, 2);
		$this->loader->add_action( 'wp_footer', $plugin_public, 'custom_comment_validation_js');
		//
		// Been testing themes where these hooks did not work and ended up in using 'wp_footer' ¯\_(ツ)_/¯
		//
		//$this->loader->add_action( 'enqueue_scripts', $plugin_public, 'simple_guestbook_comment_styles' );
		//$this->loader->add_action( 'wp_enqueue_styles', $plugin_public, 'simple_guestbook_comment_styles' );
		$this->loader->add_action( 'wp_footer', $plugin_public, 'simple_guestbook_comment_styles');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
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
	 * @return    Simple_Guestbook_Loader    Orchestrates the hooks of the plugin.
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
	 * Retrieve the base name of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The base name of the plugin.
	 */
	public function get_plugin_basename() {
		return $this->base_name;
	}

	/**
	 * Retrieve the option name of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The option name of the plugin.
	 */
	public function get_plugin_optionname() {
		return $this->option_name;
	}
}
