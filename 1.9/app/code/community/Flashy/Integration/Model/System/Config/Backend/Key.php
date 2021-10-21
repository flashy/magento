<?php

class Flashy_Integration_Model_System_Config_Backend_Key extends Mage_Core_Model_Config_Data
{
    /**
     * @return Mage_Core_Model_Abstract
     * @throws Flashy_Error
     * @throws Mage_Core_Exception
     */
    protected function _beforeSave()
    {
        if($this->getValue() != '') {

            $flashy_helper = Mage::helper('flashy');
            $flashy = new Flashy_Flashy($this->getValue());

            $info = $flashy_helper->tryOrLog( function () use($flashy) {
                return $flashy->account->info();
            });

            if(!$info['success']) {
                throw Mage::exception(
                    'Mage_Core', Mage::helper('flashy')->__('Flashy API Key is not valid.')
                );
            }
        }

        return parent::_beforeSave();
    }

    /**
     * @return Mage_Core_Model_Abstract
     * @throws Flashy_Error
     * @throws Mage_Core_Exception
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _afterSave()
    {
        $api_key = $this->getValue();
        $scope = $this->getScope();
        $scope_id = $this->getScopeId();
        if($api_key == ''){
            $value = 0;
            $flashy_id = 0;
        }
        else {
            $store_email = Mage::getStoreConfig('trans_email/ident_general/email', $scope_id);
            $store_name = Mage::getStoreConfig('general/store_information/name', $scope_id);
            $base_url = Mage::app()->getStore($scope_id)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);

            if(empty($store_name)){
                $store = Mage::getModel('core/store')->load($scope_id);
                $store_name = $store->getName();
            }

            $data = array(
                "profile" => array(
                    "from_name" => $store_name,
                    "from_email" => $store_email,
                    "reply_to" => $store_email,
                ),
                "store"	=> array(
                    "platform" => "magento",
                    "api_key" => $api_key,
                    "store_name" => $store_name,
                    "store" => $base_url,
                    "debug" => array(
                        "magento" => Mage::getVersion(),
                        "php" => phpversion(),
                        "memory_limit" => ini_get('memory_limit'),
                    ),
                )
            );
            $urls = array("contacts", "products", "orders");
            foreach ($urls as $url) {
                $data[$url] = array(
                    "url" => $base_url . "flashy?export=$url&store_id=$scope_id&limit=100&page=1&flashy_pagination=true&flashy_key=" . $api_key,
                    "format" => "json_url",
                );
            }

            $flashy = new Flashy_Flashy($api_key);
            $flashy_helper = Mage::helper('flashy');

            $connect = $flashy_helper->tryOrLog( function () use($flashy, $data) {
                return $flashy->account->connect($data);
            });

            $value = intval($connect['success']);

            $info = $flashy_helper->tryOrLog( function () use($flashy) {
                return $flashy->account->info();
            });

            if( $info['success'] == true ) {
                $flashy_id = $info['account']['id'];
            }
        }
        Mage::getConfig()->saveConfig('flashy/flashy/flashy_connected', $value, $scope, $scope_id);
        Mage::getConfig()->saveConfig('flashy/flashy/flashy_id', $flashy_id, $scope, $scope_id);
        return parent::_afterSave();
    }

    protected function _afterDelete()
    {
        Mage::getConfig()->deleteConfig('flashy/flashy/flashy_connected', $this->getScope(), $this->getScopeId());
        Mage::getConfig()->deleteConfig('flashy/flashy/flashy_id', $this->getScope(), $this->getScopeId());
        return parent::_afterDelete();
    }
}