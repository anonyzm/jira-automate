<?php

namespace App\Modules\Report\DTO;

use App\Modules\Report\Interface\ReportMessageInteface;
use App\Modules\Report\Entity\ReportMessage;

class TaskStatusChangedMessage implements ReportMessageInteface
{
    public function __construct(
        private string $taskCode,
        private string $taskTitle,
        private string $taskLink,
        private string $taskUser,
        private string $taskStatusOld,
        private string $taskStatusNew,
    ) {}

    public function json(): ReportMessage
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
        return new ReportMessage($message);
    }
}
