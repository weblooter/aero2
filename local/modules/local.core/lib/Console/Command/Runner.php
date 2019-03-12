<?php

namespace Local\Core\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * <code>
 * php -d mbstring.func_overload=2  local/tools/console runner
 * </code>
 * Class Runner
 * @package Local\Core\Console\Command
 */
class Runner extends Command
{
    /**
     * Установка параметров
     */
    protected function configure(): void
    {
        $this
            ->setName( 'runner' )
            ->setDescription( 'Запускает раннер для очереди задач. 
                             Пример вызова:
                             $ <info>php -d mbstring.func_overload=2 console runner</info>' );
    }

    /**
     * Бизнес-логика
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute( InputInterface $input, OutputInterface $output ): void
    {
        $runner = new \Local\Core\Inner\JobQueue\Runner();
        $runner->start();
    }
}
