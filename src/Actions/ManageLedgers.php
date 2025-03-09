<?php

namespace Vrajroham\LaravelBitpay\Actions;

use BitPaySDK\Exceptions\BitPayException;
use BitPaySDK\Model\Ledger\Ledger;
use BitPaySDK\Model\Ledger\LedgerEntry;


/**
 * Ledgers are records of money movement. The Ledgers resource can be used to retrieve account balances by Currency,
 * and also to retrieve information about Ledger entries by Entry Code.
 *
 * @link https://developer.bitpay.com/reference/ledgers
 */
trait ManageLedgers
{

    /**
     * Retrieve account balances.
     *
     * @link https://developer.bitpay.com/reference/retrieve-account-balances Retrieve Account Balances
     *
     * @return Ledger[] A list of Ledger objects populated with the currency and current balance of each one.
     * @throws BitPayException
     */
    public static function getLedgers(): array
    {
        return (new self())->client->getLedgers();
    }

    /**
     * Retrieve ledger entries.
     *
     * @link https://developer.bitpay.com/reference/retrieve-ledger-entries Retrieve Ledger Entries
     *
     * @param $currency  string ISO 4217 3-character currency code for the ledger to retrieve.
     * @param $startDate string The start date for fetching ledger entries. Format YYYY-MM-DD
     * @param $endDate   string The end date for fetching ledger entries. Format YYYY-MM-DD
     *
     * @return LedgerEntry[] A list of LedgerEntry objects that match the provided filters.
     * @throws BitPayException
     */
    public static function getLedgerEntries(string $currency, string $startDate, string $endDate): array
    {
        return (new self())->client->getLedgerEntries($currency, $startDate, $endDate);
    }

}
