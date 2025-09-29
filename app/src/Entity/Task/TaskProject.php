<?php

namespace App\Entity\Task;

use App\Interface\ArrayableInterface;

class TaskProject implements ArrayableInterface
{
    public function __construct(
        public string $id = '',
        public string $name = '',
        public string $key = '',
    ) {}

    public function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? '',
            $data['name'] ?? '',
            $data['key'] ?? ''
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'key' => $this->key,
        ];
    }
}