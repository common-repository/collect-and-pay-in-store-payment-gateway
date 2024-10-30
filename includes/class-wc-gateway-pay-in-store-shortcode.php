<?php
class BznShortcodePayIn {
    public function __construct() {
        add_shortcode('order_processing_time', array($this, 'order_processing_time_shortcode'));
        add_shortcode('store_opening_hours', array($this, 'store_opening_hours_shortcode'));
        add_shortcode('store_maps_location', array($this, 'store_maps_location_shortcode'));
    }

    // Shortcode for Order Processing Time
    public function order_processing_time_shortcode() {
        $gateway = new WC_Gateway_PayInStore();
        $order_processing_time = $gateway->get_option('order_processing_time');
        return esc_html($order_processing_time);
    }

    // Shortcode for Store Opening Hours
    public function store_opening_hours_shortcode() {
        $gateway = new WC_Gateway_PayInStore();
        $store_schedule = $gateway->get_formatted_store_schedule();
        return wp_kses_post($store_schedule);
    }

    // Shortcode for Store Maps Location
    public function store_maps_location_shortcode() {
        $gateway = new WC_Gateway_PayInStore();
        $store_address = $gateway->get_option('store_address');
        $store_maps_enabled = $gateway->get_option('store_maps_enabled');
        if ($store_maps_enabled === 'yes') {
            $google_maps_url = "https://www.google.com/maps?q=" . urlencode($store_address);
            return '<a href="' . esc_url($google_maps_url) . '" target="_blank">' . esc_html($store_address) . '</a>';
        } else {
            return esc_html($store_address);
        }
    }
}
new BznShortcodePayIn();

