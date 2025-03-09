<?php

namespace Vrajroham\LaravelBitpay\Actions;

use BitPaySDK\Exceptions\BitPayException;
use BitPaySDK\Model\Wallet\Wallet;


trait ManageWallets
{
    /**
     * Retrieve the cryptocurrency wallets supported by BitPay.
     *
     * @link https://developer.bitpay.com/reference/retrieve-the-supported-wallets Retrieve the Supported Wallets
     *
     * @return Wallet[] An array of BitPay Wallet objects.
     * @throws BitPayException
     */
    public static function getSupportedWallets(): array
    {
        return (new self())->client->getSupportedWallets();
    }

}
