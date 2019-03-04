<?php

namespace Tsc\CatStorageSystem\Exceptions;

class FileDoesntExistException extends \Exception
{
    public function __construct($path)
    {
        parent::__construct(sprintf('The file "%s" doesn\'t exist.', $path));
    }
}
