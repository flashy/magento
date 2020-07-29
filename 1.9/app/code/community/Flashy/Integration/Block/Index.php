<?php   
class Flashy_Integration_Block_Index extends Mage_Core_Block_Template {

    public static $_store = '';

    public $flashy;

    protected function _getStore()
    {
        if(self::$_store){
            self::$_store = Mage::app()->getStore()->getStoreId();
        }

        return self::$_store;
    }

    public function getOrderDetails()
    {
        $h = Mage::helper("flashy");
        $h->addLog('getOrderDetails');

        $this->flashy = new Flashy_Flashy( Mage::getStoreConfig('flashy/flashy/flashy_key') );
        $h->addLog('step1:flashy_key');

        $orderId = Mage::getSingleton('checkout/session')->getLastOrderId();
        $h->addLog('step2: orderId='.$orderId);

        $order = Mage::getModel('sales/order')->load($orderId);
        $h->addLog('step3: order loaded');

        $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
        $h->addLog('step4: customer loaded');

        $contactData = [
            'email' => $customer->getEmail(),
            'first_name' => $customer->getFirstname(),
            'last_name' => $customer->getLastname(),
            'gender' => $customer->getGender()
        ];
        $h->addLog('step5: Contact data ' . print_r($contactData, true));

        $this->flashy->contacts->create($contactData);
        $h->addLog('step6: flashy contact created');

        $total = (float) $order->getSubtotal();
        $h->addLog('step7: order total=' . $total);

        $items = $order->getAllItems();
        $h->addLog('step8: getting order items');

        $products = [];

        foreach($items as $i):
            $products[] = $i->getProductId();
        endforeach;
        $h->addLog('step9: getting product ids');

        $currency = Mage::app()->getStore($this->_getStore())->getCurrentCurrencyCode();
        $h->addLog('step10: currency='.$currency);

        $data = array(
            "order_id"  => $orderId,
            "value"   => $total,
            "content_ids"  => $products,
            "currency"  => $currency
        );
        $h->addLog('step11: data=' . print_r($data, true));

        return $data;
    }

    public function getFlashyId()
    {
        if(Mage::getStoreConfig('flashy/flashy/active', $this->_getStore())){
            return Mage::getStoreConfig('flashy/flashy/flashy_id', $this->_getStore());
        }
        else {
            return false;
        }
    }
}