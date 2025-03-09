<?php

namespace Vrajroham\LaravelBitpay\Actions;

use BitPaySDK\Exceptions\BitPayException;
use BitPaySDK\Model\Payout\PayoutRecipient;
use BitPaySDK\Model\Payout\PayoutRecipients;
use Vrajroham\LaravelBitpay\Constants\WebhookAutoPopulate;


/**
 * The Recipient resource allows a merchant to invite his clients to signup for a BitPay personal account.
 * To create payouts, merchants will first need to issue email invites using this resource.
 *
 * @link https://developer.bitpay.com/reference/recipients
 */
trait ManageRecipients
{

    /**
     * Construct a BitPay Recipient instance.
     *
     * @param string|null $email           Recipient email address
     * @param string|null $label           For merchant use, pass through - could be customer name or unique reference.
     * @param string|null $notificationURL URL to which BitPay sends webhook notifications to inform the merchant about
     *                                     the status of a given recipient. HTTPS is mandatory.
     *
     * @return PayoutRecipient
     */
    public static function PayoutRecipient(
        ?string $email = null,
        ?string $label = null,
        ?string $notificationURL = null
    ): PayoutRecipient {
        return new PayoutRecipient($email, $label, $notificationURL);
    }

    /**
     * Construct a BitPay Recipients instance.
     *
     * @param PayoutRecipient[] $recipients An array of PayoutRecipient objects
     *
     * @return PayoutRecipients
     */
    public static function PayoutRecipients(array $recipients = []): PayoutRecipients
    {
        return new PayoutRecipients($recipients);
    }

    /**
     * Invite Recipient(s).
     *
     * By default, a merchant can invite a maximum of 1000 distinct recipients via the business account.
     *
     * @link https://developer.bitpay.com/reference/invite-recipients Invite Recipients
     *
     * @param $recipients PayoutRecipients A PayoutRecipients object with one or more PayoutRecipient included.
     *
     * @return PayoutRecipient[] A list of BitPay PayoutRecipient objects.
     * @throws BitPayException
     */
    public static function invitePayoutRecipients(PayoutRecipients $recipients): array
    {
        $thisInstance = new self();

        try {
            foreach ($recipients->getRecipients() as $recipient) {
                if (empty($recipient['notificationURL']) &&
                    in_array(WebhookAutoPopulate::RECIPIENTS, $thisInstance->config['auto_populate_webhook'])) {
                    $recipient['notificationURL'] = route('laravel-bitpay.webhook.capture');
                }
            }
        } catch (\Throwable $exception) {
            // Misconfiguration or route macro not in use
        }

        return $thisInstance->client->submitPayoutRecipients($recipients);
    }

    /**
     * Retrieve a BitPay payout recipient by its ID.
     *
     * @link https://developer.bitpay.com/reference/retrieve-a-recipient Retrieve a Recipient
     *
     * @param $recipientId string The ID of the recipient to retrieve.
     *
     * @return PayoutRecipient A BitPay PayoutRecipient object.
     * @throws BitPayException
     */
    public static function getPayoutRecipient(string $recipientId): PayoutRecipient
    {
        return (new self())->client->getPayoutRecipient($recipientId);
    }

    /**
     * Retrieve recipients by status.
     *
     * @link https://developer.bitpay.com/reference/retrieve-recipients-by-status Retrieve Recipients by Status
     *
     * @param $status     string|null The recipient status you want to query on.
     * @param $limit      int|null Maximum results that the query will return (useful for paging results).
     * @param $offset     int|null Number of results to offset (ex. skip 10 will give you results starting with the 11th result).
     *
     * @return PayoutRecipient[] A list of BitPay PayoutRecipient objects.
     * @throws BitPayException
     */
    public static function getPayoutRecipients(string $status = null, int $limit = null, int $offset = null): array
    {
        return (new self())->client->getPayoutRecipients($status, $limit, $offset);
    }

    /**
     * Update a Recipient.
     *
     * @link https://developer.bitpay.com/reference/update-a-recipient Update a Recipient
     *
     * @param $recipientId string The ID for the recipient to be updated.
     * @param $recipient   PayoutRecipient A PayoutRecipient object with updated parameters defined.
     *
     * @return PayoutRecipient The updated PayoutRecipient object.
     * @throws BitPayException
     */
    public static function updatePayoutRecipient(string $recipientId, PayoutRecipient $recipient): PayoutRecipient
    {
        return (new self())->client->updatePayoutRecipient($recipientId, $recipient);
    }

    /**
     * Remove a recipient
     *
     * @link https://developer.bitpay.com/reference/remove-a-recipient Remove a Recipient
     *
     * @param $recipientId string The ID of the recipient to be removed.
     *
     * @return bool True if the recipient was successfully removed, false otherwise.
     * @throws BitPayException
     */
    public static function removePayoutRecipient(string $recipientId): bool
    {
        return (new self())->client->deletePayoutRecipient($recipientId);
    }

    /**
     * Request a Recipient webhook to be resent.
     *
     * @link https://developer.bitpay.com/reference/request-a-recipient-webhook-to-be-resent Request a Recipient Webhook to be Resent
     *
     * @param  $recipientId string The ID of the recipient for which you want the last webhook to be resent.
     *
     * @return bool True if the webhook has been resent for the current recipient status, false otherwise.
     * @throws BitPayException
     */
    public static function requestPayoutRecipientWebhook(string $recipientId): bool
    {
        return (new self())->client->requestPayoutRecipientNotification($recipientId);
    }

}
