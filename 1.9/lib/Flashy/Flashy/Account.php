<?php

class Flashy_Account {

    public function __construct($master)
    {
        $this->master = $master;
    }

    /**
     * Get account info
     * @return array
     *     - return[]
     *         - success boolean true / false
     *         - errors array error list
     *         - account array
     */
    public function info()
    {
        $_params = array();

        $account = $this->master->call('account/info', $_params);

        return $account;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function connect($data)
    {
        $_params = $data;

        $account = $this->master->call('platforms/connect', $_params);

        return $account;
    }
}