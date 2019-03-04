<?php

namespace Tsc\CatStorageSystem\Models;

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

    /** @var string */
    private $content;

    /** @var DirectoryInterface */
    private $parent;

    /**
     * Create a new instance of the object in order to store it
     *
     * @param DirectoryInterface $parent
     * @param string $name
     * @param string $content
     *
     * @return static
     */
    public static function toCreate(DirectoryInterface $parent, $name, $content = '')
    {
        return (new static)
            ->setName($name)
            ->setParentDirectory($parent)
            ->setContent($content)
            ->setSize(0);
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
        return sprintf('%s/%s', $this->parent->getPath(), $this->name);
    }

    /**
     * {@inheritdoc}
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return $this->content;
    }
}
