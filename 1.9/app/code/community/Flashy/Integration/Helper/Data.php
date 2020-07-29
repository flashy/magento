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

        if( $tracking['value'] <= 0 ) return false;

        $tracking['currency'] = Mage::app()->getStore()->getCurrentCurrencyCode();

        $flashy_cart = Mage::getSingleton('core/cookie')->get('flashy_cart');

        $tracking = json_encode($tracking);

        Mage::getSingleton('core/cookie')->set('flashy_cart', base64_encode($tracking), 86400, '/');

        return $tracking;
    }
    public function addLog($m)
    {
        if (Mage::getStoreConfig('flashy/flashy/log')) {
            Mage::log($m, null, 'flashy.log', true);
        }
    }
}