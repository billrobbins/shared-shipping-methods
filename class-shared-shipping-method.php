<?php

/**
 * Shared Shipping Methods.
 *
 * This class adds a new shipping method that allows you to select an existing shipping method from another zone.
 */
class Shared_Shipping_Method extends WC_Shipping_Method {

	protected $selected_zone;

	/**
	 *
	 */
	public function __construct( $instance_id = 0 ) {
		$this->instance_id        = absint( $instance_id );
		$this->id                 = 'shared_shipping_method';
		$this->method_title       = __( 'Shared Shipping Methods', 'woocommerce' );
		$this->method_description = __( 'Use an existing shipping method from another zone.', 'woocommerce' );
		$this->title              = 'Shared Shipping Method';
		$this->supports           = array(
			'shipping-zones',
			'instance-settings',
			'instance-settings-modal',
		);
		$this->init();
	}

	/**
	 * Initializes the settings for this shared shipping method instance.
	 */
	public function init() {
		$this->init_form_fields();
		$this->init_settings();

		$this->title    = $this->get_option( 'title' );
		$this->requires = $this->get_option( 'selected_shared_method' );

		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	/**
	 * Initialize form fields for this shipping method.  If the shared shipping zone has been deleted
	 * we delete the existing option and return.
	 */
	public function init_form_fields() {

		$shared_shipping_zone = get_option( 'shared_shipping_zone' );

		if ( ! $shared_shipping_zone ) {
			return;
		}

		try {
			$selected_zone = new WC_Shipping_Zone( $shared_shipping_zone );
		} catch ( Exception $e ) {
			delete_option( 'shared_shipping_zone' );
			return;
		}

		$methods       = $selected_zone->get_shipping_methods();
		$other_methods = array();

		foreach ( $methods as $method ) {
			$other_methods[ $method->id . ':' . $method->instance_id ] = $method->title;
		}

		$this->instance_form_fields = array(
			'title'                  => array(
				'title'       => __( 'Title', 'woocommerce' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
				'default'     => $this->method_title,
				'desc_tip'    => true,
			),
			'selected_shared_method' => array(
				'title'   => __( 'Select other method', 'woocommerce' ),
				'type'    => 'select',
				'class'   => 'wc-enhanced-select',
				'default' => '',
				'options' => $other_methods,
			),
		);
	}


	/**
	 * Loads this shipping method's name/options on the edit zone screen.
	 */
	public function get_instance_form_fields() {
		return parent::get_instance_form_fields();
	}

	/**
	 * WooCommerce calls this method in the cart to determine the costs for a "package."
	 * We're loading the original shipping method here and using its cost.
	 */
	public function calculate_shipping( $package = array() ) {

		$selected_shared_method = $this->get_option( 'selected_shared_method' );

		$shipping_methods = WC()->shipping()->get_shipping_methods();

		foreach ( $shipping_methods as $shipping_method ) {
			$parts = explode( ':', $selected_shared_method );

			$shipping_method_id = $parts[0];
			$method_instance_id = $parts[1];

			if ( $shipping_method->id === $shipping_method_id ) {
				$class_name = get_class( $shipping_method );
				$instance   = new $class_name( $method_instance_id );
				break;
			}
		}

		if ( ! isset( $instance ) ) {
			return;
		}

		$rate = array(
			'id'      => $this->get_rate_id(),
			'label'   => $this->title,
			'cost'    => $instance->cost,
			'package' => $package,
		);

		$this->add_rate( $rate );
	}
}