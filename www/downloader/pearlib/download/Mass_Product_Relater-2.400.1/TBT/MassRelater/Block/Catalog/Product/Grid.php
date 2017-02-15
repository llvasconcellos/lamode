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
 * @category   TBT
 * @package    TBT_MassRelater
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml Enhanced customer grid block with Mass Relater features
 *
 * @category   TBT
 * @package    TBT_MassRelater
 * @author      Magento Core Team <core@magentocommerce.com>
 */
 
class TBT_MassRelater_Block_Catalog_Product_Grid extends TBT_Enhancedgrid_Block_Catalog_Product_Grid
{
    
    protected $isEnabled = array();

    public function __construct()
    {
        parent::__construct();
        $this->isEnabled = array(
            'all' => (int)Mage::getStoreConfig('enhancedgrid/massrelater/isenabled') === 1,
            'relation' => (int)Mage::getStoreConfig('enhancedgrid/massrelater/enablerelation') === 1,
            'crosssell' => (int)Mage::getStoreConfig('enhancedgrid/massrelater/enablecrosssell') === 1,
            'upsell' => (int)Mage::getStoreConfig('enhancedgrid/massrelater/enableupsell') === 1
        );
        
    }
    

    protected function _prepareMassaction()
    {
        if(!$this->isEnabled['all']) {
            return parent::_prepareMassaction();
        } else {
            parent::_prepareMassaction();
        }
        

        
        if($this->isEnabled['relation']) {
            // Divider
            $this->getMassactionBlock()->addItem('relateDivider', $this->getMADivider("Relate"));
            // Relate
            $this->getMassactionBlock()->addItem('relateProducts', array(
                'label' => $this->__('Relate To Each Other'),
                'url'   => $this->getUrl('massrelater/*/massRelateProducts', array('_current'=>true)),
                'confirm' => $this->__('Are you sure you would like to make the selected products related to each other?')
            ));
            // Relate To...
            $this->getMassactionBlock()->addItem('relateTo', array(
                'label' => $this->__('Add Relatives...'),
                'url'   => $this->getUrl('massrelater/*/massRelateTo', array('_current'=>true)),
                 'callback' => 'chooseWhatToRelateTo()'
            ));
            // Unrelate
            $this->getMassactionBlock()->addItem('unRelate', array(
                'label' => $this->__('Clear Relations'),
                'url'   => $this->getUrl('massrelater/*/massUnRelate', array('_current'=>true)),
                 'confirm' => $this->__('Are you sure you would like to remove these product\'s related links?')
            ));
        }
        
        
        if($this->isEnabled['crosssell']) {
            // Divider
            $this->getMassactionBlock()->addItem('crossSellDivider', $this->getMADivider("Cross-Sell"));
            // Cross Sell
            $this->getMassactionBlock()->addItem('crossSellProducts', array(
                'label' => $this->__('Cross-Sell To Each Other'),
                'url'   => $this->getUrl('massrelater/*/massCrossSellProducts', array('_current'=>true)),
                 'confirm' => $this->__('Are you sure you\'d like ot make the selected products cross-sell each other?')
            ));
            // Cross-sell To...
            $this->getMassactionBlock()->addItem('crossSellTo', array(
                'label' => $this->__('Add Cross-Sells...'),
                'url'   => $this->getUrl('massrelater/*/massCrossSellTo', array('_current'=>true)),
                 'callback' => 'chooseWhatToCrossSellTo()'
            ));
            // Un-Cross Sell
            $this->getMassactionBlock()->addItem('unCrossSell', array(
                'label' => $this->__('Clear Cross-Sell'),
                'url'   => $this->getUrl('massrelater/*/massUnCrossSell', array('_current'=>true)),
                 'confirm' => $this->__('Are you sure you would like to remove these product\'s cross sell links?')
            ));
        }
        
        
        if($this->isEnabled['upsell']) {
            // Divider
            $this->getMassactionBlock()->addItem('upSellDivider', $this->getMADivider("Up-Sell"));
            
            // Up-sell To...
            $this->getMassactionBlock()->addItem('upSellTo', array(
                'label' => $this->__('Add Up-Sells...'),
                'url'   => $this->getUrl('massrelater/*/massUpSellTo', array('_current'=>true)),
                 'callback' => 'chooseWhatToUpSellTo()'
            ));
            // Un Up-sell...
            $this->getMassactionBlock()->addItem('unUpSell', array(
                'label' => $this->__('Clear Up-Sells...'),
                'url'   => $this->getUrl('massrelater/*/massUnUpSell', array('_current'=>true)),
                 'confirm' => $this->__('Are you sure you would like to remove any up-sells for selected product(s)?')
            ));
        }
        

        return $this;
    }
}
