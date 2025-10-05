<?php

namespace App\Messenger\Middleware;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;

class LoggerMiddleware implements MiddlewareInterface
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        try {
            $messageClass = get_class($envelope->getMessage());

            if (null === $envelope->last(ReceivedStamp::class)) {
                $this->logger->info("Dispatching message: {$messageClass}");
            } else {
                $this->logger->info("Handling message: {$messageClass}");
            }            

            return $stack->next()->handle($envelope, $stack);
        } catch (\Throwable $e) {
            $this->logger->error("Error handling message: {$e->getMessage()}", ['trace' => $e->getTraceAsString()]);
            throw $e;
        }
    }
}