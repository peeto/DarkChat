<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use DarkChat\Chat;

Chat::load([
    'name' => 'test'
]);

