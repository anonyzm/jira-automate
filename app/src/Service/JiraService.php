<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use GuzzleHttp\Client;
use App\Exception\TaskTrackerException;
use App\Interface\TaskTrackerInterface;

class JiraService implements TaskTrackerInterface
{
    private Client $client;

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly string $jiraLink,
        private readonly string $baseUrl,
        private readonly string $token
        )
    {
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout'         => 0,
            'allow_redirects' => true,
            'verify' => false,
            'scheme' => 'https',
        ]);
    }

    public function getTaskLink(string $taskKey): string
    {
        return $this->jiraLink . '/browse/' . $taskKey;
    }

    public function getTask(string $taskKey): array
    {
        return $this->get('/rest/api/latest/issue/' . $taskKey);
    }

    public function createTask(array $taskData): array
    {
        return $this->post('/rest/api/latest/issue/', $taskData);
    }

    public function assignTask(string $taskKey, string $assignee): void
    {
        throw new TaskTrackerException('method not implemented yet');
    }

    public function addComment(string $taskKey, string $comment): void
    {
        throw new TaskTrackerException('method not implemented yet');
    }

    public function updateTask(string $taskKey, array $taskData): array
    {
        throw new TaskTrackerException('method not implemented yet');
    }

    // -------------------------- private methods ----------------------------

    /**
     * Summary of get
     * @param string $url
     * @throws \App\Exception\TaskTrackerException
     * @return array
     */
    private function get(string $url): array
    {
        /**
         * @var \GuzzleHttp\Psr7\Response
         */
        $response = $this->client->get( $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token
            ]
        ]);
        if ($response->getStatusCode() !== 200) {
            throw new TaskTrackerException('Failed to get data from Jira with status code: ' . $response->getStatusCode());
        }
        $data = json_decode($response->getBody(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new TaskTrackerException('Failed to parse data from Jira with error: ' . json_last_error_msg());
        }
        return $data;
    }

    /**
     * Summary of post
     * @param string $url
     * @param array $data
     * @throws \App\Exception\TaskTrackerException
     * @return array
     */
    private function post(string $url, array $data): array
    {
        /**
         * @var \GuzzleHttp\Psr7\Response
         */
        $response = $this->client->post( $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($data)
        ]);
        if ($response->getStatusCode() !== 200 || $response->getStatusCode() !== 201) {
            throw new TaskTrackerException('Failed to get data from Jira with status code: ' . $response->getStatusCode());
        }
        $data = json_decode($response->getBody(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new TaskTrackerException('Failed to parse data from Jira with error: ' . json_last_error_msg());
        }
        return $data;
    }
}