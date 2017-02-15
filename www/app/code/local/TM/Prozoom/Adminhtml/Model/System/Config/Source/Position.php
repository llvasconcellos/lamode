<?php

class TM_Prozoom_Adminhtml_Model_System_Config_Source_Position
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'right', 'label'=>Mage::helper('prozoom')->__('Right')),
            array('value'=>'left', 'label'=>Mage::helper('prozoom')->__('Left')),
            array('value'=>'top', 'label'=>Mage::helper('prozoom')->__('Top')),
            array('value'=>'bottom', 'label'=>Mage::helper('prozoom')->__('Bottom'))
        );
    }
}
