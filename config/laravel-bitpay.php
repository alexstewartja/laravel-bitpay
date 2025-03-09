<?php

return [
    /*
     * This is the directory/folder path in which to store the private key.
     * The default value is `storage/.laravel_bitpay`
     */
    'private_key_dir'             => env('BITPAY_PRIVATE_KEY_DIR') ?: '.laravel_bitpay',

    /*
     * This is name for the private key file.
     * The default value is `bitpay.pk`
     */
    'private_key_name'             => env('BITPAY_PRIVATE_KEY_NAME') ?: 'bitpay.pk',

    /*
     * Specifies using either the Live (mainnet) or Test (testnet) BitPay network.
     *
     * The default is mainnet
     */
    'network'                 => env('BITPAY_NETWORK', 'mainnet'),

    /*
     * The key_storage option allows you to specify a class for persisting and retrieving keys.
     *
     * By default this uses the Bitpay\Storage\EncryptedFilesystemStorage class.
     */
    'key_storage'             => \BitPayKeyUtils\Storage\EncryptedFilesystemStorage::class,

    /*
     * This is the password used to encrypt and decrypt private keys on the filesystem.
     */
    'key_storage_password'    => env('BITPAY_KEY_STORAGE_PASSWORD', 'RandomPasswordForEncryption'),

    /*
     * Generate/Enable use of BitPay token for 'merchant' facade?
     *
     * Default: true
     */
    'merchant_facade_enabled' => env('BITPAY_ENABLE_MERCHANT', true),

    /*
     * Generate/Enable use of BitPay token for 'payout' facade?
     *
     * Default: false
     */
    'payout_facade_enabled'   => env('BITPAY_ENABLE_PAYOUT', false),

    /*
     * Generate/Enable use of BitPay token for 'pos' facade?
     *
     * Default: false
     */
    'pos_facade_enabled'   => env('BITPAY_ENABLE_POS', false),

    /*
     * BitPay Merchant Token
     *
     * Default: null
     */
    'merchant_token'          => env('BITPAY_MERCHANT_TOKEN'),

    /*
     * BitPay Payout Token
     *
     * Default: null
     */
    'payout_token'            => env('BITPAY_PAYOUT_TOKEN'),

    /*
     * BitPay Payout Token
     *
     * Default: null
     */
    'pos_token'            => env('BITPAY_POS_TOKEN'),

    /*
     * Indicates if configured Webhook URL (notificationURL property) should automatically be set on:
     * - Invoices
     * - Refunds
     * - Recipients
     * - Payouts/PayoutGroups
     *
     * This feature is overridden when a value is manually set on a respective resource
     * before submitting it to the BitPay API.
     *
     * Uncomment an entry to enable its auto-population.
     */
    'auto_populate_webhook'   => [
//        \Vrajroham\LaravelBitpay\Constants\WebhookAutoPopulate::INVOICES,
//        \Vrajroham\LaravelBitpay\Constants\WebhookAutoPopulate::REFUNDS,
//        \Vrajroham\LaravelBitpay\Constants\WebhookAutoPopulate::RECIPIENTS,
//        \Vrajroham\LaravelBitpay\Constants\WebhookAutoPopulate::PAYOUTS,
    ],
];
