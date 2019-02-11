<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Basket;

use Wizaplace\SDK\Catalog\DeclinationId;
use function theodorejb\polycast\to_string;

/**
 * Class ProductComment
 * @package Wizaplace\SDK\Basket
 */
final class ProductComment extends Comment
{
    /**
     * @var DeclinationId
     */
    private $declinationId;

    /**
     * ProductComment constructor.
     *
     * @param DeclinationId $declinationId
     * @param string        $comment
     */
    public function __construct(DeclinationId $declinationId, string $comment)
    {
        parent::__construct($comment);

        $this->declinationId = $declinationId;
    }

    /**
     * @return DeclinationId
     */
    public function getDeclinationId(): DeclinationId
    {
        return $this->declinationId;
    }

    /**
     * @internal
     */
    public function toArray(): array
    {
        return [
            'declinationId' => to_string($this->declinationId),
            'comment' => $this->comment,
        ];
    }
}
