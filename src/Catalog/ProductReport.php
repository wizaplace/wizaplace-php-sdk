<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Catalog;

/**
 * @see \Wizaplace\Catalog\CatalogService::reportProduct
 */
final class ProductReport
{
    /** @var string */
    private $productId;

    /** @var string */
    private $reporterEmail;

    /** @var string */
    private $reporterName;

    /** @var string */
    private $message;

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function setProductId(string $productId)
    {
        $this->productId = $productId;
    }

    public function getReporterEmail(): string
    {
        return $this->reporterEmail;
    }

    public function setReporterEmail(string $reporterEmail)
    {
        $this->reporterEmail = $reporterEmail;
    }

    public function getReporterName(): string
    {
        return $this->reporterName;
    }

    public function setReporterName(string $reporterName)
    {
        $this->reporterName = $reporterName;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message)
    {
        $this->message = $message;
    }
}
