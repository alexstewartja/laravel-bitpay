<?php

namespace Vrajroham\LaravelBitpay\Tests;

use BitPaySDK\Model\Invoice\Buyer;
use BitPaySDK\Model\Invoice\Invoice as BitPaySDKInvoice;
use PHPUnit\Framework\TestCase;
use Vrajroham\LaravelBitpay\LaravelBitpay;

class LaravelBitpayInvoiceTest extends TestCase
{
    /** @test */
    public function isInstanceOfInvoice()
    {
        $this->assertTrue(LaravelBitpay::Invoice() instanceof BitPaySDKInvoice);
    }

    /** @test */
    public function isInstanceOfBuyer()
    {
        $this->assertTrue(LaravelBitpay::Buyer() instanceof Buyer);
    }
}
