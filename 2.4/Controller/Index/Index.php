<?php
namespace Flashy\Integration\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Flashy\Integration\Helper\Data
     */
    public $helper;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * Index constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Flashy\Integration\Helper\Data $helper
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Flashy\Integration\Helper\Data $helper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->helper = $helper;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * Execute index action
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $flashy_key = $this->getRequest()->getParam('flashy_key');
        $store_id = $this->getRequest()->getParam('store_id', 0);
        $export_type = $this->getRequest()->getParam('export', 'products');

        if ( $this->helper->getFlashyKey(\Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store_id) === $flashy_key ) {
            $limit = $this->getRequest()->getParam('limit');
            $page = $this->getRequest()->getParam('page');
            $total = 0;
            switch ($export_type){
                case 'products':
                    $resultArray = $this->helper->exportProducts($store_id, $limit, $page);
                    break;
                case 'contacts':
                    $resultArray = $this->helper->exportContacts($store_id, $limit, $page);
                    break;
                case 'orders':
                    $resultArray = $this->helper->exportOrders($store_id, $limit, $page);
                    break;
                default:
                    $result->setStatusHeader(401);
                    $resultArray = array("success" => false, "error" => true, "message" => "Export type is not supported.");
                    return  $result->setData($resultArray);
            }
            //$resultArray = array("data" => $export_data, "store_id" => $store_id, "total"=> $total, "success" => true);
        } else {
            $result->setStatusHeader(401);
            $resultArray = array("success" => false, "error" => true, "message" => "You are not authorized to view the content.");
        }
        return  $result->setData($resultArray);
    }
}
