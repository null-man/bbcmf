<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Haotong Lin <lofanmi@gmail.com>
// +----------------------------------------------------------------------

/**
 * 保证运行环境正常
 */
class baseTest extends \PHPUnit_Framework_TestCase
{
    public function testIndex(){
        $this->assertEquals('1', '1');
    }
}
