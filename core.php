<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class PayInStore {
    public function __construct() {
        add_action('plugins_loaded', array($this, 'init'), 0);
        add_filter('woocommerce_email_classes', array($this, 'add_customer_ready_to_collect_email'));
        add_filter('wc_order_statuses', array($this, 'add_custom_order_status_ready_to_collect'));
        add_filter('woocommerce_order_actions', array($this, 'add_custom_order_action'));
        add_action('woocommerce_order_action_send_ready_to_collect_email', array($this, 'process_custom_order_action'));
        register_activation_hook(__FILE__, array($this, 'copy_email_templates_on_activation'));
        add_action('init', array($this, 'register_custom_order_status'));
        add_filter( 'woocommerce_order_is_completed_statuses', function( $statuses ) {
            $statuses[] = 'wc-order-collected';
            return $statuses;
        } );
        
    }
    
    public function init() {
        if (!class_exists('WC_Payment_Gateway')) {
            return;
        }

        require_once plugin_dir_path(__FILE__) . 'includes/class-pay-in-store-gateway.php';
        require_once plugin_dir_path(__FILE__) . 'includes/class-pay-in-store-dash.php';

        $pay_in_store_gateway = new PayInStoreGateway();
        new PayinStoreDash();
        $pay_in_store_gateway->init();
    }

    public function add_customer_ready_to_collect_email($email_classes) {
        $file_path = plugin_dir_path(__FILE__) . 'includes/class-wc-email-customer-ready-to-collect.php';

        if (file_exists($file_path)) {
            require_once $file_path;
            $email_classes['WC_Email_Customer_Ready_To_Collect'] = new WC_Email_Customer_Ready_To_Collect();
        } else {
            error_log('Pay in Store WooCommerce Payment Gateway: Unable to load email class file.');
        }

        return $email_classes;
    }

    public function add_custom_order_status_ready_to_collect($order_statuses) {
        if (is_array($order_statuses)) {
            $order_statuses['wc-ready-to-collect'] = esc_html(_x('Ready to Collect', 'Order status', 'pay-in-store'));
            $order_statuses['wc-order-collected'] = esc_html(_x('Order Collected from Store', 'Order status', 'pay-in-store'));
        } else {
            error_log(esc_html('Pay in Store WooCommerce Payment Gateway: Unable to add custom order status.'));
        }
    
        return $order_statuses;
    }
    
    
    // dnt
    public function add_custom_order_action($actions) {
        if (is_array($actions)) {
            $actions['send_ready_to_collect_email'] = esc_html__('Send Ready to Collect Email', 'pay-in-store');
        } else {
            error_log(esc_html('Pay in Store WooCommerce Payment Gateway: Unable to add custom order action.'));
        }
    
        return $actions;
    }
    
    // dnt
    public function process_custom_order_action($order) {
        $order->update_status('wc-ready-to-collect', esc_html__('Order is ready to collect', 'pay-in-store'));
        $order->update_status('wc-order-collected', esc_html__('Order Collected from Store', 'pay-in-store'));
    }
    // move templates to woocommerce
    public function copy_email_templates_on_activation() {
        $src = plugin_dir_path(__FILE__) . 'templates/emails';
        $dst = plugin_dir_path(__FILE__) . '../woocommerce/templates/emails';

        if (!file_exists($dst)) {
            mkdir($dst, 0755, true);
        }

        $this->copy_directory($src, $dst);
    }

    public function copy_directory($src, $dst) {
        $dir = opendir($src);
        @mkdir($dst);

        while (false !== ($file = readdir($dir))) {
            if (($file !== '.') && ($file !== '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->copy_directory($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }

        closedir($dir);
    }
    public function register_custom_order_status() {
        register_post_status('wc-ready-to-collect', array(
            'label'                     => esc_html__('Ready to Collect', 'pay-in-store'),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Ready to Collect <span class="count">(%s)</span>', 'Ready to Collect <span class="count">(%s)</span>', 'pay-in-store')
        ));
        register_post_status('wc-order-collected', array(
            'label'                     => esc_html__('Order Collected from Store', 'pay-in-store'),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop('Order Collected from Store <span class="count">(%s)</span>', 'Order Collected from Store <span class="count">(%s)</span>', 'pay-in-store'),
            'complete'                  => true, // Set order as complete
        ));      
    }
}

new PayInStore();