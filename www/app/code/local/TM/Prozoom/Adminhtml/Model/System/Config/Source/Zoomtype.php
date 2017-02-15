<?php

class TM_Prozoom_Adminhtml_Model_System_Config_Source_Zoomtype
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'standard', 'label'=>Mage::helper('prozoom')->__('Standard')),
            array('value'=>'reverse', 'label'=>Mage::helper('prozoom')->__('Reverse')),
            array('value'=>'innerzoom', 'label'=>Mage::helper('prozoom')->__('Innerzoom'))
        );
    }
}
