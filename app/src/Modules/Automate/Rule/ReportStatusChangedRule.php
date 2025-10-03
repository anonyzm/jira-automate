<?php

namespace App\Modules\Automate\Rule;

use App\Modules\Automate\Interface\RuleInterface;
use App\Entity\JiraData;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;    
use App\Entity\ChangeLog;
use App\Messenger\DTO\ReportMessage;    
use App\Modules\Report\Entity\TaskStatusChangedMessage;

class ReportStatusChangedRule implements RuleInterface 
{
    const STATUS_ATTRIBUTE = 'status';
    const DEFAULT_RULE_TAG = 'HOTFIX';
    
    public function __construct(
        private LoggerInterface $logger,
        private readonly MessageBusInterface $bus,
        private readonly string $statusAttribute = self::STATUS_ATTRIBUTE,
        private readonly array $ruleTags = [self::DEFAULT_RULE_TAG]
    ) {}

    public function isApplicable(JiraData $data): bool
    {
        $hasTag = false;
        $isStatusChanged = false;
        // проверяем что у таска есть хотя бы один из нужных лейблов
        foreach ($this->ruleTags as $tag) {
            if (in_array($tag, $data->task->tags)) {
                $hasTag = true;
                break;
            }
        }
        // проверяем что статус таска изменился
        foreach ($data->changeLog as $change) {
            /** @var ChangeLog $change */
            if ($change->field === $this->statusAttribute) {
                $isStatusChanged = true;
                break;
            }
        }
        return $hasTag && $isStatusChanged;
    }

    public function apply(JiraData $data): void
    {
        $this->logger->info('[ReportStatusChangedRule:apply]', ['data' => $data->toArray()]);

        $oldStatus = '';
        $newStatus = '';
        foreach ($data->changeLog as $change) {
            /** @var ChangeLog $change */
            if ($change->field === $this->statusAttribute) {
                $oldStatus = $change->fromString;
                $newStatus = $change->toString;
                break;
            }
        }

        $message = (new TaskStatusChangedMessage())->fromArray([
            'taskCode' => $data->task->key,
            'taskTitle' => $data->task->title,
            'taskLink' => $data->task->link,
            'taskUser' => $data->jiraUser->displayName,
            'taskStatusOld' => $oldStatus,
            'taskStatusNew' => $newStatus,
        ]);

        $this->logger->info('[ReportStatusChangedRule:apply]', ['message' => (string) $message->message()]);

         // отправляем сообщение в очередь для автоматизации
        $this->bus->dispatch(
            new ReportMessage(
                type: $message::class,
                message: $message
        ));
    }
}