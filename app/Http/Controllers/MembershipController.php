<?php

namespace App\Http\Controllers;

use App\Models\Channel;

class MembershipController extends Controller
{
    public function subscribeToChannelMembership(Channel $channel)
    {
        // I need a PaymentIntent ID
        // and a Stripe Customer User Id thing
        // USer is required, no guest.
        // successful payment, confirmation page is displayed,
        // verify sub is active
        // grant user access to prods
        // every time a payment is due, invoice and PaymentIntent are generated
        // paymentIntent ID attached to the invoice and we can access from invoice and subscription objects
        // PaymentIntent has state
        // paymentIntent status succeeded, invoiceStatus paid, substatus - active
        // paymentIntent status requires_payment_method, invoice status open, sub sctatus incomplete
        // paymentIntent status requires_action, invoice status open, sub status incomplete
        // stripe handles recurring charges.
        // automatically invoicing customers and attempting payments when billing cycle starts.
        // smart retries if failure.
        // enable email reinders for overdue payments.
        // or build our own. if payment fails the sub status is set to past_due and PaymentIntent status is requires_payment_method or requires_action
        // can setup a webhook endpoint and listen for past_due
        // payment form is handled by stripe
        // paymentMethod is saved to the customer and set as default method. Required!
        //
    }
}
