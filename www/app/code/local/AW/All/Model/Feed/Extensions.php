<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/LICENSE-M1.txt
 *
 * @category   AW
 * @package    AW_All
 * @copyright  Copyright (c) 2009-2010 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/LICENSE-M1.txt
 */ 

class AW_All_Model_Feed_Extensions extends AW_All_Model_Feed_Abstract{
	
	 /**
     * Retrieve feed url
     *
     * @return string
     */
    public function getFeedUrl()
    {
		return AW_All_Helper_Config::EXTENSIONS_FEED_URL;
    }
	
	
	/**
	 * Checks feed
	 * @return 
	 */
	public function check(){
		if(!(Mage::app()->loadCache('aw_all_extensions_feed')) || (time()-Mage::app()->loadCache('aw_all_extensions_feed_lastcheck')) > Mage::getStoreConfig('awall/feed/check_frequency')){
			$this->refresh();
		}
	}
	
	public function refresh(){
		$exts = array();
		try{
			$Node = $this->getFeedData();
			if(!$Node) return false;
			foreach($Node->children() as $ext){
				$exts[(string)$ext->name] = array(
					'display_name' => (string)$ext->display_name,
					'version' => (string)$ext->version,
					'url'		=> (string)$ext->url
				);
			}
			
			Mage::app()->saveCache(serialize($exts), 'aw_all_extensions_feed');
			Mage::app()->saveCache(time(), 'aw_all_extensions_feed_lastcheck');
			return true;
		}catch(Exception $E){
			return false;			
		}
	}
}