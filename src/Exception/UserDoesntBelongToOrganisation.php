<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Exception;

use Throwable;

class UserDoesntBelongToOrganisation extends \Exception
{
    /**
     * UserDoesntBelongToOrganisation constructor.
     * @param string $message
     * @param Throwable|null $previous
     * @internal
     */
    public function __construct($message = "", Throwable $previous = null)
    {
        parent::__construct($message, 404, $previous);
    }
}
