<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace;

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
        $this->page = $data['page'];
        $this->nbResults = $data['nbResults'];
        $this->nbPages = $data['nbPages'];
        $this->resultsPerPage = $data['resultsPerPage'];
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
