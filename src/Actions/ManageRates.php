<?php

namespace Vrajroham\LaravelBitpay\Actions;

use BitPaySDK\Exceptions\BitPayException;
use \BitPaySDK\Model\Rate\Rate;
use BitPaySDK\Model\Rate\Rates;


trait ManageRates
{
    /**
     * Retrieve the exchange rate table maintained by BitPay.
     *
     * @link https://bitpay.com/exchange-rates Exchange Rates
     *
     * @return Rates A Rates object populated with the BitPay exchange rate table.
     * @throws BitPayException
     */
    public static function getRates(): Rates
    {
        return (new self())->client->getRates();
    }

    /**
     * Retrieve all the rates for a given cryptocurrency
     *
     * @link https://developer.bitpay.com/reference/retrieve-all-the-rates-for-a-given-cryptocurrency Retrieve all the rates for a given cryptocurrency
     *
     * @param string $baseCurrency The cryptocurrency for which you want to fetch the rates.
     *                             Current supported values are BTC, BCH, ETH, XRP, DOGE and LTC
     *
     * @return Rates A Rates object populated with the currency rates for the requested baseCurrency.
     * @throws BitPayException
     */
    public static function getCurrencyRates(string $baseCurrency): Rates
    {
        return (new self())->client->getCurrencyRates($baseCurrency);
    }

    /**
     * Retrieve the rate for a cryptocurrency / fiat pair
     *
     * @link https://developer.bitpay.com/reference/retrieve-the-rates-for-a-cryptocurrency-fiat-pair Retrieve the rates for a cryptocurrency / fiat pair
     *
     * @param string $baseCurrency The cryptocurrency for which you want to fetch the fiat-equivalent rate.
     *                             Current supported values are BTC, BCH, ETH, XRP, DOGE and LTC
     * @param string $currency     The fiat currency for which you want to fetch the baseCurrency rate
     *
     * @return Rate A Rate object populated with the currency rate for the requested baseCurrency.
     * @throws BitPayException
     */
    public static function getCurrencyPairRate(string $baseCurrency, string $currency): Rate
    {
        return (new self())->client->getCurrencyPairRate($baseCurrency, $currency);
    }
}
