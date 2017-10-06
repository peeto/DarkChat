<?php 
// Autoload files using Composer autoload
require_once dirname(__DIR__) . '/vendor/autoload.php'; 

use peeto\DarkChat\Chat;
use PHPUnit\Framework\TestCase;

/**
 * Independent file to test if DarkChat can send a message
 * 
 * Usage: php tests/test.php
 */
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['HTTP_USER_AGENT'] = 'phpunit';
$_GET['hc'] = 'send';
$_GET['sendname'] = 'phpunit';
$_GET['sendmessage'] = '<b>Message from phpunit</b>.';

$chat = Chat::load([
    'name' => 'test'
]);

