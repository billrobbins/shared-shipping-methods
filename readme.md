## Shared Shipping Methods for WooCommerce

This extension for WooCommerce allows you to reuse shipping methods from a specific zone in the other zones in your store.

### Installation

You can clone this repo into the `wp-content/plugins` folder on your server or download the repo and ZIP the contents.  Then install it as you would any WordPress plugin by going to Plugins > Add New.  

When the plugin is activated, it will create a new shipping zone called "Shared Shipping Methods" where you can add the methods you'd like to share.  The location is set to Antartica by default since it's not a common shipping destination which means it's unlikely these methods would be used where they aren't intended.

### Useage

Get started by adding the shipping methods you'd like to share.  Here's how.

1.  Go to WooCommerce > Settings > Shipping and click on the "Shared Shipping Methods" zone.  
2.  Add a new shipping method here.  Be sure to fill in all settings you'll need.
3.  Repeat this for any other shipping methods you'd like to be able to share.

Now you can add these to other shipping zones.  Here's what you'll need to do.

1.  In the shipping zones screen, click on an existing zone you'd like to use or create a new one.
2.  Add a new shipping method and select "Share Shipping Methods" from the drop-down.
3.  Now hover over the new shipping method you selected, and click edit.  This will open up a modal with the settings for this specific instance of the shipping method.  
4.  Select the shared method you want to use from the drop-down.  
5.  Give your method a title.  This will be visible to customers at checkout and in the cart.
6.  Save your settings.

There is also a setting in WooCommerce > Settings > Shipping > Shipping Options that can be used to specify the shared shipping methods zone.  The selected zone here will be the source of the available shared shipping methods.