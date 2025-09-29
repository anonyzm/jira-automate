<?php

namespace App\Command;

use App\Tests\TestTaskCreate;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Modules\Scheduler\Interface\TaskRouterInterface;
use App\Tests\TestTask;

#[AsCommand(
    name: 'app:test-create-task',
    description: 'Workflow test command',
)]
class TestCommand extends Command
{
    public function __construct(
        private readonly TaskRouterInterface $routerService
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->info('Запускаем тестовый роутинг задачи');

        $task = (new TestTaskCreate())->mock();
        $this->routerService->routeTask($task);
        
        $io->info('Выполнение закончено');        
        return Command::SUCCESS;
    }
}
