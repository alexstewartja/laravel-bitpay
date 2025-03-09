<?php

namespace Vrajroham\LaravelBitpay\Actions;

use BitPaySDK\Exceptions\BitPayException;
use BitPaySDK\Model\Subscription\BillData;
use BitPaySDK\Model\Subscription\Item;
use BitPaySDK\Model\Subscription\Subscription;


/**
 * Subscriptions are repeat billing agreements with specific buyers.
 * BitPay sends bill emails to buyers identified in active subscriptions according to the specified schedule.
 *
 * @link https://developer.bitpay.com/reference/subscriptions
 */
trait ManageSubscriptions
{
    /**
     * Construct a BitPay Subscription instance.
     *
     * @return Subscription
     */
    public static function Subscription(): Subscription
    {
        return new Subscription();
    }

    /**
     * @param float  $price       Line item unit price for the corresponding Subscription's currency
     * @param int    $quantity    Line item number of units
     * @param string $description Line item description
     *
     * @return Item A BitPay Subscription Item
     */
    public static function SubscriptionItem(float $price = 0.0, int $quantity = 0, string $description = ""): Item
    {
        return Item::createFromArray(['price' => $price, 'quantity' => $quantity, 'description' => $description]);
    }

    /**
     * Construct a BitPay Subscriptions BillData instance.
     *
     * @param string|null           $number   Recurring bill identifier, specified by merchant
     * @param string|null           $currency ISO 4217 3-character currency code.
     *                                        This is the currency associated with the items' price field.
     * @param string|null           $email    Subscription recipient's email address
     * @param string|\DateTime|null $dueDate  Date and time at which a bill is due, ISO-8601 format yyyy-mm-ddThh:mm:ssZ (UTC).
     * @param array|null            $items    Array of line items
     *
     * @return BillData A BitPay Subscription's billData
     */
    public static function BillData(?string $number, ?string $currency, ?string $email, string|\DateTime|null $dueDate = null, ?array $items = []): BillData
    {
        return new BillData($number, $currency, $email, $dueDate, $items);
    }

    /**
     * Create a BitPay Subscription.
     *
     * @link https://developer.bitpay.com/reference/create-a-subscription Create a Subscription
     *
     * @param $subscription Subscription A Subscription object with request parameters defined.
     *
     * @return Subscription A BitPay generated Subscription object.
     * @throws BitPayException
     */
    public static function createSubscription(Subscription $subscription): Subscription
    {
        return (new self())->client->createSubscription($subscription);
    }

    /**
     * Retrieve a BitPay subscription by its ID.
     *
     * @link https://developer.bitpay.com/reference/retrieve-a-subscription Retrieve a Subscription
     *
     * @param $subscriptionId string The ID of the subscription to retrieve.
     *
     * @return Subscription A BitPay Subscription object.
     * @throws BitPayException
     */
    public static function getSubscription(string $subscriptionId): Subscription
    {
        return (new self())->client->getSubscription($subscriptionId);
    }

    /**
     * Retrieve a collection of BitPay subscriptions by status.
     *
     * @link https://developer.bitpay.com/reference/retrieve-subscriptions-by-status Retrieve Subscriptions by Status
     *
     * @param $status string|null The status on which to filter the subscriptions.
     *
     * @return Subscription[] A filtered list of BitPay Subscription objects.
     * @throws BitPayException
     */
    public static function getSubscriptions(?string $status = null): array
    {
        return (new self())->client->getSubscriptions($status);
    }

    /**
     * Update a BitPay Subscription.
     *
     * @link https://developer.bitpay.com/reference/update-a-subscription Update a Subscription
     *
     * @param $subscription   Subscription A Subscription object with the parameters to update defined.
     * @param $subscriptionId string The ID of the Subscription to update.
     *
     * @return Subscription An updated Subscription object.
     * @throws BitPayException
     */
    public static function updateSubscription(Subscription $subscription, string $subscriptionId): Subscription
    {
        return (new self())->client->updateSubscription($subscription, $subscriptionId);
    }
}
