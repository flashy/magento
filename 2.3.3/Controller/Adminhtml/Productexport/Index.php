<?php
namespace Flashy\Integration\Controller\Adminhtml\Productexport;

use Magento\Framework\Controller\ResultFactory;

class Index extends \Flashy\Integration\Controller\Adminhtml\Productexport
{
    /**
     * Index action.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Flashy_Integration::productexport');
        $resultPage->getConfig()->getTitle()->prepend(__('Flashy Product Export'));
        $resultPage->addBreadcrumb(__('Flashy Product Export'), __('Flashy Product Export'));
        return $resultPage;
    }
}
