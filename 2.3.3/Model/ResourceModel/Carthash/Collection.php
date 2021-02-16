<?php
namespace Flashy\Integration\Model\ResourceModel\Carthash;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Carthash Resource Model Collection
 */
class Collection extends AbstractCollection
{
    /**
     * Initialize resource collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Flashy\Integration\Model\Carthash', 'Flashy\Integration\Model\ResourceModel\Carthash');
    }
}