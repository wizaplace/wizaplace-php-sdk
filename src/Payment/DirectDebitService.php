<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @license     Proprietary
 * @copyright   Copyright (c) Wizacha
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Payment;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Symfony\Component\Validator\Constraints\Bic;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Iban;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validation;
use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Exception\SomeParametersAreInvalid;

/**
 *  Service for generating direct debit mandates and processing payments
 * @package Wizaplace\SDK\Payment
 */
class DirectDebitService extends AbstractService
{
    /** @var mixed[] */
    protected $constraintsByPsp;

    protected function generatePspConstraints()
    {
        $this->constraintsByPsp =  [
            1013 =>
                [
                    'iban' => [new NotBlank(), new Iban()],
                    'bic' => [new NotBlank(), new Bic()],
                    'bank-name' => [new NotBlank()],
                    'gender' => [new Choice(['M', 'F'])],
                    'firstname' => [new NotBlank()],
                    'lastname' =>  [new NotBlank()]
                ]
        ];
    }

    /**
     * @param string[] $data Data send to the PSP
     * @return string[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     * @throws SomeParametersAreInvalid
     */
    public function createMandate(int $paymentProcessorId, int $orderId, array $data): array
    {
        $this->client->mustBeAuthenticated();

        // Data validation
        $this->generatePspConstraints();
        $this->validateDirectDebitData($paymentProcessorId, $data);

        try {
            return $this->client->post(
                'direct_debit_payment/create_mandate/' . $paymentProcessorId . '/' . $orderId,
                [RequestOptions::FORM_PARAMS => $data]
            );
        } catch (ClientException $e) {
            if (400 === $e->getResponse()->getStatusCode()) {
                throw new SomeParametersAreInvalid($e->getMessage(), $e->getCode(), $e);
            }
            throw $e;
        }
    }

    /**
     * @return string[]
     * @throws SomeParametersAreInvalid
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function processPayment(int $paymentProcessorId, int $orderId): array
    {
        $this->client->mustBeAuthenticated();

        try {
            return $this->client->post(
                'direct_debit_payment/process_payment/' . $paymentProcessorId . '/' . $orderId
            );
        } catch (ClientException $e) {
            if (400 === $e->getResponse()->getStatusCode()) {
                throw new SomeParametersAreInvalid($e->getMessage(), $e->getCode(), $e);
            }
            throw $e;
        }
    }

    /**
     * @param string[] $data
     * @throws SomeParametersAreInvalid
     * @internal
     */
    protected function validateDirectDebitData(int $paymentProcessorId, array $data): void
    {
        if (false === \array_key_exists($paymentProcessorId, $this->constraintsByPsp)) {
            throw new SomeParametersAreInvalid('Invalid payment processor id');
        }

        $validator = Validation::createValidatorBuilder()->getValidator();
        $violations = $validator->validate(
            $data,
            new Collection(
                [
                    'fields' => $this->constraintsByPsp[$paymentProcessorId],
                    'allowExtraFields' => false,
                    'missingFieldsMessage' => "'{{ field }}' must be set",
                ]
            )
        );

        if (\count($violations) > 0) {
            throw new SomeParametersAreInvalid(
                'Mandate creation data validation failed: ' . json_encode(
                    array_map(
                        function (ConstraintViolationInterface $violation): array {
                            return [
                                'field' => $violation->getPropertyPath(),
                                'message' => $violation->getMessage(),
                            ];
                        },
                        iterator_to_array($violations)
                    )
                )
            );
        }
    }
}
