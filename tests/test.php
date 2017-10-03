<?php 
require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use DarkChat\Chat;
use PHPUnit\Framework\TestCase;

//var_dump(Chat::load('test'));


        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['HTTP_USER_AGENT'] = 'phpunit';
        $_GET['hc'] = 'send';
        $_GET['sendname'] = 'phpunit';
        $_GET['sendmessage'] = '<b>Message from phpunit</b>.';

        $chat = Chat::load('test');



//php tests/test.php
