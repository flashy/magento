<?php

class Flashy_Integration_Model_List
{
    public function toOptionArray()
    {
        $options = array();

        if (Mage::getStoreConfig( 'flashy/flashy/flashy_key' ) !== '')
        {
            $this->flashy = new Flashy_Flashy(Mage::getStoreConfig('flashy/flashy/flashy_key'));

            $lists = $this->flashy->lists->all();
            
            foreach ($lists['lists'] as $list)
            {
                $options[] = array(
                    'value' => strval($list['id']),
                    'label' => $list['title']
                );
            }
        }

        return $options;
    }
}