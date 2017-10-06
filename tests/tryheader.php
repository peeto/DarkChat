<?php
// Autoload files using Composer autoload
require_once dirname(__DIR__) . '/vendor/autoload.php'; 

use peeto\DarkChat\Chat;

Chat::load([
    'name' => 'test'
]);

