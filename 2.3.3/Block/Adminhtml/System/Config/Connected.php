<?php
namespace Flashy\Integration\Block\Adminhtml\System\Config;

class Connected extends \Magento\Backend\Block\AbstractBlock implements
    \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{
    /**
     * @var \Flashy\Integration\Helper\Data
     */
    public $helper;
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;

    /**
     * Url constructor.
     *
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Flashy\Integration\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Flashy\Integration\Helper\Data $helper
    ) {
        $this->_request = $request;
        $this->helper = $helper;
    }

    /**
     * Render element html
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $store = $this->_request->getParam("website", 0);
        if($store > 0){
        $scope = \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE;
        }
        else{
            $store = $this->_request->getParam("store", 0);
            $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        }
        $flashy_connected = $this->helper->getFlashyConnected($scope, $store);
        $html = '<table><tr><td class="label"></td><td class="value"><span class="flashy-' . ($flashy_connected?'':'not-') . 'connected"> ' . __(($flashy_connected?'C':'Not c').'onnected with Flashy.') . '</span></td><td></td></tr></table>';
        return $html;
    }
}
