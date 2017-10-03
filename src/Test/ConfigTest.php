<?php
namespace DarkChat;
// Autoload files using Composer autoload
require_once __DIR__ . '/../../vendor/autoload.php';


use DarkChat\Config;
use PHPUnit\Framework\TestCase;

/**
 * phpunit test to test if the configuration can be loaded
 */
class ConfigTest extends TestCase
{

    public function testHasConfig()
    {
        $config = new Config();
        $this->assertObjectHasAttribute('config', $config);
    }
}

