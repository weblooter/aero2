<?php

namespace Local\Core\Console\Command;

use Local\Core\Model\Data\JobQueueTable;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;


class Worker extends Command
{
    /**
     * Установка параметров
     */
    protected function configure(): void
    {
        $this->setName('worker')
            ->setDescription("Запускает worker, исполняющий задачу из очереди. 
                             Пример вызова для дебага: 
                             $ php -d mbstring.func_overload=2 console worker 1002 NONE")
            ->addArgument('jobID', InputArgument::REQUIRED, '')
            ->addArgument('executorID', InputArgument::REQUIRED, '');
    }

    /**
     * Бизнес-логика
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {

        $jobID = $input->getArgument('jobID');
        $executorID = $input->getArgument('executorID');



        $strJobPrefix = end(array_diff(explode('/', $_SERVER['DOCUMENT_ROOT']), ['']));

        cli_set_process_title($strJobPrefix.'_queue_job_worker_'.$jobID);

        $arJob = JobQueueTable::getList([
            'filter' => [
                'ID' => $jobID,
                'EXECUTE_BY' => $executorID,
            ],
        ])
            ->fetch();

        if (is_array($arJob)) {
            /** @var \Local\Core\Inner\JobQueue\Abstracts\Worker $worker */
            $worker = new $arJob['WORKER_CLASS_NAME']($arJob['INPUT_DATA'], $jobID, $executorID);
            $worker->execute();

        }
    }
}
