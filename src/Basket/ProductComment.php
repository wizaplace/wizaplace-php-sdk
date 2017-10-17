<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);


namespace Wizaplace\SDK\Basket;

use Wizaplace\SDK\Exception\SomeParametersAreInvalid;

final class ProductComment extends Comment
{
    /**
     * @var string
     */
    private $declinationId;

    public function __construct(string $declinationId, string $comment)
    {
        parent::__construct($comment);
        if ($declinationId === '') {
            throw new SomeParametersAreInvalid('Missing declination Id');
        }

        $this->declinationId = $declinationId;
    }

    public function getDeclinationId(): string
    {
        return $this->declinationId;
    }

    /**
     * @internal
     */
    public function toArray(): array
    {
        return [
            'declinationId' => $this->declinationId,
            'comment' => $this->comment,
        ];
    }
}
