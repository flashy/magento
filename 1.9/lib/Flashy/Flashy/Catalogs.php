<?php

class Flashy_Catalogs {

    public function __construct($master)
    {
        $this->master = $master;
    }

    /**
     * Create catalog
     * @param struct $catalog
     * @return array of structs 
     *     - return[] struct the sending results for a single recipient
     *         - success boolean true / false
     *         - errors array error list
     *         - catalog array if the catalog created successfully
     */
    public function create($catalog)
    {
        $_params = array("catalog" => $catalog);

        $catalog = $this->master->call('catalogs/create', $_params);

        return $catalog;
    }

    /**
     * Get catalogs
     * @return array of structs 
     *     - return[] struct the sending results for a single recipient
     *         - success boolean true / false
     *         - errors array error list
     */
    public function get()
    {
        $_params = array();

        $catalogs = $this->master->call('catalogs', $_params);

        return $catalogs;
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
    public function search($title)
    {
        $_params = array("title" => $title);

        $catalog = $this->master->call('catalogs/search', $_params);

        return $catalog;
    }
}