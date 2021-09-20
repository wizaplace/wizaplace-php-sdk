<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\User;

use Wizaplace\SDK\Pagination;

final class UsersPaginatedResult
{
    /** @var User[] */
    private $users;
    /** @var Pagination */
    private $pagination;

    /** @param mixed[] $data */
    public function __construct(array $data)
    {
        $this->users = array_map(
            static function (array $user): User {
                return new User($user);
            },
            $data['results']
        );
        $this->pagination = new Pagination(
            [
                'page' => $data['page'],
                'nbResults' => $data['totalElements'],
                'nbPages' => (int) \ceil($data['totalElements'] / $data['elements']),
                'resultsPerPage' => $data['elements'],
            ]
        );
    }

    /** @return User[] */
    public function getUsers(): array
    {
        return $this->users;
    }

    /** @return Pagination */
    public function getPagination(): Pagination
    {
        return $this->pagination;
    }
}
