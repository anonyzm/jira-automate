<?php

namespace App\Modules\Scheduler\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;
use App\Modules\Scheduler\Interface\TaskRouterInterface;
use App\Entity\Task\Task;

class JiraController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly TaskRouterInterface $routerService
    ) {}

    public function callback(Request $request): Response
    {       
        // Логируем входящий запрос
        $this->logger->info('Jira callback received', [
            'method' => $request->getMethod(),
            'path' => $request->getPathInfo(),
            'content' => $request->getContent(),
            'headers' => $request->headers->all()
        ]);

        $taskData = $this->getTaskData($request);
        $task = (new Task)->fromArray($taskData);
        
        $this->routerService->routeTask($task);

        return new JsonResponse([
            'message' => 'Thanks for callback',
            'timestamp' => date('Y-m-d H:i:s')
        ], 200);
    }

    private function getTaskData(Request $request): array
    {
        $data = json_decode($request->getContent(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \ErrorException('Failed to parse data from Jira with error: ' . json_last_error_msg());
        }
        return $data;
    }
}