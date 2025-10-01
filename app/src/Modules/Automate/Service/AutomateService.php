<?php

namespace App\Modules\Automate\Service;

use App\Entity\JiraData;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Modules\Automate\Interface\RuleInterface;
use App\Modules\Automate\Rule\ReportStatusChangedRule;

class AutomateService { 
    private array $rules = [
        ReportStatusChangedRule::class,
    ];

    public function __construct(
        protected LoggerInterface $logger,
        protected ContainerInterface $container
    ) {}

    public function automate(JiraData $data): void
    {
        $dataArray = $data->toArray();
        $this->logger->info('[AutomateService:automate]', [
            'data' => $dataArray,
        ]);

        foreach ($this->rules as $ruleClass) {
            try {
                $this->logger->info('[AutomateService:checkingRule:1]', ['ruleClass' => $ruleClass]);
                /** @var RuleInterface $rule */
                $rule = $this->container->get($ruleClass);
                $this->logger->info('[AutomateService:checkingRule:2]', ['ruleClass' => get_class($rule)]);

                /** @var RuleInterface $rule */
                if ($rule->isApplicable($data)) {
                    $this->logger->info('[AutomateService:checkingRule:3]', ['ruleClass' => $ruleClass]);

                    // $this->logger->info('[AutomateService:automate] Applying rule', [
                    //     'rule' => $ruleClass,
                    //     'data' => $dataArray
                    // ]);
                    $rule->apply($data);
                    // $this->logger->info('[AutomateService:automate] Rule applied', [
                    //     'rule' => $ruleClass,
                    //     'data' => $dataArray
                    // ]);
                    $this->logger->info('[AutomateService:checkingRule:4]', ['ruleClass' => $ruleClass]);

                }
            } catch (\Exception $e) {
                $this->logger->error('[AutomateService:automate] Error applying rule', [
                    'rule' => $ruleClass,
                    'error' => $e->getMessage(),
                    'stacktrace' => $e->getTraceAsString(),
                    'data' => $dataArray
                ]);
            }
        }
    }
}