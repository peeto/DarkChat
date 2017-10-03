<?php
namespace peeto\DarkChat;
// Autoload files using Composer autoload
require_once __DIR__ . '/../../vendor/autoload.php';


use peeto\DarkChat\Config;
use PHPUnit\Framework\TestCase;

/**
 * phpunit test to test if messages can be sent and received
 */
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
        $chat = Chat::load([
            'name' => 'test'
        ]);

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
        $chat = Chat::load([
            'name' => 'test'
        ]);

        $this->assertArrayHasKey('date_time', $chat->getInput('messages')[0]);
    }

}

