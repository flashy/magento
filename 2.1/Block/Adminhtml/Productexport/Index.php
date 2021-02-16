<?php
namespace Flashy\Integration\Block\Adminhtml\Productexport;

class Index extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->removeButton('back')->removeButton('reset');
        $this->updateButton('save','label', "Export Products");
        $this->_objectId = 'export_id';
        $this->_blockGroup = 'Flashy_Integration';
        $this->_controller = 'adminhtml_productexport';
    }

    /**
     * Get header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Flashy Product Export');
    }
}
