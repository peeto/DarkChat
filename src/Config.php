<?php
namespace peeto\DarkChat;


/**
 * Config class
 * 
 * Loads the configuration for DarkChat
 */
class Config
{
    protected $config;

    public function __construct($configfile) {
        if ($configfile!='' && file_exists($configfile)) {
            include $configfile;
        } elseif (file_exists(__DIR__ . '/config/config.php')) {
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
