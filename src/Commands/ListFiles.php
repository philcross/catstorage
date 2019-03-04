<?php

namespace Tsc\CatStorageSystem\Commands;

use Tsc\CatStorageSystem\Models\Directory;
use Symfony\Component\Console\Helper\Table;
use Tsc\CatStorageSystem\FileSystemInterface;
use Symfony\Component\Console\Command\Command;
use Tsc\CatStorageSystem\Models\FileInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Tsc\CatStorageSystem\Models\DirectoryInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Tsc\CatStorageSystem\Exceptions\PathNotInRootException;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class ListFiles extends Command
{
    /** @var FileSystemInterface */
    private $filesystem;

    /** @var InputInterface */
    private $input;

    /** @var OutputInterface */
    private $output;

    /** @var array */
    private $files = [];

    /** @var string  */
    protected static $defaultName = 'cats:list-files';

    public function __construct(FileSystemInterface $filesystem)
    {
        $this->filesystem = $filesystem;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Lists files in the Cats filesystem')
            ->addArgument('directory', InputArgument::OPTIONAL, 'What directory would you like to list?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input  = $input;
        $this->output = $output;

        $this->writeFilesTable();

        if (empty($this->files)) {
            $this->output->writeln('This directory does not have any files.');
        } else {
            $this->manipulate();
        }
    }

    private function writeFilesTable()
    {
        $currentDirectory = Directory::hydrate($this->input->getArgument('directory') ?: '');

        $this->output->writeln('<info>Fetching files...</info>');
        $this->output->writeln(' - Directory: '.$currentDirectory->getPath());

        $files = $this->transformFiles($currentDirectory);

        $table = new Table($this->output);

        $table->setHeaders(['Filename', 'Size', 'Created', 'Modified'])->setRows($files);

        $table->render();
    }

    private function manipulate()
    {
        if (!$this->wouldLikeToManipulateFiles()) {
            return;
        }

        $filename = $this->askWhichFile();

        if ($filename === 'Cancel / None') {
            return;
        }

        $manipulation = $this->askWhatToDoWithFile($filename);
        $file         = $this->getFileFromSelection($filename);

        switch ($manipulation) {
            case 'delete':
                $this->deleteFile($file);
                break;

            case 'move':
                $this->moveFile($file);
                break;

            case 'cancel':
                return;
        }
    }

    private function deleteFile(FileInterface $file)
    {
        try {
            $result = $this->filesystem->deleteFile($file);

            if (!$result) {
                throw new \Exception('There was an unknown error while deleting this file.');
            }

            return $this->execute($this->input, $this->output);
        } catch (\Exception $e) {
            $this->output->writeln('<error>' . $e->getMessage() . '</error>');
            return;
        }

        $this->output->writeln('<info>File '.$file->getName().' successfully deleted</info>');
    }

    private function moveFile(FileInterface $file)
    {
        $question = new Question('Please enter the new name for this file (should include the path): ', $file->getName());

        $helper  = $this->getHelper('question');
        $newPath = $helper->ask($this->input, $this->output, $question);

        try {
            $this->filesystem->renameFile($file, $newPath);

            $this->output->writeln('<info>You have successfully moved this file to '.$newPath.'</info>');

            return $this->execute($this->input, $this->output);
        } catch (PathNotInRootException $e) {
            $this->output->writeln('<error>'.$e->getMessage().'</error>');

            return $this->moveFile($file);
        }
    }

    private function transformFiles(DirectoryInterface $currentDirectory)
    {
        $this->files = $this->filesystem->getFiles($currentDirectory);

        return array_map(function (FileInterface $file) {
            return [
                $file->getName(),
                $this->toFriendlyFilesize($file->getSize()),
                $file->getCreatedTime()->format('l, jS F Y, H:i'),
                $file->getModifiedTime()->format('l, jS F Y, H:i'),
            ];
        }, $this->files);
    }

    /**
     * @see http://jeffreysambells.com/2012/10/25/human-readable-filesize-php
     *
     * @param     $bytes
     * @param int $decimals
     *
     * @return string
     */
    private function toFriendlyFilesize($bytes, $decimals = 0)
    {
        $size = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }

    private function getFileFromSelection($filename)
    {
        /** @var FileInterface $file */
        $file = array_values(array_filter($this->files, function (FileInterface $file) use ($filename) {
            return $file->getName() === $filename;
        }));

        if (!$file) {
            return null;
        } else {
            return $file[0];
        }
    }


    /**
     * Question Prompt Methods
     */

    private function wouldLikeToManipulateFiles()
    {
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Would you like to do anything with these files? [yes/no] ', false);

        return $helper->ask($this->input, $this->output, $question);
    }

    private function askWhichFile()
    {
        $available = array_map(function (FileInterface $file) {
            return $file->getName();
        }, $this->files);

        $question = new ChoiceQuestion('Which file would you like to change?', array_merge($available, [
            'Cancel / None'
        ]));

        $question->setErrorMessage('Option %s is invalid.');

        return $this->getHelper('question')->ask($this->input, $this->output, $question);
    }

    private function askWhatToDoWithFile($filename)
    {
        $options = ['delete', 'move', 'cancel'];

        $question = new ChoiceQuestion(sprintf('What would you like to do with "%s"?: ', $filename), $options);

        $question->setErrorMessage('Option %s is invalid.');

        return $this->getHelper('question')->ask($this->input, $this->output, $question);
    }
}
