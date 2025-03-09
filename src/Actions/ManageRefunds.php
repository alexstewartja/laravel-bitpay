<?php

namespace Vrajroham\LaravelBitpay\Actions;

use BitPaySDK\Exceptions\BitPayApiException;
use BitPaySDK\Exceptions\BitPayException;
use BitPaySDK\Exceptions\BitPayExceptionProvider;
use BitPaySDK\Exceptions\BitPayGenericException;
use BitPaySDK\Model\Invoice\Refund;


/**
 * Refund requests are full or partial refunds associated to an invoice. Fully paid invoices can be refunded via the merchant's authorization to issue a refund, while underpaid and overpaid invoices are automatically executed by BitPay to issue the underpayment or overpayment amount to the customer.
 *
 * @link https://developer.bitpay.com/reference/refunds Refunds
 */
trait ManageRefunds
{

    /**
     * Construct a BitPay Refund instance.
     *
     * @param float  $amount   The amount to be refunded, denominated in the invoice original currency - partial refunds are supported
     * @param string $currency Reference currency used for the refund, usually the same as the currency used to create the invoice.
     * @param string $token    The API token used when originally creating the invoice.
     *
     * @return Refund
     */
    public static function Refund(float $amount = 0.0, string $currency = "", string $token = ""): Refund
    {
        return new Refund($amount, $currency, $token);
    }

    /**
     * Create a BitPay Refund Request.
     *
     * @link https://developer.bitpay.com/reference/create-a-refund-request Create a Refund Request
     *
     * @param string      $invoiceId          The BitPay invoice ID having the associated refund to be created.
     * @param float       $amount             Amount to be refunded in the currency indicated.
     * @param string      $currency           Reference currency used for the refund, usually the same as the currency used
     *                                        to create the invoice.
     * @param bool        $preview            Whether to create the refund request as a preview (which will not be acted on
     *                                        until status is updated) (defaults to false)
     * @param bool        $immediate          Whether funds should be removed from merchant ledger immediately on submission
     *                                        or at time of processing (defaults to false)
     * @param bool        $buyerPaysRefundFee Whether the buyer should pay the refund fee (defaults to false)
     * @param string|null $guid               A variable provided by the merchant and designed to be used by the merchant to correlate the refund with a refund ID in their system.
     *
     * @return Refund An updated Refund Object
     * @throws BitPayException
     */
    public static function createRefund(
        string  $invoiceId,
        float   $amount,
        string  $currency,
        bool    $preview = false,
        bool    $immediate = false,
        bool    $buyerPaysRefundFee = false,
        ?string $guid = null
    ): Refund {
        return (new self())->client->createRefund($invoiceId, $amount, $currency, $preview, $immediate, $buyerPaysRefundFee, $guid);
    }

    /**
     * Retrieve a BitPay Refund Request by its ID or GUID.
     *
     * @link https://developer.bitpay.com/reference/retrieve-a-refund-request Retrieve a Refund Request
     * @link https://developer.bitpay.com/reference/retrieve-a-refund-by-guid-request Retrieve a Refund by GUID Request
     *
     * @param string|null $refundId   string The ID for the refund to retrieve.
     * @param string|null $refundGuid string The GUID for the refund to retrieve.
     *
     * @return Refund A BitPay Refund object.
     * @throws BitPayApiException
     * @throws BitPayGenericException
     */
    public static function getRefund(?string $refundId, ?string $refundGuid): Refund
    {
        if ($refundId) {
            return (new self())->client->getRefund($refundId);
        } elseif ($refundGuid) {
            return (new self())->client->getRefundByGuid($refundGuid);
        }

        BitPayExceptionProvider::throwValidationException("A valid Refund ID or GUID is required");
    }

    /**
     * Retrieve all refund requests on a BitPay invoice.
     *
     * @link https://developer.bitpay.com/reference/retrieve-refunds-of-an-invoice Retrieve Refunds of an Invoice
     *
     * @param $invoiceId  string ID of the BitPay invoice having the associated refunds.
     *
     * @return Refund[] An array of BitPay Refund objects associated to the specified Invoice.
     * @throws BitPayException
     */
    public static function getRefunds(string $invoiceId): array
    {
        return (new self())->client->getRefunds($invoiceId);
    }

    /**
     * Cancel a BitPay Refund Request by its ID or GUID.
     *
     * @link https://developer.bitpay.com/reference/cancel-a-refund-request Cancel a Refund Request
     * @link https://developer.bitpay.com/reference/cancel-a-refund-by-guid-request Cancel a Refund by GUID Request
     *
     * @param string|null $refundId   The ID for the refund to cancel.
     * @param string|null $refundGuid The GUID for the refund to cancel.
     *
     * @return Refund A canceled BitPay Refund object.
     * @throws BitPayException
     */
    public static function cancelRefund(?string $refundId, ?string $refundGuid): Refund
    {
        if ($refundId) {
            return (new self())->client->cancelRefund($refundId);
        } elseif ($refundGuid) {
            return (new self())->client->cancelRefundByGuid($refundGuid);
        }

        BitPayExceptionProvider::throwValidationException("A valid Refund ID or GUID is required");
    }

    /**
     * Request a Refund webhook to be resent.
     *
     * @link https://developer.bitpay.com/reference/request-a-refund-notification-to-be-resent Request a Refund Notification to be Resent
     *
     * @param        $refundId    string The ID of the refund for which you want the last webhook to be resent.
     * @param string $refundToken The resource token for the refundId you want the notification to be resent. You first need to retrieve this token from the refund object itself.
     *
     * @return bool True if the webhook has been resent for the current refund status, false otherwise.
     * @throws BitPayException
     */
    public static function requestRefundWebhook(string $refundId, string $refundToken): bool
    {
        return (new self())->client->sendRefundNotification($refundId, $refundToken);
    }

    /**
     * Update the status of a BitPay Refund Request by its ID or GUID.
     *
     * @link https://developer.bitpay.com/reference/update-a-refund-request Update a Refund Request
     * @link https://developer.bitpay.com/reference/update-a-refund-by-guid-request Update a Refund by GUID Request
     *
     * @param string|null $refundId   The ID for the refund to update.
     * @param string|null $refundGuid The GUID for the refund to update.
     * @param string      $status     The new status to update the refund to. Set to `created` in order to confirm the refund request and initiate the flow to send it to shopper.
     *
     * @return Refund An updated BitPay Refund object.
     * @throws BitPayException
     */
    public function updateRefund(
        ?string $refundId,
        ?string $refundGuid,
        string  $status
    ): Refund {
        if ($refundId) {
            return (new self())->client->updateRefund($refundId, $status);
        } elseif ($refundGuid) {
            return (new self())->client->updateRefundByGuid($refundGuid, $status);
        }

        BitPayExceptionProvider::throwValidationException("A valid Refund ID or GUID is required");
    }
}
