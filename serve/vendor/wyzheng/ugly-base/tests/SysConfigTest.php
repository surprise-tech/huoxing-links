<?php

namespace Ugly\Base\Tests;

class SysConfigTest extends TestCase
{
    public function test_sys_config()
    {
        sys_config(['title' => 'string', 'array' => ['key' => 'val']]);
        $this->assertTrue(sys_config('title') === 'string');
        $this->assertTrue(is_array(sys_config('array')));
        $this->assertTrue(sys_config('array')['key'] === 'val');
    }
}
