<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm
 *
 * User: kx
 * Date: 2024/11/20
 * Time: 16:23
 */

namespace Shanla\Tools\Tests;


use PHPUnit\Framework\TestCase;
use Shanla\Tools\CheckTool;

class CheckToolTest extends TestCase
{
    public function testCheckMobile()
    {
        $result = CheckTool::checkMobile('127sdfadfk');
        $this->assertEquals(false, $result);
        $result = CheckTool::checkMobile('13121341212');
        $this->assertEquals(true, $result);
    }

    public function testCheckEmail()
    {
        $result = CheckTool::checkEmail('127sdfadfk');
        $this->assertEquals(false, $result);
        $result = CheckTool::checkEmail('895560759@qq.com');
        $this->assertEquals(true, $result);
    }
}