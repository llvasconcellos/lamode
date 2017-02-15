<?php
/**
 * NOIX Internet
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL).
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @package    Noix_Correios
 * @copyright  Copyright (c) 2009 NOIX Internet [ magento@noixinternet.com.br ]
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Noix_Correios_Model_Mysql4_Relatorio_Calculosfrete_Collection extends Varien_Data_Collection_Db
{ 
    public function __construct()
    {
        $resources = Mage::getSingleton('core/resource');
        
        parent::__construct($resources->getConnection('noix_correios_read'));
 
        $this->_select->from(
        	array('subscriber' => $resources->getTableName('noix_correios/relatorio_calculosfrete')),
 	       	array('*')
        );
        
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('noix_correios/relatorio_calculosfrete'));
    }
}