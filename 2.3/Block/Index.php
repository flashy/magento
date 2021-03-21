<?php
namespace Flashy\Integration\Block;

class Index extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Flashy\Integration\Helper\Data
     */
    public $helper;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param \Flashy\Integration\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Flashy\Integration\Helper\Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * Get flashy id.
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getFlashyId()
    {
        return $this->helper->getFlashyId();
    }

    /**
     * Get flashy purchase.
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getFlashyPurchase()
    {
        return $this->helper->getFlashyPurchase();
    }

    /**
     * Get current order data.
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getOrderDetails()
    {
        return $this->helper->getOrderDetails();
    }
}
