<?php

namespace Vrajroham\LaravelBitpay\Console;

use BitPayKeyUtils\KeyHelper\PrivateKey;
use BitPaySDK\Env;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;
use Vrajroham\LaravelBitpay\Traits\CreateKeypairTrait;


class CreateKeypairCommand extends Command
{
    use CreateKeypairTrait;


    private const HEADER_FOOTER_WIDTH = 50;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel-bitpay:createkeypair';
    /**
     * The console command description.
     *
     * @var string
     */
    protected      $description = 'Create and persist keypair(s). Pair client with BitPay server.';
    private        $config;
    private        $privateKey;
    private        $publicKey;
    private        $storageEngine;
    private        $client;
    private        $network;
    private string $sin;
    private array  $tokenLabels;
    private array  $tokens;
    private array  $pairingCodes;
    private array  $pairingExpirations;
    private array  $approveLinks;
    private        $bar;
    private Client $bitpayClient;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->addOption('fresh', 'f', InputOption::VALUE_NONE, 'Explicitly generate a fresh private key. By default, if a (valid) key exists, it will be used instead.');
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->init();
            $this->network      = Str::lower($this->config['network']) === 'testnet' ? Env::TEST_URL : Env::PROD_URL;
            $this->bitpayClient = new Client(['base_uri' => $this->network]);
            $enabled_facades    = $this->getEnabledFacades();

            if (empty($enabled_facades)) {
                $error_message = 'No facades enabled for token generation! Open your <options=bold,underscore>.env</> file and set values for <options=bold,underscore>BITPAY_ENABLE_MERCHANT</>, <options=bold,underscore>BITPAY_ENABLE_PAYOUT</> and/or <options=bold,underscore>BITPAY_ENABLE_POS</>';
                $this->error($error_message);
                exit(1);
            }

            // Each facade effects 2 progress advances and keypair loading/generation effects a max of 5.
            $this->bar = $this->output->createProgressBar((count($enabled_facades) * 2) + 5);
            $this->bar->setProgressCharacter('⚡');
            $this->bar->setBarCharacter('-');
            $this->bar->setEmptyBarCharacter(' ');
            $this->bar->minSecondsBetweenRedraws(0);
            $this->newLine();
            $this->createAndPersistKeypair();

