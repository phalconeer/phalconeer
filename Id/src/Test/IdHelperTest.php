<?php
namespace Phalconeer\Id\Test;

use Phalconeer\Id;
use Test;

class IdHelperTest extends Test\UnitTestCase
{
    public function testIndenpendentSafeUserIdGenerator()
    {
        $limit = 10;
        $userIdsGenerated = [];
        for ($j = 0; $j < $limit; $j++) {
            $safeUserId = Id\Helper\IdHelper::generateIndependentSafeUserId(
                null,
                [
                    '+' => 'fn',
                    '/' => 'tc'
                ]
            );
            $userIdsGenerated[$safeUserId] = true;
        }

        $this->assertEquals($limit, count($userIdsGenerated), 'Independent SafeUserId generated non-unique items');
    }
}