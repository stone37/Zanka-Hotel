<?php

namespace App\Notifier\Transport;
;
use Symfony\Component\Notifier\Exception\UnsupportedSchemeException;
use Symfony\Component\Notifier\Transport\AbstractTransportFactory;
use Symfony\Component\Notifier\Transport\Dsn;

class HSmsTransportFactory extends AbstractTransportFactory
{
    protected function getSupportedSchemes(): array
    {
        return ['hsms'];
    }

    public function create(Dsn $dsn): HSmsTransport
    {
        $scheme = $dsn->getScheme();

        if ('hsms' !== $scheme) {
            throw new UnsupportedSchemeException($dsn, 'hsms', $this->getSupportedSchemes());
        }

        $user = $this->getUser($dsn);
        $password = $this->getPassword($dsn);
        $token = $dsn->getRequiredOption('token');
        $host = 'default' === $dsn->getHost() ? null : $dsn->getHost();
        $port = $dsn->getPort();

        return (new HSmsTransport($user, $password, $token, $this->client, $this->dispatcher))->setHost($host)->setPort($port);
    }
}