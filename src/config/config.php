<?php
namespace DarkChat;

/**
 * DarkChat configuration file
 */
$config = [
    /**
     * Where the database is located relative to the src directory
     */
    'DATABASE_LOCATION' => '/database/darkchat.db',
    
    /**
     * Maximum numbers to keep in the database
     */
    'NUM_MESSAGES_KEEP' => 50,
    
    /**
     * Maximum number of message to display
     */
    'NUM_MESSAGES_DISPLAY' => 20,
    
    /**
     * How dates and time are displayed
     * see PHP's date() function
     */
    'TIME_FORMAT' => 'g:i:sa l jS F Y',
    
    /**
     * How often new messages will be loaded by JavaScript
     * (milliseconds)
     */
    'MESSAGES_REFRESH_DELAY' => 5000,
    
    /**
     * How long status messages will display
     * (milliseconds)
     */
    'UI_STATUS_SHOW_TIME' => 2500
];

