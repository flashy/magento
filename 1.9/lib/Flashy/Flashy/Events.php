<?php

class Flashy_Events {

    public function __construct($master)
    {
        $this->master = $master;
    }

    public function track($contact_id = null)
    {
        if($contact_id == null)
            $contact_id = $this->getContactId($contact_id);

        if($contact_id == null) return array('success' => false, 'errors' => 'contact id or flashy id not found');

        $_params = array("contact_id" => $contact_id);

        return $this->master->call('track', $_params, 'events');
    }

    public function bulk($contact_id = null, $events_list = "cookie", $identity = "contact_id")
    {
        if( $contact_id == null )
        {
            $identity = "flashy_id";

            $contact_id = $this->getContactId($contact_id);
        }

        if($contact_id == null) return array('success' => false, 'errors' => 'contact id or flashy id not found');

        if($events_list == "cookie" && isset($_COOKIE['flashy_thunder']))
        {
            $events = json_decode(base64_decode($_COOKIE['flashy_thunder']), true);

            foreach ($events as &$event)
            {
                $event['body'][$identity] = ( $identity == "contact_id" ) ? $contact_id : base64_encode($contact_id);
            }
        }
        else if( $events_list !== "cookie" )
        {
            $events = $events_list;
        }
        else
        {
            $events = array();
        }

        if(count($events) == 0) return array('success' => false, 'errors' => 'events not found');

        $_params = array("events" => $events);

        $call = $this->master->call('bulk', $_params, 'events');

        if( isset($call['success']) && $call['success'] == true )
        {
            $this->deleteCookie();
        }

        return $call;
    }

    public function deleteCookie()
    {
        setcookie("flashy_thunder", "", time()-3600, "/");
    }

    public function setCustomer($flashy_id)
    {
        if( !isset($_COOKIE['flashy_id']) )
            setcookie("flashy_id", base64_encode($flashy_id), time() + (360 * 24 * 60 * 60), "/");
    }

    public function getContactId($contact_id = null) {
        if( isset($_COOKIE['flashy_id']) )
            return $_COOKIE['flashy_id'];
        else
            return null;
    }
}