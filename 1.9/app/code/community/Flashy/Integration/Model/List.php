<?php

class Flashy_Integration_Model_List
{
    public function toOptionArray()
    {
        $options = array();

        if (Mage::getStoreConfig( 'flashy/flashy/flashy_key' ) !== '')
        {
            $flashy_helper = Mage::helper("flashy");
            $this->flashy = new Flashy_Flashy(Mage::getStoreConfig('flashy/flashy/flashy_key'));

            $lists = $flashy_helper->tryOrLog( function () {
                return $this->flashy->lists->all();
            });

            $options[] = array(
                'value' => strval(''),
                'label' => 'Choose a list'
            );
            
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