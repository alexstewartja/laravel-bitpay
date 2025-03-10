<?php

namespace Vrajroham\LaravelBitpay\Exceptions;

use Exception;

class InvalidConfigurationException extends Exception
{
    public static function invalidNetworkName(): self
    {
        return new static('Invalid network option provided in config. Should be mainnet or testnet only.');
    }

    public static function invalidStorageClass(): static
    {
        return new static('Invalid key storage class provided in config.');
    }

    public static function invalidOrEmptyPassword(): static
    {
        return new static('Password missing in config. Password is required to encrypt and decrypt keys on the filesystem.');
    }

    public static function emptyMerchantToken(): static
    {
        return new static('BitPay merchant token is empty. Set BITPAY_MERCHANT_TOKEN in your .env file.');
    }

    public static function emptyPayoutToken(): static
    {
        return new static('BitPay payout token is empty. Set BITPAY_PAYOUT_TOKEN in your .env file.');
    }

    public static function emptyPosToken(): static
    {
        return new static('BitPay POS token is empty. Set BITPAY_POS_TOKEN in your .env file.');
    }
}
