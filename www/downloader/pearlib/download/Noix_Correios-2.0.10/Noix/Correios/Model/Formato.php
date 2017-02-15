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
 
class Noix_Correios_Model_Formato
{
    public function toOptionArray()
    {
        return array(
            array( 'value' => 1,
                'label' => Mage::helper('adminhtml')->__('Formato caixa/pacote') ),

            array( 'value' => 2,
                'label' => Mage::helper('adminhtml')->__('Formato rolo/prisma') )
        );
    }

}