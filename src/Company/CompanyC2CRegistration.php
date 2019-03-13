<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Company;

class CompanyC2CRegistration extends AbstractCompanyRegistration
{
    /**
     * CompanyC2CRegistration constructor.
     */
    public function __construct()
    {
        parent::__construct(true);
    }
}
