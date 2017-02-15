<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Gr
 * @package     Gr_Deepzoom
 * @copyright   Copyright (c) 2010 groupeReflect (http://www.groupereflect.net)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class Gr_Deepzoom_Model_Product_Attribute_Backend_Media 
	extends Mage_Catalog_Model_Product_Attribute_Backend_Media {
	
	const XML_PATH_DEEPZOOM_IS_ENABLE = 'catalog/grdeepzoom/is_active';
		
    public function afterSave($object){ 
    	parent::afterSave($object);
    	if( Mage::getStoreConfig(Gr_Deepzoom_Helper_Data::XML_PATH_DEEPZOOM_IS_ENABLE)  == 1) {
	        if ($object->getIsDuplicate() == true) {
	            $this->duplicate($object);
	            return;
	        }
	        $attrCode = $this->getAttribute()->getAttributeCode();
	        $value = $object->getData($attrCode);
	        if (!is_array($value) || !isset($value['images']) || $object->isLockedAttribute($attrCode)) {
	            return;
	        }
	        $toDelete = array();
	        $filesToValueIds = array();
	        foreach ($value['images'] as &$image) {
	        	if(!empty($image['removed'])) {
	                Mage::helper('grdeepzoom')->deleteImage(Mage::getBaseDir('media').'/catalog/product/'.$image['file']);
	        		continue;
	            }
	            
	            Mage::helper('grdeepzoom')->createImage(Mage::getBaseDir('media').'/catalog/product/'.$image['file']);
	        }
    	}
    }
}