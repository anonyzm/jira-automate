<?php

namespace App\Messenger\DTO;

use App\Messenger\JsonSerializer;
use App\Modules\Report\Interface\ReportMessageInterface;

class ReportMessage
{
    private JsonSerializer $jsonSerializer;
    
    public string $type = '';
    public string $serializedMessage = '';

    public function __construct(
        string $type = '',
        ReportMessageInterface $message,        
    ) {
        $this->type = $type;
        $this->jsonSerializer = new JsonSerializer();
        $this->serializedMessage = $this->jsonSerializer->serialize($message);
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
