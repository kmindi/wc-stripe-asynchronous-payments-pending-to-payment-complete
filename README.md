# Pending Payments to Payment Complete for WooCommerce Stripe Payment Gateway

Wordpress Plugin that executes "payment complete" if SEPA direct debit is used and the payment is still pending, but you want to regard it as complete.

## Installation

The most convenient way is to use [GitHub Updater](https://github.com/afragen/github-updater):
1. Install [GitHub Updater](https://github.com/afragen/github-updater)
2. Go to Settings->GitHub Updater->Install Plugin and point it to `kmindi/wc-stripe-asynchronous-payments-pending-to-payment-complete`

## How it works

1. They key part is that we hook into the action `wc_gateway_stripe_process_response` from the Stripe plugin.
2. In this action hook we execute `$order->payment_complete` if the response is using "sepa_debit" and the order is in "pending" state.
