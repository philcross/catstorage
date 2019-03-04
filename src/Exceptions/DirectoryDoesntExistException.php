<?php

namespace Tsc\CatStorageSystem\Exceptions;

class DirectoryDoesntExistException extends \Exception
{
    public function __construct($path)
    {
        parent::__construct(sprintf('The directory "%s" doesn\'t exist.', $path));
    }
}
