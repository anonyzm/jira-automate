<?php

namespace App\Modules\Report\Service;

use App\Modules\Report\DTO\PachkaMessage;
use Psr\Log\LoggerInterface;
use GuzzleHttp\Client;
use App\Modules\Report\Interface\ReportServiceInterface;

class PachkaService implements ReportServiceInterface
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
        $this->logger->info('[PachkaService:sendMessage]', ['message' => $message]);
        $this->post((string) $message);
    }

    private function post(string $message): bool
    {
        try {
            $response = $this->client->post($this->pachkaWebhook, [
                'json' => [
                    'message' => $message
                ]
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