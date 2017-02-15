<?php
/**
 * Pedro Teixeira
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL).
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   PedroTeixeira
 * @package    PedroTeixeira_Correios
 * @copyright  Copyright (c) 2010 Pedro Teixeira (http://www.pteixeira.com.br)
 * @author     Pedro Teixeira <pedro@pteixeira.com.br>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class PedroTeixeira_Correios_Model_Source_PostMethods
{

    public function toOptionArray()
    {
        return array(
        	array('value'=>000, 'label'=>Mage::helper('adminhtml')->__('Frete Grátis')),
            array('value'=>40010, 'label'=>Mage::helper('adminhtml')->__('Sedex Sem Contrato (Correios)')),
            array('value'=>40096, 'label'=>Mage::helper('adminhtml')->__('Sedex Com Contrato (Correios/Locaweb)')),
            array('value'=>81019, 'label'=>Mage::helper('adminhtml')->__('E-Sedex Com Contrato (Correios/Locaweb)')),
            array('value'=>41025, 'label'=>Mage::helper('adminhtml')->__('PAC Normal (Locaweb)')),
            array('value'=>41106, 'label'=>Mage::helper('adminhtml')->__('PAC Sem Contrato (Correios)')),
            array('value'=>41068, 'label'=>Mage::helper('adminhtml')->__('PAC Com Contrato (Correios/Locaweb)')),
            array('value'=>40215, 'label'=>Mage::helper('adminhtml')->__('Sedex 10 (Correios)')),
            array('value'=>40290, 'label'=>Mage::helper('adminhtml')->__('Sedex HOJE (Correios)')),
            array('value'=>40045, 'label'=>Mage::helper('adminhtml')->__('Sedex a Cobrar (Correios)')),
        );
    }

}