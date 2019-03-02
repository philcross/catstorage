<?php

namespace Tsc\CatStorageSystem;

use DateTimeInterface;

class File implements FileInterface
{
    /** @var string */
    private $name;

    /** @var integer */
    private $size;

    /** @var DateTimeInterface */
    private $created;

    /** @var DateTimeInterface */
    private $modified;

    /** @var DirectoryInterface */
    private $parent;

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
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * {@inheritdoc}
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedTime()
    {
        return $this->created;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedTime(DateTimeInterface $created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getModifiedTime()
    {
        return $this->modified;
    }

    /**
     * {@inheritdoc}
     */
    public function setModifiedTime(DateTimeInterface $modified)
    {
        $this->modified = $modified;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getParentDirectory()
    {
        return $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function setParentDirectory(DirectoryInterface $parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return sprintf('%s/%s', $this->parent->getPath(), 'test.txt');
    }
}
