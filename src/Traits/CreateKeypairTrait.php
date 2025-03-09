<?php

namespace Vrajroham\LaravelBitpay\Traits;

use BitPaySDK\Model\Facade;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Vrajroham\LaravelBitpay\Exceptions\InvalidConfigurationException;


trait CreateKeypairTrait
{
    public function init(): void
    {
        $config = config('laravel-bitpay');

        if (! in_array(Str::lower($config['network']), ['mainnet', 'testnet'])) {
            throw InvalidConfigurationException::invalidNetworkName();
        }

        if (! class_exists($config['key_storage'])) {
            throw InvalidConfigurationException::invalidStorageClass();
        }

        if ('' === trim($config['key_storage_password'])) {
            throw InvalidConfigurationException::invalidOrEmptyPassword();
        }

        $pkPath = storage_path($config['private_key_dir']);
        if (!Storage::exists($pkPath)) {
            Storage::makeDirectory($pkPath);
        }

        $this->config = $config;
    }

    protected function privateKeyAbsPath(): string {
        return storage_path($this->config['private_key_dir'] . DIRECTORY_SEPARATOR . $this->config['private_key_name']);
    }

    protected function getEnabledFacades(): array
    {
        $enabledFacades = [];

        foreach ($this->getAllFacades() as $facade) {
            if (! empty($this->config["{$facade}_facade_enabled"]) && $this->config["{$facade}_facade_enabled"]) {
                $enabledFacades[] = $facade;
            }
        }

        return $enabledFacades;
    }

    protected function getAllFacades(): array
    {
        return array_values((new \ReflectionClass(Facade::class))->getConstants());
    }

    /**
     * Write facade-specific token to .env
     * Update token if it exists, otherwise add it below existing BITPAY_* entries.
     * Finally, if token doesn't exist nor does any other BITPAY_* entries, write it to the end
     * of the .env
     *
     * @param string $facade One of the constants defined on `BitPaySDK\Model\Facade`
     *
     * @throws \Exception
     */
    protected function writeNewEnvironmentFileWith(string $facade)
    {
        $replString      = $this->getEnvReplacementString($facade) . '=' . $this->tokens[$facade];
        $envFilePath     = $this->laravel->environmentFilePath();
        $envFileContents = file_get_contents($envFilePath);

        if (strpos($envFileContents, $this->getEnvReplacementString($facade)) !== false) {
            file_put_contents($envFilePath, preg_replace(
                    $this->keyReplacementPattern($facade),
                    $replString,
                    $envFileContents)
            );
        } else {
            $envLines = file($envFilePath, FILE_IGNORE_NEW_LINES);
            $offset   = null;

            foreach ($envLines as $line) {
                if (strpos($line, "BITPAY_") === 0) {
                    $offset = array_search($line, $envLines);
                }
            }

            // Place token at end of .env if no other BITPAY_* entries exist
            if ($offset === null) {
                $offset     = count($envLines) - 1;
                $replString = "\n" . $replString;
            }

            array_splice($envLines, $offset + 1, 0, $replString);
            file_put_contents($envFilePath, implode("\n", $envLines));
        }
    }

    /**
     *
     * @param string $facade One of the constants defined on `BitPaySDK\Model\Facade`
     *
     * @throws \Exception
     */
    private function getEnvReplacementString(string $facade): string
    {
        if (in_array($facade, $this->getAllFacades(), true)) {
            $facade_upper = Str::upper($facade);

            return "BITPAY_{$facade_upper}_TOKEN";
        }

        throw new \Exception("'$facade' is not a valid BitPay facade!", 1);
    }

    /**
     *
     * @param string $facade One of the constants defined on `BitPaySDK\Model\Facade`
     *
     * @throws \Exception
     */
    protected function keyReplacementPattern(string $facade): string
    {
        if (in_array($facade, $this->getAllFacades(), true)) {
            $token = $this->config["{$facade}_token"];
        } else {
            throw new \Exception("'$facade' is not a valid BitPay facade!", 1);
        }

        $replString = $this->getEnvReplacementString($facade);
        $escaped    = preg_quote('=' . $token, '/');

        return "/^" . $replString . "{$escaped}/m";
    }
}
