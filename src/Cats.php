<?php

namespace Tsc\CatStorageSystem;

use Symfony\Component\Console\Application;
use Tsc\CatStorageSystem\Commands\ListDirectories;

class Cats
{
    /** @var Application */
    private $console;

    /** @var FileSystemInterface */
    private $filesystem;

    /**
     * Constructor
     *
     * @param Application $console
     * @param FileSystemInterface $filesystem
     */
    public function __construct(Application $console, FileSystemInterface $filesystem)
    {
        $this->console    = $console;
        $this->filesystem = $filesystem;
    }

    /**
     * Register commands, and run the console application
     *
     * @return void
     *
     * @throws \Exception
     */
    public function run()
    {
        $this->console->add(new ListDirectories($this->filesystem));

        $this->console->run();
    }
}
