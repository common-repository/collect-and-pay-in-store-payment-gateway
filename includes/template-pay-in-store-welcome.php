<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<div class="bzn-dash-pay-instore">
    <h1>
        <?php esc_html_e('Welcome to Collect and Pay in Store - Payment Gateway', 'pay-in-store'); ?>
    </h1>
    <p>
        <?php esc_html_e("Here's where you can manage all the settings and features of your plugin.", 'pay-in-store'); ?>
    </p>
    <div class="container">
        <div class="box">
            <img class="icon" src="<?php echo esc_url('https://cdn-icons-png.flaticon.com/64/10080/10080427.png'); ?>"
                alt="<?php echo esc_attr__('Home', 'pay-in-store'); ?>">
            <h3>
                <?php echo esc_html__('Set Up', 'pay-in-store'); ?>
            </h3>
            <p>
                <?php echo esc_html__('It is possible to configure Pay in Store within the WooCommerce Payments feature. Within this module, all requisite fields can be found.', 'pay-in-store'); ?>
            </p>
            <a
                href="<?php echo esc_url(admin_url('admin.php?page=wc-settings&tab=checkout&section=collectandpayinstore')); ?>"><?php echo esc_html__('Set Up Payment', 'pay-in-store'); ?></a>
            <a
                href="<?php echo esc_url(admin_url('admin.php?page=wc-settings&tab=email&section=wc_email_customer_ready_to_collect')); ?>"><?php echo esc_html__('Modify Email Template', 'pay-in-store'); ?></a>
        </div>

        <div class="box">
            <img class="icon" src="<?php echo esc_url('https://cdn-icons-png.flaticon.com/64/10079/10079799.png'); ?>"
                alt="<?php echo esc_attr('Home'); ?>">
            <h3>
                <?php esc_html_e('Shortcodes', 'pay-in-store'); ?>
            </h3>
            <p>
                <?php esc_html_e('The Collect and Pay in Store Payment Gateway includes three shortcodes that can be utilized for customization', 'pay-in-store'); ?>
            </p>
            <pre
                class="code-snippet"><?php esc_html_e('[order_processing_time] ', 'pay-in-store'); ?><br><?php esc_html_e('[store_maps_location]', 'pay-in-store'); ?><br> <?php esc_html_e('[store_opening_hours]', 'pay-in-store'); ?> </pre>
        </div>

        <div class="box">
            <img class="icon" src="<?php echo esc_url('https://cdn-icons-png.flaticon.com/64/10080/10080175.png'); ?>"
                alt="<?php echo esc_attr__('Home', 'pay-in-store'); ?>">
            <h3>
                <?php echo esc_html__('Support', 'pay-in-store'); ?>
            </h3>
            <p>
                <?php echo esc_html__('We are here to help you thrive! Let us know if you have any problems or any extra feature you would like to see.', 'pay-in-store'); ?>
            </p>
            <a href="<?php echo esc_url('https://bzn.gr'); ?>" target="_blank"><?php echo esc_html__('Visit our Website', 'pay-in-store'); ?></a>
            <a href="<?php echo esc_url('https://bzn.gr/contact/'); ?>" target="_blank"><?php echo esc_html__('Contact Us', 'pay-in-store'); ?></a>
            <a href="<?php echo esc_url('https://www.buymeacoffee.com/bzngr'); ?>" target="_blank"><?php echo esc_html__('Buy me a beer', 'pay-in-store'); ?></a>
        </div>


    </div>
    <h3>
        <?php echo esc_html('Edit Texts'); ?>
    </h3>
    <div class="bzn-form-payin">
        <form method="post" action="options.php">
            <?php settings_fields('collect-and-pay-in-store'); ?>
            <?php do_settings_sections('collect-and-pay-in-store'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">
                        <?php _e('Minimum Amount Message', 'pay-in-store'); ?>
                    </th>
                    <td><input type="text" name="pay_in_store_min_amount_message"
                            value="<?php echo esc_attr(get_option('pay_in_store_min_amount_message')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <?php _e('Maximum Amount Message', 'pay-in-store'); ?>
                    </th>
                    <td><input type="text" name="pay_in_store_max_amount_message"
                            value="<?php echo esc_attr(get_option('pay_in_store_max_amount_message')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <?php _e('Processing Message', 'pay-in-store'); ?>
                    </th>
                    <td><input type="text" name="pay_in_store_order_proc_time"
                            value="<?php echo esc_attr(get_option('pay_in_store_order_proc_time')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <?php _e('Open Hours Message', 'pay-in-store'); ?>
                    </th>
                    <td><input type="text" name="pay_in_store_store_opening_hours"
                            value="<?php echo esc_attr(get_option('pay_in_store_store_opening_hours')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <?php _e('Google Message', 'pay-in-store'); ?>
                    </th>
                    <td><input type="text" name="pay_in_store_store_googlemaps"
                            value="<?php echo esc_attr(get_option('pay_in_store_store_googlemaps')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <?php _e('Location Message', 'pay-in-store'); ?>
                    </th>
                    <td><input type="text" name="pay_in_store_store_location"
                            value="<?php echo esc_attr(get_option('pay_in_store_store_location')); ?>" /></td>
                </tr>
            </table>
            <div class="bzn-form-pay-button">
                <?php submit_button(); ?>
            </div>
        </form>
    </div>
    
    <h3>
        <?php echo esc_html('Some of our features'); ?>
    </h3>
    <p>
        &bull;
        <?php echo esc_html('Allows customers to pay in store using cash or other means upon pickup.', 'pay-in-store'); ?>
        <br>
        &bull;
        <?php echo esc_html('Customizable payment method title and description visible to customers during checkout.', 'pay-in-store'); ?><br>
        &bull;
        <?php echo esc_html('Ability to display a custom image alongside the payment method description.', 'pay-in-store'); ?><br>
        &bull;
        <?php echo esc_html('Option to set minimum and maximum order amounts for this payment method.', 'pay-in-store'); ?><br>
        &bull;
        <?php echo esc_html('Option to enable or disable the payment method for virtual orders.', 'pay-in-store'); ?><br>
        &bull;
        <?php echo esc_html('Option to limit the payment method to specific shipping methods.', 'pay-in-store'); ?><br>
        &bull;
        <?php echo esc_html('Schedule table for displaying store hours for customers.', 'pay-in-store'); ?><br>
        &bull;
        <?php echo esc_html('Option to enable a Google Maps URL for displaying the store address.', 'pay-in-store'); ?><br>
        &bull;
        <?php echo esc_html('Redirect customers to a custom thank you page after completing their order.', 'pay-in-store'); ?><br>
        &bull;
        <?php echo esc_html('Option to set the order processing time.', 'pay-in-store'); ?><br>
        &bull;
        <?php echo esc_html('Customizable email instructions for customers after completing their order.', 'pay-in-store'); ?><br>
        &bull;
        <?php echo esc_html('Order action for sending a "ready to collect" email to customers.', 'pay-in-store'); ?><br>
        &bull;
        <?php echo esc_html('Admin settings for customizing the plugin\'s features.', 'pay-in-store'); ?><br>
    </p>
</div>