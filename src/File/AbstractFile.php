<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

namespace Wizaplace\SDK\File;

use Psr\Http\Message\StreamInterface;

/**
 * Class AbstractFile
 * @package Wizaplace\SDK\File
 */
abstract class AbstractFile
{
    /** @var string */
    private $name;

    /** @var StreamInterface */
    private $contents;

    /** @var string */
    private $filename;

    /**
     * AbstractFile constructor.
     *
     * @param string          $name
     * @param StreamInterface $contents
     * @param string          $filename
     */
    public function __construct(string $name, StreamInterface $contents, string $filename)
    {
        $this->name = $name;
        $this->contents = $contents;
        $this->filename = $filename;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return StreamInterface
     */
    public function getContents(): StreamInterface
    {
        return $this->contents;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }
}
