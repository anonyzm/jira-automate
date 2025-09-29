<?php

namespace App\Modules\Scheduler\Interface;

use App\Entity\Task\Task;

interface TaskRouterInterface
{
    public function routeTask(Task $task): void;
}
