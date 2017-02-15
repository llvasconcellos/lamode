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


/**
 * Manage Deepzoom Image
 *
 * @category   Gr
 * @package    Gr_Deepzoom
 * @author     Nicolas Fabre <nicolas.fabre@groupereflect.net>
 */
class Gr_Deepzoom_Helper_Data extends Mage_Core_Helper_Data {
	const XML_PATH_DEEPZOOM = 'catalog/grdeepzoom';
	const XML_PATH_DEEPZOOM_IS_ENABLE = 'catalog/grdeepzoom/is_active';
	const XML_PATH_DEEPZOOM_ONLY_ADMIN = 'catalog/grdeepzoom/only_admin';
	const PRODUCT_VIEW_ACTION_FULLNAME = 'catalog_product_view';
	
	protected $_reserved = array('is_active','only_admin');
	protected $_boolean = array('autoHideControls');
	/**
	 * Extension name
	 * 
	 * @var string
	 */
	const DESCRIPTOR_FILE_EXTENSION = '.dzi';
	/**
	 * Create Deepzoom images (images + descriptor)
	 * 
	 * @param string $source
	 * @return bool True if image has been created or already exist
	 */
	public function createImage($source) {
		if( Mage::getStoreConfig(Gr_Deepzoom_Helper_Data::XML_PATH_DEEPZOOM_IS_ENABLE) == 0) {
			return false;
		}
		if(is_file($source)) {
        	$destination = $source.self::DESCRIPTOR_FILE_EXTENSION;
        	if(!is_file($destination)) { 
	        	$deep = new Oz_Deepzoom_ImageCreator();
	        	$deep->create($source,$destination);
	        	if(is_file($destination)) {
	        		return true;	
	        	}
        	}else return true;	
        }
        return false;
	}
	
	/**
	 * Delete Deepzoom images (images + descriptor  + source)
	 * 
	 * @param string $source
	 */
	public function  deleteImage($source) {
		if( Mage::getStoreConfig(self::XML_PATH_DEEPZOOM_IS_ENABLE) == 0) {
			return false;
		}
		$descriptor = $source.self::DESCRIPTOR_FILE_EXTENSION;
		$aImage = pathinfo($descriptor); 
        
        $imageName = $aImage['filename'];
        $dirName = $aImage['dirname'];
        /**
         * Add retrocompatibilty for Magento 1.3
         * Replace Varien_Io_File::rmdirRecursive($dirName.DIRECTORY_SEPARATOR.$imageName.'_files');
         */
        $dir = new Varien_Io_File();
		$dir->rmdir($dirName.DIRECTORY_SEPARATOR.$imageName.'_files',true);
		@unlink($descriptor);
		@unlink($source);
	}
	
	/**
	 * Return Descriptor Url
	 * 
	 * @param string $imageUrl
	 * @return string
	 */
	public function getDescriptorUrl($imageUrl) {
		if( Mage::getStoreConfig(self::XML_PATH_DEEPZOOM_IS_ENABLE) == 0) {
			return false;
		}
		if(Mage::getStoreConfigFlag(self::XML_PATH_DEEPZOOM_ONLY_ADMIN) == 0 
			&& !is_file(Mage::getBaseDir('media').'/catalog/product/'.$imageUrl.'.dzi')) {
			$this->createImage(Mage::getBaseDir('media').'/catalog/product/'.$imageUrl);	
		}
		return Mage::app()->getStore()->getBaseUrl('media').'catalog/product/'.$imageUrl.'.dzi';
	}
	
	public function getGalleryImagesJson($gallery) {
    	$collection = array();
    	if(count($gallery)) {
    		foreach ($gallery as $_image) {
    			$collection[] = array(
    				'id'	=> $_image->getId(), 
    				'url'	=> $this->getDescriptorUrl($_image->getFile()), 
    			);	
    		}
    	}
    	return Zend_Json::encode($collection);
    }
    
    public function getConfigJson() {
    	$mConfig = Mage::getStoreConfig(self::XML_PATH_DEEPZOOM);
    	$config = array();
    	foreach ($mConfig as $_key => $_value){
    		if(in_array($_key,$this->_reserved)) {
    			continue;
    		}
    		if(in_array($_key,$this->_boolean)) {
    			$config[$_key] = (boolean)$_value;
    		}
    		else $config[$_key] = (float)$_value;
    	}
    	$config['imagePath'] = Mage::getDesign()->getSkinUrl(Mage::getStoreConfig(self::XML_PATH_DEEPZOOM.'/imagePath'));
     	return Zend_Json::encode($config);	
    }
}