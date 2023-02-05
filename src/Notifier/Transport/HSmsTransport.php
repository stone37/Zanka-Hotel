<?php

namespace App\Notifier\Transport;

use Symfony\Component\Notifier\Exception\TransportException;
use Symfony\Component\Notifier\Exception\UnsupportedMessageTypeException;
use Symfony\Component\Notifier\Message\MessageInterface;
use Symfony\Component\Notifier\Message\SentMessage;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\Transport\AbstractTransport;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HSmsTransport extends AbstractTransport
{
    protected const HOST = 'hsms.ci';

    private string $clientID;
    private string $clientSecret;
    private string $token;

    public function __construct(string $clientID, string $clientSecret, string $token, HttpClientInterface $client = null, EventDispatcherInterface $dispatcher = null)
    {
        $this->clientID = $clientID;
        $this->clientSecret = $clientSecret;
        $this->token = $token;

        parent::__construct($client, $dispatcher);
    }
    protected function doSend(MessageInterface $message): SentMessage
    {
        if (!$message instanceof SmsMessage) {
            throw new UnsupportedMessageTypeException(__CLASS__, SmsMessage::class, $message);
        }

        $url = 'https://'.$this->getEndpoint().'/api/envoi-sms/';
        $headers = [
            'Authorization' => 'Bearer ' . $this->token,
            'Content-Type' => 'application/form-data'
        ];

        $response = $this->client->request('POST', $url, [
            'headers' => $headers,
            'body' => [
                'clientid' => $this->clientID,
                'clientsecret' => $this->clientSecret,
                'telephone' => $message->getPhone(),
                'message' => $message->getSubject()
            ]
        ]);

        if (201 !== $response->getStatusCode()) {
            $content = $response->toArray(false);
            $errorMessage = $content['requestError']['serviceException']['messageId'] ?? '';
            $errorInfo = $content['requestError']['serviceException']['text'] ?? '';

            throw new TransportException(sprintf('Unable to send the SMS: "%s" (%s).', $errorMessage, $errorInfo), $response);
        }

        return new SentMessage($message, (string) $this);
    }

    public function supports(MessageInterface $message): bool
    {
        return $message instanceof SmsMessage;
    }

    public function __toString(): string
    {
        return sprintf('hsms://%s', $this->getEndpoint());
    }
}
