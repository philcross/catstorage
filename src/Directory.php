<?php

namespace Tsc\CatStorageSystem;

use DateTime;
use DateTimeInterface;

class Directory implements DirectoryInterface
{
    /** @var string */
    private $name;

    /** @var \DateTimeInterface|null */
    private $createdTime;

    /** @var string */
    private $path;

    /**
     * @param string $path
     *
     * @return static
     */
    public static function toCreate($path)
    {
        return (new static)
            ->setName(basename($path))
            ->setPath($path);
    }

    /**
     * @param string $path
     * @param DateTimeInterface|null $created
     *
     * @return static
     */
    public static function hydrate($path, DateTimeInterface $created = null)
    {
        return (new static)
            ->setName(basename($path))
            ->setPath($path)
            ->setCreatedTime($created ?: new DateTime);
    }

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
