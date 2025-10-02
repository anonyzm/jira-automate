<?php

namespace App\Modules\Report\Entity;

use App\Modules\Report\Interface\ReportMessageInteface;

class TaskStatusChangedMessage implements ReportMessageInteface
{
    public function __construct(
        public string $taskCode,
        public string $taskTitle,
        public string $taskLink,
        public string $taskUser,
        public string $taskStatusOld,
        public string $taskStatusNew,
    ) {}

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
}
