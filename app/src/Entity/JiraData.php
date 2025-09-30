<?php

namespace App\Entity;

use App\Interface\ArrayableInterface;

use App\Entity\Task\Task;
use App\Entity\JiraUser;
use App\Entity\ChangeLog;

class JiraData implements ArrayableInterface
{
    public function __construct(
        public ?Task $task = null,
        public ?JiraUser $jiraUser = null,
        public array $changeLog = [],
    ) {}

    public function fromArray(array $data): self
    {
        return new self(
            (new Task)->fromArray($data['task']),
            (new JiraUser)->fromArray($data['jiraUser']),
            array_map(
                fn($item) => (new ChangeLog)->fromArray($item), 
                $data['changeLog']
            )
        );
    }
    
    public function toArray(): array
    {
        return [
            'task' => $this->task->toArray(),
            'jiraUser' => $this->jiraUser->toArray(),
            'changeLog' => array_map(
                fn($item) => $item->toArray(), 
                $this->changeLog
            ),
        ];
    }
}