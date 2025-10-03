<?php

namespace App\Modules\Report\Entity;

use App\Modules\Report\Interface\ReportMessageInterface;
use App\Interface\ArrayableInterface;

class TaskStatusChangedMessage implements ReportMessageInterface, ArrayableInterface
{
    public string $taskCode;
    public string $taskTitle;
    public string $taskLink;
    public string $taskUser;
    public string $taskStatusOld;
    public string $taskStatusNew;

    public function message(): string
    {
        $message = sprintf(
            "🚩 **Изменен статус задачи** 🚩\n" .
                    "**Задача:** *%s*\n" .
                    "**Ссылка:** [%s](%s)\n" .
                    "**Пользователь:** *%s*\n" .
                    "**Новый статус:** %s\n" .
                    "**Старый статус:** %s\n",
            $this->taskTitle,
            $this->taskCode,
            $this->taskLink,
            $this->taskUser,
            $this->taskStatusOld,
            $this->taskStatusNew,
        );
        return $message;
    }

    public function fromArray(array $data): self
    {
        $msg = new self();
        $msg->taskCode = $data['taskCode'];
        $msg->taskTitle = $data['taskTitle'];
        $msg->taskLink = $data['taskLink'];
        $msg->taskUser = $data['taskUser'];
        $msg->taskStatusOld = $data['taskStatusOld'];
        $msg->taskStatusNew = $data['taskStatusNew'];
        return $msg;
    }

    public function toArray(): array
    {
        return [
            'taskCode' => $this->taskCode,
            'taskTitle' => $this->taskTitle,
            'taskLink' => $this->taskLink,
            'taskUser' => $this->taskUser,
            'taskStatusOld' => $this->taskStatusOld,
            'taskStatusNew' => $this->taskStatusNew,
        ];
    }
}
