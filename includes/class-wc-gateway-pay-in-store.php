<?php

class WC_Gateway_PayInStore extends WC_Payment_Gateway
{

    public function __construct()
    {
        $this->id = 'collectandpayinstore';
        $this->icon = apply_filters('woocommerce_collectandpayinstore_icon', '');
        $this->method_title = esc_html__('Collect and Pay in Store', 'pay-in-store');
        $this->method_description = esc_html__('The feature enables customers to make payments, either in cash or through other acceptable means, upon picking up their purchased items in-store. This provides convenience and flexibility for both the customers and the business, allowing for a smoother transaction process.', 'pay-in-store');
        $this->has_fields = false;

        // Load the settings
        $this->init_form_fields();
        $this->init_settings();

        // Get settings
        $this->title = $this->get_option('title');
        $image_url = $this->get_option('image_url');
        if ($image_url) {
            $this->description = '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($this->description) . '" />' . $this->description = $this->get_option('description');
        } else {
            $this->description = $this->get_option('description');
        }
        $this->instructions = $this->get_option('instructions', $this->description);
        $this->enable_for_methods = $this->get_option('enable_for_methods', array());
        $this->enable_for_virtual = $this->get_option('enable_for_virtual', 'yes') === 'yes' ? true : false;

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));

        add_action('woocommerce_thankyou_collectandpayinstore', array($this, 'thankyou_page'));
        add_action('woocommerce_admin_field_schedule_table', array($this, 'generate_schedule_table_html'));
        add_action('woocommerce_update_options_payment_gateways_collectandpayinstore', array($this, 'save_custom_fields'));
        add_action('woocommerce_email_before_order_table', array($this, 'email_instructions'), 10, 3);
        // send email
        add_filter('woocommerce_order_actions', array($this, 'add_order_action'));
        add_action('woocommerce_order_action_send_ready_to_collect_email', array($this, 'process_order_action'));


    }

    /**
     * Initialise Gateway Settings Form Fields.
     */
    public function init_form_fields()
    {
        $shipping_methods = array();

        if (is_admin()) {
            foreach (WC()->shipping()->load_shipping_methods() as $method) {
                $shipping_methods[$method->id] = $method->get_method_title();
            }
        }

        $this->form_fields = array(
            'enabled' => array(
                'title' => esc_html__('Enable Collect and Pay in Store', 'pay-in-store'),
                'label' => esc_html__('Enable Collect and Pay in Store', 'pay-in-store'),
                'type' => 'checkbox',
                'description' => '',
                'default' => 'no',
            ),
            'title' => array(
                'title' => esc_html__('Title', 'pay-in-store'),
                'type' => 'text',
                'description' => esc_html__('Upon checkout, customers will see the available payment methods for their purchases. This may include options such as "Cash on Pickup" or "Pay in-store upon Pickup" for those who prefer to pay in cash or through other acceptable means at the time of picking up their items. By providing clear and concise descriptions of available payment options, customers can make informed decisions and have a hassle-free checkout experience..', 'pay-in-store'),
                'default' => esc_html__('Collect and Pay in Store', 'pay-in-store'),
                'desc_tip' => true,
                'class' => 'bzn-instructions',
            ),
            'description' => array(
                'title' => esc_html__('Description', 'pay-in-store'),
                'type' => 'textarea',
                'description' => esc_html__('On the website, customers will be able to view the available payment methods during the checkout process. This may include a description such as "Cash on Pickup" or "Pay in-store upon Pickup" for those who prefer to make their payment in person at the time of picking up their items. By providing clear and detailed descriptions of the available payment options, customers can choose the method that works best for them and complete their purchase with confidence.', 'pay-in-store'),
                'default' => esc_html__('Pay with cash or card upon pickup from store.', 'pay-in-store'),
                'desc_tip' => true,
                'class' => 'bzn-instructions',
            ),
            'image_url' => array(
                'title' => esc_html__('Description Image URL', 'pay-in-store'),
                'type' => 'url',
                'description' => esc_html__('If a custom image is available, it can be used to further illustrate the payment method being described and create a more visually engaging checkout process. Enter your image URL or leave blank to disable.'),
                'default' => '',
                'desc_tip' => true,
            ),
            'instructions' => array(
                'title' => esc_html__('Instructions', 'pay-in-store'),
                'type' => 'textarea',
                'description' => esc_html__('Upon completing their purchase, customers will be directed to a thank you page. Instructions can be added to this page to provide customers with any necessary information regarding their order and the next steps they should take. These instructions may include details on when and where to pick up their items, any additional documentation required, or contact information for customer support. By providing clear and concise instructions on the thank you page, customers can have a positive post-purchase experience and feel confident in their decision to shop with your business.                ', 'pay-in-store'),
                'default' => esc_html__('Pay with cash or card upon pickup from store.', 'pay-in-store'),
                'desc_tip' => true,
                'class' => 'bzn-instructions',
            ),
            'enable_for_methods' => array(
                'title' => esc_html__('Enable for shipping methods', 'pay-in-store'),
                'type' => 'multiselect',
                'class' => 'wc-enhanced-select',
                'css' => 'width: 450px;',
                'default' => '',
                'description' => esc_html__('To restrict the Collect and Pay in Store payment method to certain shipping methods, this can be configured within the payment settings. This allows for greater flexibility in payment options based on the specific shipping method selected by the customer. By setting this up, customers will only see the Collect and Pay in Store payment method as an option if they have selected a qualifying shipping method. If this feature is not required, it can be left blank to enable the payment method for all shipping methods.                ', 'pay-in-store'),
                'options' => $shipping_methods,
                'desc_tip' => true,

            ),
            'max_amount' => array(
                'title' => esc_html__('Maximum Amount', 'pay-in-store'),
                'type' => 'number',
                'description' => esc_html__('The maximum amount that can be paid in store will depend on the specific businesss policies and preferences. However, it is important to consider the practicality of handling cash transactions and ensure that the maximum amount is set at a reasonable level to avoid potential issues with cash handling and security. Additionally, any applicable laws and regulations should be taken into account when determining the maximum amount that can be paid in store.', 'pay-in-store'),
                'default' => '5000',
                'desc_tip' => true,
                'custom_attributes' => array(
                    'min' => '0',
                    // Minimum value is 0
                    'step' => '0.01',
                    // Allow decimal values
                ),
                'validate' => array(
                    'is_positive' => true,
                    // Value must be positive
                ),
                'class' => 'bzn-money-amount',
            ),

            'min_amount' => array(
                'title' => esc_html__('Minimum Amount', 'pay-in-store'),
                'type' => 'number',
                'description' => esc_html__('Similar to the maximum amount, the minimum amount that can be paid in store will depend on the specific businesss policies and preferences. However, it is important to ensure that the minimum amount is set at a reasonable level that aligns with the cost of goods sold and the costs associated with handling the transaction. If the minimum amount is too low, it may not be practical or cost-effective for the business to offer the Collect and Pay in Store payment method. Ultimately, the minimum amount should be set based on a careful analysis of the businesss financial and operational requirements.', 'pay-in-store'),
                'default' => '10',
                'desc_tip' => true,
                'custom_attributes' => array(
                    'min' => '0',
                    // Minimum value is 0
                    'step' => '0.01',
                    // Allow decimal values
                ),
                'validate' => array(
                    'is_positive' => true,
                    // Value must be positive
                ),
                'class' => 'bzn-money-amount',
            ),
            'enable_for_virtual' => array(
                'title' => esc_html__('Accept Virtual Orders', 'pay-in-store'),
                'description' => esc_html__('If the order is virtual and no physical items are being shipped, it may not be practical to offer the Collect and Pay in Store payment method. However, if the business is willing and able to accommodate this payment method for virtual orders, it can be enabled in the payment settings. It is important to clearly communicate to customers that they will need to arrange for pickup and payment in store if they choose this option for virtual orders. Additionally, it may be necessary to establish specific guidelines or restrictions around the use of this payment method for virtual orders to ensure a smooth and secure transaction process.', 'pay-in-store'),
                'type' => 'checkbox',
                'default' => 'yes'
            ),
            'order_processing_time' => array(
                'title' => esc_html__('Order Processing Time', 'pay-in-store'),
                'type' => 'text',
                'description' => esc_html__('The order processing time will depend on the specific business operational capabilities and the volume of orders being received. To provide customers with accurate information and set clear expectations, the order processing time should be communicated clearly on the website or checkout page. This may include a range of hours or days, such as "24-48 hours" or "3-5 business days," depending on the business processing timeframes. It is important to ensure that the order processing time is reasonable and achievable to avoid potential delays or customer dissatisfaction.', 'pay-in-store'),
                'default' => '',
                'class' => 'bzn-money-amount',
            ),
            'store_schedule' => array(
                'title' => esc_html__('Store Schedule', 'pay-in-store'),
                'type' => 'schedule_table',
            ),
            'store_address' => array(
                'title' => esc_html__('Store Address', 'pay-in-store'),
                'type' => 'text',
                'description' => esc_html__('The store address where customers can pick up their orders should be clearly communicated to customers to ensure a smooth and efficient pickup process. This may include the street address, city, and zip code, such as "123 Main Street, Anytown, USA 12345." Alternatively, if the business has a prominent or well-known location, the address may be shortened to just the street name and city for simplicity. In some cases, it may also be helpful to provide additional information, such as parking instructions or landmarks to assist customers in finding the pickup location. If preferred, GPS coordinates can also be provided, such as "40.748817,-73.985428."', 'pay-in-store'),
                'default' => '',
                'desc_tip' => true,
            ),
            'store_maps_enabled' => array(
                'title' => esc_html__('Enable Google Maps URL', 'pay-in-store'),
                'description' => esc_html__('Enabling a Google Maps URL can provide customers with an easy and convenient way to locate the pickup location. This can be accomplished by generating a shareable link to the business location on Google Maps and including it on the website or checkout page. This can be especially useful for customers who may be unfamiliar with the area or who are using a mobile device for navigation. By including a Google Maps URL, customers can quickly and easily access directions and get to the pickup location with minimal hassle.', 'pay-in-store'),
                'type' => 'checkbox',
                'label' => '',
                'default' => 'no'
            ),

        );

        $pages = get_pages();
        $page_options = array('' => esc_html__('Default', 'pay-in-store'));
        foreach ($pages as $page) {
            $page_options[$page->ID] = $page->post_title;
        }

        $this->form_fields['thank_you_page'] = array(
            'title' => esc_html__('Thank You Page', 'pay-in-store'),
            'type' => 'select',
            'description' => esc_html__('Select the page where customers should be redirected to after completing their order. Leave as "Default" to use the default WooCommerce thank you page.'),
            'default' => '',
            'options' => $page_options,
            'desc_tip' => true,
        );

    }
    public function generate_schedule_table_html()
    {
        $days = array(
            'monday' => esc_html__('Monday', 'woocommerce'),
            'tuesday' => esc_html__('Tuesday', 'woocommerce'),
            'wednesday' => esc_html__('Wednesday', 'woocommerce'),
            'thursday' => esc_html__('Thursday', 'woocommerce'),
            'friday' => esc_html__('Friday', 'woocommerce'),
            'saturday' => esc_html__('Saturday', 'woocommerce'),
            'sunday' => esc_html__('Sunday', 'woocommerce'),
        );

        ob_start();

        require_once plugin_dir_path(__FILE__) . 'template-pay-in-store-schedule.php';

        return ob_get_clean();
    }

    public function get_formatted_store_schedule()
    {
        $days = array(
            'monday' => esc_html__('Monday', 'woocommerce'),
            'tuesday' => esc_html__('Tuesday', 'woocommerce'),
            'wednesday' => esc_html__('Wednesday', 'woocommerce'),
            'thursday' => esc_html__('Thursday', 'woocommerce'),
            'friday' => esc_html__('Friday', 'woocommerce'),
            'saturday' => esc_html__('Saturday', 'woocommerce'),
            'sunday' => esc_html__('Sunday', 'woocommerce'),
        );

        $schedule = array();
        foreach ($days as $key => $label) {
            if ($this->get_option($key . '_open') === 'yes') {
                $hours = esc_attr($this->get_option($key . '_hours'));
                $schedule[] = esc_html($label) . ': ' . esc_html($hours);
            }
        }

        return implode('<br>', $schedule);
    }


    /**
     * Check If The Gateway Is Available For Use
     *
     * @return bool
     */
    public function is_available()
    {
        $order = null;
        $needs_shipping = false;

        if (WC()->cart && WC()->cart->needs_shipping()) {
            $needs_shipping = true;
        } elseif (is_page(wc_get_page_id('checkout')) && 0 < get_query_var('order-pay')) {
            $order_id = absint(get_query_var('order-pay'));
            $order = wc_get_order($order_id);
            // deprecated sizeof replaced with  count
            if (0 < count($order->get_items())) {
                foreach ($order->get_items() as $item) {
                    $_product = $item->get_product();
                    if ($_product && $_product->needs_shipping()) {
                        $needs_shipping = true;
                        break;
                    }
                }
            }
        }

        $needs_shipping = apply_filters('woocommerce_cart_needs_shipping', $needs_shipping);

        // Virtual order, with virtual disabled
        if (!$this->enable_for_virtual && !$needs_shipping) {
            return false;
        }

        if (!empty($this->enable_for_methods) && $needs_shipping) {
            $chosen_shipping_methods_session = WC()->session->get('chosen_shipping_methods');
            if (isset($chosen_shipping_methods_session[0])) {
                $chosen_shipping_methods = array($chosen_shipping_methods_session[0]);
            } else {
                $chosen_shipping_methods = array();
            }

            if (0 < count(array_intersect($chosen_shipping_methods, $this->enable_for_methods))) {
                return true;
            }

            return false;
        }

        return parent::is_available();
    }

    /**
     * Process the payment and return the result.
     *
     * @param int $order_id
     * @return array
     */
    public function process_payment($order_id)
    {
        $order = wc_get_order($order_id);
        // Retrieve the maximum amount option for the Collect and Pay in Store payment method
        $max_amount = $this->get_option('max_amount');

        // If the maximum amount option is set and the order total exceeds it, display an error message and do not process the order
        if ($max_amount && $order->get_total() > $max_amount) {
            $order->add_order_note(esc_html__('The order total exceeds the maximum amount for Collect and Pay in Store payment.', 'pay-in-store'));
            $message = get_option('pay_in_store_max_amount_message');
            wc_add_notice(sprintf($message, wc_price($max_amount)), 'error');
            return;
        }

        // Retrieve the minimum amount option for the Collect and Pay in Store payment method
        $min_amount = $this->get_option('min_amount');

        // If the minimum amount option is set and the order total falls below it, display an error message and do not process the order
        if ($min_amount && $order->get_total() < $min_amount) {
            $order->add_order_note(esc_html__('The order total falls below the minimum amount for Collect and Pay in Store payment.', 'pay-in-store'));
            $message = get_option('pay_in_store_min_amount_message');
            wc_add_notice(sprintf($message, wc_price($min_amount)), 'error');
            return;
        }

        // Mark as on-hold (we're awaiting the payment)
        $order->update_status('on-hold', esc_html__('Awaiting Collect and Pay in Store payment', 'pay-in-store'));

        // Reduce stock levels
        $order->reduce_order_stock();

        // Remove cart
        WC()->cart->empty_cart();

        // Redirect to thank you page
        $thank_you_page = $this->get_option('thank_you_page');
        if ($thank_you_page) {
            $redirect = get_permalink($thank_you_page);
        } else {
            $redirect = esc_url($this->get_return_url($order));
        }

        return array(
            'result' => 'success',
            'redirect' => $redirect,
        );
    }


    /**
     * Output for the order received page.
     */
    public function thankyou_page()
    {
        if ($this->instructions) {
            echo wpautop(wptexturize(esc_html($this->instructions)));
        }
        $order_processing_time = $this->get_option('order_processing_time');
        if (!empty($order_processing_time)) {
            $order_proc_time = get_option('pay_in_store_order_proc_time');

            echo '<h2>' . esc_html($order_proc_time) . '</h2>';

            echo wpautop(wptexturize(esc_html($order_processing_time)));
        }


        $store_address = $this->get_option('store_address');
        $store_open_hours = get_option('pay_in_store_store_opening_hours');
        echo '<h2>' . esc_html__($store_open_hours) . '</h2>';
        echo wpautop(wptexturize($this->get_formatted_store_schedule()));
        if ($this->get_option('store_maps_enabled') === 'yes') {
            $google_maps_url = "https://www.google.com/maps?q=" . urlencode(esc_url($store_address));
            $googlemapstext = get_option('pay_in_store_store_googlemaps');
            echo '<a href="' . esc_url($google_maps_url) . '" target="_blank" class="button">' . esc_html(__($googlemapstext)) . '</a>';
        } else {
            $locationtext = get_option('pay_in_store_store_location');
            echo '<p>' . esc_html__($locationtext) . esc_html($store_address) . '</p>';
        }

    }

    /**
     * Add content to the WC emails.
     *
     * @param WC_Order $order
     * @param bool $sent_to_admin
     * @param bool $plain_text
     */
    public function email_instructions($order, $sent_to_admin, $plain_text = false)
    {
        if ($this->instructions && !$sent_to_admin && $this->id === $order->get_payment_method() && $order->has_status('on-hold')) {
            echo wpautop(wptexturize($this->instructions)) . PHP_EOL;

            $order_processing_time = $this->get_option('order_processing_time');
            if (!empty($order_processing_time)) {
                $order_proc_time = get_option('pay_in_store_order_proc_time');
                echo '<h2>' . esc_html($order_proc_time) . '</h2>';
                echo wpautop(wptexturize($order_processing_time)) . PHP_EOL;
            }
            $store_open_hours = get_option('pay_in_store_store_opening_hours');
            echo '<h2>' . esc_html__($store_open_hours) . '</h2>';
            echo wpautop(wptexturize($this->get_formatted_store_schedule()));
            $store_address = $this->get_option('store_address');
            if ($this->get_option('store_maps_enabled') === 'yes') {
                $google_maps_url = "https://www.google.com/maps?q=" . urlencode($store_address);
                $googlemapstext = get_option('pay_in_store_store_googlemaps');
                echo '<a href="' . esc_url($google_maps_url) . '" target="_blank" class="button">' . esc_html(__($googlemapstext)) . '</a>';
            } else {
                $locationtext = get_option('pay_in_store_store_location');
                echo '<p>' . esc_html__($locationtext) . esc_html($store_address) . '</p>';
            }
        }
    }




    public function process_admin_options()
    {
        parent::process_admin_options();
        $max_amount = isset($_POST[$this->plugin_id . $this->id . '_max_amount']) ? wc_clean($_POST[$this->plugin_id . $this->id . '_max_amount']) : '';
        update_option($this->plugin_id . $this->id . '_max_amount', $max_amount);
        $min_amount = isset($_POST[$this->plugin_id . $this->id . '_min_amount']) ? wc_clean($_POST[$this->plugin_id . $this->id . '_min_amount']) : '';
        update_option($this->plugin_id . $this->id . '_min_amount', $min_amount);
    }
    public function save_custom_fields()
    {
        $days = array(
            'monday',
            'tuesday',
            'wednesday',
            'thursday',
            'friday',
            'saturday',
            'sunday',
        );

        foreach ($days as $day) {
            $this->settings[$day . '_open'] = isset($_POST[$this->plugin_id . $this->id . '_settings'][$day . '_open']) ? 'yes' : 'no';
            $this->settings[$day . '_hours'] = isset($_POST[$this->plugin_id . $this->id . '_settings'][$day . '_hours']) ? sanitize_text_field($_POST[$this->plugin_id . $this->id . '_settings'][$day . '_hours']) : '';
        }
        update_option($this->plugin_id . $this->id . '_settings', $this->settings);
    }
    public function send_ready_to_collect_email($order_id)
    {
        $order = wc_get_order($order_id);
        $mailer = WC()->mailer();
        $email = $mailer->emails['WC_Email_Customer_Ready_To_Collect'];
        $email->trigger($order_id);
    }
    public function add_order_action($actions)
    {
        $actions['send_ready_to_collect_email'] = esc_html__('Send Ready to Collect Email', 'pay-in-store');
        return $actions;
    }

    public function process_order_action($order)
    {
        $this->send_ready_to_collect_email($order->get_id());
    }

}