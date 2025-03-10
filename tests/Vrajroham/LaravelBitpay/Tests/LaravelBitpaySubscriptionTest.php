<?php

namespace Vrajroham\LaravelBitpay\Tests;

use BitPaySDK\Model\Currency;
use BitPaySDK\Model\Subscription\BillData;
use BitPaySDK\Model\Subscription\Item as SubscriptionItem;
use BitPaySDK\Model\Subscription\Subscription;
use PHPUnit\Framework\TestCase;
use Vrajroham\LaravelBitpay\LaravelBitpay;


class LaravelBitpaySubscriptionTest extends TestCase
{
    /** @test */
    public function isInstanceOfSubscription()
    {
        $this->assertTrue(LaravelBitpay::Subscription() instanceof Subscription);
    }

    /** @test */
    public function isInstanceOfSubscriptionItem()
    {
        $this->assertTrue(LaravelBitpay::SubscriptionItem() instanceof SubscriptionItem);
    }

    /** @test */
    public function isInstanceOfBillData()
    {
        $this->assertTrue(LaravelBitpay::BillData(
                'testBillData-1234',
                Currency::USD,
                'test@example.com',
                '2025-12-01T09:00:00Z') instanceof BillData);
    }
}
