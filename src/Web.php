<?php
namespace peeto\DarkChat;
/**
 * This file is used by the Chat class for rendering HTML
 */

// iname is the "instance name" used in id's and other web components to
// facilitate multiple darkchat instances on the same web page
$iname = $this->getInput('instance');

// display components
include 'web/header.php';
if ($this->getConfig('SHOW_MESSAGES_FIRST'))
{
    include 'web/displaymessages.php';
    include 'web/sendmessage.php';
} else {
    include 'web/sendmessage.php';
    include 'web/displaymessages.php';
}
include 'web/footer.php';