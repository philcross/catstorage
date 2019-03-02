<?php

namespace Tsc\CatStorageSystem;

use DateTimeInterface;

class Directory implements DirectoryInterface
{
    /** @var string */
    private $name;

    /** @var \DateTimeInterface */
    private $createdTime;

    /** @var string */
    private $path;

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedTime(DateTimeInterface $created)
    {
        $this->createdTime = $created;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedTime()
    {
        return $this->createdTime;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->path;
    }
}
