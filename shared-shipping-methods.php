<?php
/**
 * Plugin Name:       Shared Shipping Methods
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       Share shipping methods with other zones.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Bill Robbins
 * Author URI:        https://justabill.blog
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       shared-shipping-methods
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) || ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {
	exit;
}

/**
 * Loads the Shared_Shipping_Methods_Settings class.
 */
if ( ! class_exists( 'Shared_Shipping_Methods_Settings' ) ) {
	include_once 'class-shared-shipping-methods-settings.php';
	new Shared_Shipping_Methods_Settings();
}

/**
 * Loads the Shared_Shipping_Method class.
 */
function shared_shipping_method_init() {
	if ( ! class_exists( 'Shared_Shipping_Method' ) ) {
		include_once 'class-shared-shipping-method.php';
	}
}
add_action( 'woocommerce_shipping_init', 'shared_shipping_method_init' );