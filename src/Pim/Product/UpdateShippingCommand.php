<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Pim\Product;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validation;
use Wizaplace\SDK\ArrayableInterface;
use Wizaplace\SDK\Exception\SomeParametersAreInvalid;

final class UpdateShippingCommand implements ArrayableInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @Assert\Choice({"A", "D"})
     * @var string
     */
    private $status;

    /**
     * @var string
     */
    private $shipping;

    /**
     * @var string
     */
    private $deliveryTime;

    /**
     * @Assert\NotNull()
     * @var array
     */
    private $rates;

    /**
     * @Assert\NotNull()
     * @var bool
     */
    private $specificRate;

    /**
     * @Assert\GreaterThan(
     *     value = 0
     * )
     * @var int
     */
    private $productId;

    /**
     * @var string
     */
    private $description;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getShipping(): string
    {
        return $this->shipping;
    }

    /**
     * @return string
     */
    public function getDeliveryTime(): string
    {
        return $this->deliveryTime;
    }

    /**
     * @return array
     */
    public function getRates(): array
    {
        return $this->rates;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return bool
     */
    public function isSpecificRate(): bool
    {
        return $this->specificRate;
    }

    /**
     * @return int
     */
    public function getProductId(): int
    {
        return $this->productId;
    }

    /**
     * @param string $status
     *
     * @return UpdateShippingCommand
     */
    public function setStatus(string $status): UpdateShippingCommand
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param array $rates
     *
     * @return UpdateShippingCommand
     */
    public function setRates(array $rates): UpdateShippingCommand
    {
        $this->rates = $rates;

        return $this;
    }

    /**
     * @param bool $specificRate
     *
     * @return UpdateShippingCommand
     */
    public function setSpecificRate(bool $specificRate): UpdateShippingCommand
    {
        $this->specificRate = $specificRate;

        return $this;
    }

    /**
     * @param int $productId
     *
     * @return UpdateShippingCommand
     */
    public function setProductId(int $productId): UpdateShippingCommand
    {
        $this->productId = $productId;

        return $this;
    }

    /**
     * @internal
     * @throws SomeParametersAreInvalid
     */
    public function validate(): void
    {
        $builder = Validation::createValidatorBuilder()
            ->enableAnnotationMapping();

        $validator = $builder->getValidator()->startContext();

        $validator->validate($this);

        $violations = $validator->getViolations();

        if (count($violations) > 0) {
            throw new SomeParametersAreInvalid('Shipping data validation failed: '.json_encode(array_map(function (ConstraintViolationInterface $violation): array {
                return [
                    'field' => $violation->getPropertyPath(),
                    'message' => $violation->getMessage(),
                ];
            }, iterator_to_array($violations))));
        }
    }

    public function toArray(): array
    {
        $data = [];

        if (isset($this->status)) {
            $data['status'] = $this->getStatus();
        }

        if (isset($this->rates)) {
            $data['rates'] = $this->getRates();
        }

        if (isset($this->specificRate)) {
            $data['specific_rate'] = $this->isSpecificRate();
        }

        if (isset($this->productId)) {
            $data['product_id'] = $this->getProductId();
        }

        return $data;
    }
}
