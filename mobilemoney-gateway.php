<?php

/*
 * Plugin Name: WooCommerce Mobile Money Payment Gateway
 * Plugin URI: http://falicrea.com
 * Description: Take mobile transaction payments on your store.
 * Author: Tiafeno Finel
 * Author URI: http://falicrea.com
 * Version: 0.0.1
 */
defined('ABSPATH') || exit;
include_once('admin/class.mobilemoney_visa_transaction.php');

// @reference https://rudrastyh.com/woocommerce/payment-gateway-plugin.html#gateway_plugin
/*
 * This action hook registers our PHP class as a WooCommerce payment gateway
 */
add_filter('woocommerce_payment_gateways', 'mobilemoney_add_gateway_class');
function mobilemoney_add_gateway_class ($gateways)
{
    $gateways[] = 'WC_Mobilemoney_Gateway'; // Class name
    return $gateways;
}

/*
 * The class itself, please note that it is inside plugins_loaded action hook
 */
add_action('plugins_loaded', 'mobilemoney_init_gateway_class');
function mobilemoney_init_gateway_class ()
{
    if (!class_exists('WC_Payment_Gateway')) return;

    class WC_Mobilemoney_Gateway extends WC_Payment_Gateway
    {

        /**
         * Class constructor, more about it in Step 3
         */
        public function __construct ()
        {

            $this->id = 'mobilemoney'; // payment gateway plugin ID
            $this->icon = ''; // URL of the icon that will be displayed on checkout page near your gateway name
            $this->has_fields = true; // in case you need a custom credit card form
            $this->method_title = 'Mobile Money Gateway';
            $this->method_description = 'Description of mobile money payment gateway'; // will be displayed on the options page

            // gateways can support subscriptions, refunds, saved payment methods,
            // but in this tutorial we begin with simple payments
            $this->supports = [
                'products'
            ];

            // Method with all the options fields
            $this->init_form_fields();

            // Load the settings.
            $this->init_settings();
            $this->title = $this->get_option('title');
            $this->description = $this->get_option('description');
            $this->enabled = $this->get_option('enabled');
            $this->testmode = 'yes' === $this->get_option('testmode');
            $this->private_key = $this->testmode ? $this->get_option('test_private_key') : $this->get_option('private_key');
            $this->publishable_key = $this->testmode ? $this->get_option('test_publishable_key') : $this->get_option('publishable_key');

            // This action hook saves the settings
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, [$this, 'process_admin_options']);

            // We need custom JavaScript to obtain a token
            add_action('wp_enqueue_scripts', [$this, 'payment_scripts']);

            // You can also register a webhook here
            //add_action( 'woocommerce_api_{webhook name}', array( $this, 'webhook' ) );

        }

        /**
         * Plugin options, we deal with it in Step 3 too
         */
        public function init_form_fields ()
        {

            $this->form_fields = [
                'enabled' => [
                    'title' => 'Enable/Disable',
                    'label' => 'Enable Mobile Money Gateway',
                    'type' => 'checkbox',
                    'description' => '',
                    'default' => 'no'
                ],
                'title' => [
                    'title' => 'Title',
                    'type' => 'text',
                    'description' => 'This controls the title which the user sees during checkout.',
                    'default' => 'Mobile Money with transaction',
                    'desc_tip' => true,
                ],
                'description' => [
                    'title' => 'Description',
                    'type' => 'textarea',
                    'description' => 'This controls the description which the user sees during checkout.',
                    'default' => 'Pay with your mobile money via our super-cool payment gateway.',
                ],
                'testmode' => [
                    'title' => 'Test mode',
                    'label' => 'Enable Test Mode',
                    'type' => 'checkbox',
                    'description' => 'Place the payment gateway in test mode using test API keys.',
                    'default' => 'yes',
                    'desc_tip' => true,
                ],
                'test_publishable_key' => [
                    'title' => 'Test Publishable Key',
                    'type' => 'text'
                ],
                'test_private_key' => [
                    'title' => 'Test Private Key',
                    'type' => 'password',
                ],
                'publishable_key' => [
                    'title' => 'Live Publishable Key',
                    'type' => 'text'
                ],
                'private_key' => [
                    'title' => 'Live Private Key',
                    'type' => 'password'
                ]
            ];

        }

        /**
         * You will need it if you want your custom credit card form, Step 4 is about it
         * args: mm_transaction, mm_date
         */
        public function payment_fields ()
        {

            // ok, let's display some description before the payment form
            if ($this->description) {
                // you can instructions for test mode, I mean test card numbers etc.
                if ($this->testmode) {
                    $this->description .= ' TEST MODE ENABLED. In test mode, you can use the card numbers listed in 
 <span href="#" id="paiement_visa" style="color: red; cursor: pointer;" >Payer via APPROCARTE ORANGE</span>.';
                    $this->description = trim($this->description);
                }
                // display the description with <p> tags etc.
                echo wpautop(wp_kses_post($this->description));
            }

            // I will echo() the form, but you can close PHP tags and print it directly in HTML
            echo '<fieldset id="wc-' . esc_attr($this->id) . '-cc-form" class="wc-credit-card-form wc-payment-form" style="background:transparent;">';

            // Add this action hook if you want your custom payment gateway to support it
            do_action('woocommerce_credit_card_form_start', $this->id);
            echo '<div class="form-row form-row-wide">
                    <label>ID de transaction <span class="required">*</span></label>
                    <input name="mm_transaction" id="mm_transaction" type="text" autocomplete="off" required>
                    <span class="badge">Format: 00000000.0000.000000</span>
                </div>
                <div class="form-row form-row-first">
                    <label>Date d\'envoye <span class="required">*</span></label>
                    <input name="mm_date" id="mm_date" type="text" autocomplete="off" placeholder="MM / YY" required>
                </div>
                <div class="form-row form-row-last">
                    <!--<label>ID de transaction <span class="required">*</span></label>
                    <input id="mm_transaction" type="password" autocomplete="off" placeholder="Transaction ID">-->
                </div>
                <div class="clear"></div>';
            do_action('woocommerce_credit_card_form_end', $this->id);

            echo '<div class="clear"></div></fieldset>';
        }

        /*
         * Custom CSS and JS, in most cases required only when you decided to go with a custom credit card form
         */
        public function payment_scripts ()
        {
            // and this is our custom JS in your plugin directory that works with token.js
            wp_register_script('woocommerce_mm', plugins_url('mobilemoney.js', __FILE__), ['jquery'], '1.0.1');
            // in most payment processors you have to use PUBLIC KEY to obtain a token
            wp_localize_script('woocommerce_mm', 'mm_params', [
                'publishableKey' => $this->publishable_key
            ]);

            wp_enqueue_script('woocommerce_mm');

        }

        /*
          * Fields validation, more in Step 5
         */
        public function validate_fields ()
        {

            if (empty($_POST['mm_transaction'])) {
                wc_add_notice('Transaction is required!', 'error');
                return false;
            } else {
                $transaction = $_POST['mm_transaction'];
                $response = preg_match("/([A-Z0-9]\w{7,}\.[0-9]{4}\.[A-Z0-9]{6})/", trim($transaction));
                if (!$response):
                    wc_add_notice('Transaction format invalid', 'error');
                    return;
                endif;
            }
            return true;

        }


        /*
         * We're processing the payments here, everything about it is in Step 5
         */
        public function process_payment ($order_id)
        {

            global $woocommerce, $wpdb;
            // we need it to get any order detailes
            $order = wc_get_order($order_id);

            if (!isset($_POST['mm_transaction']) || empty($_POST['mm_transaction'])) {
                wc_add_notice('Transaction input is required.', 'error');
                return;
            }

            $args = [
                'key' => '_id_transaction',
                'value' => esc_sql(trim($_POST['mm_transaction'])),
                'compare' => '='
            ];

            // Find transaction field
            $meta_query_args = ['relation' => 'OR', $args];
            $meta_query = new WP_Meta_Query($meta_query_args);

            /*
             * Your API interaction could be built with wp_remote_post()
             * @url https://developer.wordpress.org/reference/functions/wp_remote_post/
              */
            $response = wp_remote_post('http://api.falicrea.net', $args);
            if (!is_wp_error($response)) {
                $body = json_decode($response['body'], true);
                // it could be different depending on your payment processor
                if ($body['response']['responseCode'] == 'APPROVED') {
                    // we received the payment
                    $order->payment_complete();
                    $order->reduce_order_stock();
                    // some notes to customer (replace true with false to make it private)
                    $order->add_order_note('Hey, your order is paid! Thank you!', true);
                    // Empty cart
                    $woocommerce->cart->empty_cart();
                    // Redirect to the thank you page
                    return [
                        'result' => 'success',
                        'redirect' => $this->get_return_url($order)
                    ];
                } else {
                    wc_add_notice('Please try again.', 'error');
                    return;
                }
            } else {
                wc_add_notice('Connection error.', 'error');
                return;
            }
        }

        /*
         * In case you need a webhook, like PayPal IPN etc
         */
        public function webhook ()
        {


        }
    }
}

