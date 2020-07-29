<?php

class Flashy_Sms {

    public function __construct($master) {
        $this->master = $master;
    }

    /**
     * Send a new text message through Flashy
     * @param struct $message the information on the message to send
     *     - from_name string optional from name to be used
     *     - to string of recipient phone number.
     *     - message string text message that will be sent
     *     - track_clicks boolean whether or not to turn on click tracking for the message
     * @param string $send_at when this message should be sent as a UTC timestamp in YYYY-MM-DD HH:MM:SS format. If you specify a time in the past, the message will be sent immediately. An additional fee applies for scheduled email, and this feature is only available to accounts with a positive balance.
     * @return array of structs for each recipient containing the key "email" with the email address, and details of the message status for that recipient
     *     - return[] struct the sending results for a single recipient
     *         - status string the sending status of the recipient - either "sent", "queued", "scheduled", "rejected", "failed" or "invalid"
     *         - error_code string the reason for the rejection if the recipient status is "rejected" - one of "hard-bounce", "soft-bounce", "spam", "unsub", "custom", "invalid-sender", "invalid", "test-mode-limit", or "rule"
     *         - id string the message's unique id
     */
    public function send($message, $async=false, $send_at=null) {
        $_params = array("message" => $message, "async" => $async, "send_at" => $send_at);
        return $this->master->call('sms/send', $_params);
    }
}