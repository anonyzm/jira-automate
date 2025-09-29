<?php
namespace App\Modules\Scheduler\Service;

use App\Modules\Scheduler\Interface\TaskRouterInterface;
use Psr\Log\LoggerInterface;    
use App\Entity\Task\Task;
use Symfony\Component\Messenger\MessageBusInterface;
use Ramsey\Uuid\Uuid;
use App\Messenger\DTO\AutomateMessage;

class JiraRouterService implements TaskRouterInterface
{
    public function __construct(
        private readonly MessageBusInterface $bus, 
        private readonly LoggerInterface $logger,
        private readonly string $jiraProjectId,
        private readonly string $jiraTestMode,
        private readonly string $jiraTestTag,
        private readonly string $jiraAiTag,
        private readonly string $jiraAiBaseStatus,
    ) {}

    public function routeTask(Task $task): void
    {
        $uuid = (string) Uuid::uuid4();
        $this->logger->info('[routeTask:begin]', ['uuid' => $uuid, 'task' => $task]);

        // проверяем, нужно ли автоматизировать таск через ИИ
        if ($this->isAIable($task)) {
            $this->logger->info('[routeTask:aiable]', ['uuid' => $uuid, 'task' => $task]);

            throw new \Exception('AI automatization is not supported yet');
        } 
        // проверяем, нужно ли автоматизировать таск через правила
        else if ($this->isAutomatable($task)) {
            $this->logger->info('[routeTask:automatable]', ['uuid' => $uuid, 'task' => $task]);
            // отправляем сообщение в очередь для автоматизации
            $this->bus->dispatch(
                new AutomateMessage(
                    uuid: $uuid,
                    task: $task
                )
            );
        }

        $this->logger->info('[routeTask:end]', ['uuid' => $uuid, 'task' => $task]);       
    }

    private function isAutomatable(Task $task): bool 
    {
        return (((bool) $this->jiraTestMode && in_array($this->jiraTestTag, $task->tags)) // указан тестовый тег в тестовом режиме
            || !((bool) $this->jiraTestMode)) // выключен тестовый режим (автоматизация для всех тасков проекта)
            && $task->project->id === $this->jiraProjectId // таск из нужного проекта
            && !in_array($this->jiraAiTag, $task->tags); // таск не имеет тега для AI обработки
    }

    private function isAIable(Task $task): bool 
    {
        return $task->project->id === $this->jiraProjectId // таск из нужного проекта
            && in_array($this->jiraAiTag, $task->tags) // таск имеет тег для AI обработки
            && $task->status->id === $this->jiraAiBaseStatus; // таск в базовом статусе (новая задача)
    }
}