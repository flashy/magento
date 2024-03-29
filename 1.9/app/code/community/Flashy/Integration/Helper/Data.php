<?php

class Flashy_Integration_Helper_Data extends Mage_Core_Helper_Abstract {

    public function getFlashyId()
    {
        if(Mage::getStoreConfig('flashy/flashy/active')){
            return Mage::getStoreConfig('flashy/flashy/flashy_id');
        }
        else {
            return false;
        }
    }

    public function getCart()
    {
        $cart = Mage::getModel('checkout/cart')->getQuote();

        $tracking = [];

        foreach($cart->getAllVisibleItems() as $item)
        {
            $tracking['content_ids'][] = $item->getProductId();
        }

        $tracking['value'] = intval($cart->getGrandTotal());

        if( count($tracking['content_ids']) < 1 )
        {
            return false;
        }

        $tracking['currency'] = Mage::app()->getStore()->getCurrentCurrencyCode();

        $tracking = json_encode($tracking);

        Mage::getSingleton('core/cookie')->set('flashy_cart', base64_encode($tracking), 86400, '/');

        return $tracking;
    }

    public function extractDataFromCustomer($customer)
    {
        $data = [
            'email' => $customer->getEmail(),
            'first_name' => $customer->getFirstname(),
            'last_name' => $customer->getLastname(),
        ];

        if($customer->getGender() != '')
        {
            $data['gender'] = $this->fitGender($customer->getGender());
        }

        if($customer->getDob() != '')
        {
            $data['birthday'] = strtotime($customer->getDob());
        }

        if($customer->getTelephone() != '')
        {
            $data['phone'] = $customer->getTelephone();
        }

        if($customer->getCity() != '')
        {
            $data['city'] = $customer->getCity();
        }

        return $data;
    }

    public function extractDataFromOrder($order)
    {
        $data = [
            'email' => $order->getCustomerEmail(),
            'first_name' => $order->getCustomerFirstname(),
            'last_name' => $order->getCustomerLastname(),
        ];

        if($order->getCustomerDob() != '')
        {
            $data['birthday'] = $order->getCustomerDob();
        }

        if($order->getCustomerGender() != '')
        {
            $data['gender'] = $this->fitGender($order->getCustomerGender());
        }

        return $data;
    }

    public function extractDataFromBilling($billing)
    {
        $data = [];

        if($billing->getTelephone() != '')
        {
            $data['phone'] = $billing->getTelephone();
        }

        if($billing->getCity() != '')
        {
            $data['city'] = $billing->getCity();
        }

        if($billing->getCountry() != '')
        {
            $data['country'] = $billing->getCountry();
        }

        return $data;
    }

    public function fitGender($gender)
    {
        switch($gender) {
            case '1':
                return 'Male';
            case '2':
                return 'Female';

            default:
                return 'Unknown';
        }
    }

    public function addLog($m)
    {
        if (Mage::getStoreConfig('flashy/flashy/log')) {
            Mage::log($m, null, 'flashy.log', true);
        }
    }

    /**
     * @param Closure $func
     * @return mixed
     */
    public static function tryOrLog(Closure $func)
    {
        $flashy_helper = Mage::helper("flashy");

        if( phpversion() > 7 )
        {
            try {
                return $func();
            }
            catch ( \Throwable $e )
            {
                $flashy_helper->addLog("Was not able to do something safely: {$e->getMessage()} \n " . $e->getTraceAsString());
            }
        }
        else
        {
            try {
                return $func();
            }
            catch ( Exception $e )
            {
                $flashy_helper->addLog("Was not able to do something safely: {$e->getMessage()} \n " . $e->getTraceAsString());
            }
        }

        return null;
    }
}