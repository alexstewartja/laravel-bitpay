<?php

namespace Vrajroham\LaravelBitpay\Actions;

use BitPaySDK\Exceptions\BitPayException;
use BitPaySDK\Model\Payout\Payout;
use BitPaySDK\Model\Payout\PayoutGroup;
use Illuminate\Support\Collection;
use Throwable;
use Vrajroham\LaravelBitpay\Constants\WebhookAutoPopulate;


/**
 * Payouts are groups of cryptocurrency payments to employees, customers, partners, etc.
 *
 * @link https://developer.bitpay.com/reference/payouts Payouts
 */
trait ManagePayouts
{
    /**
     * Construct a BitPay Payout instance.
     *
     *
     * @param float|null  $amount         The total amount of the payout in fiat currency.
     * @param string|null $currency       Currency code set for the payout amount (ISO 4217 3-character currency code).
     *                                    Supported currency codes for payouts are EUR, USD, GBP, CAD, NZD, AUD, ZAR
     * @param string|null $ledgerCurrency Ledger currency code set for the payout request (ISO 4217 3-character
     *                                    currency code), it indicates on which ledger the payout request will be
     *                                    recorded. If not provided in the request, this parameter will be set by
     *                                    default to the active ledger currency on your account, e.g. your settlement
     *                                    currency.
     *
     * @return Payout
     */
    public static function Payout(float $amount = null, string $currency = null, string $ledgerCurrency = null): Payout
    {
        return new Payout($amount, $currency, $ledgerCurrency);
    }

    /**
     * Construct a BitPay PayoutGroup instance.
     *
     *
     * @param array $payouts List of payouts to include in the payout group
     *
     * @return PayoutGroup
     */
    public static function PayoutGroup(array $payouts): PayoutGroup
    {
        $payoutGroup = new PayoutGroup();
        $payoutGroup->setPayouts($payouts);

        return $payoutGroup;
    }

    /**
     * Create a payout.
     *
     * @link https://developer.bitpay.com/reference/create-a-payout Create a Payout
     *
     * @param Payout $payout A Payout object with request parameters defined.
     *
     * @return Payout A BitPay generated Payout object.
     * @throws BitPayException
     */
    public static function createPayout(Payout $payout): Payout
    {
        $thisInstance = new self();

        self::maybeAutoPopulatePayoutNotificationURL($payout);

        return $thisInstance->client->submitPayout($payout);
    }

    private static function maybeAutoPopulatePayoutNotificationURL(Payout &$payout)
    {
        try {
            $thisInstance = new self();
            if (empty($payout->getNotificationURL()) &&
                in_array(WebhookAutoPopulate::PAYOUTS, $thisInstance->config['auto_populate_webhook'])) {
                $payout->setNotificationURL(route('laravel-bitpay.webhook.capture'));
            }
        } catch (Throwable $exception) {
            // Misconfiguration or route macro not in use
        }
    }

    /**
     * Retrieve a payout
     *
     * @link https://developer.bitpay.com/reference/retrieve-a-payout Retrieve a Payout
     *
     * @param string $payoutId The ID of the payout to retrieve.
     *
     * @return Payout A BitPay Payout object.
     * @throws BitPayException
     */
    public static function getPayout(string $payoutId): Payout
    {
        return (new self)->client->getPayout($payoutId);
    }

    /**
     * Cancel a payout
     *
     * @link https://developer.bitpay.com/reference/cancel-a-payout Cancel a Payout
     *
     * @param string $payoutId The ID of the payout to cancel.
     *
     * @return bool True if payout was canceled successfully, false otherwise.
     * @throws BitPayException
     */
    public static function cancelPayout(string $payoutId): bool
    {
        return (new self())->client->cancelPayout($payoutId);
    }

    /**
     * Request the last Payout webhook to be resent.
     *
     * @link https://developer.bitpay.com/reference/request-a-payout-webhook-to-be-resent Request a Payout Webhook to be Resent
     *
     * @param  $payoutId string The ID of the payout for which you want the last webhook to be resent.
     *
     * @return bool True if the webhook has been resent for the current payout status, false otherwise.
     * @throws BitPayException
     */
    public static function requestPayoutWebhook(string $payoutId): bool
    {
        return (new self())->client->requestPayoutNotification($payoutId);
    }

    /**
     * Create a BitPay payout group
     *
     * @link https://developer.bitpay.com/reference/create-payout-group Create Payout Group
     *
     * @param PayoutGroup $payoutGroup A PayoutGroup object.
     *
     * @return PayoutGroup A BitPay generated PayoutGroup object.
     * @throws BitPayException
     */
    public static function createPayoutGroup(PayoutGroup $payoutGroup): PayoutGroup
    {
        $payouts = Collection::make($payoutGroup->getPayouts())->each(fn($payout) => self::maybeAutoPopulatePayoutNotificationURL($payout));

        return (new self())->client->createPayoutGroup($payouts->toArray());
    }

    /**
     * Retrieve a filtered list of payouts
     *
     * @link https://developer.bitpay.com/reference/retrieve-payouts-filtered-by-query Retrieve Payouts Filtered by Query
     *
     * @param string|null $startDate The start date to filter the Payouts.
     * @param string|null $endDate   The end date to filter the Payout.
     * @param string|null $status    The payout status you want to query on
     * @param string|null $reference The optional reference specified at payout request creation.
     * @param int|null    $limit     Maximum results that the query will return (useful for paging results).
     * @param int|null    $offset    number of results to offset (ex. skip 10 will give you results starting
     *                               with the 11th result).
     *
     * @return Payout[] A list of BitPay Payout objects.
     * @throws BitPayException
     */
    public static function getPayouts(
        string $startDate = null,
        string $endDate = null,
        string $status = null,
        string $reference = null,
        int    $limit = null,
        int    $offset = null): array
    {
        return (new self())->client->getPayouts(
            $startDate,
            $endDate,
            $status,
            $reference,
            $limit,
            $offset
        );
    }

    /**
     * Cancel a payout group
     *
     * @link https://developer.bitpay.com/reference/cancel-a-payout-group Cancel a Payout Group
     *
     * @param string $payoutGroupId The ID of the payout group to cancel.
     *
     * @return PayoutGroup A payout group object with payouts that were either canceled successfully, or failed to cancel.
     * @throws BitPayException
     */
    public static function cancelPayoutGroup(string $payoutGroupId): PayoutGroup
    {
        return (new self())->client->cancelPayoutGroup($payoutGroupId);
    }

}
