<?php
namespace Flashy\Integration\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Carthash Model
 */
class Carthash extends AbstractModel
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Flashy\Integration\Model\ResourceModel\Carthash::class);
    }

}
