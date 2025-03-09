<?php

namespace Vrajroham\LaravelBitpay\Actions;

use BitPaySDK\Exceptions\BitPayApiException;
use BitPaySDK\Exceptions\BitPayException;
use BitPaySDK\Exceptions\BitPayGenericException;
use BitPaySDK\Model\Settlement\Settlement;


/**
 * Settlements are transfers of payment profits from BitPay to bank accounts and cryptocurrency wallets owned by
 * merchants, partners, etc.
 *
 * @link https://developer.bitpay.com/reference/settlements
 */
trait ManageSettlements
{

    /**
     * Retrieve a filtered list of settlements.
     *
     * @link https://developer.bitpay.com/reference/retrieve-settlements Retrieve Settlements
     *
     * @param string      $currency  The three digit currency string for the ledger to retrieve.
     * @param string      $startDate The start of the date window to query for settlements. Format YYYY-MM-DD
     * @param string      $endDate   The end of the date window to query for settlements. Format YYYY-MM-DD
     * @param string|null $status    The settlement status you want to query on.
     *                               Can be `new`, `processing`, `rejected` or `completed`.
     * @param int|null    $limit     Maximum results that the query will return (useful for paging results).
     * @param int|null    $offset    Number of results to offset
     *                               (ex. skip 10 will give you results starting with the 11th result)
     *
     * @return Settlement[] A list of BitPay Settlement objects.
     * @throws BitPayException
     */
    public static function getSettlements(
        string $currency,
        string $startDate,
        string $endDate,
        ?string $status = null,
        ?int    $limit = null,
        ?int    $offset = null
    ): array {
        return (new self())->client->getSettlements($currency, $startDate, $endDate, $status, $limit, $offset);
    }

    /**
     * Retrieve a settlement.
     *
     * @link https://developer.bitpay.com/reference/retrieve-a-settlement Retrieve a Settlement
     *
     * @param $settlementId string ID of the specific settlement resource to be fetched.
     *
     * @return Settlement A BitPay Settlement object.
     * @throws BitPayException
     */
    public static function getSettlement(string $settlementId): Settlement
    {
        return (new self())->client->getSettlement($settlementId);
    }

    /**
     * Fetch a reconciliation report. Allows merchant to retrieve a detailed report of the activity within
     * the settlement period, in order to reconcile incoming settlements from BitPay.
     *
     * @link https://developer.bitpay.com/reference/fetch-a-reconciliation-report Fetch a Reconciliation Report
     *
     * @param string $settlementId ID of the specific settlement resource to be reconciled.
     * @param string $settlementToken Resource token of the specific `settlementId` to be reconciled.
     *
     * @return Settlement A detailed BitPay Settlement object.
     * @throws BitPayApiException
     * @throws BitPayGenericException
     */
    public static function getSettlementReconciliationReport(string $settlementId, string $settlementToken): Settlement
    {
        return (new self())->client->getSettlementReconciliationReport($settlementId, $settlementToken);
    }
}
