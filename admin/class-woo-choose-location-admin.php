<?php

use Carbon_Fields\Container;
use Carbon_Fields\Field;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://https://ridwan-arifandi.com/
 * @since      1.0.0
 *
 * @package    Woo_Choose_Location
 * @subpackage Woo_Choose_Location/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woo_Choose_Location
 * @subpackage Woo_Choose_Location/admin
 * @author     OrangerDev Team <orangerdigiart@gmail.com>
 */
class Woo_Choose_Location_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Add plugin options
	 * Hooked via	action carbon_fields_register_fields, priority 10
	 * @since 		1.0.0
	 * @return 		void
	 */
	public function add_plugin_options() {

		Container::make( "theme_options", __("Woo Choose Location", "woo-choose-location"))
			->add_fields([
				Field::make( "select",	 "woo_location_default", __("Default Location", "woo-choose-location"))
					->add_options(array($this, "get_default_location_options"))
			]);

	}

	public function get_default_location_options() {

		$options = array();

		$taxonomy = 'pa_location';
		$terms = get_terms($taxonomy);
		foreach ($terms as $key => $term) :
			$options[$term->slug] = $term->name;
		endforeach;

		return $options;

	}

	public function crb_load() {
		\Carbon_Fields\Carbon_Fields::boot();
	}

}
