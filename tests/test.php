<?php 
require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use DarkChat\Chat;

var_dump(Chat::load('test'));

//php tests/test.php
