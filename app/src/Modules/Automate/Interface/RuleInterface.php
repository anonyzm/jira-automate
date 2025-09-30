<?php

namespace App\Modules\Automate\Interface;

use App\Entity\JiraData;

interface RuleInterface
{
    public function isApplicable(JiraData $data): bool;
    public function apply(JiraData $data): void;
}


