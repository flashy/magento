<?php
namespace Flashy\Integration\Block;

class Product extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Flashy\Integration\Helper\Data
     */
    public $helper;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param \Flashy\Integration\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Flashy\Integration\Helper\Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * Get flashy id.
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getFlashyId()
    {
        return $this->helper->getFlashyId();
    }

    /**
     * Get product data.
     *
     * @return array|bool|string
     */
    public function getProductDetails()
    {
        return $this->helper->getProductDetails();
    }

    /**
     * Get cart data.
     *
     * @return array|bool|string
     */
    public function getCart()
    {
        return $this->helper->getCart();
    }

    /**
     * Get flashy cart cache
     *
     * @return null|string
     */
    public function getFlashyCartCache()
    {
        return $this->helper->getFlashyCartCache();
    }

    /**
     * Set flashy cart cache
     *
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function setFlashyCartCache()
    {
        return $this->helper->setFlashyCartCache();
    }

    /**
     * Get flashy id from cookie.
     *
     * @return null|string
     */
    public function getFlashyIdCookie()
    {
        return $this->helper->getFlashyIdCookie();
    }

    /**
     * Check if customer is logged in.
     *
     * @return bool
     */
    public function customerIsLoggedIn()
    {
        return $this->helper->customerIsLoggedIn();
    }

    /**
     * Get customer email.
     *
     * @return string
     */
    public function getCustomerEmail()
    {
        return $this->helper->getCustomerEmail();
    }
}
