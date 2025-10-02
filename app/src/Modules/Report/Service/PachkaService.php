<?php

namespace App\Modules\Report\Service;

use App\Modules\Report\DTO\PachkaMessage;
use Psr\Log\LoggerInterface;
use GuzzleHttp\Client;

class PachkaService
{       
    private Client $client;

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly string $pachkaWebhook,
    ) {
        $this->client = new Client();
    }

    public function sendMessage(string $message): void
    {
        $pachkaMessage = new PachkaMessage($message);
        $this->logger->info('[PachkaService:sendMessage]', ['message' => $pachkaMessage]);
        $this->post((string) $pachkaMessage);
    }

    private function post(string $message): bool
    {
        try {
            $response = $this->client->post($this->pachkaWebhook, [
                'json' => $message
            ]);
            $this->logger->info('[PachkaService:post]', [
                'status' => $response->getStatusCode(), 
                'response' => $response->getBody()
            ]);
        } catch (\Exception $e) {
            $this->logger->error('[PachkaService:post]', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
        return $response->getStatusCode() === 200;
    }
}