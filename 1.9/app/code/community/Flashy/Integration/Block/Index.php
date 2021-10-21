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
        $flashy_helper = Mage::helper("flashy");
        $flashy_helper->addLog('getOrderDetails');

        $this->flashy = new Flashy_Flashy( Mage::getStoreConfig('flashy/flashy/flashy_key') );
        $flashy_helper->addLog('step1:flashy_key');

        $orderId = Mage::getSingleton('checkout/session')->getLastOrderId();
        $flashy_helper->addLog('step2: orderId='.$orderId);

        $order = Mage::getModel('sales/order')->load($orderId);
        $flashy_helper->addLog('step3: order loaded');

        if($order->getCustomerId()) {
            $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
            $flashy_helper->addLog('step4: customer loaded');

            $contactData = [
                'email' => $customer->getEmail(),
                'first_name' => $customer->getFirstname(),
                'last_name' => $customer->getLastname(),
                'gender' => $customer->getGender()
            ];
        } else {
            $billingAddress = $order->getBillingAddress();
            $flashy_helper->addLog('step4: billingAddress loaded');
            $contactData = [
                'email' => $billingAddress->getEmail(),
                'first_name' => $billingAddress->getFirstname(),
                'last_name' => $billingAddress->getLastname(),
                'gender' => $billingAddress->getGender()
            ];
        }
        $flashy_helper->addLog('step5: Contact data ' . print_r($contactData, true));

        $create = $flashy_helper->tryOrLog( function () use($contactData) {
            return $this->flashy->contacts->create($contactData);
        });

        $flashy_helper->addLog('step6: flashy contact created');

        $total = (float) $order->getSubtotal();
        $flashy_helper->addLog('step7: order total=' . $total);

        $items = $order->getAllItems();
        $flashy_helper->addLog('step8: getting order items');

        $products = [];

        foreach($items as $i):
            $products[] = $i->getProductId();
        endforeach;
        $flashy_helper->addLog('step9: getting product ids');

        $currency = Mage::app()->getStore($this->_getStore())->getCurrentCurrencyCode();
        $flashy_helper->addLog('step10: currency='.$currency);

        $data = array(
            "order_id"  => $orderId,
            "value"   => $total,
            "content_ids"  => $products,
            "status" => $order->getStatus(),
            "email" => $contactData['email'],
            "currency"  => $currency
        );

        $flashy_helper->addLog('step11: data=' . print_r($data, true));

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