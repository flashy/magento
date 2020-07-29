<?php

class Flashy_Contacts {

    public function __construct($master)
    {
        $this->master = $master;
    }

    /**
     * Create contact
     * @param struct $contact
     *     - email string required valid email address.
     *     - phone string of contact phone number.
     *     - first_name string of recipient phone number.
     *     - last_name string text message that will be sent
     *     - source string contact source.
     * @return array of structs 
     *     - return[] struct the sending results for a single recipient
     *         - success boolean true / false
     *         - errors array error list
     *         - contact array if the contact created successfully
     */
    public function create($contact)
    {
        $_params = array("contact" => $contact);

        $contact = $this->master->call('contacts/create', $_params);

        // If we created the contact successfully we will also send all the events history of the contact + setCustomer.
        if( isset($contact['success']) && $contact['success'] == true )
        {
            $this->master->events->bulk($contact['contact']['contact_id']);

            $this->master->events->setCustomer($contact['contact']['email']);
        }

        return $contact;
    }

    /**
     * Create contact
     * @param string $email email address of the contact we want to update
     * @param struct $contact
     *     - phone string of contact phone number.
     *     - first_name string of recipient phone number.
     *     - last_name string text message that will be sent
     *     - source string contact source.
     * @return array of structs 
     *     - return[] struct the sending results for a single recipient
     *         - success boolean true / false
     *         - errors array error list
     *         - contact array if the contact created successfully
     */
    public function update($email, $contact)
    {
        $_params = array("email" => $email, "contact" => $contact);

        $contact = $this->master->call('contacts/update', $_params);

        return $contact;
    }

    /**
     * Create contact
     * @param string $email email address of the contact we want to get
     * @return array of structs 
     *     - return[] struct the sending results for a single recipient
     *         - success boolean true / false
     *         - errors array error list
     *         - contact array if the contact created successfully
     */
    public function get($email)
    {
        $_params = array("email" => $email);

        $contact = $this->master->call('contacts', $_params);

        return $contact;
    }
}