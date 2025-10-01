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
                /** @var RuleInterface $rule */
                $rule = $this->container->get($ruleClass);

                if ($rule->isApplicable($data)) {
                    $rule->apply($data);
                    $this->logger->info('[AutomateService:automate] Rule applied', [
                        'rule' => $ruleClass,
                        'data' => $dataArray
                    ]);

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