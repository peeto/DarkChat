<?php
namespace peeto\DarkChat;
// Autoload files using Composer autoload
require_once __DIR__ . '/../vendor/autoload.php';


/**
 * Config class
 * 
 * Loads the configuration for DarkChat
 */
class Config
{
    protected $config;

    public function __construct() {
        include(__DIR__ . '/config/config.php');
        $this->config = $config;
    }

    protected function getConfig($key) {
        return $this->config[$key];
    }
}
