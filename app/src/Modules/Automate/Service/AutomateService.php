<?php

namespace App\Modules\Automate\Service;

use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;
use App\Entity\Task\Task;
use App\Modules\Automate\Rule\ReportChangedRule;
use App\Modules\Automate\Interface\RuleInterface;

class AutomateService {
    private array $rules = [
        ReportChangedRule::class,
    ];

    public function __construct(
        protected LoggerInterface $logger,
        protected ContainerInterface $container,
    ) {}

    public function automate(Task $task): void
    {
        $this->logger->info('[AutomateService:automate]', ['task' => $task]);
        
        foreach ($this->rules as $ruleClass) {
            try {
                /** @var RuleInterface $rule */
                $rule = $this->container->get($ruleClass);
                
                if ($rule->isApplicable($task)) {
                    $rule->apply($task);
                    $this->logger->info('[AutomateService:automate] Rule applied', [
                        'rule' => $ruleClass,
                        'task' => $task
                    ]);
                }
            } catch (\Exception $e) {
                $this->logger->error('[AutomateService:automate] Error applying rule', [
                    'rule' => $ruleClass,
                    'error' => $e->getMessage(),
                    'task' => $task
                ]);
            }
        }
    }
}