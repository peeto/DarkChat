<form name="<?php echo $iname; ?>frmsendmessage" method="post" action="<?php echo $this->getInput('route'); ?>" onsubmit="return <?php echo $iname; ?>sendMessage()">
<input type="hidden" name="hc" value="send">
<div class="sendmessage">
<table>
    <tr>
        <th align="right" valign="top">
            <label for="<?php echo $iname; ?>sendname">Name</label>
        </th>
        <td align="left" valign="top">
            <input type="text" id="<?php echo $iname; ?>sendname" name="sendname" value="<?php echo $this->getInput('name'); ?>" size="32" maxlength="255" />
        </td>
    </tr>
    <tr>
        <th align="right" valign="top">
            <label for="<?php echo $iname; ?>sendmessage">Message</label>
        </th>
        <td align="left" valign="top">
            <textarea id="<?php echo $iname; ?>sendmessage" name="sendmessage" cols="40" rows="4" maxlength="4096"></textarea>
        </td>
    </tr>
    <tr>
        <th align="right" valign="top"></th>
        <td align="left" valign="top">
            <input id="<?php echo $iname; ?>sendsubmit" type="submit" value="Send"> 
            <input id="<?php echo $iname; ?>sendreset" type="reset" value="Clear"> 
            <span id="<?php echo $iname; ?>sendstatus"><?php echo $this->getInput('status'); ?></span>
        </td>
    </tr>
</table>
</div>
</form>