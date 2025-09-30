<?php

namespace App\Modules\Report\Entity;

use Stringable;

class ReportMessage implements Stringable
{
    public function __construct(
        private string $message,
    ) {}

    public function __toString(): string
    {
        return json_encode([
            'message' => $this->message,
        ]);
    }        
}