<?php

namespace App\Modules\Report\Handler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Psr\Log\LoggerInterface;
use App\Modules\Report\Interface\ReportMessageInteface;
use App\Modules\Report\Interface\ReportServiceInterface;

#[AsMessageHandler]
class ReportHandler
{
    public function __construct(
        private MessageBusInterface $bus,
        private LoggerInterface $logger,
        private ReportServiceInterface $reportService
    ) {}

    public function __invoke(ReportMessageInteface $message): void
    {
        $this->logger->info('[ReportHandler:__invoke]');
        try {
            $this->logger->info('[ReportHandler:__invoke]', [
                'message' => $message->json(),
            ]);
            // отправляем уведомление
            $this->reportService->sendMessage($message);
        } catch (\Exception $e) {
            $this->logger->error('[ReportHandler:__invoke] Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}