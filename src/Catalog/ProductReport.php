<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Catalog;

use Wizaplace\SDK\Exception\SomeParametersAreInvalid;

/**
 * Class ProductOffer
 * @package Wizaplace\SDK\Catalog
 *
 * @see \Wizaplace\SDK\Catalog\CatalogService::reportProduct
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

    /**
     * @return string
     */
    public function getProductId(): string
    {
        return $this->productId;
    }

    /**
     * @param string $productId
     *
     * @return ProductReport
     */
    public function setProductId(string $productId): self
    {
        $this->productId = $productId;

        return $this;
    }

    /**
     * @return string
     */
    public function getReporterEmail(): string
    {
        return $this->reporterEmail;
    }

    /**
     * @param string $reporterEmail
     *
     * @return ProductReport
     */
    public function setReporterEmail(string $reporterEmail): self
    {
        $this->reporterEmail = $reporterEmail;

        return $this;
    }

    /**
     * @return string
     */
    public function getReporterName(): string
    {
        return $this->reporterName;
    }

    /**
     * @param string $reporterName
     *
     * @return ProductReport
     */
    public function setReporterName(string $reporterName): self
    {
        $this->reporterName = $reporterName;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     *
     * @return ProductReport
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @throws SomeParametersAreInvalid
     */
    public function validate(): void
    {
        if (!isset($this->productId)) {
            throw new SomeParametersAreInvalid('Missing product ID', 400);
        }

        if (!isset($this->reporterEmail)) {
            throw new SomeParametersAreInvalid('Missing reporter\'s email', 400);
        }

        if (!isset($this->reporterName)) {
            throw new SomeParametersAreInvalid('Missing reporter\'s name', 400);
        }

        if (!isset($this->message)) {
            throw new SomeParametersAreInvalid('Missing message', 400);
        }
    }
}
