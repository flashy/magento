<?php
namespace Flashy\Integration\Block\Adminhtml\Productexport\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Flashy\Integration\Helper\Data
     */
    public $helper;
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * Form constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Flashy\Integration\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Flashy\Integration\Helper\Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form before rendering HTML.
     *
     * @return \Magento\Backend\Block\Widget\Form\Generic
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl('*/*/export'),
                    'method' => 'post',
                ],
            ]
        );

        $fieldset = $form->addFieldset('general', ['legend' => __('Export Products')]);
        $fieldset->addField(
            'store',
            'select',
            [
                'name' => 'store',
                'label' => __('Select Store'),
                'title' => __('Select Store'),
                'required' => true,
                'values' => $this->_systemStore->getStoreValuesForForm(false, true)
            ]
        );
        $fieldset->addField(
            'catalog',
            'select',
            [
                'name' => 'catalog',
                'label' => __('Catalog'),
                'title' => __('Catalog'),
                'required' => true,
                'note'     => __('You can create new catalog on Flashyapp.com'),
                'values' => $this->helper->getFlashyCatalogOptions()
            ]
        );
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
