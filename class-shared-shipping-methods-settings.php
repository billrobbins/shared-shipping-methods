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
	 * ID for the shared shipping method.
	 *
	 * @var string
	 */
	protected $shared_shipping_method_id = 'shared_shipping_method';

	/**
	 * Class ID for the shared shipping method.
	 *
	 * @var string
	 */
	protected $shared_shipping_method_class_id = 'Shared_Shipping_Method';

	/**
	 * Shared Shipping Methods option
	 *
	 * @var string
	 */
	protected $shared_shipping_zone_option = 'shared_shipping_zone';

	/**
	 * The class sets up the shared shipping option and adds the shared shipping method
	 * to the list of shipping methods.  It also hooks our activation method.
	 */
	public function __construct() {

		$this->shared_shipping_zone = get_option( $this->shared_shipping_zone_option );

		add_filter( 'woocommerce_shipping_settings', array( $this, 'insert_settings_page_option_field' ) );
		add_filter( 'woocommerce_shipping_methods', array( $this, 'add_availability' ) );
		add_filter( 'woocommerce_shipping_methods', array( $this, 'selectively_remove_availability' ) );

		register_activation_hook( __DIR__ . '/shared-shipping-methods.php', array( $this, 'activate_shared_shipping_methods' ) );

	}

	/**
	 * Add a new field to the shipping options page.
	 *
	 * This field allows the user to select a zone to share shipping methods from.  It excludes
	 * zones where a shared shipping method is already present to prevent a loop.
	 *
	 * @param array $settings List of shipping settings.
	 * @return array The updated list of shipping settings.
	 */
	public function insert_settings_page_option_field( array $settings ): array {

		$shipping_zones = WC_Shipping_Zones::get_zones();
		$zone_options   = array(
			'' => 'None',
		);

		foreach ( $shipping_zones as $shipping_zone ) {
			$zone = WC_Shipping_Zones::get_zone_by( 'zone_id', $shipping_zone['id'] );
			if ( ! $this->check_shipping_method_in_zone( $zone, $this->shared_shipping_method_id ) ) {
				$zone_options[ $shipping_zone['zone_id'] ] = $shipping_zone['zone_name'];
			}
		}

		$new_option[] = array(
			'title'    => __( 'Share shipping methods zone', 'shared-shipping-methods' ),
			'desc_tip' => __( 'Select a source zone for sharing shipping methods.', 'shared-shipping-methods' ),
			'id'       => $this->shared_shipping_zone_option,
			'type'     => 'select',
			'class'    => 'wc-enhanced-select',
			'default'  => '',
			'options'  => $zone_options,
		);

		// The settings section is closed by the last item in the $settings array so we need to insert the new option before that.
		$position = count( $settings ) - 1;
		array_splice( $settings, $position, 0, $new_option );

		return $settings;

	}

	/**
	 * Checks to see if a shipping method is in a zone.
	 *
	 * @param WC_Shipping_Zone $zone               The zone to check.
	 * @param string           $shipping_method_id The ID of the shipping method to check.
	 *
	 * @return bool
	 */
	private function check_shipping_method_in_zone( WC_Shipping_Zone $zone, string $shipping_method_id ): bool {

		$shipping_methods = $zone->get_shipping_methods();

		foreach ( $shipping_methods as $method ) {
			if ( $method->id === $shipping_method_id ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Adds the Shared Shipping Methods class to the list of shipping methods.
	 *
	 * @param array $methods List of shipping methods.
	 * @return array
	 */
	public function add_availability( array $methods ): array {
		$methods[ $this->shared_shipping_method_id ] = $this->shared_shipping_method_class_id;
		return $methods;
	}

	/**
	 * Removes the Shared Shipping Methods class from the list of shipping methods
	 * if the curret zone is the shared shipping zone.
	 *
	 * @param array $methods List of shipping methods.
	 * @return array
	 */
	public function selectively_remove_availability( array $methods ): array {
		if ( isset( $_GET['zone_id'] ) && $_GET['zone_id'] === $this->shared_shipping_zone ) {
			unset( $methods[ $this->shared_shipping_method_id ] );
		}
		return $methods;
	}

	/**
	 * Handles the activation of the plugin.
	 *
	 * When the plugin is activated, it checks to see if the shared_shipping_zone
	 * option is set.  If it isn't, a new zone is created and the ID is saved to
	 * the option.
	 * The order is set to 100 so it's at the bottom of the list.
	 * The location is set to Antartica to prevent these shipping methods from being
	 * directly offered to customers in the cart.
	 *
	 * @throws Exception If the zone fails to save.
	 * @return void
	 */
	public function activate_shared_shipping_methods(): void {

		$shared_shipping_zone = get_option( $this->shared_shipping_zone_option );

		// If the shared shipping zone is set we don't need to create a new zone.
		if ( isset( $shared_shipping_zone ) ) {
			return;
		}

		try {
			$zone = new WC_Shipping_Zone();

			$zone->set_zone_name( 'Shared Shipping Methods' );
			$zone->set_zone_order( '100' );
			$zone->add_location( 'AQ', 'country' );
			$zone->save();

			$zone_id = $zone->get_id();
			add_option( $this->shared_shipping_zone_option, $zone_id, '', 'no' );

		} catch ( Exception $e ) {
			$logger = wc_get_logger();
			$logger->log(
				'error',
				'Failed to save shared_shipping_zone option for zone id: ' . $zone_id,
				array(
					'source' => 'Shared Shipping Methods',
				)
			);
		}

	}

}
