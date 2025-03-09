<?php

namespace Vrajroham\LaravelBitpay\Traits;

use BitPaySDK\Client as BitpayClient;
use BitPaySDK\Env;
use BitPaySDK\Exceptions\BitPayException;
use BitPaySDK\Tokens;
use Vrajroham\LaravelBitpay\Exceptions\InvalidConfigurationException;


trait MakesHttpRequests
{
    /**
     * Get the BitPay client.
     *
     * @throws InvalidConfigurationException
     * @throws BitPayException
     */
    public function setupClient()
    {
        $this->validateAndLoadConfig();

        $this->client = BitpayClient::createWithData(
            'testnet' == $this->config['network'] ? Env::TEST : Env::PROD,
            $this->config['private_key'],
            new Tokens(
                $this->config['merchant_token'], //merchant
                $this->config['payout_token'], //payout
                $this->config['pos_token'] //pos
            ),
            $this->config['key_storage_password'] //used to decrypt your private key, if encrypted
        );
    }

    /**
     * Validate and load the config.
     *
     * @throws InvalidConfigurationException
     */
    public function validateAndLoadConfig(): void
    {
        $config = config('laravel-bitpay');

        if ('mainnet' !== $config['network'] && 'testnet' !== $config['network']) {
            throw InvalidConfigurationException::invalidNetworkName();
        }

        if (! class_exists($config['key_storage'])) {
            throw InvalidConfigurationException::invalidStorageClass();
        }

        if ('' === trim($config['key_storage_password'])) {
            throw InvalidConfigurationException::invalidOrEmptyPassword();
        }

        if (! empty($config['merchant_facade_enabled']) && empty($config['merchant_token'])) {
            throw InvalidConfigurationException::emptyMerchantToken();
        }

        if (! empty($config['payout_facade_enabled']) && empty($config['payout_token'])) {
            throw InvalidConfigurationException::emptyPayoutToken();
        }

        $this->config = $config;
    }
}
