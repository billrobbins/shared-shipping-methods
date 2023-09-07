## Shared Shipping Methods for WooCommerce

This extension for WooCommerce allows you to reuse shipping methods from a specific zone in the other zones in your store.

### Installation

1. Clone this repository into the `wp-content/plugins` folder on your server.
   **OR**
2. Download the repository, zip the contents, and install as you would with any WordPress plugin by navigating to `Plugins > Add New`.

Once activated, the plugin creates a new shipping zone named "Shared Shipping Methods." The default location is set to Antartica to avoid unnecessary use as it's an unlikely shipping destination.

### Usage

Get started by adding the shipping methods you'd like to share.  Here's how.

1.  Navigate to WooCommerce > Settings > Shipping and select the "Shared Shipping Methods" zone.  
2. Add a new shipping method, ensuring you've filled all necessary settings.
3. Repeat these steps to share additional shipping methods.

### Applying Shared Methods to Other Zones

1. From the shipping zones screen, select an existing zone or create a new one.
2. Add a shipping method and choose "Share Shipping Methods" from the drop-down menu.
3. Hover over the newly added method and click edit to access the modal with specific settings for this instance of the shipping method.   
4. Select the desired shared method from the drop-down menu. 
5. Assign a title to your method. This title will be visible to customers during checkout.
6. Save your settings.

To specify the shared shipping methods zone, go to `WooCommerce > Settings > Shipping > Shipping Options`. The selected zone in this setting will be considered as the source of available shared shipping methods.