<?php

namespace App\Entity;

use App\Interface\ArrayableInterface;

// "user":{
//  "self":"https://jira.workflow.local/rest/api/2/user?username=admin",
//  "name":"admin",
//  "key":"JIRAUSER10000",
//  "emailAddress":"bazoonster@gmail.com",
//  "avatarUrls":{
//      "48x48":"https://www.gravatar.com/avatar/601d0f2ce648a4e92b32ef2f2527f616?d=mm&s=48",
//      "24x24":"https://www.gravatar.com/avatar/601d0f2ce648a4e92b32ef2f2527f616?d=mm&s=24",
//      "16x16":"https://www.gravatar.com/avatar/601d0f2ce648a4e92b32ef2f2527f616?d=mm&s=16",
//      "32x32":"https://www.gravatar.com/avatar/601d0f2ce648a4e92b32ef2f2527f616?d=mm&s=32"
//  },
//  "displayName":"Admin",
//  "active":true,
//  "timeZone":"Europe/Moscow"
// },
class JiraUser implements ArrayableInterface
{
    public function __construct(
        public string $apiUrl = '',
        public string $name = '',
        public string $key = '',
        public string $emailAddress = '',
        // public string $avatarUrl = '',
        public string $displayName = '',
        public string $active = '',
        public string $timeZone = '',
    ) {}

    public function fromArray(array $data): self
    {
        return new self(
            $data['self'], 
            $data['name'], 
            $data['key'], 
            $data['emailAddress'], 
            // reset($data['avatarUrl']), 
            $data['displayName'], 
            $data['active'], 
            $data['timeZone']
        );
    }

    public function toArray(): array
    {
        return [
            'apiUrl' => $this->apiUrl,
            'name' => $this->name,
            'key' => $this->key,
            'emailAddress' => $this->emailAddress,
            // 'avatarUrl' => $this->avatarUrl,
            'displayName' => $this->displayName,
            'active' => $this->active,
            'timeZone' => $this->timeZone,
        ];
    }
}