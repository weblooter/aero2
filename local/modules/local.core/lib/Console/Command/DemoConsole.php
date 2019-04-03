<?php

namespace Local\Core\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class DemoConsole
 * Демонстрационная версия консольной команды.
 *
 * @link    https://symfony.com/doc/current/console.html
 * @package App\Command
 */
class DemoConsole extends Command
{
    protected $requireName;

    /**
     * DemoConsole constructor.
     *
     * @param bool $requireName
     */
    public function __construct(bool $requireName = false)
    {
        $this->requireName = $requireName;

        parent::__construct();
    }

    /**
     * Установка параметров
     */
    protected function configure(): void
    {
        $this->setName('kd:demo')
            ->setDescription('Демострация работы консольной команды')
            ->setHelp("Это текст справки по команде,\nкоторый выводится если вызвать команду с ключем --help:\n$ php consoleapp.php kd:demo --help")
            ->addArgument('username', ($this->requireName ? InputArgument :: REQUIRED : InputArgument::OPTIONAL), 'Ваше имя');
    }

    /**
     * Бизнесс-логика
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln('Привет, '.$input->getArgument('username').'!');
    }
}