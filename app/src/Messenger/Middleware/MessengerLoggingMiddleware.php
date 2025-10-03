<?php
    namespace App\Messenger\Middleware;

    use Psr\Log\LoggerInterface;
    use Symfony\Component\Messenger\Envelope;
    use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
    use Symfony\Component\Messenger\Middleware\StackInterface;

    /**
     * Summary of MessengerLoggingMiddleware
     * @deprecated 
     */
    class MessengerLoggingMiddleware implements MiddlewareInterface
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
                $this->logger->info(sprintf('Dispatching message: %s', $messageClass));

                // Call the next middleware in the stack
                $envelope = $stack->next()->handle($envelope, $stack);

                $this->logger->info(sprintf('Message handled: %s', $messageClass));
            } catch (\Throwable $e) {
                $this->logger->error(sprintf('Error handling message: %s', $messageClass), [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
            return $envelope;
        }
    }