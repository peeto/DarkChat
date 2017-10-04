<?php
// Autoload files using Composer autoload
require_once __DIR__ . '/../vendor/autoload.php';

use peeto\DarkChat\Chat;
?>
<!DOCTYPE html>
<!--
Copyright (C) 2017 Chris Petersen

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
-->
<html>
    <head>
        <title>DarkChat</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style type="text/css">
        <!--
        .darkchat .messages {
            display: inline-block;
            overflow-y: scroll;
            height: 200px;
            width: 100%;
            margin: 10px;
        }
        .darkchat .messages .messageblock {
            margin: 10px;
            padding: 10px;
        }
        .darkchat .messages .messageblock:nth-child(odd) {
            background-color: #EEE;
        }
        .darkchat .messages .header .datetime {
            font-size: small;
        }
        .darkchat .messages .header .name {
            font-weight: bold;
        }
        .darkchat .messages .header .said {
            font-size: small;
            font-style: italic;
        }
        
        -->
        </style>
    
    </head>
    <body>
<?php

Chat::load([
    'name' => 'test',
    'xml_message_route' => 'tryheader.php',
    'xml_send_message_route' => 'tryheader.php'
]);

?>
    </body>
</html>