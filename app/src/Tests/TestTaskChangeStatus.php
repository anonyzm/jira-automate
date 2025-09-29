<?php

namespace App\Tests;

use App\Entity\Task\Task;

class TestTaskChangeStatus
{    
    public function mock(): Task
    {
        return (new Task())->fromArray(json_decode(file_get_contents(dirname(__DIR__) . '/Tests/Seed/task_change_status.json'), true));
    }
}