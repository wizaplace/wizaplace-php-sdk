<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK;

use function theodorejb\polycast\to_int;

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
     */
    public function __construct(array $data)
    {
        $this->page = to_int($data['page']);
        $this->nbResults = to_int($data['nbResults']);
        $this->nbPages = to_int($data['nbPages']);
        $this->resultsPerPage = to_int($data['resultsPerPage']);
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getNbResults(): int
    {
        return $this->nbResults;
    }

    public function getNbPages(): int
    {
        return $this->nbPages;
    }

    public function getResultsPerPage(): int
    {
        return $this->resultsPerPage;
    }
}
