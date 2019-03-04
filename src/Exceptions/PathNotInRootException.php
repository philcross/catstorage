<?php

namespace Tsc\CatStorageSystem\Exceptions;

class PathNotInRootException extends \Exception
{
    public function __construct($path, $root)
    {
        parent::__construct(sprintf('The path "%s" is not within the filesystem root "%s"', $path, $root));
    }
}
