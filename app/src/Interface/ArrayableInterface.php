<?php

namespace App\Interface;

interface ArrayableInterface
{
    public function fromArray(array $data): self;
    public function toArray(): array;
}
