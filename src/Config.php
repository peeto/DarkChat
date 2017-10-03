<?php
namespace DarkChat;
require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload


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
