<?php

namespace App\Messenger\DTO;

use App\Entity\JiraData;

class AutomateMessage
{
    public function __construct(
        public string $uuid = '',
        public JiraData $data = new JiraData(),
    ) {}
}
