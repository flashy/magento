<?php

class Flashy_Integration_Model_Observer
{
    public function newsletterSubscriberChange(Varien_Event_Observer $observer)
    {
        if(Mage::getStoreConfig('flashy/flashy/active')) {
            $subscriber = $observer->getEvent()->getSubscriber();

            if ($subscriber->getStatus() == Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED) {
                $list_id = Mage::getStoreConfig('flashy/flashy_lists/flashy_list', $subscriber->getStoreId());
                $flashy_key = Mage::getStoreConfig('flashy/flashy/flashy_key');
                if (!empty($list_id) && !empty($flashy_key)) {
                    $this->flashy = new Flashy_Flashy($flashy_key);

                    $subscribe = $this->flashy->lists->subscribe($list_id, array(
                        "email" => $subscriber->getSubscriberEmail(),
                    ));
                } else {
                    Mage::helper("flashy")->addLog('newsletterSubscriberChange: Flashy API Key="' . $flashy_key . '" list id="' . $list_id.'"');
                }
            }
        }
    }

    public function salesOrderChange(Varien_Event_Observer $observer)
    {
        Mage::helper("flashy")->addLog("salesOrderChange");
        $flashy_key = Mage::getStoreConfig('flashy/flashy/flashy_key');
        if(Mage::getStoreConfig('flashy/flashy/active') && !empty($flashy_key)) {

            $this->flashy = new Flashy_Flashy($flashy_key);

            $order = $observer->getEvent()->getOrder();

            $account_id = Mage::getStoreConfig('flashy/flashy/flashy_id');

            if ($account_id == null) {
                if ($info['success'] == true) {
                    $info = $this->flashy->account->info();

                    Mage::getConfig()->saveConfig('flashy/flashy/flashy_id', $info['account']['id'], 'default', 0);

                    $account_id = $info['account']['id'];
                }
            }

            if ($order->getStatus() != $order->getOrigData('status')) {
                if ($order->getCustomerId()) {
                    $email = $order->getCustomerEmail();
                } else {
                    $email = $order->getBillingAddress()->getEmail();
                }

                $data = array(
                    "order_id" => $order->getId(),
                    "status" => $order->getStatus()
                );

                foreach ($order->getTracksCollection() as $_track) {
                    $data['tracking_id'] = $_track->getNumber();
                }

                $track = $this->flashy->thunder->track($account_id, $email, "PurchaseUpdated", $data);

                Mage::helper("flashy")->addLog(json_encode($track));
            }
        }
    }
    public function salesOrderPlaceAfter(Varien_Event_Observer $observer)
    {
        $h = Mage::helper("flashy");
        $h->addLog('salesOrderPlaceAfter');
        $flashy_key = Mage::getStoreConfig('flashy/flashy/flashy_key');
        if(Mage::getStoreConfig('flashy/flashy/active') && !empty($flashy_key) && Mage::getStoreConfig('flashy/flashy/purchase')) {

            $this->flashy = new Flashy_Flashy($flashy_key);

            $order = $observer->getEvent()->getOrder();

            $account_id = Mage::getStoreConfig('flashy/flashy/flashy_id');

            if ($account_id == null) {
                if ($info['success'] == true) {
                    $info = $this->flashy->account->info();

                    Mage::getConfig()->saveConfig('flashy/flashy/flashy_id', $info['account']['id'], 'default', 0);

                    $account_id = $info['account']['id'];
                }
            }

            if($order->getCustomerId()) {
                $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
                $h->addLog('step4: customer loaded');

                $contactData = [
                    'email' => $customer->getEmail(),
                    'first_name' => $customer->getFirstname(),
                    'last_name' => $customer->getLastname(),
                    'gender' => $customer->getGender()
                ];
            } else {
                $billingAddress = $order->getBillingAddress();
                $h->addLog('step4: billingAddress loaded');
                $contactData = [
                    'email' => $billingAddress->getEmail(),
                    'first_name' => $billingAddress->getFirstname(),
                    'last_name' => $billingAddress->getLastname(),
                    'gender' => $billingAddress->getGender()
                ];
            }

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

            $currency = Mage::app()->getStore(Mage::app()->getStore()->getStoreId())->getCurrentCurrencyCode();
            $h->addLog('step10: currency='.$currency);

            $data = array(
                "account_id" => $account_id,
                "email" => $contactData['email'],
                "order_id"  => $order->getId(),
                "value"   => $total,
                "content_ids"  => $products,
                "status" => $order->getStatus(),
                "currency"  => $currency
            );

            $h->addLog('step11: data=' . print_r($data, true));

            //$this->flashy->api->events->track("Purchase", $data);
            $track = $this->flashy->thunder->track($account_id, $contactData['email'], "Purchase", $data);

            $h->addLog('step12: Purchase sent.');
            $h->addLog(json_encode($track));

        }
    }

    /**
     * Save cart data in flashy cart hash table
     *
     * @param Varien_Event_Observer $observer
     */
    public function checkoutCartChange(Varien_Event_Observer $observer)
    {
        if(Mage::getStoreConfig('flashy/flashy/active')){
            //get cart from event observer
            $cart = $observer->getEvent()->getCart();

            //cart hash will not be updated
            $updateCart = false;

            //get key from cookie
            $key = Mage::getSingleton('core/cookie')->get('flashy_id');

            //if key exists
            if ($key) {
                //get model flashy cart hash
                $cartHash = Mage::getModel('flashy/carthash');

                //load cart hash by key
                $cartHash->load($key, 'key');

                //get quote from cart
                $quote = $cart->getQuote();

                //get all visible items of the cart
                $items = $quote->getAllVisibleItems();

                //cart items data
                $cartItems = array();

                //loop through cart visible items
                foreach ($items as $item) {
                    //get product options
                    $options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());

                    //update qty
                    $options['info_buyRequest']['qty'] = $item->getQty();

                    // unset uenc from cart item data
                    unset($options['info_buyRequest']['uenc']);

                    //add info to cart items
                    $cartItems[] = $options['info_buyRequest'];

                    //cart hash will be updated
                    $updateCart = true;
                }

                //check if cart will be updated
                if ($updateCart) {
                    try {
                        //save cart hash data
                        $cartHash->setKey($key);
                        $cartHash->setCart(json_encode($cartItems));
                        $cartHash->save();
                    } catch (\Exception $e) {
                        $this->_logger->info("Could not save flashy cart hash key=" . $cartHash->getKey() . " cart=" . $cartHash->getCart());
                    }
                }
            }
        }
    }
}