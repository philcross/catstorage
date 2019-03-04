<?php

namespace Tsc\CatStorageSystem\Exceptions;

class DirectoryAlreadyExistException extends \Exception
{
    public function __construct($path)
    {
        parent::__construct(sprintf('The directory "%s" already exists.', $path));
    }
}
