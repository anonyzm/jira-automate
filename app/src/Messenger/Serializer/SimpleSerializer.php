<?php

namespace App\Messenger\Serializer;

use App\Messenger\Serializer\JsonSerializer;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use Symfony\Component\Messenger\Stamp\MessageDecodingFailedStamp;
use Symfony\Component\Messenger\Stamp\NonSendableStampInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use App\Messenger\EnvelopeWrapper;

/**
 * @author Ryan Weaver<ryan@symfonycasts.com>
 */
class SimpleSerializer implements SerializerInterface
{
    private JsonSerializer $jsonSerializer;

    public function __construct()
    {
        $this->jsonSerializer = new JsonSerializer();
    }

    public function decode(array $encodedEnvelope): Envelope
    {
        if (empty($encodedEnvelope['body'])) {
            throw new MessageDecodingFailedException('Encoded envelope should have at least a "body", or maybe you should implement your own serializer.');
        }

        return $this->safelyUnserialize($encodedEnvelope['body']);
    }

    public function encode(Envelope $envelope): array
    {
        $envelope = $envelope->withoutStampsOfType(NonSendableStampInterface::class);
        $messageWrapper = new EnvelopeWrapper($this->jsonSerializer->serialize($envelope->getMessage()));
        $newEnvelope = new Envelope($messageWrapper);       

        $body = serialize($newEnvelope);

        return [
            'body' => $body,
        ];
    }

    private function safelyUnserialize(string $contents): Envelope
    {
        if ('' === $contents) {
            throw new MessageDecodingFailedException('Could not decode an empty message using PHP serialization.');
        }
        try {
            /** @var Envelope */
            $envelope = unserialize($contents);
        } catch (\Throwable $e) {
            if ($e instanceof MessageDecodingFailedException) {
                throw $e;
            }

            throw new MessageDecodingFailedException('Could not decode Envelope: '.$e->getMessage(), 0, $e);
        } 

        if (!$envelope instanceof Envelope) {
            throw new MessageDecodingFailedException('Could not decode message into an Envelope.');
        }


        $envelopeWrapper = $envelope->getMessage();
        $message = $this->jsonSerializer->deserialize($envelopeWrapper->serializedMessage);
        $newEnvelope = new Envelope($message);

        if ($newEnvelope->getMessage() instanceof \__PHP_Incomplete_Class) {
            $newEnvelope = $newEnvelope->with(new MessageDecodingFailedStamp());
        }

        return $newEnvelope;
    }
}
