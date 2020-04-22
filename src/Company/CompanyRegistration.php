<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Company;

class CompanyRegistration extends AbstractCompanyRegistration
{
    /**
     * CompanyRegistration constructor.
     *
     * @param string $name
     * @param string $email
     */
    public function __construct(string $name, string $email)
    {
        parent::__construct(false);
        $this->name = $name;
        $this->email = $email;
    }
}
