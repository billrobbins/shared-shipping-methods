<?php

/**
 * Shared Shipping Methods Settings.
 */
class Shared_Shipping_Methods_Settings {

	/**
	 * The ID of the shared shipping zone.
	 *
	 * @var string
	 */
	protected $shared_shipping_zone;

	/**
	 * The class sets up the shared shipping option and adds the shared shipping method
	 * to the list of shipping methods.  It also hooks our activation method.
	 */
	public function __construct() {

		$this->shared_shipping_zone = get_option( 'shared_shipping_zone' );

		add_filter( 'woocommerce_shipping_settings', array( $this, 'insert_settings_page_option_field' ) );
		add_filter( 'woocommerce_shipping_methods', array( $this, 'add_availability' ) );
		add_filter( 'woocommerce_shipping_methods', array( $this, 'selectively_remove_availability' ) );

		register_activation_hook( __DIR__ . '/shared-shipping-methods.php', array( $this, 'activate_shared_shipping_methods' ) );

	}

	/**
	 * Adds a field to select a zone from the shipping options page.
	 *
	 * @param array $settings List of shipping settings.
	 * @return array The updated list of shipping settings.
	 */
	public function insert_settings_page_option_field( $settings ) {

		$zones        = WC_Shipping_Zones::get_zones();
		$zone_options = array(
			'' => 'None',
		);

		foreach ( $zones as $zone ) {
			$zone_options[ $zone['zone_id'] ] = $zone['zone_name'];
		}

		$settings[] = array(
			'name'     => __( 'Share shipping zone', 'woocommerce' ),
			'desc_tip' => __( 'Select a zone to share shipping methods', 'woocommerce' ),
			'id'       => 'shared_shipping_zone',
			'type'     => 'select',
			'class'    => 'wc-enhanced-select',
			'default'  => '',
			'options'  => $zone_options,
		);

		return $settings;

	}

	/**
	 * Adds the Shared Shipping Methods class to the list of shipping methods.
	 *
	 * @param array $methods List of shipping methods.
	 * @return array
	 */
	public function add_availability( $methods ) {
		$methods['shared_shipping_method'] = 'Shared_Shipping_Method';
		return $methods;
	}

	/**
	 * Removes the Shared Shipping Methods class from the list of shipping methods
	 * if the curret zone is the shared shipping zone.
	 *
	 * @param array $methods List of shipping methods.
	 * @return array
	 */
	public function selectively_remove_availability( $methods ) {
		if ( isset( $_GET['zone_id'] ) && $_GET['zone_id'] === $this->shared_shipping_zone ) {
			unset( $methods['shared_shipping_method'] );
		}
		return $methods;
	}

	/**
	 * Adds a new shared shipping zone when the plugin is activated
	 * if one isn't set.  The order is set high so it's at the bottom
	 * of the list. The location is set to Antartica to prevent these
	 * shipping methods from showing up in the cart.
	 */
	public function activate_shared_shipping_methods() {

		$shared_shipping_zone = get_option( 'shared_shipping_zone' );

		// If the shared shipping zone is set to none, delete the option.  If it is set, return.
		if ( '' === $shared_shipping_zone ) {
			delete_option( 'shared_shipping_zone' );
		} elseif ( isset( $shared_shipping_zone ) ) {
			return;
		}

		try {
			$zone = new WC_Shipping_Zone();

			$zone->set_zone_name( 'Shared Shipping Methods' );
			$zone->set_zone_order( '100' );
			$zone->add_location( 'AQ', 'country' );
			$zone->save();

			$zone_id = $zone->get_id();
			add_option( 'shared_shipping_zone', $zone_id, '', 'no' );

		} catch ( Exception $e ) {
			$logger = wc_get_logger();
			$logger->log( 'error', 'Failed to save shared_shipping_zone option for zone id: ' . $zone_id, array( 'source' => 'Shared Shipping Methods' ) );
		}

	}

}