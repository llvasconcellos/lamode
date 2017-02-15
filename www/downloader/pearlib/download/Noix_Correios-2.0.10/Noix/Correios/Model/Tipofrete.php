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
 
class Noix_Correios_Model_Tipofrete
{
    public function toOptionArray()
    {
        return array(
            array( 'value' => Noix_Correios_Model_Carrier_Request::SERVICO_PAC_SEM_CONTRATO,
                'label' => Mage::helper('adminhtml')->__(Noix_Correios_Model_Carrier_Request::SERVICO_PAC_SEM_CONTRATO . ' - PAC sem contrato' ) ),

            array( 'value' => Noix_Correios_Model_Carrier_Request::SERVICO_PAC_COM_CONTRATO,
                'label' => Mage::helper('adminhtml')->__(Noix_Correios_Model_Carrier_Request::SERVICO_PAC_COM_CONTRATO . ' - PAC com contrato' ) ),

            array( 'value' => Noix_Correios_Model_Carrier_Request::SERVICO_SEDEX_SEM_CONTRATO,
                'label' => Mage::helper('adminhtml')->__(Noix_Correios_Model_Carrier_Request::SERVICO_SEDEX_SEM_CONTRATO . ' - Sedex sem contrato' ) ),

            array( 'value' => Noix_Correios_Model_Carrier_Request::SERVICO_SEDEX_COM_CONTRATO1,
                'label' => Mage::helper('adminhtml')->__(Noix_Correios_Model_Carrier_Request::SERVICO_SEDEX_COM_CONTRATO1 . ' - Sedex com contrato' ) ),

            array( 'value' => Noix_Correios_Model_Carrier_Request::SERVICO_SEDEX_COM_CONTRATO2,
                'label' => Mage::helper('adminhtml')->__(Noix_Correios_Model_Carrier_Request::SERVICO_SEDEX_COM_CONTRATO2 . ' - Sedex com contrato' ) ),

            array( 'value' => Noix_Correios_Model_Carrier_Request::SERVICO_SEDEX_COM_CONTRATO3,
                'label' => Mage::helper('adminhtml')->__(Noix_Correios_Model_Carrier_Request::SERVICO_SEDEX_COM_CONTRATO3 . ' - Sedex com contrato' ) ),

            array( 'value' => Noix_Correios_Model_Carrier_Request::SERVICO_ESEDEX_COM_CONTRATO,
                'label' => Mage::helper('adminhtml')->__(Noix_Correios_Model_Carrier_Request::SERVICO_ESEDEX_COM_CONTRATO . ' - E-Sedex com contrato' ) ),

            array( 'value' => Noix_Correios_Model_Carrier_Request::SERVICO_SEDEX_A_COBRAR_SEM_CONTRATO,
                'label' => Mage::helper('adminhtml')->__(Noix_Correios_Model_Carrier_Request::SERVICO_SEDEX_A_COBRAR_SEM_CONTRATO . ' - Sedex a cobrar sem contrato' ) ),

            array( 'value' => Noix_Correios_Model_Carrier_Request::SERVICO_SEDEX_10_SEM_CONTRATO,
                'label' => Mage::helper('adminhtml')->__(Noix_Correios_Model_Carrier_Request::SERVICO_SEDEX_10_SEM_CONTRATO . ' - Sedex 10 sem contrato' ) ),

            array( 'value' => Noix_Correios_Model_Carrier_Request::SERVICO_SEDEX_HOJE_SEM_CONTRATO,
                'label' => Mage::helper('adminhtml')->__(Noix_Correios_Model_Carrier_Request::SERVICO_SEDEX_HOJE_SEM_CONTRATO . ' - Sedex Hoje sem contrato' ) )
        );
    }

}