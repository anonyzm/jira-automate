<?php

namespace App\Entity\Task;

use App\Interface\ArrayableInterface;

class TaskType implements ArrayableInterface
{
    public function __construct(
        public string $id = '',
        public string $name = '',
        public string $description = '',
    ) {}

    public function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? '',
            $data['name'] ?? '',
            $data['description'] ?? ''
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
        ];
    }
}