<?php
namespace Flashy\Integration\Block\Adminhtml\System\Config;

class Url extends \Magento\Backend\Block\AbstractBlock implements
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
        //get store id from get request
        $scope_id = $this->_request->getParam("store", 0);

        //get flashy api key
        $flashy_key = $this->helper->getFlashyKey(\Magento\Store\Model\ScopeInterface::SCOPE_STORE, $scope_id);

        //get base url
        $base_url = $this->helper->getBaseUrlByScopeId($scope_id);

        $html = '<table>';
        $entities = array('products', 'contacts', 'orders');
        foreach ($entities as $entity) {
            $flashy_url = $base_url . "flashy?export=$entity&store_id=$scope_id&flashy_key=$flashy_key";
            $html .= '<tr><td class="label">' . __("Flashy " . ucfirst($entity) . " Url") . '</td>';
            $html .= '<td class="value"><a href="' . $flashy_url . '" target="_blank">' . $flashy_url . '</a></td><td></td></tr>';
        }
        $html .= '</table>';

        return $html;
    }
}
