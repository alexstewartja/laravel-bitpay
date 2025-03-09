<?php

namespace Vrajroham\LaravelBitpay\Constants;

interface WebhookAutoPopulate
{
    const INVOICES   = 'invoices';
    const REFUNDS    = 'refunds';
    const RECIPIENTS = 'recipients';
    const PAYOUTS    = 'payouts';
}
