<?php

namespace ENC\Bundle\BackupRestoreBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RestoreCommand extends Command
{
    const COMMAND_NAME = 'database:restore';

    protected ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct(static::COMMAND_NAME);
        $this->container = $container;
    }

    protected function configure(): void
    {
        $this
            ->setName(static::COMMAND_NAME)
            ->setDefinition(array(
                new InputArgument('connection-service-id', InputArgument::REQUIRED, 'The connection service ID of the database to which you want to put the restored data.'),
                new InputArgument('file', InputArgument::REQUIRED, 'The file to restore with.')
            ))
            ->setHelp(<<<EOT
The <info>database:restore</info> command restores a database using 
a file, presumably created with the command "database:backup" from 
this bundle.

An example of usage of the command:

<info>./app/console database:restore "my-connection-service-id" "/path/to/my/backup/file.sql"</info>

EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $container = $this->getContainer();

        $factory = $container->get('backup_restore.factory');
        $connectionServiceId = $input->getArgument('connection-service-id');
        $file = $input->getArgument('file');

        $restoreInstance = $factory->getRestoreInstance($connectionServiceId);

        $restoreInstance->restoreDatabase($file);

        $connection = $container->get($connectionServiceId);

        $output->writeln('<comment>></comment> <info>Database was restored successfully.</info>');

        return self::SUCCESS;
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}
