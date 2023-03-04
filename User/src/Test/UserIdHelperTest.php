<?php
namespace Phalconeer\User\Test;

use Phalconeer\User;
use Test;

class UserIdHelperTest extends Test\UnitTestCase
{
    public function testDefaultSafeUserIdGenerator()
    {
        $i = 100;
        $limit = 10;
        $userIdsGenerated = [];
        for ($j = 0; $j < $limit; $j++) {
            $safeUserId = User\Helper\UserIdHelper::generateSafeUserId($i++, 1);
            $userIdsGenerated[$safeUserId] = true;
        }

        $this->assertEquals($limit, count($userIdsGenerated), 'SafeUserId generated non-unique items');
    }

    public function testIndenpendentSafeUserIdGenerator()
    {
        $limit = 10;
        $userIdsGenerated = [];
        for ($j = 0; $j < $limit; $j++) {
            $safeUserId = User\Helper\UserIdHelper::generateIndependentSafeUserId();
            $userIdsGenerated[$safeUserId] = true;
        }

        $this->assertEquals($limit, count($userIdsGenerated), 'Independent SafeUserId generated non-unique items');
    }
}