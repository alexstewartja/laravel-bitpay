<?php

namespace Vrajroham\LaravelBitpay\Actions;

use BitPaySDK\Exceptions\BitPayApiException;
use BitPaySDK\Exceptions\BitPayException;
use BitPaySDK\Exceptions\BitPayExceptionProvider;
use BitPaySDK\Exceptions\BitPayGenericException;
use BitPaySDK\Model\Currency;
use BitPaySDK\Model\Rate\Rate;
use BitPaySDK\Model\Rate\Rates;
use BitPaySDK\Model\Wallet\Currencies;
use BitPaySDK\Util\JsonMapperFactory;


/**
 * @link https://developer.bitpay.com/reference/currencies Currencies
 *
 *
 */
trait ManageCurrencies
{
    /**
     * @param string|null $code
     *
     * @return \BitpaySDK\Model\Currency
     */
    public static function Currency(string $code = null): Currency
    {
        $currency = new Currency();
        if ($code) {
            $currency->setCode($code);
        }

        return $currency;
    }

    /**
     * Fetch the supported currencies.
     *
     * @link https://developer.bitpay.com/reference/retrieve-the-supported-currencies Retrieve the Supported Currencies     * @return array     A list of supported BitPay Currency objects.
     *
     * @return array
     * @throws BitPayException
     */
    public static function getCurrencies(): array
    {
        $thisInstance = new self();

        $responseJson = $thisInstance->client->restCli->get("/currencies", null, false);

        try {
            $mapper = JsonMapperFactory::create();

            return $mapper->mapArray(
                json_decode($responseJson, true, 512, JSON_THROW_ON_ERROR),
                [],
                Currency::class
            );
        } catch (\Throwable $e) {
            BitPayExceptionProvider::throwDeserializeResourceException('Currencies', $e->getMessage());
        }

        return [];
    }
}
