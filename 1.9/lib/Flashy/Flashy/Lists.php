<?php

class Flashy_Lists {

    public function __construct($master)
    {
        $this->master = $master;
    }

    /**
     * Subscribe contact to list
     * @param struct $subscriber
     *     - email string required valid email address.
     *     - phone string of subscriber phone number.
     *     - first_name string of recipient phone number.
     *     - last_name string text message that will be sent
     *     - source string contact source.
     * @return array of structs 
     *     - return[] struct the sending results for a single recipient
     *         - success boolean true / false
     *         - errors array error list
     *         - subscriber array if the subscription created successfully
     */
    public function subscribe($list_id, $subscriber)
    {
        $_params = array("subscriber" => $subscriber);

        $subscriber = $this->master->call('lists/' . $list_id .'/subscribe', $_params);

        // If we created the subscription successfully we will also send all the events history of the contact + setC.
        if( isset($subscriber['success']) && $subscriber['success'] == true )
        {
            $this->master->events->bulk($subscriber['subscriber']['contact_id']);

            $this->master->events->setCustomer($subscriber['subscriber']['email']);
        }

        return $subscriber;
    }

    public function all()
    {
        $_params = array();

        $lists = $this->master->call('lists', $_params);

        return $lists;
    }
}