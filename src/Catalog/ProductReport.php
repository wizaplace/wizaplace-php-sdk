<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Catalog;

use Wizaplace\Exception\SomeParametersAreInvalid;

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

    /**
     * @throws SomeParametersAreInvalid
     */
    public function validate(): void
    {
        if (is_null($this->productId)) {
            throw new SomeParametersAreInvalid('Missing product ID', 400);
        }

        if (is_null($this->reporterEmail)) {
            throw new SomeParametersAreInvalid('Missing reporter\'s email', 400);
        }

        if (is_null($this->reporterName)) {
            throw new SomeParametersAreInvalid('Missing reporter\'s name', 400);
        }

        if (is_null($this->message)) {
            throw new SomeParametersAreInvalid('Missing message', 400);
        }
    }
}
