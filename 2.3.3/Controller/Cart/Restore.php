<?php
namespace Flashy\Integration\Controller\Cart;

use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;

class Restore extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface
{
    /**
     * @var \Flashy\Integration\Helper\Data
     */
    public $helper;

    /**
     * Index constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Flashy\Integration\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Flashy\Integration\Helper\Data $helper
    ) {
        $this->helper = $helper;
        parent::__construct($context);
    }

    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    /**
     * Execute restore action
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $key = $this->getRequest()->getParam('id', 0);
        $this->helper->restoreFlashyCartHash($key);
        /*
        $messages = $this->helper->restoreFlashyCartHash($key);
        foreach ($messages as $message) {
            if ($message['success']) {
                $this->messageManager->addSuccess($message['message']);
            } else {
                $this->messageManager->addError($message['message']);
            }
        }
        */
        $this->getResponse()->setRedirect('/checkout/cart/index');
    }
}
