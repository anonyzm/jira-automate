<?php

namespace App\Messenger\DTO;

use App\Entity\Task\Task;

class AutomateMessage
{
    public function __construct(
        public string $uuid,
        public Task $task
    ) {}
}
