<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 * Plugin Name: WooCommerce Stripe Gateway Asynchronous Payments to Payment Complete
 * GitHub Plugin URI: kmindi/wc-stripe-asynchronous-payments-pending-to-payment-complete
 * GitHub Plugin URI: https://github.com/kmindi/wc-stripe-asynchronous-payments-pending-to-payment-complete
 * Description: This plugin executes "payment complete" if sepa debit is used and the payment is still pending, but you want to regard it as complete.
 * Version: 0.1.1
 * Author: Kai Mindermann
 * Text Domain: wc_stripe_apptpc
 * License: GPL3
 */

/*
Copyright 2018 Kai Mindermann

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 3, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if(!class_exists('WC_Stripe_Asynchronous_Payments_Pending_to_Payment_Complete'))
{
    class WC_Stripe_Asynchronous_Payments_Pending_to_Payment_Complete
    {
        /**
         * Construct the plugin object
         */
        public function __construct()
        {
            // register actions
            add_action('init', array(&$this, 'init'));
        } // END public function __construct
        
        /**
         * Hook into WP's init action hook
         */
        public function init()
        {
          load_plugin_textdomain('wc_stripe_apptpc', false, basename( dirname( __FILE__ ) ) . '/languages');
          add_action( 'wc_gateway_stripe_process_response', array($this, 'action_wc_gateway_stripe_regard_sepa_pending_as_complete'), 0, 2);
        } // END public function init()

        /**
         * Action function that sets the order to "payment completed" even if the SEPA direct debit is pending at Stripe.
         * @author Kai Mindermann
         * @since 0.1.0
         */
        public function action_wc_gateway_stripe_regard_sepa_pending_as_complete ($response, $order) {
          // check if Subscriptions are enabled
          if ( class_exists( 'WC_Subscriptions_Order' ) ) {
            // check if payment (stripe response) is in pending state
            // check if payment (stripe response) is of 'sepa_debit' type
            // check if order contains a subscription
            if( $response->status === 'pending' && $response->source->type === 'sepa_debit' && function_exists( 'wcs_order_contains_subscription' ) && ( wcs_order_contains_subscription( $order->id ) || wcs_is_subscription( $order->id ) || wcs_order_contains_renewal( $order->id ) ) ) {
              $order->payment_complete($response->id);
              
              /* translators: response id */
              $order->add_order_note( sprintf( __( 'Pending Payment status of order automatically set as Payment Complete. The payment may still fail! (Charge ID: %s)', 'wc_stripe_apptpc' ), $response->id ) );
              if ( is_callable( array( $order, 'save' ) ) ) {
                $order->save();
              }
            }
            // TODO update post_meta?
            // TODO update fees?
          }
        }

        /**
         * Activate the plugin
         */
        public static function activate()
        {
            // Do nothing
        } // END public static function activate
    
        /**
         * Deactivate the plugin
         */     
        public static function deactivate()
        {
            // Do nothing
        } // END public static function deactivate
    } // END class WC_Stripe_Asynchronous_Payments_Pending_to_Payment_Complete
} // END if(!class_exists('WC_Stripe_Asynchronous_Payments_Pending_to_Payment_Complete'))

if(class_exists('WC_Stripe_Asynchronous_Payments_Pending_to_Payment_Complete'))
{
    // Installation and uninstallation hooks
    register_activation_hook(__FILE__, array('WC_Stripe_Asynchronous_Payments_Pending_to_Payment_Complete', 'activate'));
    register_deactivation_hook(__FILE__, array('WC_Stripe_Asynchronous_Payments_Pending_to_Payment_Complete', 'deactivate'));

    // instantiate the plugin class
    $main_instance = new WC_Stripe_Asynchronous_Payments_Pending_to_Payment_Complete();
}
