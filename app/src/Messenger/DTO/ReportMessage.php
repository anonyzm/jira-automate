<?php

namespace App\Messenger\DTO;

use App\Modules\Report\Interface\ReportMessageInteface;

class ReportMessage
{
    public function __construct(
        public readonly ReportMessageInteface $message
    ) {}
}
