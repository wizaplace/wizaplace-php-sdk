<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Group;

use Wizaplace\SDK\Group\Group;
use Wizaplace\SDK\Group\GroupService;
use Wizaplace\SDK\Tests\ApiTestCase;

/**
 * @see GroupService
 */
final class GroupServiceTest extends ApiTestCase
{
    public function testCreateGroup()
    {
        $groupService = $this->buildAdminGroupService();
        $group = $groupService->create('Test Group');
        static::assertUuid($group->getId());
        static::assertSame('Test Group', $group->getName());
    }

    public function testCreateGroupWithExistingName()
    {
        $groupService = $this->buildAdminGroupService();
        static::expectExceptionMessage('The group name is already used');
        static::expectExceptionCode(409);
        $groupService->create('Test Group');
    }

    public function testCreateGroupWithEmptyName()
    {
        $groupService = $this->buildAdminGroupService();
        static::expectExceptionMessage('name must be string and not empty');
        static::expectExceptionCode(400);
        $groupService->create('');
    }

    public function testCreateGroupWithNotAdminToken()
    {
        $groupService = $this->buildAdminGroupService('user@wizaplace.com');
        static::expectExceptionCode(403);
        $groupService->create('');
    }

    public function testUpdateGroup()
    {
        $groupService = $this->buildAdminGroupService();
        $groups = $groupService->list()->getItems();
        $groupBeforeUpdate = $groups[0];
        $groupBeforeUpdate->setName('updated Name');
        $group = $groupService->update($groupBeforeUpdate);
        static::assertSame('updated Name', $group->getName());
    }

    public function testUpdateGroupWithEmptyName()
    {
        $groupService = $this->buildAdminGroupService();
        $groups = $groupService->list()->getItems();
        $groupBeforeUpdate = $groups[0];
        $groupBeforeUpdate->setName('');
        static::expectExceptionMessage('name must be string and not empty');
        static::expectExceptionCode(400);
        $groupService->update($groupBeforeUpdate);
    }

    public function testUpdateGroupWithExistingName()
    {
        $groupService = $this->buildAdminGroupService();
        $groups = $groupService->list()->getItems();
        $groupBeforeUpdate = $groups[0];
        $groupBeforeUpdate->setName('updated Name');
        static::expectExceptionMessage('The group name is already used');
        static::expectExceptionCode(409);
        $groupService->update($groupBeforeUpdate);
    }

    public function testListGroupsWithoutOffsetAndLimit()
    {
        $groupService = $this->buildAdminGroupService();
        $listGroup = $groupService->list();
        //default values
        static::assertSame(0, $listGroup->getOffset());
        static::assertSame(10, $listGroup->getLimit());
        static::assertLessThanOrEqual(10, \count($listGroup->getItems()));
        foreach ($listGroup->getItems() as $group) {
            static::assertInstanceOf(Group::class, $group);
            static::assertUuid($group->getId());
            static::assertNotEmpty($group->getName());
        }
    }

    public function testListGroupsWithOffsetAndLimit()
    {
        $groupService = $this->buildAdminGroupService();
        $listGroup = $groupService->list(1, 5);

        static::assertSame(1, $listGroup->getOffset());
        static::assertSame(5, $listGroup->getLimit());
        static::assertLessThanOrEqual(5, \count($listGroup->getItems()));
    }

    public function testListGroupsWithInvalidOffset()
    {
        $groupService = $this->buildAdminGroupService();

        static::expectExceptionMessage('Offset must be a positive integer or zero, -15 given');
        static::expectExceptionCode(400);
        $groupService->list(-15, 5);
    }

    public function testListGroupsWithInvalidLimit()
    {
        $groupService = $this->buildAdminGroupService();

        static::expectExceptionMessage('Limit must be a positive integer, -1 given');
        static::expectExceptionCode(400);
        $groupService->list(1, -1);
    }

    public function testAddUsersToGroup()
    {
        $groupService = $this->buildAdminGroupService();
        $group = $groupService->create('Test Group 1');
        $response = $groupService->addUsers($group->getId(), [1, 2]);
        static::assertCount(2, $response);
        static::assertSame([1, 2], $response);
    }

    public function testAddUsersToNonExistentGroup()
    {
        $groupService = $this->buildAdminGroupService();

        static::expectExceptionMessage('Group \u0022404\u0022 not found');
        static::expectExceptionCode(404);
        $groupService->addUsers('404', [1, 2]);
    }

    public function testAddUsersToGroupWhenGroupHaveUsers()
    {
        $groupService = $this->buildAdminGroupService();
        static::expectExceptionMessage('Group must be empty');
        static::expectExceptionCode(409);
        $groupService->addUsers('6192db86-95ec-11eb-9c2c-0242ac120009', [1, 2]);
    }

