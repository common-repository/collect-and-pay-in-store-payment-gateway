<?php
/**
 * Plugin Name: Collect and Pay in Store - Payment Gateway
 * Plugin URI: https://bzn.gr/pay-in-store
 * Description: The Collect and Pay in Store plugin enables customers to pay for online orders in person at a physical store. It adds a payment method for those who prefer to pay upon pickup, with the ability to set payment limits and order processing times. Customers receive a notification email when their order is ready for pickup.
 * Version: 3.0.0
 * Author: bzn.gr
 * Author URI: https://bzn.gr
 * Requires at least: 5.0
 * Tested up to: 6.2
 * WC requires at least: 3.0
 * WC tested up to: 7.5
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: pay-in-store
 * Domain Path: /languages/
 */

 if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

require_once plugin_dir_path(__FILE__) . 'core.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-wc-gateway-pay-in-store-shortcode.php';
function pay_in_store_links($l){$l[]='<a href="'.admin_url('admin.php?page=collect-and-pay-in-store').'">'.__('Getting Started','pay-in-store').'</a>';return$l;}add_filter('plugin_action_links_'.plugin_basename(__FILE__),'pay_in_store_links');