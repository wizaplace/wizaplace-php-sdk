<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Cms;

use Psr\Http\Message\UriInterface;

final class MenuItem
{
    /** @var string */
    private $name;

    /** @var int */
    private $position;

    /** @var UriInterface */
    private $url;

    /**
     * @internal
     */
    public function __construct(string $name, int $position, UriInterface $url)
    {
        $this->name = $name;
        $this->position = $position;
        $this->url = $url;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getUrl(): UriInterface
    {
        return $this->url;
    }
}
