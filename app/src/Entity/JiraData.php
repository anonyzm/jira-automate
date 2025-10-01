<?php

namespace App\Entity;

use App\Interface\ArrayableInterface;

use App\Entity\Task\Task;
use App\Entity\JiraUser;
use App\Entity\ChangeLog;

/**
 * Этот класс - порождение костылей Symfony/Messenger, ежа и ишака
 */
class JiraData implements ArrayableInterface
{
    // TODO: костыль
    /** @property ChangeLog[] $changeLog */

    public function __construct(
        public ?Task $task = null,
        public ?JiraUser $jiraUser = null,
        // TODO: костыль
        public array $changeLogArray = [],
    ) {}

    public function fromArray(array $data): self
    {
        return new self(
            (new Task)->fromArray($data['task']),
            (new JiraUser)->fromArray($data['jiraUser']),
            array_map(
                fn($item) => (new ChangeLog)->fromArray($item), 
                $data['changeLog']
            )
        );
    }

    public function toArray(): array
    {
        return [
            'task' => $this->task->toArray(),
            'jiraUser' => $this->jiraUser->toArray(),
            'changeLogArray' => array_map(
                function ($item){
                    // TODO: ЛЮТЫЙ КОСТЫЛЬ!!! потому что сериализатор не работает нормально с массивом объектов
                    if (is_array($item)) {
                        return $item;
                    }
                    else if ($item instanceof ArrayableInterface) {
                        return $item->toArray();
                    }
                },
                $this->changeLog
            ),
        ];
    }

    public function setJiraUser(JiraUser $user): void 
    {
        $this->jiraUser = $user;
    }

    public function setTask(Task $task): void  
    {
        $this->task = $task;
    }

    // TODO: Всё дальнейшее - тоже костыль, чтобы наговнячить только в этом классе и оставить чистоту за пределами
    /**
     * @return ChangeLog[]
     */
    public function getChangeLog(): array  
    {
        $changeLogData = [];
        foreach($this->changeLogArray as $changeLog) {
            if (is_array($changeLog)) {
                $changeLogData[] = (new ChangeLog())->fromArray($changeLog);
                continue;
            } 
            else if ($changeLog instanceof ChangeLog) {
                $changeLogData[] = $changeLog;
            }            
        }
        return $changeLogData;
    }

    /**
     * @param ChangeLog[]|array $changeLogData
     */
    public function setChangeLog(array $changeLogData): void
    {
        $this->changeLogArray = [];
        foreach($changeLogData as $changeLog) {
            if (is_array($changeLog)) {
                $this->changeLogArray[] = (new ChangeLog())->fromArray($changeLog);
                continue;
            } 
            else if ($changeLog instanceof ChangeLog) {
                $this->changeLogArray[] = $changeLog;
            }      
        }
    }

    // ------------------------------magic-methods---------------------------------

    public function __get(string $name) 
    {
        if ($name === 'changeLog') {
            return $this->getChangeLog();
        }
    }

    public function __set(string $name, mixed $value) 
    {
        if ($name === 'changeLog') {
            $this->setChangeLog($value);
        }
    }
}