<?php

namespace App\Modules\Scheduler\Interface;

use App\Entity\JiraData;

interface RouterInterface
{
    public function route(JiraData $jiraData): void;
}
