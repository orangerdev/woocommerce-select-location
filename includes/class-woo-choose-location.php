<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://https://ridwan-arifandi.com/
 * @since      1.0.0
 *
 * @package    Woo_Choose_Location
 * @subpackage Woo_Choose_Location/includes
 */

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
 * @package    Woo_Choose_Location
 * @subpackage Woo_Choose_Location/includes
 * @author     OrangerDev Team <orangerdigiart@gmail.com>
 */
class Woo_Choose_Location
{

  /**
   * The loader that's responsible for maintaining and registering all hooks that power
   * the plugin.
   *
   * @since    1.0.0
   * @access   protected
   * @var      Woo_Choose_Location_Loader    $loader    Maintains and registers all hooks for the plugin.
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
  public function __construct()
  {
    if (defined('WOO_CHOOSE_LOCATION_VERSION')) {
      $this->version = WOO_CHOOSE_LOCATION_VERSION;
    } else {
      $this->version = '1.0.0';
    }
    $this->plugin_name = 'woo-choose-location';

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
   * - Woo_Choose_Location_Loader. Orchestrates the hooks of the plugin.
   * - Woo_Choose_Location_i18n. Defines internationalization functionality.
   * - Woo_Choose_Location_Admin. Defines all hooks for the admin area.
   * - Woo_Choose_Location_Public. Defines all hooks for the public side of the site.
   *
   * Create an instance of the loader which will be used to register the hooks
   * with WordPress.
   *
   * @since    1.0.0
   * @access   private
   */
  private function load_dependencies()
  {

    /**
     * The class responsible for orchestrating the actions and filters of the
     * core plugin.
     */
    require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-woo-choose-location-loader.php';

    /**
     * The class responsible for defining internationalization functionality
     * of the plugin.
     */
    require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-woo-choose-location-i18n.php';

    /**
     * The class responsible for defining all actions that occur in the admin area.
     */
    require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-woo-choose-location-admin.php';

    /**
     * The class responsible for defining all actions that occur in the public-facing
     * side of the site.
     */
    require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-woo-choose-location-public.php';

    require_once plugin_dir_path(dirname(__FILE__)) . 'functions/location.php';

    $this->loader = new Woo_Choose_Location_Loader();
  }

  /**
   * Define the locale for this plugin for internationalization.
   *
   * Uses the Woo_Choose_Location_i18n class in order to set the domain and to register the hook
   * with WordPress.
   *
   * @since    1.0.0
   * @access   private
   */
  private function set_locale()
  {

    $plugin_i18n = new Woo_Choose_Location_i18n();

    $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
  }

  /**
   * Register all of the hooks related to the admin area functionality
   * of the plugin.
   *
   * @since    1.0.0
   * @access   private
   */
  private function define_admin_hooks()
  {

    $plugin_admin = new Woo_Choose_Location_Admin($this->get_plugin_name(), $this->get_version());

    $this->loader->add_action('after_setup_theme', $plugin_admin, 'crb_load');
    $this->loader->add_action('carbon_fields_register_fields', $plugin_admin, 'add_plugin_options');
  }

  /**
   * Register all of the hooks related to the public-facing functionality
   * of the plugin.
   *
   * @since    1.0.0
   * @access   private
   */
  private function define_public_hooks()
  {

    $plugin_public = new Woo_Choose_Location_Public($this->get_plugin_name(), $this->get_version());

    $this->loader->add_action('template_redirect',                        $plugin_public, 'set_location_by_url', 999);
    $this->loader->add_action('wp_footer',                                $plugin_public, 'set_location_by_external', 999, 1);
    // $this->loader->add_action('wp_footer',                                $plugin_public, 'display_sticky_choose_location', 999, 1);
    $this->loader->add_action('etheme_header',                            $plugin_public, 'display_sticky_choose_location', 999, 21);
    $this->loader->add_action('wp_enqueue_scripts',                       $plugin_public, 'enqueue_scripts', 999, 1);
    $this->loader->add_action('woocommerce_before_add_to_cart_quantity',  $plugin_public, 'display_check_location_store', -1, 1);
    $this->loader->add_action('wp_ajax_wb_get_location_store',            $plugin_public, 'ajax_get_location_stores', 999, 1);
    $this->loader->add_action('wp_ajax_nopriv_wb_get_location_store',     $plugin_public, 'ajax_get_location_stores', 999, 1);
    $this->loader->add_filter('body_class',                               $plugin_public, 'add_body_class', 999, 1);
  }

  /**
   * Run the loader to execute all of the hooks with WordPress.
   *
   * @since    1.0.0
   */
  public function run()
  {
    $this->loader->run();
  }

  /**
   * The name of the plugin used to uniquely identify it within the context of
   * WordPress and to define internationalization functionality.
   *
   * @since     1.0.0
   * @return    string    The name of the plugin.
   */
  public function get_plugin_name()
  {
    return $this->plugin_name;
  }

  /**
   * The reference to the class that orchestrates the hooks with the plugin.
   *
   * @since     1.0.0
   * @return    Woo_Choose_Location_Loader    Orchestrates the hooks of the plugin.
   */
  public function get_loader()
  {
    return $this->loader;
  }

  /**
   * Retrieve the version number of the plugin.
   *
   * @since     1.0.0
   * @return    string    The version number of the plugin.
   */
  public function get_version()
  {
    return $this->version;
  }
}
