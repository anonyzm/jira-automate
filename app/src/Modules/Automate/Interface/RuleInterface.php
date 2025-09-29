<?php

namespace App\Modules\Automate\Interface;

use App\Entity\Task\Task;

interface RuleInterface
{
    public function isApplicable(Task $task): bool;
    public function apply(Task $task): void;
}