            foreach ($enabled_facades as $facade) {
                $this->sectionHeader(strtoupper($facade) . ' FACADE');
                $this->pairWithServerAndCreateToken($facade);
                $this->writeFacadeTokenToEnv($facade);
                $this->newLine();
                $this->line("<options=bold,underscore>Token Label</> : <options=bold;fg=bright-cyan>{$this->tokenLabels[$facade]}</>");
                $this->line("<options=bold,underscore>Token</>       : <options=bold;fg=bright-cyan>{$this->tokens[$facade]}</> (Copied to <options=bold;fg=gray>.env</> for your convenience)");
                $this->line("<options=bold,underscore>Pairing Code</>: <options=bold;fg=bright-cyan>{$this->pairingCodes[$facade]}</> (Expires: <options=bold;fg=bright-red>{$this->pairingExpirations[$facade]}</>)");
                $this->newLine();
                $this->line("Approve your API Token by visiting: <fg=blue;href={$this->approveLinks[$facade]}>{$this->approveLinks[$facade]}</>");
                $this->sectionFooter();
            }
        } catch (\Throwable $exception) {
            $this->error('Whoops! We have a problem: ' . $exception->getMessage());
            exit(1);
        }
    }

    /**
     * @inheritDoc
     * Polyfill which enables compatibility with Laravel <8.x
     *
     * @param int $count
     *
     * @return void
     * @since 5.0.1
     */
    public function newLine($count = 1)
    {
        $this->output->newLine($count);
    }

    /**
     * Create private key and public keypair and store in secure file storage, using an existing (valid) keypair by
     * default.
     *
     *
     * @throws \Exception
     */
    public function createAndPersistKeypair()
    {
        $this->sectionHeader('KEYPAIR GENERATION');

        if (in_array('__construct', get_class_methods($this->config['key_storage']))) {
            $this->storageEngine = new $this->config['key_storage']($this->config['key_storage_password']);
        } else {
            $this->storageEngine = new $this->config['key_storage']();
        }

        if ($this->option('fresh')) {
            $this->generateFreshKeyPair();
        } else {
            try {
                // Try to load the configured Private Key, expect it to be missing or corrupted
                $this->privateKey = $this->storageEngine->load($this->privateKeyAbsPath());

                // Private Key may be uncorrupted but has invalid hexits or decimals
                if ($this->privateKey->isValid()) {
                    $this->publicKey = $this->privateKey->getPublicKey();

                    $this->advanceWithInfo(' 🗝️ - Using existing private key.', 1, 3);
                } else {
                    $this->generateFreshKeyPair();
                }
            } catch (\Throwable $exception) {
                $this->generateFreshKeyPair();
            }
        }

        $this->sin = (string)$this->publicKey->getSin()->__toString();

        $this->advanceWithInfo(' 🆔 - Created Service Identification Number (SIN Key) for client.');

        $this->sectionFooter();
    }

    private function sectionHeader(string $sectionTitle)
    {
        $diffWidth   = self::HEADER_FOOTER_WIDTH - (strlen($sectionTitle) + 2);
        $borderWidth = round($diffWidth / 2, 0, PHP_ROUND_HALF_DOWN);

        $this->info('<fg=magenta>' . str_repeat('#', $borderWidth) . ' '
            . $sectionTitle . ' ' . str_repeat('#', $borderWidth) . '</>');
        $this->newLine();
    }

    /**
     * @throws \Exception
     */
    private function generateFreshKeyPair()
    {
        $this->advanceWithInfo(' 🔑 - Generating private key.');

        $this->privateKey = new PrivateKey($this->privateKeyAbsPath());
        $this->privateKey->generate();

        $this->advanceWithInfo(' 🌐 - Generating public key.');

        $this->publicKey = $this->privateKey->getPublicKey();

        $this->advanceWithInfo(' 🧰 - Using <options=bold;fg=gray>' . get_class($this->storageEngine) . '</> for secure storage.');

        $this->storageEngine->persist($this->privateKey);

        $this->advanceWithInfo(' 🔐 - Private key stored securely.');
    }

    /**
     * Advance progress bar by $steps, and write 'info' level $message to console, optionally skipping by $skipSteps.
     *
     * @param string $message
     * @param int    $steps
     * @param int    $skipSteps
     */
    private function advanceWithInfo(string $message, int $steps = 1, int $skipSteps = 0)
    {
        $this->bar->clear();
        if ($skipSteps > 0) {
            $this->bar->setMaxSteps($this->bar->getMaxSteps() - $skipSteps);
        }
        $this->bar->advance($steps);
        $this->info($message);
    }

    private function sectionFooter()
    {
        $this->newLine();
        $this->info('<fg=magenta>' . str_repeat('#', self::HEADER_FOOTER_WIDTH) . '</>');
        $this->newLine(2);
    }

    /**
     * Initiates client-server pairing. Create token and pairing code on BitPay server for provided facade.
     *
     * @param string $facade One of 'merchant' or 'payout'
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function pairWithServerAndCreateToken(string $facade)
    {
        $this->advanceWithInfo(" 🖥️ - Connecting to BitPay server and generating $facade token.");

        // Token label is limited to 60 characters
        $this->tokenLabels[$facade] = Str::substr(ucwords(str_replace(" ", "-", config('app.name'))), 0, 36)
            . '__' . Str::ucfirst($facade) . '__' . date('h-i-s_A');

        $postData = [
            'id'     => $this->sin,
            'label'  => $this->tokenLabels[$facade],
            'facade' => $facade,
        ];
        $response = $this->bitpayClient->post('/tokens', [
            'json'    => $postData,
            'headers' => [
                'Content-Type' => 'application/json',
                'X-Accept-Version' => '2.0.0',
                'Accept' => 'application/json',
            ],
        ]);
        sleep(3); // Guard against BitPay's rate limiting (`Too many requests` error)
        $response = json_decode($response->getBody()->getContents());

        $this->advanceWithInfo(" 🥳 - New $facade token and pairing code received from BitPay server.");

        $this->tokens[$facade]             = $response->data[0]->token;
        $this->config["{$facade}_token"]   = $response->data[0]->token;
        $this->pairingCodes[$facade]       = $response->data[0]->pairingCode;
        $this->pairingExpirations[$facade] = date('M j, Y h:i:s A', intval($response->data[0]->pairingExpiration) / 1000);
        $this->approveLinks[$facade]       = "{$this->network}api-access-request?pairingCode={$this->pairingCodes[$facade]}";
    }
}
