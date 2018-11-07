<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK;

class RequestLogger
{
    /**
     * @var string
     */
    private $logFilename;

    /**
     * @param string $logFilename
     */
    public function __construct(string $logFilename)
    {
        $this->logFilename = $logFilename;
    }

    /**
     * @param string $method
     * @param string $url
     * @param int $requestExecutionTime
     */
    public function log(string $method, string $url, int $requestExecutionTime): void
    {
        $message = sprintf(
            "%s %s %s %d\n",
            (new \DateTime())->format('c'),
            $method,
            $url,
            $requestExecutionTime
        );

        try {
            $handle = fopen($this->logFilename, 'a');
            fwrite($handle, $message);
            fclose($handle);
        } catch (\Throwable $throwable) {
            error_log($throwable);
        }
    }
}
