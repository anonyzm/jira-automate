<?php

namespace App\Interface;

interface TaskTrackerInterface
{
    public function getTask(string $taskKey): array;

    public function createTask(array $taskData): array;

    public function assignTask(string $taskKey, string $assignee): void;

    public function addComment(string $taskKey, string $comment): void;

    public function updateTask(string $taskKey, array $taskData): array;

}
