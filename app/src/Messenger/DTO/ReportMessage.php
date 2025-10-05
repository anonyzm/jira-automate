<?php

namespace App\Messenger\DTO;

use App\Modules\Report\Interface\ReportMessageInterface;
use App\Modules\Report\Entity\EmptyMessage;

class ReportMessage
{
    public function __construct(
        public string $type = '',
        public ReportMessageInterface $message = new EmptyMessage(),        
    ) {}
}
