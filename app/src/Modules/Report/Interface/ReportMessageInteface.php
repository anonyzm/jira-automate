<?php

namespace App\Modules\Report\Interface;

use App\Modules\Report\Entity\ReportMessage;

interface ReportMessageInteface
{
    public function json(): ReportMessage;
}