    public function testAddUsersToGroupWithNotAdminToken()
    {
        $groupService = $this->buildAdminGroupService('user@wizaplace.com');

        static::expectExceptionCode(403);
        $groupService->addUsers('6192db86-95ec-11eb-9c2c-0242ac120009', [1, 2]);
    }

    public function testAddUserToGroup()
    {
        $groupService = $this->buildAdminGroupService();
        $listUsers = $groupService->listUsers('4745118a-a762-11eb-a843-0242ac12000a');
        static::assertCount(2, $listUsers->getItems());
        $groupService->addUser('4745118a-a762-11eb-a843-0242ac12000a', 3);
        $listUsers = $groupService->listUsers('4745118a-a762-11eb-a843-0242ac12000a');
        static::assertCount(3, $listUsers->getItems());
    }

    public function testAddUserToNonExistentGroup()
    {
        $groupService = $this->buildAdminGroupService();

        static::expectExceptionMessage('Group \u0022404\u0022 not found');
        static::expectExceptionCode(404);
        $groupService->addUser('404', 1);
    }

    public function testAddUserToGroupWithNotAdminToken()
    {
        $groupService = $this->buildAdminGroupService('user@wizaplace.com');

        static::expectExceptionCode(403);
        $groupService->addUser('6192db86-95ec-11eb-9c2c-0242ac120009', 1);
    }

    public function testDeleteUserFromGroup()
    {
        $groupService = $this->buildAdminGroupService();
        $listUsers = $groupService->listUsers('4745118a-a762-11eb-a843-0242ac12000a');
        static::assertCount(3, $listUsers->getItems());
        $groupService->deleteUser('4745118a-a762-11eb-a843-0242ac12000a', 3);
        $listUsers = $groupService->listUsers('4745118a-a762-11eb-a843-0242ac12000a');
        static::assertCount(2, $listUsers->getItems());
    }

    public function testDeleteUserToNonExistentGroup()
    {
        $groupService = $this->buildAdminGroupService();

        static::expectExceptionMessage('Group \u0022404\u0022 not found');
        static::expectExceptionCode(404);
        $groupService->deleteUser('404', 1);
    }

    public function testDeleteUserGroupWithNotAdminToken()
    {
        $groupService = $this->buildAdminGroupService('user@wizaplace.com');

        static::expectExceptionCode(403);
        $groupService->deleteUser('6192db86-95ec-11eb-9c2c-0242ac120009', 1);
    }

    public function testDeleteUsersFromGroup()
    {
        $groupService = $this->buildAdminGroupService();
        $listUsers = $groupService->listUsers('4745118a-a762-11eb-a843-0242ac12000a');
        static::assertCount(2, $listUsers->getItems());
        $groupService->deleteUsers('4745118a-a762-11eb-a843-0242ac12000a');
        $listUsers = $groupService->listUsers('4745118a-a762-11eb-a843-0242ac12000a');
        static::assertCount(0, $listUsers->getItems());
    }

    public function testDeleteUsersToNonExistentGroup()
    {
        $groupService = $this->buildAdminGroupService();

        static::expectExceptionMessage('Group \u0022404\u0022 not found');
        static::expectExceptionCode(404);
        $groupService->deleteUsers('404');
    }

    public function testDeleteUsersGroupWithNotAdminToken()
    {
        $groupService = $this->buildAdminGroupService('user@wizaplace.com');

        static::expectExceptionCode(403);
        $groupService->deleteUsers('6192db86-95ec-11eb-9c2c-0242ac120009');
    }

    public function testListUsersGroup()
    {
        $groupService = $this->buildAdminGroupService();

        $groupService->addUsers('4745118a-a762-11eb-a843-0242ac12000a', [1, 2, 3]);
        $listUsers = $groupService->listUsers('4745118a-a762-11eb-a843-0242ac12000a');
        static::assertCount(3, $listUsers->getItems());
        //default value
        static::assertSame(0, $listUsers->getOffset());
        static::assertSame(10, $listUsers->getLimit());

        //with offset and limit
        $listUsers = $groupService->listUsers('4745118a-a762-11eb-a843-0242ac12000a', 1, 1);
        static::assertCount(1, $listUsers->getItems());
        static::assertSame(1, $listUsers->getOffset());
        static::assertSame(1, $listUsers->getLimit());

        //with invalid offset
        static::expectExceptionMessage('Offset must be a positive integer or zero, -15 given');
        static::expectExceptionCode(400);
        $groupService->listUsers('4745118a-a762-11eb-a843-0242ac12000a', -15, 5);
    }

    public function testListUsersGroupWithNotAdminToken()
    {
        $groupService = $this->buildAdminGroupService('user@wizaplace.com');

        static::expectExceptionCode(403);
        $groupService->listUsers('6192db86-95ec-11eb-9c2c-0242ac120009');
    }

    private function buildAdminGroupService($email = 'admin@wizaplace.com'): GroupService
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($email, static::VALID_PASSWORD);

        return new GroupService($apiClient);
    }
}
