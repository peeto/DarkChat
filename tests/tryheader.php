<?php
// Autoload files using Composer autoload
require_once __DIR__ . '/../vendor/autoload.php';

use peeto\DarkChat\Chat;

Chat::load([
    'name' => 'test'
]);

