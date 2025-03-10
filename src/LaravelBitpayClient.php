<?php

namespace Vrajroham\LaravelBitpay;

use BitPaySDK\Client;
use BitPaySDK\Exceptions\BitPayException;
use Vrajroham\LaravelBitpay\Actions\ManageBills;
use Vrajroham\LaravelBitpay\Actions\ManageCurrencies;
use Vrajroham\LaravelBitpay\Actions\ManageInvoices;
use Vrajroham\LaravelBitpay\Actions\ManageLedgers;
use Vrajroham\LaravelBitpay\Actions\ManagePayouts;
use Vrajroham\LaravelBitpay\Actions\ManageRates;
use Vrajroham\LaravelBitpay\Actions\ManageRecipients;
use Vrajroham\LaravelBitpay\Actions\ManageRefunds;
use Vrajroham\LaravelBitpay\Actions\ManageSettlements;
use Vrajroham\LaravelBitpay\Actions\ManageSubscriptions;
use Vrajroham\LaravelBitpay\Actions\ManageWallets;
use Vrajroham\LaravelBitpay\Traits\MakesHttpRequests;


class LaravelBitpayClient
{
    use MakesHttpRequests;
    use ManageBills;
    use ManageCurrencies;
    use ManageRates;
    use ManageInvoices;
    use ManageLedgers;
    use ManagePayouts;
    use ManageRecipients;
    use ManageRefunds;
    use ManageSettlements;
    use ManageSubscriptions;
    use ManageWallets;


    protected Client $client;
    protected array  $config;


    /**
     * Setup client while creating the instance.
     *
     * @throws Exceptions\InvalidConfigurationException
     * @throws BitPayException
     */
    public function __construct()
    {
        $this->setupClient();
    }

    public static function privateKeyAbsPath(): string
    {
        $config = config('laravel-bitpay');
        return storage_path($config['private_key_dir'] . DIRECTORY_SEPARATOR . $config['private_key_name']);
    }
}
