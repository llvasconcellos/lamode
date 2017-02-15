<?php
class Magestore_BannerSlider_Block_BannerSlider extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getBannerSlider()     
     { 
        if (!$this->hasData('bannerslider')) {
            $this->setData('bannerslider', Mage::registry('bannerslider'));
        }
        return $this->getData('bannerslider');
        
    }
}