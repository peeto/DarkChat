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
        if (file_exists(__DIR__ . '/config/config.php')) {
            include __DIR__ . '/config/config.php';
        } elseif (__DIR__ . '/config/config_default.php') {
            include __DIR__ . '/config/config_default.php';
        } else {
            throw new Exception('Configuration missing');
        }
        $this->config = $config;
        unset($config);
    }

    protected function getConfig($key) {
        return $this->config[$key];
    }
}
