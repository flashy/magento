<?php
namespace Flashy\Integration\Controller\Adminhtml\Productexport;

use Magento\Framework\Controller\ResultFactory;

class Export extends \Flashy\Integration\Controller\Adminhtml\Productexport
{
    /**
     * @var \Flashy\Integration\Helper\Data
     */
    public $helper;

    /**
     * Export constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Flashy\Integration\Helper\Data $helper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Flashy\Integration\Helper\Data $helper
    ) {
        $this->helper = $helper;
        parent::__construct($context);
    }

    /**
     * Execute export.
     *
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $storeId = $this->getRequest()->getPost('store');
        $catalogId = $this->getRequest()->getPost('catalog');

        if ( $this->helper->getFlashyKey(\Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId) === '' ) {
            $this->messageManager->addError(__('Error! Flashyapp API Key or Catalog missing.'));
            return $this->goBack();
        }

        $export = $this->helper->exportProductsSend($storeId, $catalogId);

        if( isset($export['error']) ) {
            $this->messageManager->addError(__('Error! Flashy API Key incorrect.'));
        } else {
            $this->messageManager->addSuccess(__('Success! %1 products exported (%2 products missed).', $export['counter']['success'], $export['counter']['errors']));
        }
        return $this->goBack();
    }

    /**
     * Redirect to export index.
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function goBack(){
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('*/*/index');
        return $resultRedirect;
    }
}
