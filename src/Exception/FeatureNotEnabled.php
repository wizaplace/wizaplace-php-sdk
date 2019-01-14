<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Exception;

use Throwable;

class FeatureNotEnabled extends \Exception
{
    /**
     * @internal
     *
     * @param string         $message
     * @param Throwable|null $previous
     */
    public function __construct($message = "", Throwable $previous = null)
    {
        parent::__construct($message, 501, $previous);
    }
}
