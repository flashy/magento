<?php
namespace Flashy\Integration\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Carthash Resource Model
 */
class Carthash extends AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('flashy_cart_hash', 'id');
    }
}
