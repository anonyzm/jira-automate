<?php

namespace App\Modules\Report\DTO;

use Stringable;

class PachkaMessage implements Stringable
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