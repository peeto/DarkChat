<?php
namespace DarkChat;
require_once __DIR__ . '/../../vendor/autoload.php'; // Autoload files using Composer autoload


use DarkChat\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{

    public function testHasConfig()
    {
        $config = new Config();
        $this->assertObjectHasAttribute('config', $config);
    }
}

