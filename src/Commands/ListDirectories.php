<?php

namespace Tsc\CatStorageSystem\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Tsc\CatStorageSystem\Models\Directory;
use Symfony\Component\Console\Helper\Table;
use Tsc\CatStorageSystem\FileSystemInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Tsc\CatStorageSystem\Models\DirectoryInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListDirectories extends Command
{
    /** @var FileSystemInterface */
    private $filesystem;

    /** @var string  */
    protected static $defaultName = 'cats:list-directories';

    public function __construct(FileSystemInterface $filesystem)
    {
        $this->filesystem = $filesystem;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('List directories in the Cats filesystem')
            ->addArgument('directory', InputArgument::OPTIONAL, 'What directory would you like to list?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $currentDirectory = Directory::hydrate($input->getArgument('directory') ?: '');

        $output->writeln('<info>Fetching directories...</info>');
        $output->writeln(' - Directory: ' . $currentDirectory->getName());

        $directories = $this->transformDirectories($currentDirectory);

        $table = new Table($output);

        $table->setHeaders(['Directory', 'Path', 'Size', 'Files', 'Directories', 'Created'])->setRows($directories);

        $table->render();

        if (empty($directories)) {
            $output->writeln('This directory does not have any sub-directories.');
        }
    }

    private function transformDirectories(DirectoryInterface $currentDirectory)
    {
        return array_map(function (DirectoryInterface $directory) {
            return [
                $directory->getName(),
                $directory->getPath(),
                $this->filesystem->getDirectorySize($directory),
                $this->filesystem->getFileCount($directory),
                $this->filesystem->getDirectoryCount($directory),
                $directory->getCreatedTime()->format('l, jS F Y, H:i'),
            ];
        }, $this->filesystem->getDirectories($currentDirectory));
    }
}
