<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/LICENSE-L.txt
 *
 * @category   AW
 * @package    AW_Blog
 * @copyright  Copyright (c) 2009-2010 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/LICENSE-L.txt
 */

class AW_Blog_Helper_Data extends Mage_Core_Helper_Abstract
{
	const XML_PATH_ENABLED     		= 'blog/blog/enabled';
	const XML_PATH_TITLE       		= 'blog/blog/title';
	const XML_PATH_MENU_LEFT   		= 'blog/blog/menuLeft';
	const XML_PATH_MENU_RIGHT  		= 'blog/blog/menuRoght';
	const XML_PATH_FOOTER_ENABLED   = 'blog/blog/footerEnabled';
	const XML_PATH_LAYOUT      		= 'blog/blog/layout';

    public function isEnabled()
    {
        return Mage::getStoreConfig( self::XML_PATH_ENABLED );
    }
	
	public function isTitle()
    {
        return Mage::getStoreConfig( self::XML_PATH_TITLE );
    }
	
	public function isMenuLeft()
    {
        return Mage::getStoreConfig( self::XML_PATH_MENU_LEFT );
    }
	
	public function isMenuRight()
    {
        return Mage::getStoreConfig( self::XML_PATH_MENU_RIGHT );
    }
	
	public function isFooterEnabled()
    {
        return Mage::getStoreConfig( self::XML_PATH_FOOTER_ENABLED );
    }
	
	public function isLayout()
    {
        return Mage::getStoreConfig( self::XML_PATH_LAYOUT );
    }
	
	public function getUserName()
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        return trim("{$customer->getFirstname()} {$customer->getLastname()}");
    }

	public function getRoute(){
		$route = Mage::getStoreConfig('blog/blog/route');
		if (!$route){
			$route = "blog";
		}
		return $route;
	}

    public function getUserEmail()
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        return $customer->getEmail();
    }
}
