<?php
namespace peeto/DarkChat;

class DarkChatConfig
{
    protected $config;

    public function __construct() {
        include('config/config.php');
        $this->config = $config;
    }

    protected function getConfig( $key ) {
        return $this->config[$key];
    }
}
