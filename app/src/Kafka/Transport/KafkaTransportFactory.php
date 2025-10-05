<?php

namespace App\Kafka\Transport;

use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Psr\Log\LoggerInterface;

class KafkaTransportFactory implements TransportFactoryInterface
{
    function __construct(
        private LoggerInterface $logger
    ) { }
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @psalm-param array<string, bool|float|int|string|array<string>> $options
     */
    public function createTransport(string $dsn, array $options, SerializerInterface $serializer): TransportInterface
    {
        return new KafkaTransport(Connection::builder($options), $serializer, $this->logger);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @psalm-param array<string, bool|float|int|string|array<string>> $options
     */
    public function supports(string $dsn, array $options): bool
    {
        return str_starts_with($dsn, 'kafka://');
    }
}
