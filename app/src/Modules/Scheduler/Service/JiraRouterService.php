<?php
namespace App\Modules\Scheduler\Service;

use App\Modules\Scheduler\Interface\RouterInterface;
use Psr\Log\LoggerInterface;    
use Symfony\Component\Messenger\MessageBusInterface;
use Ramsey\Uuid\Uuid;
use App\Messenger\DTO\AutomateMessage;
use App\Entity\JiraData;

class JiraRouterService implements RouterInterface
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly MessageBusInterface $bus, 
        private readonly string $jiraProjectId,
        private readonly string $jiraTestMode,
        private readonly string $jiraTestTag,
        private readonly string $jiraAiTag,
        private readonly string $jiraAiBaseStatus,
    ) {}

    public function route(JiraData $jiraData): void
    {
        $uuid = (string) Uuid::uuid4();
        $this->logger->info('[route:begin]', ['uuid' => $uuid, 'data' => print_r($jiraData, true)]);

        // проверяем, нужно ли автоматизировать таск через ИИ
        if ($this->isAIable($jiraData)) {
            $this->logger->info('[route:aiable]', ['uuid' => $uuid, 'data' => print_r($jiraData, true)]);

            throw new \Exception('AI automatization is not supported yet');
        } 
        // проверяем, нужно ли автоматизировать таск через правила
        else if ($this->isAutomatable($jiraData)) {
            $this->logger->info('[route:automatable]', ['uuid' => $uuid, 'data' => print_r($jiraData, true)]);
            // отправляем сообщение в очередь для автоматизации
            $this->bus->dispatch(
                new AutomateMessage(
                    uuid: $uuid,
                    data: $jiraData
                )
            );
        }

        $this->logger->info('[route:end]', ['uuid' => $uuid, 'data' => print_r($jiraData, true)]);       
    }

    // {
    //     "testMode":true,
    //     "aiTag":"ai-tag",
    //     "testTag":"test-auto",
    //     "taskTags":[
    //         "ai-tag",
    //         "test-auto"
    //     ],
    //     "projectId":"10000",
    //     "taskProjectId":"10000"
    // }
    private function isAutomatable(JiraData $data): bool 
    {
        $this->logger->info('[route:isAutomatable]', [
            'testMode' => (bool) $this->jiraTestMode,
            'aiTag' => $this->jiraAiTag,
            'testTag' => $this->jiraTestTag,
            'taskTags' => $data->task->tags,
            'projectId' => $this->jiraProjectId,
            'taskProjectId' => $data->task->project->id,
            '1-st' => ((bool) $this->jiraTestMode && in_array($this->jiraTestTag, $data->task->tags)),
            '2-nd' => !((bool) $this->jiraTestMode),
            '3-rd' => $data->task->project->id === $this->jiraProjectId,
            '4-th' => !in_array($this->jiraAiTag, $data->task->tags),
        ]);        
        return (((bool) $this->jiraTestMode && in_array($this->jiraTestTag, $data->task->tags)) // указан тестовый тег в тестовом режиме
            || !((bool) $this->jiraTestMode)) // выключен тестовый режим (автоматизация для всех тасков проекта)
            && $data->task->project->id === $this->jiraProjectId // таск из нужного проекта
            && !in_array($this->jiraAiTag, $data->task->tags); // таск не имеет тега для AI обработки
    }

    private function isAIable(JiraData $data): bool 
    {
        return $data->task->project->id === $this->jiraProjectId // таск из нужного проекта
            && in_array($this->jiraAiTag, $data->task->tags) // таск имеет тег для AI обработки
            && $data->task->status->id === $this->jiraAiBaseStatus; // таск в базовом статусе (новая задача)
    }
}