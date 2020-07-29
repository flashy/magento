<?php

class Flashy_Integration_IndexController extends Mage_Core_Controller_Front_Action {
	
	public function indexAction() {
        $flashy_key = $this->getRequest()->getParam('flashy_key');
        $store_id = $this->getRequest()->getParam('store_id');

        $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);

        if ( Mage::getStoreConfig( 'flashy/flashy/flashy_key', $store_id) === $flashy_key ) {
            $export_type = $this->getRequest()->getParam('export', 'products');

            $limit = $this->getRequest()->getParam('limit');
            $page = $this->getRequest()->getParam('page');
            switch ($export_type){
                case 'products':
                    $resultArray = $this->exportProducts($store_id, $limit, $page);
                    break;
                case 'contacts':
                    $resultArray = $this->exportContacts($store_id, $limit, $page);
                    break;
                case 'orders':
                    $resultArray = $this->exportOrders($store_id, $limit, $page);
                    break;
                default:
                    $this->getResponse()->setHeader('HTTP/1.0', 401, true);
                    $resultArray = array("success" => false, "error" => true, "message" => "Export type is not supported.");
                    return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($resultArray));
            }
        } else {
            $this->getResponse()->setHeader('HTTP/1.0', 401, true);
            $resultArray = array("success" => false, "error" => true, "message" => "You are not authorized to view the content");
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($resultArray));
	}

	public function getProductsTotalCount($store_id) {
        if( $store_id == 0 ) {
            $products = Mage::getModel('catalog/product')->getCollection();
        } else {
            $products = Mage::getModel('catalog/product')->setStoreId($store_id)->getCollection();
        }
        if( $store_id != 0 ) {
            $products->addStoreFilter($store_id);
        }
        return $products->getSize();

    }
	public function exportProducts($store_id, $limit, $page) {
        if( $store_id == 0 ) {
            $products = Mage::getModel('catalog/product')->getCollection();
        } else {
            $products = Mage::getModel('catalog/product')->setStoreId($store_id)->getCollection();
        }

        $products->addAttributeToSelect('*');

        if( $store_id != 0 ) {
            $products->addStoreFilter($store_id);
        }

        if($limit){
            $products->setPageSize($limit);
            if($page){
                $products->setCurPage($page);
            }
        }

        $export_products = array();

        $i = 0;

        $currency = Mage::app()->getStore($store_id)->getCurrentCurrencyCode();

        foreach($products as $prod) {
            try {
                $sync_id = $prod->getId();

                $_product = Mage::getModel('catalog/product')->load($sync_id);

                if( $store_id != 0 ) {
                    $_product->setStoreId($store_id);
                }

                $export_products[$i] = array(
                    'id'			=> $sync_id,
                    'link'			=> $_product->getProductUrl($_product),
                    'title'			=> $_product->getName(),
                    'description'	=> $_product->getShortDescription(),
                    'price'			=> $_product->getPrice(),
                    'currency'		=> $currency,
                    'tags'			=> $_product->getMetaKeyword()
                );

                if( $_product->getImage() && $_product->getImage() != 'no_selection' ) {
                    $export_products[$i]['image_link'] = $_product->getImageUrl();
                }

                $categoryCollection = $_product->getCategoryCollection()->addAttributeToSelect('name');

                $export_products[$i]['product_type'] = "";

                foreach( $categoryCollection as $_cat ) {
                    $export_products[$i]['product_type'] .= $_cat->getName() . '>';
                }

                $export_products[$i]['product_type'] = substr($export_products[$i]['product_type'], 0, -1);

                $i++;
            } catch (Exception $e) {
                continue;
            }
        }

        $page_size = $products->getPageSize();
        $current_page = $products->getCurPage();
        $total = $this->getProductsTotalCount($store_id);

        $flashy_pagination = false;
        $next_url = null;
        if($limit){
            if(ceil($total/$page_size) > $current_page){
                $flashy_key = Mage::getStoreConfig('flashy/flashy/flashy_key', $store_id);
                $base_url = Mage::app()->getStore($store_id)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
                $nextpage = $current_page + 1;
                $next_url = $base_url . "flashy?export=producrs&store_id=$store_id&limit=$limit&page=$nextpage&flashy_key=$flashy_key";
            }
            if($total > $limit){
                $flashy_pagination = true;
            }
        }

        return array(
            "data" => $export_products,
            "store_id" => $store_id,
            "size" => $products->getSize(),
            "page_size" => $page_size,
            "current_page" => $current_page,
            "count"=> count($export_products),
            "total"=> $total,
            "flashy_pagination"=> $flashy_pagination,
            "next_url"=> $next_url,
            "success" => true
        );
    }

    public function getCustomersTotalCount($store_id) {
        $websiteId = Mage::app()->getStore($store_id)->getWebsiteId();
        $customers = Mage::getModel('customer/customer')->getCollection();
        if($websiteId > 0) {
            $customers->addAttributeToFilter("website_id", array("eq" => $websiteId));
        }
        return $customers->getSize();
    }

    public function getSubscibersTotalCount($store_id) {
        if( $store_id == 0 ) {
            $subscribers = Mage::getModel('newsletter/subscriber')->getCollection();
        } else {
            $subscribers = Mage::getModel('newsletter/subscriber')->setStoreId($store_id)->getCollection();
        }
        if( $store_id != 0 ) {
            $subscribers->addStoreFilter($store_id);
        }
        $subscribers->addFieldToFilter('main_table.customer_id', ['eq' => 0]);
        return $subscribers->getSize();
    }

    public function exportContacts($store_id, $limit, $page) {

	    $total1 = $this->getCustomersTotalCount($store_id);
	    $total2 = $this->getSubscibersTotalCount($store_id);

	    $c = true;
	    $s = true;
	    $offset = 0;
	    $limit1 = $limit;
	    if($limit) {
            if (($page * $limit) <= $total1) {
                //we'll show only customers
                $s = false;
            } else {
                $offset = $page * $limit - $total1;
                if ($offset < $limit) {
                    //we'll show both customers and subscribers
                    $limit1 = $offset;
                    $offset = 0;
                } else {
                    //we'll show only subscribers
                    $c = false;
                    $offset -= $limit;
                }

            }
        }

        $i = 0;
	    $export_customers = array();
        if($c) {
            //get website id from  store id
            $websiteId = Mage::app()->getStore($store_id)->getWebsiteId();

            //get customer collection
            $customers = Mage::getModel('customer/customer')->getCollection();

            //get all attributes
            $customers->addAttributeToSelect('*');

            //filter by website
            if ($websiteId > 0) {
                $customers->addAttributeToFilter("website_id", array("eq" => $websiteId));
            }

            if ($limit) {
                $customers->setPageSize($limit);
                if ($page) {
                    $customers->setCurPage($page);
                }
            }

            foreach ($customers as $_customer) {
                //add customer fields
                $export_customers[$i] = array(
                    'email' => $_customer->getEmail(),
                    'first_name' => $_customer->getFirstname(),
                    'last_name' => $_customer->getLastname()
                );

                //get default shipping address of customer
                $address = $_customer->getDefaultShippingAddress();

                //add address fields
                if ($address) {
                    $export_customers[$i]['phone'] = $address->getTelephone();
                    $export_customers[$i]['city'] = $address->getCity();
                    $export_customers[$i]['country'] = $address->getCountry();
                }
                $i++;
            }
        }

        if($s) {
            //get subscriber collection
            $subscribers = Mage::getModel('newsletter/subscriber')->getCollection();

            //filter by store id
            if ($store_id > 0) {
                $subscribers->addStoreFilter($store_id);
            }

            //get only guest subscribers as customers are included already
            $subscribers->addFieldToFilter('main_table.customer_id', ['eq' => 0]);

            if($limit1) {
                $select = $subscribers->getSelect();

                $select->limit($limit1, $offset);
            }

            foreach ($subscribers as $subscriber) {
                //add subscriber email, no other fields are available by default
                $export_customers[$i]['email'] = $subscriber->getEmail();
                $i++;
            }
        }

        $page_size = $limit;
        $current_page = $page;
        $total = $total1 + $total2;

        $flashy_pagination = false;
        $next_url = null;
        if($limit){
            if(ceil($total/$page_size) > $current_page){
                $flashy_key = Mage::getStoreConfig('flashy/flashy/flashy_key', $store_id);
                $base_url = Mage::app()->getStore($store_id)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
                $nextpage = $current_page + 1;
                $next_url = $base_url . "flashy?export=contacts&store_id=$store_id&limit=$limit&page=$nextpage&flashy_key=$flashy_key";
            }
            if($total > $limit){
                $flashy_pagination = true;
            }
        }

        return array(
            "data" => $export_customers,
            "store_id" => $store_id,
            "size" => $total,
            "page_size" => $page_size,
            "current_page" => $current_page,
            "count"=> count($export_customers),
            "total"=> $total,
            "flashy_pagination"=> $flashy_pagination,
            "next_url"=> $next_url,
            "success" => true
        );
    }

    public function getOrdersTotalCount($store_id) {
        if( $store_id == 0 ) {
            $orders = Mage::getModel('sales/order')->getCollection();
        } else {
            $orders = Mage::getModel('sales/order')->setStoreId($store_id)->getCollection();
        }
        if( $store_id != 0 ) {
            $orders->addStoreFilter($store_id);
        }
        return $orders->getSize();
    }

    public function exportOrders($store_id, $limit, $page) {
        //get order collection
        $orders = Mage::getModel('sales/order')->getCollection();

        //get all attributes
        $orders->addAttributeToSelect('*');

        //filter by store id
        if($store_id > 0) {
            $orders->addFieldToFilter('main_table.store_id', ['eq' => $store_id]);
        }

        if($limit){
            $orders->setPageSize($limit);
            if($page){
                $orders->setCurPage($page);
            }
        }

        $i = 0;
        $export_orders = array();
        foreach ($orders as $order) {
            $items = $order->getAllItems();
            //$items = $order->getAllVisibleItems();

            $products = [];

            foreach ($items as $item):
                $products[] = $item->getProductId();
            endforeach;

            $export_orders[$i] = array(
                "email" => $order->getCustomerEmail(),
                "order_id" => $order->getId(),
                "order_increment_id" => $order->getIncrementId(),
                "value" => (float)$order->getGrandTotal(),
                "date" => strtotime($order->getCreatedAt()),
                "content_ids" => implode(',',$products),
                "currency" => $order->getOrderCurrencyCode()
            );
            $i++;
        }

        $page_size = $orders->getPageSize();
        $current_page = $orders->getCurPage();
        $total = $this->getOrdersTotalCount($store_id);

        $flashy_pagination = false;
        $next_url = null;
        if($limit){
            if(ceil($total/$page_size) > $current_page){
                $flashy_key = Mage::getStoreConfig('flashy/flashy/flashy_key', $store_id);
                $base_url = Mage::app()->getStore($store_id)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
                $nextpage = $current_page + 1;
                $next_url = $base_url . "flashy?export=orders&store_id=$store_id&limit=$limit&page=$nextpage&flashy_key=$flashy_key";
            }
            if($total > $limit){
                $flashy_pagination = true;
            }
        }

        return array(
            "data" => $export_orders,
            "store_id" => $store_id,
            "size" => $orders->getSize(),
            "page_size" => $page_size,
            "current_page" => $current_page,
            "count"=> count($export_orders),
            "total"=> $total,
            "flashy_pagination"=> $flashy_pagination,
            "next_url"=> $next_url,
            "success" => true
        );
    }
}