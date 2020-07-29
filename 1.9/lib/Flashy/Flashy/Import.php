<?php

class Flashy_Import {

    public function __construct($master)
    {
        $this->master = $master;
    }

    /**
     * Import products
     * @param struct $products
     *     - product
     * @return array of structs 
     *     - return[] struct the sending results for a single recipient
     *         - success boolean true / false
     *         - errors array of error products
     */
    public function products($products, $catalog_id)
    {
        $_params = array("products" => $products, "catalog_id" => $catalog_id);

        $products = $this->master->call('import/products', $_params);

        return $products;
    }
}