<?php
namespace DarkChat;
require_once __DIR__ . '/../../vendor/autoload.php'; // Autoload files using Composer autoload


use DarkChat\Config;
use PHPUnit\Framework\TestCase;

class ChatTest extends TestCase
{

    public function testCanSend()
    {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['HTTP_USER_AGENT'] = 'phpunit';
        $_GET['hc'] = 'send';
        $_GET['sendname'] = 'phpunit';
        $_GET['sendmessage'] = 'Message from phpunit.';
        $_GET['tzoffset'] = '11';

        $this->setOutputCallback(function() {});
        $chat = Chat::load('test');

        $this->assertEquals( 'Message sent', $chat->getInput('status'));
    }

    public function testHasMessages()
    {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['HTTP_USER_AGENT'] = 'phpunit';
        $_GET['hc'] = '';
        $_GET['sendname'] = 'phpunit';
        $_GET['sendmessage'] = 'Message from phpunit.';
        $_GET['tzoffset'] = '11';

        //$this->setOutputCallback(function() {});
        $chat = Chat::load('test');

        $this->assertArrayHasKey('date_time', $chat->getInput('messages')[0]);
    }

}

