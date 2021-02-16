<?php
namespace Flashy\Integration\Block\Restore;

class Cart extends \Magento\Framework\View\Element\Template
{

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Get key from request
     *
     * @return mixed
     */
    public function getKey(){
        return $this->getRequest()->getParam('id', 0);
    }
}