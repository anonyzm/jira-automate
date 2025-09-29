<?php

namespace App\Entity\Task;

use App\Interface\ArrayableInterface;

class Task implements ArrayableInterface
{
    public function __construct(
        public string $id = '',
        public string $key = '',
        public string $title = '',
        public string $description = '',
        public array $tags = [],
        public ?TaskType $type = null,
        public ?TaskUser $creator = null,
        public ?TaskUser $assignee = null,
        public ?TaskProject $project = null,
        public ?TaskStatus $status = null
    ) {}

    public function fromArray(array $data): self
    {
        return new self(
            $data['issue']['id'] ?? '',
            $data['issue']['key'] ?? '',
            $data['issue']['fields']['summary'] ?? '',
            $data['issue']['fields']['description'] ?? '',
            $data['issue']['fields']['labels'] ?? [],
            (new TaskType())->fromArray($data['issue']['fields']['issuetype']),
            (new TaskUser())->fromArray($data['user']),
            (new TaskUser())->fromArray($data['user']),
            (new TaskProject())->fromArray($data['issue']['fields']['project']),
            (new TaskStatus())->fromArray($data['issue']['fields']['status'])
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'key' => $this->key,
            'title' => $this->title,
            'description' => $this->description,
            'tags' => $this->tags,
            'type' => $this->type->toArray(),
            'creator' => $this->creator->toArray(),
            'assignee' => $this->assignee->toArray(),
            'project' => $this->project->toArray(),
            'status' => $this->status->toArray(),
        ];
    }
}