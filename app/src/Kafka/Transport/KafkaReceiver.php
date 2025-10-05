<?php

namespace App\Kafka\Transport;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\Transport\Receiver\ReceiverInterface;
use Symfony\Component\Messenger\Transport\Serialization\PhpSerializer;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

class KafkaReceiver implements ReceiverInterface
{
    private SerializerInterface $serializer;
    private Connection $connection;

    public function __construct(
        Connection $connection, 
        SerializerInterface $serializer = null,
        private LoggerInterface $logger
        )
    {
        $this->connection = $connection;
        $this->serializer = $serializer ?? new PhpSerializer();
    }

    /** @psalm-return array<Envelope> */
    public function get(): iterable
    {
        yield from $this->getEnvelope();
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    public function ack(Envelope $envelope): void
    {
        // no ack method for kafka transport
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    public function reject(Envelope $envelope): void
    {
        // no reject method for kafka transport
    }

    /** @psalm-return array<Envelope> */
    private function getEnvelope(): iterable
    {
        try {
            $kafkaMessage = $this->connection->get();       

            if (RD_KAFKA_RESP_ERR_NO_ERROR !== $kafkaMessage->err) {
                switch ($kafkaMessage->err) {
                    case RD_KAFKA_RESP_ERR__PARTITION_EOF: // No more messages
                    case RD_KAFKA_RESP_ERR__TIMED_OUT: // Attempt to connect again
                        return;
                    default:
                        throw new TransportException($kafkaMessage->errstr(), $kafkaMessage->err);
                }
            }

            $this->logger->info('KafkaReceiver:getEnvelop Success', [
                'payload' => $kafkaMessage->payload,
                'headers' => $kafkaMessage->headers,
            ]);
        } catch (\Throwable $exception) {
            $this->logger->error('KafkaReceiver:getEnvelop Error', [
                'error' => $exception->getMessage(),
                'stacktrace' => $exception->getTraceAsString(),
            ]);
            throw new TransportException($exception->getMessage(), 0, $exception);
        }

        try {
            yield $this->serializer->decode([
                'body' => $kafkaMessage->payload,
                'headers' => $kafkaMessage->headers,
            ]);
        } catch (\Throwable $exception) {
            $this->logger->error('KafkaReceiver:getEnvelop Serializer Error', [
                'error' => $exception->getMessage(),
                'stacktrace' => $exception->getTraceAsString(),
                'payload' => $kafkaMessage->payload,
                'headers' => $kafkaMessage->headers,
            ]);
            throw new TransportException($exception->getMessage(), 0, $exception);
        }
    }
}
