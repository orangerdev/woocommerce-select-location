<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://https://ridwan-arifandi.com/
 * @since      1.0.0
 *
 * @package    Woo_Choose_Location
 * @subpackage Woo_Choose_Location/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Woo_Choose_Location
 * @subpackage Woo_Choose_Location/includes
 * @author     OrangerDev Team <orangerdigiart@gmail.com>
 */
class Woo_Choose_Location_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'woo-choose-location',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
