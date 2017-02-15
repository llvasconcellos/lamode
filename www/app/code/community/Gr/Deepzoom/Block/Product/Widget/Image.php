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

class Gr_Deepzoom_Block_Product_Widget_Image extends Mage_Catalog_Block_Product_Widget_Link {
	protected $_descriptor;
 	protected function _construct()
    {
        parent::_construct();
        $this->_entityResource = Mage::getResourceSingleton('catalog/product');
    }
    
	public function getDescriptor() {
		 if (!$this->_descriptor && $this->_entityResource) {
            if (!$this->getData('descriptor')) {
                $idPath = explode('/', $this->_getData('id_path'));
                if (isset($idPath[1])) {
                    $id = $idPath[1];
                    if ($id) {
                        $this->_descriptor = $this->_entityResource->getAttributeRawValue($id, 'image', Mage::app()->getStore());
                    }
                }
            } else {
                $this->_descriptor = $this->getData('descriptor');
            }
        }

        return $this->_descriptor;
	}	
}