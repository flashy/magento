<?php
namespace Flashy\Integration\Observer\Newsletter;

class SubscriberSaveBefore implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Flashy\Integration\Helper\Data
     */
    public $helper;

    /**
     * SubscriberSaveBefore constructor.
     *
     * @param \Flashy\Integration\Helper\Data $helper
     */
    public function __construct(
        \Flashy\Integration\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Execute observer.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        if($this->helper->getFlashyActive()) {
            $subscriber = $observer->getEvent()->getSubscriber();

            if ($subscriber->getStatus() == \Magento\Newsletter\Model\Subscriber::STATUS_SUBSCRIBED) {
                $this->helper->subscriberSend($subscriber->getSubscriberEmail(), $subscriber->getStoreId());
            }
        }
    }
}
