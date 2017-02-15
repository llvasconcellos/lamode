<?php

class TM_Prozoom_Adminhtml_Model_System_Config_Source_Preloadposition
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'center', 'label'=>Mage::helper('prozoom')->__('Center')),
            array('value'=>'bycss', 'label'=>Mage::helper('prozoom')->__('By CSS'))
        );
    }
}
