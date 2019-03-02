<?php

namespace Tsc\CatStorageSystem;

use \DateTimeInterface;

interface DirectoryInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name);

    /**
     * @return DateTimeInterface
     */
    public function getCreatedTime();

    /**
     * @param DateTimeInterface $created
     *
     * @return $this
     */
    public function setCreatedTime(DateTimeInterface $created);

    /**
     * @return string
     */
    public function getPath();

    /**
     * @param string $path
     *
     * @return $this
     */
    public function setPath($path);
}
