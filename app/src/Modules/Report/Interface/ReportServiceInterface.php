<?php

namespace App\Modules\Report\Interface;

interface ReportServiceInterface
{
    public function sendMessage(ReportMessageInteface $reportMessage): void;
}