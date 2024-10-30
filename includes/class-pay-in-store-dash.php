<?php 
class PayinStoreDash {

    public function __construct() {
        add_action('admin_menu', array($this, 'add_submenu_page'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function add_submenu_page() {
        add_submenu_page(
            'woocommerce',
            'Collect and Pay in Store',
            'Collect and Pay in Store',
            'manage_options',
            'collect-and-pay-in-store',
            array($this, 'submenu_page_callback')
        );
    }
    public function register_settings() {
        register_setting('collect-and-pay-in-store', 'pay_in_store_min_amount_message', array(
            'type' => 'string',
            'default' => __('The order total falls below the minimum amount for Pay in Store payment. Please ensure your order meets the minimum of %s.', 'pay-in-store'),
            'sanitize_callback' => 'sanitize_text_field',
        ));

        register_setting('collect-and-pay-in-store', 'pay_in_store_max_amount_message', array(
            'type' => 'string',
            'default' => __('The order total exceeds the maximum amount for Pay in Store payment. Please ensure your order does not exceed %s.', 'pay-in-store'),
            'sanitize_callback' => 'sanitize_text_field',
        ));

        register_setting('collect-and-pay-in-store', 'pay_in_store_order_proc_time', array(
            'type' => 'string',
            'default' => __('Order Processing Time', 'pay-in-store'),
            'sanitize_callback' => 'sanitize_text_field',
        ));

        register_setting('collect-and-pay-in-store', 'pay_in_store_store_opening_hours', array(
            'type' => 'string',
            'default' => __('Store Opening Hours', 'pay-in-store'),
            'sanitize_callback' => 'sanitize_text_field',
        ));

        register_setting('collect-and-pay-in-store', 'pay_in_store_store_googlemaps', array(
            'type' => 'string',
            'default' => __('Find Store in Google Maps', 'pay-in-store'),
            'sanitize_callback' => 'sanitize_text_field',
        ));

        register_setting('collect-and-pay-in-store', 'pay_in_store_store_location', array(
            'type' => 'string',
            'default' => __('You can pick your order from this location', 'pay-in-store'),
            'sanitize_callback' => 'sanitize_text_field',
        ));

    }
    
    public function submenu_page_callback() {
        require_once plugin_dir_path(__FILE__) . 'template-pay-in-store-welcome.php';
    }
    public function enqueue_styles() {
        wp_enqueue_style('my-plugin-style', plugin_dir_url(__FILE__) . '../assets/admin.css');
    }
}