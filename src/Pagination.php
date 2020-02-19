<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK;

use function theodorejb\polycast\to_int;

/**
 * Class Pagination
 * @package Wizaplace\SDK
 */
final class Pagination
{
    /** @var int */
    private $page;
    /** @var int */
    private $nbResults;
    /** @var int */
    private $nbPages;
    /** @var int */
    private $resultsPerPage;

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->page = to_int($data['page']);
        $this->nbResults = to_int($data['nbResults']);
        $this->nbPages = to_int($data['nbPages']);
        $this->resultsPerPage = to_int($data['resultsPerPage']);
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @return int
     */
    public function getNbResults(): int
    {
        return $this->nbResults;
    }

    /**
     * @return int
     */
    public function getNbPages(): int
    {
        return $this->nbPages;
    }

    /**
     * @return int
     */
    public function getResultsPerPage(): int
    {
        return $this->resultsPerPage;
    }
}
