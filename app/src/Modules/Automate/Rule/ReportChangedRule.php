<?php

namespace App\Modules\Automate\Rule;

use App\Modules\Automate\Interface\RuleInterface;
use App\Entity\Task\Task;
use Psr\Log\LoggerInterface;

class ReportChangedRule implements RuleInterface 
{
    public function __construct(
        protected LoggerInterface $logger,
    ) {}

    public function isApplicable(Task $task): bool
    {
        return true;
    }

    public function apply(Task $task): void
    {
        $this->logger->info('[ReportChangedRule:apply]', ['task' => $task]);
    }
}