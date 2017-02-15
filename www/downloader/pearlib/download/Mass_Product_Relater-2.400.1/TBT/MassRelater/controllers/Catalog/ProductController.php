<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    TBT_MassRelater
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog product controller
 *
 * @category   Mage
 * @package    TBT_MassRelater
 * @author      Magento Core Team <core@magentocommerce.com>
 */
include_once "TBT/Enhancedgrid/controllers/Catalog/ProductController.php";
class TBT_MassRelater_Catalog_ProductController extends TBT_Enhancedgrid_Catalog_ProductController
{

    protected function _construct()
    {
        parent::_construct();
    }




    

     /**
     *************************************************************************    
     * Relating
     *************************************************************************
     */  
    /** 
     * This will relate all products to a specifc list of products 
     *
     */     
    public function massRelateToAction()
    {
        $productIds = $this->getRequest()->getParam('product');
        $productIds2List = $this->getRequest()->getParam('callbackval');
        $productIds2 = explode(',', $productIds2List);
        
        if (!is_array($productIds)) {
            $this->_getSession()->addError($this->__('Please select product(s)'));
        }
        else {
            try {
                foreach ($productIds as $productId) {
                    $link = array();
                    foreach($productIds2 as $relatedToThisId) {
                      if($productId != $relatedToThisId) {
                        $link[$relatedToThisId] = array('position' => null);
                      }
                    }
                    $product = Mage::getModel('catalog/product')->load($productId);
                    
                    // Fetch and append to already related products.
                    $oldRelatedProducts = $product->getRelatedProducts();
                    foreach($oldRelatedProducts as $oldRelatedProduct) {
                      $link[$oldRelatedProduct->getId()] = array('position' => null);
                    }
                    
                    $product->setRelatedLinkData($link);
                    if ($this->massactionEventDispatchEnabled)
                      Mage::dispatchEvent('catalog_product_prepare_save', 
                          array('product' => $product, 'request' => $this->getRequest()));
                    $product->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully related to products('.
                      $productIds2List.').', count($productIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /** 
     * This will relate all products selected to each other.     
     *
     */     
    public function massRelateProductsAction()
    {
        $productIds = $this->getRequest()->getParam('product');
        if (!is_array($productIds)) {
            $this->_getSession()->addError($this->__('Please select product(s)'));
        }
        else {
            try {
                foreach ($productIds as $productId) {
                    $link = array();
                    foreach($productIds as $relatedToThisId) {
                      if($productId != $relatedToThisId) {
                        $link[$relatedToThisId] = array('position' => null);
                      }
                    }
                    $product = Mage::getModel('catalog/product')->load($productId);
                    
                    // Fetch and append to already related products.
                    $oldRelatedProducts = $product->getRelatedProducts();
                    foreach($oldRelatedProducts as $oldRelatedProduct) {
                      $link[$oldRelatedProduct->getId()] = array('position' => null);
                    }
                    
                    $product->setRelatedLinkData($link);
                    if ($this->massactionEventDispatchEnabled)
                      Mage::dispatchEvent('catalog_product_prepare_save', 
                          array('product' => $product, 'request' => $this->getRequest()));
                    $product->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully related to each other.', count($productIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
    

    /**
     * This will unrelate related product's relations.
     *          
     */     
    public function massUnRelateAction() {
        $productIds = $this->getRequest()->getParam('product');
        if (!is_array($productIds)) {
            $this->_getSession()->addError($this->__('Please select product(s)'));
        } else {
            try {
                foreach ($productIds as $productId) {
                    $product = Mage::getModel('catalog/product')->load($productId);
                    $product->setRelatedLinkData(array());
                    if ($this->massactionEventDispatchEnabled)
                      Mage::dispatchEvent('catalog_product_prepare_save', 
                          array('product' => $product, 'request' => $this->getRequest()));
                    $product->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) are no longer related to any other products.', count($productIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

     
    /**
     *************************************************************************    
     * Cross-Selling
     *************************************************************************
     */  
     
    /**
     * This will unrelate related product's relations.
     *          
     */     
    public function massUnCrossSellAction() {
        $productIds = $this->getRequest()->getParam('product');
        if (!is_array($productIds)) {
            $this->_getSession()->addError($this->__('Please select product(s)'));
        } else {
            try {
                foreach ($productIds as $productId) {
                    $product = Mage::getModel('catalog/product')->load($productId);
                    $product->setCrossSellLinkData(array());
                    if ($this->massactionEventDispatchEnabled)
                      Mage::dispatchEvent('catalog_product_prepare_save', 
                          array('product' => $product, 'request' => $this->getRequest()));
                    $product->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) now have no products as cross sell links.', count($productIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
     
    /** 
     * This will cross sell all products with each other.     
     *
     */  
    public function massCrossSellProductsAction()
    {
        $productIds = $this->getRequest()->getParam('product');
        if (!is_array($productIds)) {
            $this->_getSession()->addError($this->__('Please select product(s)'));
        }
        else {
            try {
                foreach ($productIds as $productId) {
                    $link = array();
                    foreach($productIds as $relatedToThisId) {
                      if($productId != $relatedToThisId) {
                        $link[$relatedToThisId] = array('position' => null);
                      }
                    }
                    
                    $product = Mage::getModel('catalog/product')->load($productId);
                    // Fetch and append to already related products.
                    $oldRelatedProducts = $product->getCrossSellProducts();
                    foreach($oldRelatedProducts as $oldRelatedProduct) {
                      $link[$oldRelatedProduct->getId()] = array('position' => null);
                    }
                    
                    $product->setCrossSellLinkData($link);
                    if ($this->massactionEventDispatchEnabled)
                      Mage::dispatchEvent('catalog_product_prepare_save', 
                        array('product' => $product, 'request' => $this->getRequest()));
                    $product->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully cross-related to each other.', count($productIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
    /** 
     * This will relate all products to a specifc list of products 
     *
     */     
    public function massCrossSellToAction()
    {
        $productIds = $this->getRequest()->getParam('product');
        $productIds2List = $this->getRequest()->getParam('callbackval');
        $productIds2 = explode(',', $productIds2List);
        
        if (!is_array($productIds)) {
            $this->_getSession()->addError($this->__('Please select product(s)'));
        }
        else {
            try {
                foreach ($productIds as $productId) {
                    $link = array();
                    foreach($productIds2 as $relatedToThisId) {
                      if($productId != $relatedToThisId) {
                        $link[$relatedToThisId] = array('position' => null);
                      }
                    }
                    $product = Mage::getModel('catalog/product')->load($productId);
                    
                    // Fetch and append to already related products.
                    $oldRelatedProducts = $product->getCrossSellProducts();
                    foreach($oldRelatedProducts as $oldRelatedProduct) {
                      $link[$oldRelatedProduct->getId()] = array('position' => null);
                    }
                    
                    $product->setCrossSellLinkData($link);
                    if ($this->massactionEventDispatchEnabled)
                      Mage::dispatchEvent('catalog_product_prepare_save', 
                          array('product' => $product, 'request' => $this->getRequest()));
                    $product->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully set as cross-sells by products('.
                      $productIds2List.').', count($productIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
    
    
    
    
    
    /**
     *************************************************************************    
     * Up-Selling
     *************************************************************************
     */  
    /**
     * This will unrelate related product's relations.
     *          
     */     
    public function massUnUpSellAction() {
        $productIds = $this->getRequest()->getParam('product');
        if (!is_array($productIds)) {
            $this->_getSession()->addError($this->__('Please select product(s)'));
        } else {
            try {
                foreach ($productIds as $productId) {
                    $product = Mage::getModel('catalog/product')->load($productId);
                    $product->setUpSellLinkData(array());
                    if ($this->massactionEventDispatchEnabled)
                      Mage::dispatchEvent('catalog_product_prepare_save', 
                          array('product' => $product, 'request' => $this->getRequest()));
                    $product->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) now have 0 up-sells', count($productIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }         
    /** 
     * This will relate all products to a specifc list of products 
     *
     */     
    public function massUpSellToAction()
    {
        $productIds = $this->getRequest()->getParam('product');
        $productIds2List = $this->getRequest()->getParam('callbackval');
        $productIds2 = explode(',', $productIds2List);
        
        if (!is_array($productIds)) {
            $this->_getSession()->addError($this->__('Please select product(s)'));
        }
        else {
            try {
                foreach ($productIds as $productId) {
                    $link = array();
                    $product = Mage::getModel('catalog/product')->load($productId);
                    foreach($productIds2 as $relatedToThisId) {
                      if($productId != $relatedToThisId) {
                        $link[$relatedToThisId] = array('position' => null);
                      }
                    }
                    
                    // Fetch and append to already related products.
                    $oldRelatedProducts = $product->getUpSellProducts();
                    foreach($oldRelatedProducts as $oldRelatedProduct) {
                      $link[$oldRelatedProduct->getId()] = array('position' => null);
                    }
                    
                    $product->setUpSellLinkData($link);
                    if ($this->massactionEventDispatchEnabled)
                      Mage::dispatchEvent('catalog_product_prepare_save', 
                          array('product' => $product, 'request' => $this->getRequest()));
                    $product->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) are now up-sold by products('.
                      $productIds2List.').', count($productIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
    
     
}