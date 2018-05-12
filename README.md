# Pending Payments to Payment Complete for WooCommerce Stripe Payment Gateway

Wordpress Plugin that executes "payment complete" if SEPA direct debit is used and the payment is still pending, but you want to regard it as complete. Use this extension if you trust that the customer has provided correct payment information and that the payment usually succeeds. If it does not you should handle it manually and maybe charge the customer additional fees.

## Installation

The most convenient way is to use [GitHub Updater](https://github.com/afragen/github-updater):
1. Install [GitHub Updater](https://github.com/afragen/github-updater)
2. Go to Settings->GitHub Updater->Install Plugin and point it to `kmindi/wc-stripe-asynchronous-payments-pending-to-payment-complete`

## Problem description

The Stripe payment system handles various different payment methods. These methods can either be synchronous (like credit card payments) or asynchronous (like SEPA direct debit). If an asynchronous payment method is used for a WooCommerce order, a charge is created and the order is set to "on-hold" until the charge succeeds. For SEPA direct debit this can take 5-14 days! 

Now imagine you sell virtual products like memberships that require purchasing a subscription product which will renew automatically. Not only the first order but all orders, will be "on-hold" until the charge succeeds. This means a subscription will also be inactive during that time, even though the customer provided the payment information and does not expect a break in his subscription.

## How this extension solves the problem

1. They key part is that we hook into the action `wc_gateway_stripe_process_response` from the Stripe plugin.
2. In this action hook we execute `$order->payment_complete` if the response is using "sepa_debit" and the order is in "pending" state.
