<?php

namespace App\Messenger;

class EnvelopeWrapper
{
    public function __construct(
        public string $serializedMessage,
    ) {}
}
