<?php
namespace Flashy\Integration\Model\Config\Backend;

class FlashyKey extends \Magento\Framework\App\Config\Value
{
    /**
     * @var \Flashy\Integration\Helper\Data
     */
    public $helper;
    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Flashy\Integration\Helper\Data $helper
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Flashy\Integration\Helper\Data $helper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->helper = $helper;

        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    public function beforeSave()
    {
        if(!(($this->getValue() == '') || ($this->helper->checkApiKey($this->getValue())))) {
            throw new \Magento\Framework\Exception\ValidatorException(__('Flashy API Key is not valid.'));
        }

        $this->setValue($this->getValue());

        parent::beforeSave();
    }

    public function afterSave()
    {
        if($this->getValue() == ''){
            $value = 0;
        }
        else {
            $value = $this->helper->connectionRequest($this->getValue(), $this->getScope(), $this->getScopeId());
        }
        $this->helper->setFlashyConnected($value, $this->getScope(), $this->getScopeId());

        return parent::afterSave();
    }

    public function afterDelete()
    {
        $this->helper->removeFlashyConnected($this->getScope(), $this->getScopeId());

        return parent::afterDelete();
    }
}