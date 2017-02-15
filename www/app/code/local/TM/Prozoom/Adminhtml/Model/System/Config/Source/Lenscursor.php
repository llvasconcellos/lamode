<?php

class TM_Prozoom_Adminhtml_Model_System_Config_Source_Lenscursor
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'none', 'label'=>Mage::helper('prozoom')->__('None')),
            array('value'=>'default', 'label'=>Mage::helper('prozoom')->__('Default')),
            array('value'=>'pointer', 'label'=>Mage::helper('prozoom')->__('Pointer')),
            array('value'=>'crosshair', 'label'=>Mage::helper('prozoom')->__('Crosshair'))
        );
    }
}
