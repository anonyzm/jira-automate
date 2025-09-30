<?php

namespace App\Entity;

use App\Interface\ArrayableInterface;

// {
//  "id":"10300",
//  "items":[
//      {
//          "field":"status",
//          "fieldtype":"jira",
//          "from":"10001",
//          "fromString":"Done",
//          "to":"10100",
//          "toString":"Canceled"
//      }
//  ]
// }
class ChangeLog implements ArrayableInterface
{
    public function __construct(
        public string $field = '',
        public string $fieldtype = '',
        public string $from = '',
        public string $fromString = '',
        public string $to = '',
        public string $toString = '',
    ) {}

    public function fromArray(array $data): self
    {
        return new self(
            $data['field'], 
            $data['fieldtype'], 
            $data['from'], 
            $data['fromString'], 
            $data['to'], 
            $data['toString']
        );
    }

    public function toArray(): array
    {
        return [
            'field' => $this->field,
            'fieldtype' => $this->fieldtype,
            'from' => $this->from,
            'fromString' => $this->fromString,
            'to' => $this->to,
            'toString' => $this->toString,
        ];
    }
}