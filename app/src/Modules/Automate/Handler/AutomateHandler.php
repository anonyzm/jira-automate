<?php

namespace App\Modules\Automate\Handler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Psr\Log\LoggerInterface;
use App\Messenger\DTO\AutomateMessage;
use App\Modules\Automate\Service\AutomateService;

#[AsMessageHandler]
class AutomateHandler
{
    public function __construct(
        protected MessageBusInterface $bus,
        protected LoggerInterface $logger,
        protected AutomateService $automateService
    ) {}

    public function __invoke(AutomateMessage $message): void
    {
        try {
            $this->logger->info('[AutomateHandler:__invoke]', [
                'uuid' => $message->uuid, 
                'task' => print_r($message->data, true),
            ]);
            // обрабатываем таск согласно правилам
            $this->automateService->automate($message->data);
        } catch (\Exception $e) {
            $this->logger->error('[AutomateHandler:__invoke] Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}