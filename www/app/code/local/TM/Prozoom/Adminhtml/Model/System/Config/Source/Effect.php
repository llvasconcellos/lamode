<?php

class TM_Prozoom_Adminhtml_Model_System_Config_Source_Effect
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'none', 'label'=>Mage::helper('prozoom')->__('None')),
            array('value'=>'fade', 'label'=>Mage::helper('prozoom')->__('Fade'))
        );
    }
}
