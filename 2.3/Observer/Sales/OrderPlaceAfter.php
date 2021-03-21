<?php
namespace Flashy\Integration\Observer\Sales;

class OrderPlaceAfter implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Flashy\Integration\Helper\Data
     */
    public $helper;

    /**
     * OrderSaveAfter constructor.
     *
     * @param \Flashy\Integration\Helper\Data $helper
     */
    public function __construct(
        \Flashy\Integration\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Flashy_Error
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        $order = $observer->getEvent()->getOrder();
        $this->helper->orderPlace($order);
    }
}
