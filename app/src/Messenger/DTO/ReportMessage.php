<?php

namespace App\Messenger\DTO;

use App\Messenger\JsonSerializer;
use App\Modules\Report\Interface\ReportMessageInterface;

class ReportMessage
{
    private JsonSerializer $jsonSerializer;
    public string $serializedMessage = '';

    public function __construct(
        public readonly string $type = '',
        private readonly ReportMessageInterface $message,        
    ) {
        $this->jsonSerializer = new JsonSerializer();
        $this->serializedMessage = $this->jsonSerializer->serialize($this->message);
    }

    /**
     * Summary of getMessage
     * @return ReportMessageInterface
     */
    public function getMessage(): ReportMessageInterface
    {
        return $this->jsonSerializer->deserialize($this->serializedMessage);
    }
}
