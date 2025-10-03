<?php

namespace App\Modules\Report\Handler;

use App\Messenger\DTO\ReportMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Psr\Log\LoggerInterface;
use App\Modules\Report\Interface\ReportServiceInterface;

#[AsMessageHandler]
class ReportHandler
{
    public function __construct(
        private MessageBusInterface $bus,
        private LoggerInterface $logger,
        private ReportServiceInterface $reportService
    ) {}

    public function __invoke(ReportMessage $message): void
    {
        $this->logger->info('[ReportHandler:__invoke]');
        $this->logger->info('[ReportHandler:__invoke]', [
            'type' => $message->type, 
            'message' => $message->getMessage()->message()
        ]);
        try {
            // отправляем уведомление
            $this->reportService->sendMessage($message->getMessage()->message());
        } catch (\Exception $e) {
            $this->logger->error('[ReportHandler:__invoke] Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}