<?php

namespace App\Tests;

use App\Entity\Task\Task;

class TestTaskCreate
{    
    public function mock(): Task
    {
        return (new Task())->fromArray(json_decode(file_get_contents(dirname(__DIR__) . '/Tests/Seed/new_task.json'), true));
    }
}