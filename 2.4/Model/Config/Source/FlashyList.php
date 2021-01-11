<?php
namespace Flashy\Integration\Model\Config\Source;

class FlashyList implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Flashy\Integration\Helper\Data
     */
    public $helper;

    /**
     * FlashyList constructor.
     *
     * @param \Flashy\Integration\Helper\Data $helper
     */
    public function __construct(
        \Flashy\Integration\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Get lists as associative array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->helper->getFlashyListOptionsArray();

    }

    /**
     * Get lists.
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->helper->getFlashyListOptions();
    }
}
