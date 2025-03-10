<?php

namespace Vrajroham\LaravelBitpay\Tests;

use BitPaySDK\Model\Payout\PayoutRecipient;
use PHPUnit\Framework\TestCase;
use Vrajroham\LaravelBitpay\LaravelBitpay;

class LaravelBitpayRecipientTest extends TestCase
{
    /** @test */
    public function isInstanceOfRecipient()
    {
        $this->assertTrue(LaravelBitpay::PayoutRecipient() instanceof PayoutRecipient);
    }
}
