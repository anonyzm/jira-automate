<?php

namespace App\Modules\Report\Entity;

use App\Modules\Report\Interface\ReportMessageInterface;

class EmptyMessage implements ReportMessageInterface
{
    public function message(): string
    {
        return '';
    }
}

