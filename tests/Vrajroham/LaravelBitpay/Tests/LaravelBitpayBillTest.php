<?php

namespace Vrajroham\LaravelBitpay\Tests;

use BitPaySDK\Model\Bill\Bill;
use BitPaySDK\Model\Bill\Item as BillItem;
use PHPUnit\Framework\TestCase;
use Vrajroham\LaravelBitpay\LaravelBitpay;

class LaravelBitpayBillTest extends TestCase
{
    /** @test */
    public function isInstanceOfBill()
    {
        $this->assertTrue(LaravelBitpay::Bill() instanceof Bill);
    }

    /** @test */
    public function isInstanceOfBillItem()
    {
        $this->assertTrue(LaravelBitpay::BillItem() instanceof BillItem);
    }
}
