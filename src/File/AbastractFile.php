<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

namespace Wizaplace\SDK\File;

use Psr\Http\Message\StreamInterface;

abstract class AbastractFile
{
    /** @var string */
    private $name;

    /** @var StreamInterface */
    private $contents;

    /** @var string */
    private $filename;

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
