<?php
namespace Flashy\Integration\Observer\Checkout;

class CartSaveAfter implements \Magento\Framework\Event\ObserverInterface
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
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        $cart = $observer->getEvent()->getCart();
        $this->helper->updateFlashyCartHash($cart);
    }
}
