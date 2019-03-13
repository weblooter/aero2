<?php

namespace Local\Core\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Класс пошагового счетчика. На данный момент нужен для эмуляции длительной работы
 * Class StepCounter
 * @package Local\Core\Console\Command
 */
class StepCounter extends Command
{
    /**
     * StepCounter constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Установка параметров
     */
    protected function configure(): void
    {
        $this->setName('kd:step-counter')->setDescription('Счетчик')->setHelp("Это текст справки по команде,\nкоторый выводится если вызвать команду с ключем --help:\n$ php consoleapp.php kdd:stepcounter --help")->addArgument('start', InputArgument::REQUIRED, 'Начать с')->addArgument('step', InputArgument::REQUIRED, 'С шагом')->addArgument('iterations', InputArgument::REQUIRED, 'Количество повторений');
    }

    /**
     * Бизнесс-логика
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $start = $input->getArgument('start');
        $step = $input->getArgument('step');
        $iterations = $input->getArgument('iterations');

        $counter = 0;

        while( true )
        {
            $counter++;
            $output->writeln('Процесс: '.getmypid().' - '.$start);
            //            \Local\Core\Inner\ContainerDI::getInstance()->get( 'logger.service' )->alert( 'Процесс: '.getmypid().' - '.$start );

            $start += $step;
            sleep(1);

            if( $counter >= $iterations )
            {
                return;
            }
        }
    }
}