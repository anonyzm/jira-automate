<?php

namespace App\Modules\Scheduler\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;
use App\Modules\Scheduler\Interface\RouterInterface;
use App\Entity\Task\Task;
use App\Entity\JiraUser;
use App\Entity\ChangeLog;
use App\Entity\JiraData;
use App\Interface\TaskTrackerInterface;

class JiraController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly RouterInterface $routerService,
        private readonly TaskTrackerInterface $taskTrackerService
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
        try {
            /** @var array $jiraData */
            $jiraDataArray = $this->getJiraData($request);

            /** @var Task|null $task */
            $task = null;
            /** @var JiraUser|null $jiraUser */
            $jiraUser = null;
            /** @var ChangeLog[] $changeLog */
            $changeLog = [];
            
            if (!empty($jiraDataArray['issue'])) {
                $task = (new Task)->fromArray($jiraDataArray['issue']);
                $task->link = $this->taskTrackerService->getTaskLink($task->key);
            }
            if (!empty($jiraDataArray['user'])) {
                $jiraUser = (new JiraUser)->fromArray($jiraDataArray['user']);
            }
            if (!empty($jiraDataArray['changelog']['items'])) {
                foreach ($jiraDataArray['changelog']['items'] as $item) {
                    $changeLog[] = (new ChangeLog)->fromArray($item);
                }
            }

            // инициализируем структуру данных
            $jiraData = new JiraData();
            $jiraData->setTask($task);
            $jiraData->setJiraUser($jiraUser);
            $jiraData->setChangeLog($changeLog);

            $this->routerService->route($jiraData);
        } catch (\Exception $e) {
            $this->logger->error('Jira callback error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return new JsonResponse([
                'message' => 'Error processing Jira callback',
                'timestamp' => date('Y-m-d H:i:s')
            ], 400);
        }    

        return new JsonResponse([
            'message' => 'Thanks for callback',
            'timestamp' => date('Y-m-d H:i:s')
        ], 200);
    }

    private function getJiraData(Request $request): array
    {
        $data = json_decode($request->getContent(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \ErrorException('Failed to parse data from Jira with error: ' . json_last_error_msg());
        }
        return $data;
    }
}