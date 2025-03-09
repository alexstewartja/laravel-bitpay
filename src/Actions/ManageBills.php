<?php

namespace Vrajroham\LaravelBitpay\Actions;

use BitPaySDK\Exceptions\BitPayException;
use BitPaySDK\Model\Bill\Bill;
use BitPaySDK\Model\Bill\Item;
use BitPaySDK\Model\Facade;


/**
 * Bills are payment requests addressed to specific buyers.
 * Bill line items have fixed prices, typically denominated in fiat currency.
 *
 * @link https://developer.bitpay.com/reference/bills Bills
 */
trait ManageBills
{
    /**
     * Construct a BitPay Bill instance.
     *
     * @param string|null $number   A bill number for tracking purposes.
     * @param string|null $currency The three digit currency code used to compute the bill's crypto amount.
     * @param string|null $email    The email address of the receiver for this bill.
     * @param Item[]|null $items    The list of BillItems to add to this bill.
     *
     * @return Bill
     */
    public static function Bill(
        ?string $number = null,
        ?string $currency = null,
        ?string $email = null,
        ?array  $items = null): Bill
    {
        return new Bill($number, $currency, $email, $items);
    }

    /**
     * Construct a BitPay Bill Item instance.
     *
     * @return Item  A BitPay Bill Item
     */
    public static function BillItem(): Item
    {
        return new Item();
    }

    /**
     * Create a BitPay Bill.
     *
     * @link https://developer.bitpay.com/reference/create-a-bill Create a Bill
     *
     * @param $bill Bill A Bill object with request parameters defined.
     *
     * @return Bill A BitPay generated Bill object.
     * @throws BitPayException
     */
    public static function createBill(Bill $bill): Bill
    {
        return (new self())->client->createBill($bill);
    }

    /**
     * Retrieve a BitPay bill by its ID.
     *
     * @link https://developer.bitpay.com/reference/retrieve-a-bill Retrieve a Bill
     *
     * @param $billId      string The ID of the bill to retrieve.
     * @param $facade      string Facade to use when retrieving bill.
     *
     * @return Bill A BitPay Bill object.
     * @throws BitPayException
     */
    public static function getBill(string $billId, string $facade = Facade::MERCHANT): Bill
    {
        $signRequest = $facade !== Facade::POS;
        return (new self())->client->getBill($billId, $facade, $signRequest);
    }

    /**
     * Retrieve a collection of BitPay bills.
     *
     * @link Retrieve https://developer.bitpay.com/reference/retrieve-bills-by-status Bills by Status
     *
     * @param $status string|null The status on which to filter the bills.
     *
     * @return Bill[] A list of BitPay Bill objects.
     * @throws BitPayException
     */
    public static function getBills(string $status = null): array
    {
        return (new self())->client->getBills($status);
    }

    /**
     * Update a BitPay Bill.
     *
     * @link https://developer.bitpay.com/reference/update-a-bill Update a Bill
     *
     * @param $bill   Bill A Bill object with the parameters to update defined.
     * @param $billId string The ID of the Bill to update.
     *
     * @return Bill An updated Bill object.
     * @throws BitPayException
     */
    public static function updateBill(Bill $bill, string $billId): Bill
    {
        return (new self())->client->updateBill($bill, $billId);
    }

    /**
     * Deliver a BitPay Bill.
     *
     * @link https://developer.bitpay.com/reference/deliver-a-bill-via-email Deliver a Bill Via Email
     *
     * @param $billId      string The ID of the requested bill.
     * @param $billToken   string The token of the requested bill.
     * @param $signRequest bool Indicates if the delivery request should be un/signed.
     *
     * @return bool True if the bill has been delivered, false otherwise.
     * @throws BitPayException
     */
    public static function deliverBill(string $billId, string $billToken, bool $signRequest = true): bool
    {
        return (new self())->client->deliverBill($billId, $billToken, $signRequest);
    }
}
