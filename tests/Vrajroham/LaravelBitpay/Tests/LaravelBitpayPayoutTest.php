<?php

namespace Vrajroham\LaravelBitpay\Tests;

use BitPaySDK\Model\Payout\Payout;
use BitPaySDK\Model\Payout\PayoutGroup;
use BitPaySDK\Model\Payout\RecipientReferenceMethod;
use PHPUnit\Framework\TestCase;
use Vrajroham\LaravelBitpay\LaravelBitpay;


class LaravelBitpayPayoutTest extends TestCase
{
    /** @test */
    public function isInstanceOfPayout()
    {
        $this->assertTrue(LaravelBitpay::Payout() instanceof Payout);
    }

    /** @test */
    public function isInstanceOfPayoutGroup()
    {
        $this->assertTrue(LaravelBitpay::PayoutGroup([]) instanceof PayoutGroup);
    }

    /** @test */
    public function isInstanceOfPayoutInstruction()
    {
        $this->assertTrue(LaravelBitpay::PayoutInstruction(
                10,
                RecipientReferenceMethod::EMAIL,
                'test@example.com'
            ) instanceof PayoutInstruction);
    }
}
