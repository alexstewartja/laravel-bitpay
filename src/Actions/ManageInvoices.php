<?php

namespace Vrajroham\LaravelBitpay\Actions;

use BitPaySDK\Exceptions\BitPayException;
use BitPaySDK\Exceptions\BitPayExceptionProvider;
use BitPaySDK\Exceptions\BitPayValidationException;
use BitPaySDK\Model\Facade;
use BitPaySDK\Model\Invoice\Buyer;
use BitPaySDK\Model\Invoice\Invoice;
use Vrajroham\LaravelBitpay\Constants\WebhookAutoPopulate;


/**
 * Invoices are time-sensitive payment requests addressed to specific buyers. An invoice has a fixed price, typically denominated in fiat currency. It also has an equivalent price in the supported cryptocurrencies, calculated by BitPay, at a locked exchange rate with an expiration time of 15 minutes.
 *
 * @link https://developer.bitpay.com/reference/invoices Invoices
 */
trait ManageInvoices
{
    /**
     * Construct a BitPay Invoice instance.
     *
     * @param float|null  $price    float The amount for which the invoice will be created.
     * @param string|null $currency string three digit currency code used to compute the invoice bitcoin amount.
     *
     * @return Invoice
     */
    public static function Invoice(?float $price = null, ?string $currency = null): Invoice
    {
        return new Invoice($price, $currency);
    }

    /**
     * Construct a BitPay Buyer instance.
     *
     * @return Buyer
     */
    public static function Buyer(): Buyer
    {
        return new Buyer();
    }

    /**
     * Create a BitPay invoice.
     *
     * @link https://developer.bitpay.com/reference/create-an-invoice Create an Invoice
     *
     * @param $invoice Invoice An Invoice object with request parameters defined.
     * @param $facade  string Facade to use when creating invoice.
     *
     * @return Invoice $invoice A BitPay generated Invoice object.
     * @throws BitPayException
     */
    public static function createInvoice(Invoice $invoice, string $facade = Facade::MERCHANT): Invoice
    {
        $signRequest = $facade !== Facade::POS;
        $thisInstance = new self();

        try {
            if (empty($invoice->getNotificationURL()) &&
                in_array(WebhookAutoPopulate::INVOICES, $thisInstance->config['auto_populate_webhook'])) {
                $invoice->setNotificationURL(route('laravel-bitpay.webhook.capture'));
            }
        } catch (\Throwable $exception) {
            // Misconfiguration or route macro not in use
        }

        return $thisInstance->client->createInvoice($invoice, $facade, $signRequest);
    }

    /**
     * Retrieve a BitPay invoice by its ID or GUID.
     *
     * @link https://developer.bitpay.com/reference/retrieve-an-invoice Retrieve an Invoice
     * @link https://developer.bitpay.com/reference/retrieve-an-invoice-by-guid Retrieve an Invoice by GUID
     *
     * @param $invoiceId   string|null The ID of the invoice to retrieve.
     * @param $invoiceGuid string|null The GUID of the invoice to retrieve.
     * @param $facade      string Facade to use when retrieving invoice.
     *
     * @return Invoice A BitPay Invoice object.
     * @throws BitPayException
     */
    public static function getInvoice(?string $invoiceId = null, ?string $invoiceGuid = null, string $facade = Facade::MERCHANT): Invoice
    {
        $signRequest = $facade !== Facade::POS;
        
        if ($invoiceId) {
            return (new self())->client->getInvoice($invoiceId, $facade, $signRequest);
        } elseif ($invoiceGuid) {
            return (new self())->client->getInvoiceByGuid($invoiceGuid, $facade, $signRequest);
        }

        BitPayExceptionProvider::throwValidationException("A valid Invoice ID or GUID is required");
    }

    /**
     * Retrieve a collection of BitPay invoices.
     *
     * @link https://developer.bitpay.com/reference/retrieve-invoices-filtered-by-query Retrieve Invoices Filtered by Query
     *
     * @param $dateStart string The start of the date window to query for invoices. Format YYYY-MM-DD.
     * @param $dateEnd   string The end of the date window to query for invoices. Format YYYY-MM-DD.
     * @param $status    string|null The invoice status you want to query on.
     * @param $orderId   string|null The optional order ID specified at time of invoice creation.
     * @param $limit     int|null Maximum results that the query will return (useful for paging results).
     * @param $offset    int|null Number of results to offset (ex. skip 10 will give you results starting with the 11th result).
     *
     * @return Invoice[] A list of BitPay Invoice objects.
     * @throws BitPayException
     */
    public static function getInvoices(
        string $dateStart,
        string $dateEnd,
        ?string $status = null,
        ?string $orderId = null,
        ?int    $limit = null,
        ?int    $offset = null
    ): array {
        return (new self())->client->getInvoices($dateStart, $dateEnd, $status, $orderId, $limit, $offset);
    }

    /**
     * Cancel a BitPay invoice by its ID or GUID.
     *
     * @link https://developer.bitpay.com/reference/cancel-an-invoice Cancel an Invoice
     * @link https://developer.bitpay.com/reference/cancel-an-invoice-by-guid Cancel an Invoice by GUID
     *
     * @param $invoiceId   string|null The ID of the invoice to cancel.
     * @param $invoiceGuid string|null The GUID of the invoice to cancel.
     * @param $forceCancel bool Indicates canceling the invoice even if no contact information is present.
     *
     * @return Invoice A BitPay Invoice object.
     * @throws BitPayException
     */
    public static function cancelInvoice(?string $invoiceId = null, ?string $invoiceGuid = null, bool $forceCancel = false): Invoice
    {
        if ($invoiceId) {
            return (new self())->client->cancelInvoice($invoiceId, $forceCancel);
        } elseif ($invoiceGuid) {
            return (new self())->client->cancelInvoiceByGuid($invoiceGuid, $forceCancel);
        }

        BitPayExceptionProvider::throwValidationException("A valid Invoice ID or GUID is required");
    }

    /**
     * Request the last BitPay Invoice webhook to be resent.
     *
     * @link https://developer.bitpay.com/reference/request-an-invoice-webhook-to-be-resent Request an Invoice Webhook to be Resent
     *
     * @param $invoiceId string The ID of the invoice for which you want the last webhook to be resent.
     *
     * @return bool True if the webhook has been resent successfully, false otherwise.
     * @throws  BitPayException
     */
    public static function requestInvoiceWebhook(string $invoiceId, string $invoiceToken): bool
    {
        return (new self())->client->requestInvoiceNotification($invoiceId, $invoiceToken);
    }
}
