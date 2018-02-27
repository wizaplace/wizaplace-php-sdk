<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK;

final class Pagination implements \JsonSerializable
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

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return [
            'page' => $this->getPage(),
            'nbResults' => $this->getNbResults(),
            'nbPages' => $this->getNbPages(),
            'resultsPerPage' => $this->getResultsPerPage(),
        ];
    }
}
