<?php

namespace App\Messenger\Handler;

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
        $this->logger->info('[AutomateHandler:__invoke]', [
            'uuid' => $message->uuid, 
            'task' => $message->task
        ]);
        // обрабатываем таск согласно правилам
        $this->automateService->automate($message->task);
    }
}