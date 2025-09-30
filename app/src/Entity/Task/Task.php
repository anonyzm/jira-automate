<?php

namespace App\Entity\Task;

use App\Interface\ArrayableInterface;

class Task implements ArrayableInterface
{
    public string $link = '';

    public function __construct(
        public string $id = '',
        public string $key = '',
        public string $title = '',
        public string $description = '',
        public array $tags = [],
        public ?TaskType $type = null,
        public ?TaskUser $assignee = null,
        public ?TaskProject $project = null,
        public ?TaskStatus $status = null
    ) {}

    public function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? '',
            $data['key'] ?? '',
            $data['fields']['summary'] ?? '',
            $data['fields']['description'] ?? '',
            $data['fields']['labels'] ?? [],
            (new TaskType())->fromArray($data['fields']['issuetype']),
            (new TaskUser())->fromArray($data['fields']['assignee']),
            (new TaskProject())->fromArray($data['fields']['project']),
            (new TaskStatus())->fromArray($data['fields']['status'])
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'key' => $this->key,
            'title' => $this->title,
            'description' => $this->description,
            'link' => $this->link,
            'tags' => $this->tags,
            'type' => $this->type->toArray(),
            'assignee' => $this->assignee->toArray(),
            'project' => $this->project->toArray(),
            'status' => $this->status->toArray(),
        ];
    }
}