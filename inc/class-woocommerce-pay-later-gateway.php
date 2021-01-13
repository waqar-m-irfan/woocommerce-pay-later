<?php

class WooCommercePayLaterGateway extends WC_Payment_Gateway
{
    /**
     * Class constructor, more about it in Step 3
     */
    public function __construct()
    {
        $this->id = 'paylater'; // payment gateway plugin ID
        $this->icon = ''; // URL of the icon that will be displayed on checkout page near your gateway name
        $this->has_fields = true; // in case you need a custom credit card form
        $this->method_title = 'Pay Later';
        $this->method_description = 'This method lets the customer pay for the order after certain amount of time specified by admin'; // will be displayed on the options page

        // gateways can support subscriptions, refunds, saved payment methods,
        // but in this tutorial we begin with simple payments
        $this->supports = array(
            'products'
        );

        // Method with all the options fields
        $this->init_form_fields();

        // Load the settings.
        $this->init_settings();
        $this->title = $this->get_option('title');
        $this->description = $this->get_option( 'description' );
        $this->enabled = $this->get_option('enabled');
//        $this->testmode = 'yes' === $this->get_option('testmode');
//        $this->private_key = $this->testmode ? $this->get_option('test_private_key') : $this->get_option('private_key');
//        $this->publishable_key = $this->testmode ? $this->get_option('test_publishable_key') : $this->get_option('publishable_key');

        // This action hook saves the settings
        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));

        // We need custom JavaScript to obtain a token
//        add_action('wp_enqueue_scripts', array($this, 'payment_scripts'));

         // You can also register a webhook here
//         add_action( 'woocommerce_api_{webhook name}', array( $this, 'webhook' ) );
    }

    /**
     * Plugin options, we deal with it in Step 3 too
     */
    public function init_form_fields()
    {
        $this->form_fields = apply_filters('woocommerce_pay_later_fields', array(
                'enabled' => array(
                    'title'       => 'Enable/Disable',
                    'label'       => 'Enable Pay Later Gateway',
                    'type'        => 'checkbox',
                    'description' => '',
                    'default'     => 'no'
                ),
                'title' => array(
                    'title'       => 'Title',
                    'type'        => 'text',
                    'description' => 'This controls the title which the user sees during checkout.',
                    'default'     => 'Pay Later',
                    'desc_tip'    => true,
                ),
                'description' => array(
                    'title'       => 'Description',
                    'type'        => 'textarea',
                    'description' => 'This controls the description which the user sees during checkout.',
                    'default'     => 'Pay in cycles via our super-cool payment gateway.',
                ),
        ));
    }

    /**
     * You will need it if you want your custom credit card form, Step 4 is about it
     */
//    public function payment_fields()
//    {
//    }

    /*
     * Custom CSS and JS, in most cases required only when you decided to go with a custom credit card form
     */
    public function payment_scripts()
    {
    }

    /*
      * Fields validation, more in Step 5
     */
    public function validate_fields()
    {
    }

    /*
     * We're processing the payments here, everything about it is in Step 5
     */
    public function process_payment($order_id)
    {
    }

    /*
     * In case you need a webhook, like PayPal IPN etc
     */
    public function webhook()
    {
    }
}
