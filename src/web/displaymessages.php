<div id="<?php echo $iname; ?>messages" class="messages">
<?php
// if there are messages loop through all messages
if($this->getInput('messages')) foreach ($this->getInput('messages') as $message) {
?>
    <div class="messageblock">
        <div class="header">
            <div class="datetime"><?php echo $message["date_time"]; ?></div>
            <div class="username">
                <span class="name"><?php echo $message["name"]; ?></span>
                <span class="said">said...</span>
            </div>
        </div>
        <div class="messagetext"><?php echo $message["message"]; ?></div>
    </div>
<?php
} // end loop
?>
</div>