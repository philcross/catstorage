<?php

namespace Tsc\CatStorageSystem\Commands;

use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Tsc\CatStorageSystem\Models\Directory;
use Symfony\Component\Console\Helper\Table;
use Tsc\CatStorageSystem\FileSystemInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Tsc\CatStorageSystem\Models\DirectoryInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tsc\CatStorageSystem\Models\File;

class ListDirectories extends Command
{
    /** @var FileSystemInterface */
    private $filesystem;

    /** @var InputInterface */
    private $input;

    /** @var OutputInterface */
    private $output;

    /** @var array */
    private $directories = [];

    /** @var DirectoryInterface */
    private $currentDirectory;

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
        $this->input  = $input;
        $this->output = $output;

        $this->currentDirectory = Directory::hydrate($this->input->getArgument('directory') ?: '');

        $this->writeDirectoriesTable();

        if (empty($this->directories)) {
            $this->output->writeln('This directory does not have any sub-directories.');
        }

        $this->output->writeln('');

        $this->manipulate();
    }

    private function writeDirectoriesTable()
    {
        $this->output->writeln('<info>Fetching directories...</info>');
        $this->output->writeln(' - Directory: ' . $this->currentDirectory->getName());

        $directories = $this->transformDirectories($this->currentDirectory);

        $table = new Table($this->output);

        $table->setHeaders(['Directory', 'Path', 'Size', 'Files', 'Directories', 'Created'])->setRows($directories);

        $table->render();
    }

    private function manipulate()
    {
        $action = $this->askCurrentDirectoryQuestions();

        switch ($action) {
            case 'Create New Directory':
                $this->createDirectory();
                break;

            case 'Create File':
                $this->createFile();
                break;

            case 'Edit Subdirectory':
                $this->manipulateSubdirectory();
                break;

            case 'Cancel':
                return;
        }
    }

    private function createDirectory()
    {
        $directoryName = $this->askForNewDirectoryName();

        $directory = Directory::toCreate(
            rtrim($this->currentDirectory->getPath(), DIRECTORY_SEPARATOR) .
            DIRECTORY_SEPARATOR .
            ltrim($directoryName, DIRECTORY_SEPARATOR)
        );

        try {
            $created = $this->filesystem->createDirectory($directory, $this->currentDirectory);

            $this->output->writeln('<info>' . $created->getName() . ' was successfully created.</info>');

            return $this->execute($this->input, $this->output);
        } catch (\Exception $e) {
            $this->output->writeln('<error>' . $e->getMessage() . '</error>');

            return null;
        }
    }

    private function createFile()
    {
        $option = $this->askWhetherToCreateFileOrDownload();

        switch ($option) {
            case 'Create New':
                $filename = $this->askForFilename();
                $content = $this->askForContent();

                $file = File::toCreate($this->currentDirectory, $filename, $content);

                $this->filesystem->createFile($file, $this->currentDirectory);

                $this->output->writeln('<info>The file was successfully created.</info>');
                break;

            case 'Download from URL':
                $filename = $this->askForFilename();
                $url = $this->askForUrl();

                $file = File::toCreate($this->currentDirectory, $filename, file_get_contents($url));

                $this->filesystem->createFile($file, $this->currentDirectory);

                $this->output->writeln('<info>The file was successfully created.</info>');
                break;
        }
    }

    private function manipulateSubdirectory()
    {
        $directoryName = $this->askWhichDirectory();

        if ($directoryName === 'Cancel / None') {
            return;
        }

        $manipulation = $this->askWhatToDoWithDirectory($directoryName);
        $directory    = $this->getDirectoryFromSelection($directoryName);

        switch ($manipulation) {
            case 'delete':
                $this->deleteDirectory($directory);
                break;

            case 'cancel':
                return;
        }
    }

    private function deleteDirectory(DirectoryInterface $directory)
    {
        try {
            $result = $this->filesystem->deleteDirectory($directory);

            if (!$result) {
                throw new \Exception('There was an unknown error while trying to delete the directory.');
            }
        } catch (\Exception $e) {
            $this->output->writeln('<error>' . $e->getMessage() . '</error>');

            return null;
        }

        $this->output->writeln('<info>The directory has been successfully deleted.</info>');

        return $this->execute($this->input, $this->output);
    }

    private function transformDirectories(DirectoryInterface $currentDirectory)
    {
        $this->directories = $this->filesystem->getDirectories($currentDirectory);

        return array_map(function (DirectoryInterface $directory) {
            return [
                $directory->getName(),
                $directory->getPath(),
                $this->filesystem->getDirectorySize($directory),
                $this->filesystem->getFileCount($directory),
                $this->filesystem->getDirectoryCount($directory),
                $directory->getCreatedTime()->format('l, jS F Y, H:i'),
            ];
        }, $this->directories);
    }

    private function getDirectoryFromSelection($directoryName)
    {
        /** @var DirectoryInterface $file */
        $directory = array_values(array_filter($this->directories, function (DirectoryInterface $directory) use ($directoryName) {
            return $directory->getName() === $directoryName;
        }));

        if (!$directory) {
            return null;
        } else {
            return $directory[0];
        }
    }


    /**
     * Question Prompt Methods
     */

    private function askWhichDirectory()
    {
        $available = array_map(function (DirectoryInterface $directory) {
            return $directory->getName();
        }, $this->directories);

        $question = new ChoiceQuestion('Which file would you like to change?', array_merge($available, [
            'Cancel / None'
        ]));

        $question->setErrorMessage('Option %s is invalid.');

        return $this->getHelper('question')->ask($this->input, $this->output, $question);
    }

    private function askCurrentDirectoryQuestions()
    {
        $options = ['Create New Directory', 'Create File', 'Edit Subdirectory', 'Cancel'];

        if (empty($this->directories)) {
            unset($options[2]);
        }

        $question = new ChoiceQuestion('What would you like to do? ', array_values($options));

        $question->setErrorMessage('Option %s is invalid.');

        return $this->getHelper('question')->ask($this->input, $this->output, $question);
    }

    private function askWhatToDoWithDirectory($directoryName)
    {
        $options = ['delete', 'cancel'];

        $question = new ChoiceQuestion(sprintf('What would you like to do with "%s"?: ', $directoryName), $options);

        $question->setErrorMessage('Option %s is invalid.');

        return $this->getHelper('question')->ask($this->input, $this->output, $question);
    }

    private function askForNewDirectoryName()
    {
        $question = new Question('Please enter the new name for this directory: ');

        $helper  = $this->getHelper('question');
        return $helper->ask($this->input, $this->output, $question);
    }

    private function askWhetherToCreateFileOrDownload()
    {
        $options = ['Create New', 'Download from URL'];

        $question = new ChoiceQuestion('How would you like to create the file? ', $options);

        $question->setErrorMessage('Option %s is invalid.');

        return $this->getHelper('question')->ask($this->input, $this->output, $question);
    }

    private function askForFilename()
    {
        $question = new Question('Please enter the name for this new file: ');

        $helper  = $this->getHelper('question');
        return $helper->ask($this->input, $this->output, $question);
    }

    private function askForContent()
    {
        $question = new Question('Enter the content for this new file: ');

        $helper  = $this->getHelper('question');
        return $helper->ask($this->input, $this->output, $question);
    }

    private function askForUrl()
    {
        $question = new Question('Please enter the URL to download: ');

        $helper  = $this->getHelper('question');
        return $helper->ask($this->input, $this->output, $question);
    }
}
